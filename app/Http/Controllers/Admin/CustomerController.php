<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $perPage = 10;

        // Tổng hợp orders theo user_id
        $ordersAgg = DB::table('orders')
            ->select(
                'user_id',
                DB::raw('COUNT(order_id) as orders_count'),
                DB::raw('COALESCE(SUM(total_amount), 0) as orders_sum')
            )
            ->groupBy('user_id');

        // Query chính: join roles để chỉ lấy role = customer, join subquery orders
        $query = DB::table('users')
            ->leftJoin('roles', 'users.role_id', '=', 'roles.role_id')
            ->leftJoinSub($ordersAgg, 'oagg', function ($join) {
                $join->on('oagg.user_id', '=', 'users.user_id');
            })
            ->where('roles.role_name', '=', 'customer')
            ->select(
                'users.user_id',
                'users.full_name',
                'users.email',
                'users.phone',
                DB::raw('COALESCE(oagg.orders_count, 0) as orders_count'),
                DB::raw('COALESCE(oagg.orders_sum, 0) as orders_sum')
            );

        // Filter: tìm kiếm tên/email/phone
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('users.full_name', 'like', "%{$search}%")
                  ->orWhere('users.email', 'like', "%{$search}%")
                  ->orWhere('users.phone', 'like', "%{$search}%");
            });
        }

        // Filter: số đơn hàng (min / max) — dùng whereRaw trên cột của subquery
        if ($request->filled('orders_min')) {
            $query->whereRaw('COALESCE(oagg.orders_count, 0) >= ?', [(int) $request->orders_min]);
        }
        if ($request->filled('orders_max')) {
            $query->whereRaw('COALESCE(oagg.orders_count, 0) <= ?', [(int) $request->orders_max]);
        }

        // Filter: tổng chi tiêu (min / max)
        if ($request->filled('spend_min')) {
            $query->whereRaw('COALESCE(oagg.orders_sum, 0) >= ?', [(float) $request->spend_min]);
        }
        if ($request->filled('spend_max')) {
            $query->whereRaw('COALESCE(oagg.orders_sum, 0) <= ?', [(float) $request->spend_max]);
        }

        // Sắp xếp & paginate
        $query->orderBy('users.user_id', 'desc');

        $customers = $query->paginate($perPage);

        return view('admin.customers.index', compact('customers'));
    }

    public function show($id)
    {
        // Kiểm tra đảm bảo user có role = customer
        $customer = User::with('role')
            ->whereHas('role', function ($qr) {
                $qr->where('role_name', 'customer');
            })
            ->findOrFail($id);

        // Lấy 20 đơn gần nhất của khách
        $orders = DB::table('orders')
            ->where('user_id', $id)
            ->orderByDesc('order_date')
            ->limit(20)
            ->get();

        return view('admin.customers.show', compact('customer', 'orders'));
    }
}
