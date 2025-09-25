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
            'payment_method'  => 'required|in:cod,payos',
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
                'user_id'      => Auth::id(),
                'status'       => $data['payment_method'] === 'cod' ? 'processing' : 'pending',
                'total_amount' => $grandTotal,
                'fullname'     => $data['fullname'],
                'phone'        => $data['phone'],
                'address'      => $data['address'],
                'note'         => $data['note'] ?? null,
                'coupon_code'  => $data['coupon'] ?? null,
            ]);

            foreach ($cart as $i) {
                OrderItem::create([
                    'order_id'   => $order->order_id,
                    'product_id' => $i['id'],
                    'quantity'   => (int)$i['qty'],
                    'price'      => (int)$i['price'],
                ]);
            }

            if ($data['payment_method'] === 'cod') {
                DB::commit();
                session()->forget('cart');
                return redirect()->route('store.orders.index')->with('success', 'Đặt hàng COD thành công.');
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
        $status    = $request->query('status');     // PAID|PENDING|...
        $orderCode = $request->query('orderCode');  // có thể có
        $cancel    = filter_var($request->query('cancel'), FILTER_VALIDATE_BOOL);

        if ($cancel || strtoupper((string)$status) === 'CANCELLED') {
            if ($orderCode) {
                Order::where('order_code', $orderCode)
                    ->where('status', 'pending')
                    ->update(['status' => 'cancelled']);
            }
            return redirect()->route('checkout.index')->with('error', 'Bạn đã hủy thanh toán.');
        }

        $clientId = env('PAYOS_CLIENT_ID');
        $apiKey   = env('PAYOS_API_KEY');

        // Dùng orderCode nếu có; nếu không, có thể đọc từ paymentLinkId (id path cho phép cả hai)
        $id = $orderCode ?: $request->query('id'); // id = paymentLinkId nếu PayOS trả

        try {
            $http = new Client([
                'base_uri' => 'https://api-merchant.payos.vn',
                'timeout'  => 15,
            ]);

            $res = $http->get('/v2/payment-requests/' . urlencode($id), [
                'headers' => [
                    'x-client-id' => $clientId,
                    'x-api-key'   => $apiKey,
                    'Accept'      => 'application/json',
                ],
            ]);

            $json = json_decode((string) $res->getBody(), true);
            $data = $json['data'] ?? [];

            if (strtoupper($data['status'] ?? '') === 'PAID') {
                $order = Order::where('order_code', $data['orderCode'] ?? $orderCode)
                    ->orWhere('payment_link_id', $data['paymentLinkId'] ?? null)
                    ->first();

                if ($order && $order->status !== 'confirmed') {
                    DB::transaction(function () use ($order) {
                        $order->update(['status' => 'confirmed']);
                        // TODO: trừ kho theo order_items nếu bạn muốn trừ lúc đã thanh toán
                        if (!empty($order->coupon_code)) {
                            Coupon::where('code', $order->coupon_code)->increment('used_count');
                        }
                    });
                }

                session()->forget('cart');
                return redirect()->route('store.orders.index')->with('success', 'Thanh toán thành công.');
            }

            return redirect()->route('store.orders.index')->with('warning', 'Thanh toán chưa hoàn tất. Trạng thái: ' . ($data['status'] ?? $status ?? 'N/A'));
        } catch (\Throwable $e) {
            report($e);
            return redirect()->route('checkout.index')->with('error', 'Lỗi xác minh thanh toán: ' . $e->getMessage());
        }
    }

    public function payosCancel(Request $request)
    {
        $orderCode = $request->query('orderCode');
        if ($orderCode) {
            Order::where('order_code', $orderCode)
                ->where('status', 'pending')
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
