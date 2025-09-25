<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProfileController extends Controller
{
   public function index(Request $request)
{
    $user = $request->user();

    // Đơn hàng: alias total_amount -> total, order_date -> created_at
    $orders = DB::table('orders')
        ->where('user_id', $user->user_id)
        ->select('order_id','status','total_amount as total','order_date as created_at')
        ->orderBy('order_date','desc')
        ->paginate(5);

    // Yêu thích
    $favourites = DB::table('favourite')
        ->join('products', 'favourite.product_id', '=', 'products.product_id')
        ->where('favourite.user_id', $user->user_id)
        ->select('products.product_id', 'products.product_name', 'products.image_url', 'products.price')
        ->paginate(5);

    return view('store.profile.index', compact('user', 'orders', 'favourites'));
}


    public function update(Request $request)
    {
        $data = $request->validate([
            'full_name' => ['required', 'string', 'max:150'],
            'phone'     => ['nullable', 'string', 'max:20'],
            'address'   => ['nullable', 'string', 'max:255'],
        ], [], [
            'full_name' => 'Họ và tên',
            'phone'     => 'Số điện thoại',
            'address'   => 'Địa chỉ',
        ]);

        $user = $request->user();
        $user->full_name = trim($data['full_name']);
        $user->phone     = $data['phone'] ?? null;
        $user->address   = $data['address'] ?? null;
        $user->save();

        return back()->with('status', 'Cập nhật thông tin thành công!');
    }
}