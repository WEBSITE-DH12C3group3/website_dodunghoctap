<?php

namespace App\Http\Controllers\Store;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

// dùng đúng tên/cột theo SQL của bạn (orders, order_items, coupons)
use App\Models\Order;      // orders: order_id, user_id, status, total_amount
use App\Models\OrderItem;  // order_items: order_item_id, order_id, product_id, qty, price
use App\Models\Coupon;     // coupons: coupon_id, code, discount_amount, discount_percent, valid_from, valid_to, usage_limit, used_count
use App\Http\Controllers\Controller;

class CheckoutController extends Controller
{
    public function index(Request $request)
    {
        // Lấy giỏ: tùy dự án bạn đang dùng session hay carts table.
        // Ở đây demo từ session 'cart' dạng [{id,name,price,qty,image}]
        $cart = collect(session('cart', []));
        if ($cart->isEmpty()) {
            return redirect()->back()->with('error', 'Giỏ hàng trống.');
        }

        $subTotal = $cart->sum(fn($i) => $i['price'] * $i['qty']);
        $shipping = 0; // Miễn phí như mock
        $couponCode = $request->get('coupon');

        [$discount, $coupon] = $this->calcDiscount($couponCode, $subTotal);

        $grandTotal = max(0, $subTotal - $discount + $shipping);

        return view('store.checkout', [
            'cart'        => $cart,
            'subTotal'    => $subTotal,
            'shipping'    => $shipping,
            'discount'    => $discount,
            'grandTotal'  => $grandTotal,
            'couponCode'  => $couponCode,
        ]);
    }

    public function store(Request $request)
    {
        // Validate form
        $data = $request->validate([
            'fullname'        => 'required|string|max:255',
            'phone'           => 'required|string|max:20',
            'address'         => 'required|string|max:500',
            'payment_method'  => 'required|in:cod,vietqr',
            'coupon'          => 'nullable|string|max:50',
        ]);

        $cart = collect(session('cart', []));
        if ($cart->isEmpty()) {
            return back()->with('error', 'Giỏ hàng trống.');
        }

        $subTotal = $cart->sum(fn($i) => $i['price'] * $i['qty']);
        $shipping = 0;

        [$discount, $coupon] = $this->calcDiscount($data['coupon'] ?? null, $subTotal);

        $grandTotal = max(0, $subTotal - $discount + $shipping);

        DB::beginTransaction();
        try {
            // Tạo đơn
            $order = Order::create([
                'user_id'      => Auth::id(),
                'status'       => $data['payment_method'] === 'cod' ? 'processing' : 'pending',
                'total_amount' => $grandTotal,
            ]);

            foreach ($cart as $item) {
                OrderItem::create([
                    'order_id'   => $order->order_id,
                    'product_id' => $item['id'],
                    'qty'   => $item['qty'],
                    'price'      => $item['price'],
                ]);
            }

            // Cộng số lần dùng coupon (nếu có)
            if ($coupon) {
                $coupon->increment('used_count');
            }

            // Clear giỏ
            session()->forget('cart');

            // Xử lý thanh toán
            if ($data['payment_method'] === 'vietqr') {
                $vietqrUrl = $this->buildVietQr(
                    bankBin: '970422',                    // BIN VCB (ví dụ)
                    accountNo: '0123456789',                // số tài khoản nhận tiền
                    accountName: 'CONG TY TNHH PEAKVL',     // tên chủ tài khoản
                    amount: (int) $grandTotal,
                    addInfo: 'Thanh toan don hang #' . $order->order_id
                );

                DB::commit();

                // Hiển thị trang QR để KH quét
                return view('store.payment_qr', [
                    'order'     => $order,
                    'vietqrUrl' => $vietqrUrl,
                    'grandTotal' => $grandTotal,
                ]);
            }

            DB::commit();
            return redirect()->route('orders.index') // nếu bạn có trang danh sách đơn
                ->with('success', 'Đặt hàng thành công (COD). Mã đơn #' . $order->order_id);
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Có lỗi khi đặt hàng: ' . $e->getMessage());
        }
    }

    /**
     * Tính giảm giá từ coupon (amount hoặc percent) theo bảng coupons của bạn.
     * Trả về [discountAmount, CouponModel|null]
     */
    private function calcDiscount(?string $code, float $subTotal): array
    {
        if (!$code) return [0, null];

        $coupon = Coupon::where('code', $code)->first();
        if (!$coupon) return [0, null];

        // Kiểm tra hạn & giới hạn
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
        $discount = min($discount, $subTotal);

        return [$discount, $coupon];
    }

    /**
     * Build URL ảnh VietQR (img.vietqr.io) để nhúng `<img src="...">`
     */
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
