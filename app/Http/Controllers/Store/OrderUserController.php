<?php

namespace App\Http\Controllers\Store;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Order;      // orders: order_id, user_id, status, total_amount
use App\Models\OrderItem;  // order_items: order_item_id, order_id, product_id, qty, price
use App\Models\Coupon;     // coupons: coupon_id, code, discount_amount, discount_percent, valid_from, valid_to, usage_limit, used_count
use App\Http\Controllers\Controller;

class OrderUserController extends Controller
{

    public function index()
    {
        $userId = auth()->id();

        // Nếu bạn đã có Eloquent quan hệ thì thay bằng with('items') ...
        $orders = DB::table('orders')
            ->leftJoin('delivery', 'delivery.order_id', '=', 'orders.order_id')
            ->where('orders.user_id', $userId)
            ->select(
                'orders.order_id',
                'orders.order_date',
                'orders.status', // trạng thái đơn
                'orders.total_amount',
                'delivery.delivery_status' // trạng thái vận chuyển (pending/shipping/delivered…)
            )
            ->orderByDesc('orders.order_id')
            ->paginate(10);

        // Badge map
        $paymentBadge = [
            'pending' => ['Chưa thanh toán', 'bg-amber-100 text-amber-700'],
            'processing' => ['Đã thanh toán', 'bg-emerald-100 text-emerald-700'],
            'confirmed' => ['Đã thanh toán', 'bg-emerald-100 text-emerald-700'],
            'completed' => ['Đã thanh toán', 'bg-emerald-100 text-emerald-700'],
            'cancelled' => ['Đã hủy', 'bg-gray-200 text-gray-700'],
        ];

        $shippingBadge = [
            null => ['Chưa tạo', 'bg-gray-100 text-gray-600'],
            'pending' => ['Chờ giao', 'bg-sky-100 text-sky-700'],
            'shipping' => ['Đang giao', 'bg-blue-100 text-blue-700'],
            'delivered' => ['Đã giao', 'bg-emerald-100 text-emerald-700'],
            'returned' => ['Hoàn hàng', 'bg-rose-100 text-rose-700'],
            'cancelled' => ['Hủy giao', 'bg-gray-200 text-gray-700'],
        ];

        return view('store.orders.index', compact('orders', 'paymentBadge', 'shippingBadge'));
    }
}
