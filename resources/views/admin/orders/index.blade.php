@extends('layouts.app')

@section('content')
<div class="bg-white/80 dark:bg-slate-900/50 backdrop-blur p-6 rounded-2xl shadow ring-1 ring-slate-900/5 dark:ring-white/10">
    <h2 class="text-xl font-semibold text-slate-800 dark:text-slate-100 mb-6">Danh sách đơn hàng</h2>

    @if ($orders->isEmpty())
        <p class="text-slate-600 dark:text-slate-300">Chưa có đơn hàng nào.</p>
    @else
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-700 dark:text-slate-200">
                <thead class="bg-slate-100 dark:bg-slate-800">
                    <tr>
                        <th class="px-4 py-3 font-semibold">ID</th>
                        <th class="px-4 py-3 font-semibold">Khách hàng</th>
                        <th class="px-4 py-3 font-semibold">Ngày đặt</th>
                        <th class="px-4 py-3 font-semibold">Tổng tiền</th>
                        <th class="px-4 py-3 font-semibold">Thanh toán</th>
                        <th class="px-4 py-3 font-semibold">Trạng thái</th>
                        <th class="px-4 py-3 font-semibold">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($orders as $order)
                    <tr class="border-b border-slate-200 dark:border-slate-700">
                        <td class="px-4 py-3">{{ $order->order_id }}</td>
                        <td class="px-4 py-3">{{ $order->user ? $order->user->full_name : 'Khách vãng lai' }}</td>
                        <td class="px-4 py-3">{{ $order->order_date }}</td>
                        <td class="px-4 py-3">{{ number_format($order->total_amount, 0, ',', '.') }} VNĐ</td>
                        <td class="px-4 py-3">{{ ucfirst($order->payment_method) }}</td>
                        <td class="px-4 py-3">{{ ucfirst($order->status) }}</td>
                        <td class="px-4 py-3 flex gap-2">
                            <a href="{{ route('admin.orders.show', $order->order_id) }}" class="text-blue-600 hover:underline">Xem chi tiết</a>
                            <a href="{{ route('admin.orders.edit', $order->order_id) }}" class="text-green-600 hover:underline">Cập nhật</a>
                            <form action="{{ route('admin.orders.destroy', $order->order_id) }}" method="POST" onsubmit="return confirm('Bạn có chắc muốn hủy đơn hàng này?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline">Hủy</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection