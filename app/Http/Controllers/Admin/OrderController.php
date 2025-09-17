<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Delivery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    public function index()
    {
        Log::info('OrderController@index called, fetching orders');
        $orders = Order::with('user', 'delivery')->get();
        Log::info('Orders fetched: ' . $orders->count() . ' items');
        return view('admin.orders.index', compact('orders'));
    }

    public function show($id)
    {
        $order = Order::with('user', 'items.product', 'delivery')->findOrFail($id);
        return view('admin.orders.show', compact('order'));
    }

    public function edit($id)
    {
        $order = Order::with('delivery')->findOrFail($id);
        return view('admin.orders.edit', compact('order'));
    }

    public function update(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,cancelled,delivered',
            'shipping_type' => 'nullable|in:standard,express',
            'shipping_provider' => 'nullable|in:GHTK,GHN,Viettel Post,Other',
            'delivery_status' => 'nullable|string|max:50',
            'expected_delivery_date' => 'nullable|date',
        ]);

        $order->update([
            'status' => $validated['status'],
        ]);

        if ($order->delivery) {
            $order->delivery->update([
                'shipping_type' => $validated['shipping_type'],
                'shipping_provider' => $validated['shipping_provider'],
                'delivery_status' => $validated['delivery_status'],
                'expected_delivery_date' => $validated['expected_delivery_date'],
            ]);
        } else {
            Delivery::create([
                'order_id' => $order->order_id,
                'receiver_name' => 'Unknown',
                'phone' => 'Unknown',
                'email' => 'unknown@example.com',
                'address' => 'Unknown',
                'delivery_status' => $validated['delivery_status'] ?? 'pending',
                'expected_delivery_date' => $validated['expected_delivery_date'] ?? now()->addDays(3),
                'shipping_type' => $validated['shipping_type'] ?? 'standard',
                'shipping_provider' => $validated['shipping_provider'] ?? 'GHTK',
            ]);
        }

        return redirect()->route('admin.orders')->with('ok', 'Cập nhật đơn hàng thành công!');
    }

    public function destroy($id)
    {
        $order = Order::findOrFail($id);
        $order->status = 'cancelled';
        $order->save();

        return redirect()->route('admin.orders')->with('ok', 'Đơn hàng đã bị hủy!');
    }
}