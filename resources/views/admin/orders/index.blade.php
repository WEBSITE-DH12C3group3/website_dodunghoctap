@extends('layouts.app')

@section('content')
<div class="bg-white/80 dark:bg-slate-900/50 backdrop-blur p-6 rounded-2xl shadow ring-1 ring-slate-900/5 dark:ring-white/10">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-semibold text-slate-800 dark:text-slate-100">Danh sách đơn hàng</h2>
    </div>

    <div class="mb-4">
        <form action="{{ route('admin.orders') }}" method="GET" class="flex items-center space-x-2">
            <input type="text" name="search" placeholder="Tìm kiếm theo ID, tên khách hàng hoặc trạng thái..." 
                   class="flex-1 rounded-xl px-4 py-2 text-sm text-slate-700 dark:text-slate-200 
                          bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 
                          focus:outline-none focus:ring-2 focus:ring-brand-500"
                   value="{{ request('search') }}">
            <button type="submit" class="rounded-xl px-4 py-2 text-sm font-medium text-white bg-brand-600 hover:bg-brand-700 shadow-sm">
                Tìm kiếm
            </button>
            @if(request('search'))
                <a href="{{ route('admin.orders') }}" class="text-slate-500 hover:text-red-500">Xóa tìm kiếm</a>
            @endif
        </form>
    </div>

    @if (session('ok'))
        <div class="mb-4 rounded-xl border border-green-200/60 dark:border-green-400/30 bg-green-50/80 dark:bg-green-900/30 px-4 py-3 text-green-700 dark:text-green-200 shadow-sm">
            {{ session('ok') }}
        </div>
    @endif
    @if (session('error'))
        <div class="mb-4 rounded-xl border border-red-200/60 dark:border-red-400/30 bg-red-50/80 dark:bg-red-900/30 px-4 py-3 text-red-700 dark:text-red-200 shadow-sm">
            {{ session('error') }}
        </div>
    @endif

    @if ($orders->isEmpty() && request('search'))
        <p class="text-slate-600 dark:text-slate-300">Không tìm thấy đơn hàng nào phù hợp với "{{ request('search') }}".</p>
    @elseif ($orders->isEmpty())
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
                    @php
                        // Ánh xạ trạng thái đơn hàng sang tiếng Việt
                        $statusMap = [
                            'pending' => 'Chờ xử lý',
                            'confirmed' => 'Đã xác nhận',
                            'cancelled' => 'Đã hủy',
                            'delivered' => 'Đã giao',
                        ];

                        // Ánh xạ phương thức thanh toán (nếu cần)
                        $paymentMethodMap = [
                            'cod' => 'Thanh toán khi nhận hàng',
                            'bank_transfer' => 'Chuyển khoản ngân hàng',
                            // Thêm nếu có thêm phương thức
                        ];
                    @endphp
                    @foreach ($orders as $order)
                    <tr class="border-b border-slate-200 dark:border-slate-700">
                        <td class="px-4 py-3">{{ $order->order_id }}</td>
                        <td class="px-4 py-3">{{ $order->user ? $order->user->full_name : 'Khách vãng lai' }}</td>
                        <td class="px-4 py-3">{{ $order->order_date }}</td>
                        <td class="px-4 py-3">{{ number_format($order->total_amount, 0, ',', '.') }} VNĐ</td>
                        <td class="px-4 py-3">{{ $paymentMethodMap[$order->payment_method] ?? ucfirst($order->payment_method) }}</td>
                        <td class="px-4 py-3">{{ $statusMap[$order->status] ?? ucfirst($order->status) }}</td>
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
        @if ($orders->hasPages())
            <div class="mt-4">
                {{ $orders->links() }}
            </div>
        @endif
    @endif
</div>
@endsection