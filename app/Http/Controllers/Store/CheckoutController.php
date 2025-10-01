<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Coupon;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cookie;

class CheckoutController extends Controller
{
    public function index(Request $request)
    {
        $cart = collect(session('cart', []));
        if ($cart->isEmpty()) {
            return redirect()->back()->with('error', 'Giỏ hàng trống.');
        }

        $subTotal   = $cart->sum(fn($i) => $i['price'] * $i['qty']);
        $shipping   = 0;
        $couponCode = $request->get('coupon');

        [$discount, $coupon] = $this->calcDiscount($couponCode, $subTotal);
        $grandTotal = max(0, $subTotal - $discount + $shipping);

        return view('store.checkout', compact(
            'cart',
            'subTotal',
            'shipping',
            'discount',
            'grandTotal',
            'couponCode'
        ));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'fullname'        => 'required|string|max:255',
            'phone'           => 'required|string|max:30',
            'address'         => 'required|string|max:255',
            'note'            => 'nullable|string|max:500',
            'coupon'          => 'nullable|string|max:100',
            'payment_method'  => 'required|in:cod,bank_transfer,momo,vnpay,payos',
            'payment_channel' => ['nullable', 'string', 'max:50'],
        ]);

        $cart = collect(session('cart', []));
        if ($cart->isEmpty()) {
            return back()->with('error', 'Giỏ hàng trống.');
        }

        $subTotal = $cart->sum(fn($i) => $i['price'] * $i['qty']);
        $shipping = 0;
        [$discount, $coupon] = $this->calcDiscount($data['coupon'] ?? null, $subTotal);
        $grandTotal = (int) max(0, $subTotal - $discount + $shipping);

        DB::beginTransaction();
        try {
            $order = Order::create([
                'user_id'        => Auth::id(),
                'status'         => match ($data['payment_method']) {
                    'cod'    => 'pending',
                    'payos'  => 'processing',
                    'bank_transfer' => 'processing',
                    default  => 'pending',
                },
                'total_amount'   => $grandTotal,
                'payment_method' => $data['payment_method'], // DB có cột này
            ]);

            // Thêm thông tin giao hàng vào bảng delivery
            DB::table('delivery')->insert([
                'order_id'      => $order->order_id,
                'receiver_name' => $data['fullname'],
                'phone'         => $data['phone'],
                'email'        => Auth::user()->email,
                'address'       => $data['address'],
                'note'          => $data['note'] ?? null,
                'delivery_status' => 'pending',
                'shipping_type'   => 'standard',
                'shipping_provider' => 'GHTK',
            ]);


            foreach ($cart as $i) {
                OrderItem::create([
                    'order_id'   => $order->order_id,
                    'product_id' => $i['id'],
                    'quantity'   => (int)$i['qty'],
                    'price'      => (int)$i['price'],
                ]);
            }
            if ($coupon) {
                $coupon->increment('used_count');
            }

            $order->syncProductSoldForStatusChange(null, $order->status);

            if ($data['payment_method'] === 'cod') {
                DB::commit();
                session()->forget('cart');
                Cookie::queue(Cookie::forget('cart'));
                return redirect()->route('store.orders.index')->with('success', 'Đặt hàng COD thành công.');
            }
            // ... bên trong store()
            if (
                $data['payment_method'] === 'bank_transfer'
                && ($data['payment_channel'] ?? '') === 'vietqr'
            ) {

                $bankBin     = env('VIETQR_BANK_BIN', '970422');
                $accountNo   = env('VIETQR_ACCOUNT_NO', '2009122004');
                $accountName = env('VIETQR_ACCOUNT_NAME', 'CONG TY TNHH PEAKVL');

                $addInfo = 'Thanh toan don hang #' . $order->order_id;

                // TẠO ĐÚNG TÊN BIẾN: $qrUrl
                $qrUrl = $this->buildVietQr(
                    bankBin: $bankBin,
                    accountNo: $accountNo,
                    accountName: $accountName,
                    amount: (int) $grandTotal,
                    addInfo: $addInfo
                );

                // clear cart, commit...
                if (!empty($coupon)) $coupon->increment('used_count');
                session()->forget('cart');
                Cookie::queue(Cookie::forget('cart'));
                DB::commit();

                // TRUYỀN ĐÚNG TÊN SANG VIEW: 'qrUrl' => $qrUrl
                return view('store.payment_qr', [
                    'order'       => $order,
                    'qrUrl'       => $qrUrl,          // <- quan trọng
                    'amount'      => $grandTotal,
                    'bankBin'     => $bankBin,
                    'accountNo'   => $accountNo,
                    'accName'     => $accountName,    // view có thể dùng accName
                    'accountName' => $accountName,    // hoặc accountName — truyền cả 2 cho chắc
                ]);
            }


            // ==== PAYOS qua REST ====
            $clientId    = env('PAYOS_CLIENT_ID');
            $apiKey      = env('PAYOS_API_KEY');
            $checksumKey = env('PAYOS_CHECKSUM_KEY');
            $returnUrl   = env('PAYOS_RETURN_URL');
            $cancelUrl   = env('PAYOS_CANCEL_URL');

            // Tạo orderCode duy nhất (int)
            $orderCode = (int) ($order->order_id * 1000 + (int)(microtime(true) * 1000) % 1000);

            $payload = [
                'orderCode'   => $orderCode,
                'amount'      => $grandTotal,
                'description' => 'Thanh toan don hang #' . $order->order_id,
                'buyerName'   => $data['fullname'],
                'buyerPhone'  => $data['phone'],
                'items'       => $cart->map(fn($i) => [
                    'name'        => $i['name'] ?? ('SP#' . $i['id']),
                    'quantity'    => (int)$i['qty'],
                    'price'       => (int)$i['price'],
                ])->values()->all(),
                'cancelUrl'   => $cancelUrl,
                'returnUrl'   => $returnUrl,
                // expiredAt: optional (unix timestamp int32)
            ];

            // Theo tài liệu, signature = HMAC_SHA256(checksum_key, "amount=...&cancelUrl=...&description=...&orderCode=...&returnUrl=...")
            $payload['signature'] = $this->makePayosSignature($payload, $checksumKey);

            $http = new Client([
                'base_uri' => 'https://api-merchant.payos.vn',
                'timeout'  => 15,
            ]);

            $res = $http->post('/v2/payment-requests', [
                'headers' => [
                    'x-client-id' => $clientId,
                    'x-api-key'   => $apiKey,
                    'Content-Type' => 'application/json',
                    'Accept'      => 'application/json',
                ],
                'json' => $payload,
            ]);

            $json = json_decode((string) $res->getBody(), true);
            if (!isset($json['data']['checkoutUrl'])) {
                throw new \RuntimeException('Không nhận được checkoutUrl từ PayOS.');
            }

            $paymentLinkId = $json['data']['paymentLinkId'] ?? null;
            $order->update([
                'order_code'       => $json['data']['orderCode'] ?? $orderCode,
                'payment_link_id'  => $paymentLinkId,
            ]);

            DB::commit();
            return redirect()->away($json['data']['checkoutUrl']);
        } catch (\Throwable $e) {
            DB::rollBack();
            report($e);
            return back()->with('error', 'Không thể tạo phiên PayOS: ' . $e->getMessage());
        }
    }

    public function payosReturn(Request $request)
    {
        $status    = $request->query('status');     // PAID | PENDING | CANCELLED ...
        $orderCode = $request->query('orderCode');  // dạng đã gửi đi

        // ---- Cancel từ return url ----
        if ($request->boolean('cancel') || strtoupper((string)$status) === 'CANCELLED') {
            if ($orderCode) {
                $orderId = intdiv((int)$orderCode, 1000);
                Order::where('order_id', $orderId)
                    ->whereIn('status', ['pending', 'processing'])
                    ->update(['status' => 'cancelled']);
            }
            return redirect()->route('checkout.index')->with('error', 'Bạn đã hủy thanh toán.');
        }

        // ---- Xác minh trạng thái từ PayOS (GET /v2/payment-requests/{id}) ----
        try {
            $clientId = env('PAYOS_CLIENT_ID');
            $apiKey   = env('PAYOS_API_KEY');

            $id   = $orderCode ?: $request->query('id'); // có thể dùng lại orderCode
            $http = new \GuzzleHttp\Client(['base_uri' => 'https://api-merchant.payos.vn', 'timeout' => 15]);

            $res  = $http->get('/v2/payment-requests/' . urlencode((string)$id), [
                'headers' => ['x-client-id' => $clientId, 'x-api-key' => $apiKey, 'Accept' => 'application/json'],
            ]);
            $data = json_decode((string)$res->getBody(), true)['data'] ?? [];

            if (strtoupper($data['status'] ?? '') === 'PAID') {
                // Lấy lại order_id từ orderCode PayOS trả về
                $oc      = (int)($data['orderCode'] ?? $orderCode);
                $orderId = intdiv($oc, 1000);

                $order = Order::where('order_id', $orderId)->first();
                if ($order && $order->status !== 'confirmed') {
                    DB::transaction(function () use ($order) {
                        $order->update(['status' => 'confirmed']);
                        // nếu có logic trừ kho/sold thì gọi ở đây (sau khi đã confirmed)
                        if (!empty($order->coupon_code)) {
                            Coupon::where('code', $order->coupon_code)->increment('used_count');
                        }
                    });
                }

                session()->forget('cart');
                Cookie::queue(Cookie::forget('cart'));
                return redirect()->route('store.orders.index')->with('success', 'Thanh toán thành công.');
            }

            return redirect()->route('store.orders.index')
                ->with('warning', 'Thanh toán chưa hoàn tất. Trạng thái: ' . ($data['status'] ?? $status ?? 'N/A'));
        } catch (\Throwable $e) {
            report($e);
            return redirect()->route('checkout.index')->with('error', 'Lỗi xác minh thanh toán: ' . $e->getMessage());
        }
    }

    public function payosCancel(Request $request)
    {
        $orderCode = $request->query('orderCode');
        if ($orderCode) {
            $orderId = intdiv((int)$orderCode, 1000);  // lấy lại order_id
            Order::where('order_id', $orderId)
                ->whereIn('status', ['pending', 'processing'])
                ->update(['status' => 'cancelled']);
        }
        return redirect()->route('checkout.index')->with('error', 'Bạn đã hủy thanh toán.');
    }



    private function calcDiscount(?string $code, float $subTotal): array
    {
        if (!$code) return [0, null];
        $coupon = Coupon::where('code', $code)->first();
        if (!$coupon) return [0, null];

        $todayOk = (!$coupon->valid_from || now()->toDateString() >= $coupon->valid_from)
            && (!$coupon->valid_to   || now()->toDateString() <= $coupon->valid_to);
        $limitOk = (!$coupon->usage_limit || $coupon->used_count < $coupon->usage_limit);
        if (!$todayOk || !$limitOk) return [0, null];

        $discount = 0;
        if (!is_null($coupon->discount_amount)) {
            $discount = (float)$coupon->discount_amount;
        } elseif (!is_null($coupon->discount_percent)) {
            $discount = round($subTotal * ((int)$coupon->discount_percent) / 100);
        }
        return [min($discount, $subTotal), $coupon];
    }

    /**
     * Tạo chữ ký theo hướng dẫn PayOS (tham số sort alphabet & nối “key=value” bằng &)
     * signature = HMAC_SHA256(checksum_key, "amount=...&cancelUrl=...&description=...&orderCode=...&returnUrl=...")
     */
    private function makePayosSignature(array $data, string $checksumKey): string
    {
        $pairs = [
            'amount'      => (string)$data['amount'],
            'cancelUrl'   => (string)$data['cancelUrl'],
            'description' => (string)$data['description'],
            'orderCode'   => (string)$data['orderCode'],
            'returnUrl'   => (string)$data['returnUrl'],
        ];
        ksort($pairs); // theo alphabet (để chắc chắn)
        $raw = urldecode(http_build_query($pairs, '', '&')); // amount=...&cancelUrl=...&...
        return hash_hmac('sha256', $raw, $checksumKey);
    }


    private function buildVietQr(string $bankBin, string $accountNo, string $accountName, int $amount, string $addInfo): string
    {
        $base = "https://img.vietqr.io/image/{$bankBin}-{$accountNo}-compact2.png";
        $qs   = http_build_query([
            'amount'      => $amount,
            'addInfo'     => $addInfo,
            'accountName' => $accountName,
        ]);
        return $base . '?' . $qs;
    }
}
