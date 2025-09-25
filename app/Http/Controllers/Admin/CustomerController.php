<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    public function index(Request $r)
    {
        // Subquery: tổng hợp theo user_id để tránh ONLY_FULL_GROUP_BY
        $ordersAgg = DB::table('orders')
            ->select(
                'user_id',
                DB::raw('COUNT(order_id) AS orders_count'),
                DB::raw('COALESCE(SUM(total_amount),0) AS orders_sum')
            )
            ->groupBy('user_id');

        $q = User::query()
            ->with('role')
            ->whereHas('role', fn($qr) => $qr->where('role_name', 'customer'));

        if ($s = $r->get('search')) {
            $q->where(function ($w) use ($s) {
                $w->where('full_name', 'like', "%$s%")
                  ->orWhere('email', 'like', "%$s%")
                  ->orWhere('phone', 'like', "%$s%");
            });
        }

        // JOIN subquery đã tổng hợp
        $q->leftJoinSub($ordersAgg, 'oagg', function ($join) {
              $join->on('oagg.user_id', '=', 'users.user_id');
          })
          ->select(
              'users.*',
              DB::raw('COALESCE(oagg.orders_count, 0) AS orders_count'),
              DB::raw('COALESCE(oagg.orders_sum, 0) AS orders_sum')
          )
          ->orderBy('users.user_id', 'desc');

        $customers = $q->paginate(20);

        return view('admin.customers.index', compact('customers'));
    }

    public function show($id)
    {
        $customer = User::with('role')
            ->whereHas('role', fn($qr) => $qr->where('role_name', 'customer'))
            ->findOrFail($id);

        $orders = DB::table('orders')
            ->where('user_id', $id)
            ->orderByDesc('order_date')
            ->limit(20)
            ->get();

        return view('admin.customers.show', compact('customer', 'orders'));
    }
}
