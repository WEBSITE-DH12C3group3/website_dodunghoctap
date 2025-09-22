<?php

namespace App\Http\Controllers\Store;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\VnpayService;
use App\Http\Controllers\Controller;

class PaymentController extends Controller
{
    // Tạo đơn từ session cart, chuyển hướng sang VNPAY
    public function vnpayStart(Request $request, VnpayService $vnpay)
    {
        $cart = session('cart', []);
        if (empty($cart)) return back()->with('error', 'Giỏ hàng trống.');

        $total = array_sum(array_map(fn($i) => (int)$i['price'] * (int)$i['qty'], $cart));
        if ($total <= 0) return back()->with('error', 'Tổng tiền không hợp lệ.');

        $orderId = DB::table('orders')->insertGetId([
            'user_id'      => auth()->id(),
            'total_amount' => $total,
            'status'       => 'pending',
            'order_date'   => now(),
            'payment_method' => 'vnpay',
        ]);

        foreach ($cart as $item) {
            DB::table('order_items')->insert([
                'order_id'   => $orderId,
                'product_id' => $item['id'],
                'price'      => (int)$item['price'],
                'quantity'   => (int)$item['qty'],
            ]);
        }

        $url = $vnpay->createPaymentUrl($orderId, $total, $request->ip(), route('checkout.vnpay.return'));
        logger()->info('VNPAY URL = ' . $url);

        return redirect()->away($url);
    }

    // Trang return từ VNPAY
    public function vnpayReturn(Request $request, VnpayService $vnpay)
    {
        $valid    = $vnpay->verifyReturn($request->all());
        $respCode = $request->input('vnp_ResponseCode'); // '00' = thành công
        [$orderId] = explode('-', (string)$request->input('vnp_TxnRef', '0-0'), 2);

        // Lưu log giao dịch (bảng vnpay nếu bạn đã có)
        DB::table('vnpay')->insert([
            'order_id'       => (int)$orderId,
            'transaction_id' => $request->input('vnp_TransactionNo'),
            'amount'         => ((int)$request->input('vnp_Amount')) / 100,
            'payment_status' => ($valid && $respCode === '00') ? 'success' : 'failed',
            'payment_date'   => now(),
        ]);

        if ($valid && $respCode === '00') {
            DB::table('orders')->where('order_id', (int)$orderId)->update(['status' => 'paid']);
            // Xóa giỏ hàng
            session()->forget(['cart', 'cart_total']);
            return redirect()->route('home')->with('success', 'Thanh toán thành công!');
        }

        DB::table('orders')->where('order_id', (int)$orderId)->update(['status' => 'cancelled']);
        return redirect()->route('home')->with('error', 'Thanh toán không thành công.');
    }
}
