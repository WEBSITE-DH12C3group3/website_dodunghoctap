@extends('layouts.app')

@section('content')
@php
    // Ánh xạ trạng thái đơn hàng sang tiếng Việt và gán màu Tailwind
    $statusMap = [
        'pending' => ['label' => 'Chờ xử lý', 'color' => 'yellow'],
        'confirmed' => ['label' => 'Đã xác nhận', 'color' => 'indigo'],
        'cancelled' => ['label' => 'Đã hủy', 'color' => 'red'],
        'delivered' => ['label' => 'Đã giao', 'color' => 'green'],
    ];

    // Ánh xạ trạng thái giao hàng sang tiếng Việt và gán màu Tailwind
    $deliveryStatusMap = [
        'pending' => ['label' => 'Chờ giao', 'color' => 'yellow'],
        'shipping' => ['label' => 'Đang giao', 'color' => 'blue'],
        'delivered' => ['label' => 'Đã giao', 'color' => 'green'],
        'returned' => ['label' => 'Hoàn hàng', 'color' => 'red'],
        'cancelled' => ['label' => 'Hủy giao', 'color' => 'gray'],
    ];

    // Trạng thái thanh toán
    $paymentMethodMap = [
        'cod' => 'Thanh toán khi nhận hàng (COD)',
        'bank_transfer' => 'Chuyển khoản ngân hàng',
        // Thêm nếu có thêm phương thức
    ];

    // Lấy màu và label cho trạng thái đơn hàng
    $orderStatus = $statusMap[$order->status] ?? ['label' => ucfirst($order->status), 'color' => 'gray'];
    $deliveryStatus = $order->delivery ? ($deliveryStatusMap[$order->delivery->delivery_status] ?? ['label' => ucfirst($order->delivery->delivery_status), 'color' => 'gray']) : null;

@endphp

