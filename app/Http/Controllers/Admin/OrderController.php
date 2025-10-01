<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Delivery;
use App\Models\OrderItem;
use App\Exports\OrdersExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        Log::info('OrderController@index called, fetching orders');

        try {
            $query = Order::with('user', 'delivery');

            // 🔍 Tìm kiếm
            if ($request->filled('search')) {
                $searchTerm = $request->input('search');
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('order_id', 'LIKE', "%{$searchTerm}%")
                        ->orWhereHas('user', function ($q) use ($searchTerm) {
                            $q->where('full_name', 'LIKE', "%{$searchTerm}%")
                                ->orWhere('email', 'LIKE', "%{$searchTerm}%")
                                ->orWhere('phone', 'LIKE', "%{$searchTerm}%");
                        })
                        ->orWhere('status', 'LIKE', "%{$searchTerm}%");
                });
                Log::info('Order search: ' . $searchTerm);
            }

            // 📅 Lọc theo ngày đặt
            if ($request->filled('date_from')) {
                $query->whereDate('order_date', '>=', $request->date_from);
            }
            if ($request->filled('date_to')) {
                $query->whereDate('order_date', '<=', $request->date_to);
            }

            // 💰 Lọc theo tổng tiền
            if ($request->filled('amount_min')) {
                $query->where('total_amount', '>=', (float) $request->amount_min);
            }
            if ($request->filled('amount_max')) {
                $query->where('total_amount', '<=', (float) $request->amount_max);
            }

            // 💳 Lọc theo phương thức thanh toán
            if ($request->filled('payment_method')) {
                $query->where('payment_method', $request->payment_method);
            }

            // 📦 Lọc theo trạng thái
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            // 📑 Lấy danh sách
            $orders = $query->orderBy('order_id', 'desc')
                ->paginate(10)
                ->withQueryString();

            Log::info('Orders fetched (page): ' . $orders->count() . ', total: ' . $orders->total());

            return view('admin.orders.index', compact('orders'));
        } catch (\Exception $e) {
            Log::error('Error in OrderController@index: ' . $e->getMessage());
            return redirect()->route('admin.orders')
                ->with('error', 'Lỗi khi tải danh sách đơn hàng: ' . $e->getMessage());
        }
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

        $previousStatus = $order->status;

        $order->update([
            'status' => $validated['status'],
        ]);

        if ($previousStatus !== $order->status) {
            $order->syncProductSoldForStatusChange($previousStatus, $order->status);
        }

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

    public function export(Request $request)
    {
        try {
            $format = $request->query('format', 'pdf');

            // Áp dụng các bộ lọc tương tự như trong index
            $query = Order::with(['user', 'items.product', 'delivery'])
                ->when($request->search, function ($q) use ($request) {
                    $searchTerm = $request->input('search');
                    $q->where('order_id', 'LIKE', "%{$searchTerm}%")
                        ->orWhereHas('user', function ($q) use ($searchTerm) {
                            $q->where('full_name', 'LIKE', "%{$searchTerm}%")
                                ->orWhere('email', 'LIKE', "%{$searchTerm}%")
                                ->orWhere('phone', 'LIKE', "%{$searchTerm}%");
                        })
                        ->orWhere('status', 'LIKE', "%{$searchTerm}%");
                })
                ->when($request->date_from, fn($q) => $q->whereDate('order_date', '>=', $request->date_from))
                ->when($request->date_to, fn($q) => $q->whereDate('order_date', '<=', $request->date_to))
                ->when($request->amount_min, fn($q) => $q->where('total_amount', '>=', (float) $request->amount_min))
                ->when($request->amount_max, fn($q) => $q->where('total_amount', '<=', (float) $request->amount_max))
                ->when($request->payment_method, fn($q) => $q->where('payment_method', $request->payment_method))
                ->when($request->status, fn($q) => $q->where('status', $request->status));

            $orders = $query->orderBy('order_id', 'desc')->get();

            // Chuẩn bị dữ liệu cho báo cáo
            $data = [
                'orders' => $orders,
                'periodText' => $request->date_from && $request->date_to
                    ? Carbon::parse($request->date_from)->format('d/m/Y') . ' - ' . Carbon::parse($request->date_to)->format('d/m/Y')
                    : 'Tất cả',
                'userName' => auth()->user()->full_name,
                'date' => now()->format('d/m/Y H:i'),
            ];

            // Xuất PDF
            if ($format === 'pdf') {
                $pdf = Pdf::loadView('admin.orders.pdf', $data);
                return $pdf->download('bao-cao-don-hang-' . now()->format('YmdHis') . '.pdf');
            }

            // Xuất Excel
            return Excel::download(new OrdersExport($orders), 'bao-cao-don-hang-' . now()->format('YmdHis') . '.xlsx');
        } catch (\Exception $e) {
            Log::error('Error in OrderController@export: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Lỗi khi xuất báo cáo: ' . $e->getMessage());
        }
    }
}
