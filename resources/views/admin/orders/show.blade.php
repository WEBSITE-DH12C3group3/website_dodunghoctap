@extends('layouts.app')

@section('content')
<div class="bg-white/80 dark:bg-slate-900/50 backdrop-blur p-6 rounded-2xl shadow ring-1 ring-slate-900/5 dark:ring-white/10">
    <h2 class="text-xl font-semibold text-slate-800 dark:text-slate-100 mb-6">Chi tiết đơn hàng #{{ $order->order_id }}</h2>

    <div class="grid gap-6 md:grid-cols-2">
        <div>
            <h3 class="font-semibold mb-2">Thông tin đơn hàng</h3>
            <p>Khách hàng: {{ $order->user ? $order->user->full_name : 'Khách vãng lai' }}</p>
            <p>Ngày đặt: {{ $order->order_date }}</p>
            <p>Tổng tiền: {{ number_format($order->total_amount, 0, ',', '.') }} VNĐ</p>
            <p>Thanh toán: {{ ucfirst($order->payment_method) }}</p>
            <p>Trạng thái: {{ ucfirst($order->status) }}</p>
        </div>

        <div>
            <h3 class="font-semibold mb-2">Thông tin giao hàng</h3>
            @if ($order->delivery)
                <p>Tên người nhận: {{ $order->delivery->receiver_name }}</p>
                <p>Số điện thoại: {{ $order->delivery->phone }}</p>
                <p>Email: {{ $order->delivery->email }}</p>
                <p>Địa chỉ: {{ $order->delivery->address }}</p>
                <p>Ghi chú: {{ $order->delivery->note ?? 'Không có' }}</p>
                <p>Trạng thái giao: {{ ucfirst($order->delivery->delivery_status) }}</p>
                <p>Ngày giao dự kiến: {{ $order->delivery->expected_delivery_date ?? 'Chưa xác định' }}</p>
                <p>Loại hình vận chuyển: {{ ucfirst($order->delivery->shipping_type) }}</p>
                <p>Đơn vị vận chuyển: {{ $order->delivery->shipping_provider }}</p>
            @else
                <p class="text-red-600">Chưa có thông tin giao hàng.</p>
            @endif
        </div>
    </div>

    <div class="mt-6">
        <h3 class="font-semibold mb-2">Các mặt hàng trong đơn</h3>
        @if ($order->items->isEmpty())
            <p class="text-slate-600 dark:text-slate-300">Chưa có mặt hàng nào.</p>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-700 dark:text-slate-200">
                    <thead class="bg-slate-100 dark:bg-slate-800">
                        <tr>
                            <th class="px-4 py-3 font-semibold">Sản phẩm</th>
                            <th class="px-4 py-3 font-semibold">Số lượng</th>
                            <th class="px-4 py-3 font-semibold">Giá</th>
                            <th class="px-4 py-3 font-semibold">Tổng</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($order->items as $item)
                        <tr class="border-b border-slate-200 dark:border-slate-700">
                            <td class="px-4 py-3">{{ $item->product ? $item->product->product_name : 'Sản phẩm đã xóa' }}</td>
                            <td class="px-4 py-3">{{ $item->quantity }}</td>
                            <td class="px-4 py-3">{{ number_format($item->price, 0, ',', '.') }} VNĐ</td>
                            <td class="px-4 py-3">{{ number_format($item->quantity * $item->price, 0, ',', '.') }} VNĐ</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <a href="{{ route('admin.orders') }}" class="mt-6 inline-block rounded-xl px-4 py-2 text-sm font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-200/60 dark:hover:bg-slate-800/60">Quay lại danh sách</a>
</div>
@endsection