<div class="max-w-6xl mx-auto py-10"> {{-- Centering the content and setting max width --}}
    <div class="bg-white dark:bg-slate-800/90 backdrop-blur-md p-8 rounded-3xl shadow-2xl ring-1 ring-slate-900/5 dark:ring-white/10 transition-all duration-300">

        <header class="mb-8 border-b pb-4 border-slate-200 dark:border-slate-700">
            <h1 class="text-3xl font-extrabold text-slate-800 dark:text-slate-100 flex items-center">
                Chi tiết đơn hàng
                <span class="ml-3 inline-flex items-center rounded-full bg-slate-100 px-3 py-1 text-sm font-medium text-slate-800 dark:bg-slate-700 dark:text-slate-200 shadow-md">
                    #{{ $order->order_id }}
                </span>
            </h1>
        </header>

        <div class="grid gap-8 lg:grid-cols-3 md:grid-cols-2">
            {{-- THÔNG TIN CHUNG ĐƠN HÀNG --}}
            <div class="p-5 rounded-xl bg-slate-50 dark:bg-slate-700/50 shadow-md transition-all duration-300 hover:shadow-lg hover:ring-2 hover:ring-indigo-500/50">
                <h3 class="text-lg font-bold mb-3 text-slate-700 dark:text-slate-200 border-b pb-2 border-slate-200 dark:border-slate-600 flex items-center">
                    <svg class="w-5 h-5 inline mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                    Thông tin chung
                </h3>
                <dl class="space-y-3 text-sm text-slate-600 dark:text-slate-300">
                    <div><strong class="font-medium text-slate-800 dark:text-slate-100 w-36 inline-block">Khách hàng:</strong> {{ $order->user ? $order->user->full_name : 'Khách vãng lai' }}</div>
                    <div><strong class="font-medium text-slate-800 dark:text-slate-100 w-36 inline-block">Ngày đặt:</strong> {{ date('d/m/Y H:i', strtotime($order->order_date)) }}</div>
                    <div><strong class="font-medium text-slate-800 dark:text-slate-100 w-36 inline-block">Thanh toán:</strong> {{ $paymentMethodMap[$order->payment_method] ?? ucfirst($order->payment_method) }}</div>
                    <div>
                        <strong class="font-medium text-slate-800 dark:text-slate-100 w-36 inline-block">Trạng thái:</strong>
                        <span class="inline-flex items-center rounded-md bg-{{ $orderStatus['color'] }}-100 px-2 py-1 text-xs font-medium text-{{ $orderStatus['color'] }}-700 ring-1 ring-inset ring-{{ $orderStatus['color'] }}-600/20 dark:bg-{{ $orderStatus['color'] }}-900/50 dark:text-{{ $orderStatus['color'] }}-300 transition-colors duration-200">
                            {{ $orderStatus['label'] }}
                        </span>
                    </div>
                    <div class="pt-2 border-t border-slate-200 dark:border-slate-600">
                        <strong class="font-bold text-lg text-green-600 dark:text-green-400 w-36 inline-block">Tổng tiền:</strong>
                        <span class="text-xl font-extrabold text-green-600 dark:text-green-400">{{ number_format($order->total_amount, 0, ',', '.') }} VNĐ</span>
                    </div>
                </dl>
            </div>
            
            {{-- THÔNG TIN GIAO HÀNG --}}
            <div class="p-5 rounded-xl bg-slate-50 dark:bg-slate-700/50 shadow-md transition-all duration-300 hover:shadow-lg hover:ring-2 hover:ring-indigo-500/50 md:col-span-2 lg:col-span-2">
                <h3 class="text-lg font-bold mb-3 text-slate-700 dark:text-slate-200 border-b pb-2 border-slate-200 dark:border-slate-600 flex items-center">
                    <svg class="w-5 h-5 inline mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 2l-2 2m-5-2l-2 2M1 10h14a2 2 0 012 2v7H1v-7a2 2 0 012-2zM4 4h4v4H4V4zm7 0h4v4h-4V4z"></path></svg>
                    Thông tin giao hàng
                </h3>
                @if ($order->delivery)
                    <dl class="grid sm:grid-cols-2 gap-x-6 gap-y-3 text-sm text-slate-600 dark:text-slate-300">
                        <div><strong class="font-medium text-slate-800 dark:text-slate-100 w-40 inline-block">Người nhận:</strong> {{ $order->delivery->receiver_name }}</div>
                        <div><strong class="font-medium text-slate-800 dark:text-slate-100 w-40 inline-block">Điện thoại:</strong> {{ $order->delivery->phone }}</div>
                        <div class="sm:col-span-2"><strong class="font-medium text-slate-800 dark:text-slate-100 w-40 inline-block">Địa chỉ:</strong> {{ $order->delivery->address }}</div>
                        <div><strong class="font-medium text-slate-800 dark:text-slate-100 w-40 inline-block">Đơn vị V/C:</strong> {{ $order->delivery->shipping_provider }} ({{ ucfirst($order->delivery->shipping_type) }})</div>
                        <div><strong class="font-medium text-slate-800 dark:text-slate-100 w-40 inline-block">Ngày giao dự kiến:</strong> {{ $order->delivery->expected_delivery_date ?? 'Chưa xác định' }}</div>
                        <div class="sm:col-span-2">
                            <strong class="font-medium text-slate-800 dark:text-slate-100 w-40 inline-block">Trạng thái giao:</strong>
                            <span class="inline-flex items-center rounded-md bg-{{ $deliveryStatus['color'] }}-100 px-2 py-1 text-xs font-medium text-{{ $deliveryStatus['color'] }}-700 ring-1 ring-inset ring-{{ $deliveryStatus['color'] }}-600/20 dark:bg-{{ $deliveryStatus['color'] }}-900/50 dark:text-{{ $deliveryStatus['color'] }}-300 transition-colors duration-200">
                                {{ $deliveryStatus['label'] }}
                            </span>
                        </div>
                        @if ($order->delivery->note)
                        <div class="sm:col-span-2 pt-2 border-t border-slate-200 dark:border-slate-600">
                            <strong class="font-medium text-slate-800 dark:text-slate-100 inline-block">Ghi chú:</strong> {{ $order->delivery->note }}
                        </div>
                        @endif
                    </dl>
                @else
                    <div class="p-3 bg-red-100 dark:bg-red-900/50 border border-red-300 dark:border-red-700 rounded-lg">
                        <p class="text-sm font-medium text-red-700 dark:text-red-300">⚠️ Chưa có thông tin giao hàng.</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- SẢN PHẨM TRONG ĐƠN --}}
        <div class="mt-10">
            <h3 class="text-xl font-bold mb-4 text-slate-800 dark:text-slate-100 border-b pb-3 border-slate-200 dark:border-slate-700 flex items-center">
                <svg class="w-6 h-6 inline mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                Các mặt hàng trong đơn
            </h3>
            @if ($order->items->isEmpty())
                <p class="p-4 bg-yellow-50 dark:bg-yellow-900/50 border border-yellow-300 dark:border-yellow-700 rounded-lg text-sm text-yellow-700 dark:text-yellow-300">
                    Chưa có mặt hàng nào được ghi nhận trong đơn hàng này.
                </p>
            @else
                <div class="overflow-x-auto rounded-xl shadow-lg ring-1 ring-slate-900/5 dark:ring-white/10">
                    <table class="min-w-full text-left text-sm text-slate-700 dark:text-slate-200 divide-y divide-slate-200 dark:divide-slate-700">
                        <thead class="bg-slate-100 dark:bg-slate-700 uppercase tracking-wider">
                            <tr>
                                <th class="px-6 py-3 font-semibold text-xs">Sản phẩm</th>
                                <th class="px-6 py-3 font-semibold text-xs text-center">Số lượng</th>
                                <th class="px-6 py-3 font-semibold text-xs text-right">Giá (VNĐ)</th>
                                <th class="px-6 py-3 font-semibold text-xs text-right">Tổng (VNĐ)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            @foreach ($order->items as $item)
                            <tr class="bg-white dark:bg-slate-800 transition-colors duration-200 hover:bg-indigo-50 dark:hover:bg-slate-700/70">
                                <td class="px-6 py-4 font-medium">{{ $item->product ? $item->product->product_name : 'Sản phẩm đã xóa' }}</td>
                                <td class="px-6 py-4 text-center">{{ $item->quantity }}</td>
                                <td class="px-6 py-4 text-right">{{ number_format($item->price, 0, ',', '.') }}</td>
                                <td class="px-6 py-4 text-right font-semibold text-slate-900 dark:text-slate-100">{{ number_format($item->quantity * $item->price, 0, ',', '.') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        {{-- ACTIONS --}}
        <div class="mt-8 pt-6 border-t border-slate-200 dark:border-slate-700 flex gap-4">
            {{-- Thêm nút sửa, in hoặc hành động khác nếu cần --}}
            {{-- Ví dụ: Nút Sửa --}}
            {{-- <a href="{{ route('admin.orders.edit', $order->order_id) }}" 
               class="inline-flex items-center justify-center rounded-xl px-6 py-3 text-sm font-semibold text-white bg-indigo-600 shadow-md transition-all duration-300 transform hover:scale-[1.02] hover:bg-indigo-700 hover:shadow-lg focus:outline-none focus:ring-4 focus:ring-indigo-500/50">
                Cập nhật trạng thái
            </a> --}}
            
            {{-- Nút Quay lại --}}
            <a href="{{ route('admin.orders') }}" 
               class="inline-flex items-center justify-center rounded-xl px-6 py-3 text-sm font-medium text-slate-700 dark:text-slate-200 bg-slate-100 dark:bg-slate-700/50 transition-colors duration-200 hover:bg-slate-200 dark:hover:bg-slate-700 focus:outline-none focus:ring-4 focus:ring-slate-300/50">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Quay lại danh sách
            </a>
        </div>
    </div>
</div>
@endsection