@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto py-10"> {{-- Centering the content and setting max width --}}
    <div class="bg-white dark:bg-slate-800/90 backdrop-blur-md p-8 rounded-3xl shadow-2xl ring-1 ring-slate-900/5 dark:ring-white/10 transition-all duration-300 hover:shadow-3xl">
        <header class="mb-8 border-b pb-4 border-slate-200 dark:border-slate-700">
            <h1 class="text-3xl font-extrabold text-slate-800 dark:text-slate-100 flex items-center">
                Chi tiết phiếu nhập kho
                <span class="ml-3 inline-flex items-center rounded-full bg-indigo-100 px-3 py-1 text-sm font-medium text-indigo-800 dark:bg-indigo-900/50 dark:text-indigo-300 shadow-md">
                    #{{ $purchaseOrder->code }}
                </span>
            </h1>
        </header>

        <div class="grid gap-8 md:grid-cols-2">
            {{-- THÔNG TIN PHIẾU NHẬP --}}
            <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-700/50 transition-all duration-300 hover:ring-2 hover:ring-indigo-500/50">
                <h3 class="text-lg font-bold mb-3 text-slate-700 dark:text-slate-200 border-b pb-2 border-slate-200 dark:border-slate-600">
                    <svg class="w-5 h-5 inline mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                    Thông tin phiếu nhập
                </h3>
                <dl class="space-y-2 text-sm text-slate-600 dark:text-slate-300">
                    <div><strong class="font-medium text-slate-800 dark:text-slate-100 w-32 inline-block">Mã phiếu:</strong> {{ $purchaseOrder->code }}</div>
                    <div><strong class="font-medium text-slate-800 dark:text-slate-100 w-32 inline-block">Ngày nhập:</strong> {{ date('d/m/Y', strtotime($purchaseOrder->order_date)) }}</div>
                    <div><strong class="font-medium text-slate-800 dark:text-slate-100 w-32 inline-block">Người nhập:</strong> {{ $purchaseOrder->user ? $purchaseOrder->user->full_name : 'Không xác định' }}</div>
                    <div><strong class="font-medium text-slate-800 dark:text-slate-100 w-32 inline-block">Nhà cung cấp:</strong> {{ $purchaseOrder->supplier ? $purchaseOrder->supplier->supplier_name : 'Không xác định' }}</div>
                    <div class="pt-2 border-t border-slate-200 dark:border-slate-600">
                        <strong class="font-bold text-lg text-green-600 dark:text-green-400 w-32 inline-block">Tổng tiền:</strong>
                        <span class="text-lg font-extrabold text-green-600 dark:text-green-400">{{ number_format($purchaseOrder->total_amount, 0, ',', '.') }} VNĐ</span>
                    </div>
                </dl>
            </div>

            {{-- THÔNG TIN NHÀ CUNG CẤP --}}
            <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-700/50 transition-all duration-300 hover:ring-2 hover:ring-indigo-500/50">
                <h3 class="text-lg font-bold mb-3 text-slate-700 dark:text-slate-200 border-b pb-2 border-slate-200 dark:border-slate-600">
                    <svg class="w-5 h-5 inline mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-1 4h1m-5 4v-4m-2 4v-4m-2 4v-4"></path></svg>
                    Thông tin nhà cung cấp
                </h3>
                @if ($purchaseOrder->supplier)
                    <dl class="space-y-2 text-sm text-slate-600 dark:text-slate-300">
                        <div><strong class="font-medium text-slate-800 dark:text-slate-100 w-32 inline-block">Tên:</strong> {{ $purchaseOrder->supplier->supplier_name }}</div>
                        <div><strong class="font-medium text-slate-800 dark:text-slate-100 w-32 inline-block">Liên hệ:</strong> {{ $purchaseOrder->supplier->contact_info ?? 'Không có' }}</div>
                        {{-- *Thêm một số thông tin khác nếu có* --}}
                    </dl>
                @else
                    <div class="p-3 bg-red-100 dark:bg-red-900/50 border border-red-300 dark:border-red-700 rounded-lg">
                        <p class="text-sm font-medium text-red-700 dark:text-red-300">⚠️ Không có thông tin nhà cung cấp.</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- SẢN PHẨM NHẬP KHO --}}
        <div class="mt-10">
            <h3 class="text-xl font-bold mb-4 text-slate-800 dark:text-slate-100 border-b pb-3 border-slate-200 dark:border-slate-700">
                <svg class="w-6 h-6 inline mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                Danh sách sản phẩm nhập kho
            </h3>
            @if ($purchaseOrder->items->isEmpty())
                <div class="p-4 bg-yellow-50 dark:bg-yellow-900/50 border border-yellow-300 dark:border-yellow-700 rounded-lg">
                    <p class="text-sm font-medium text-yellow-700 dark:text-yellow-300">Chưa có sản phẩm nào được ghi nhận trong phiếu nhập này.</p>
                </div>
            @else
                <div class="overflow-x-auto rounded-xl shadow-lg ring-1 ring-slate-900/5 dark:ring-white/10">
                    <table class="min-w-full text-left text-sm text-slate-700 dark:text-slate-200 divide-y divide-slate-200 dark:divide-slate-700">
                        <thead class="bg-slate-100 dark:bg-slate-700 uppercase tracking-wider">
                            <tr>
                                <th class="px-6 py-3 font-semibold text-xs">Sản phẩm</th>
                                <th class="px-6 py-3 font-semibold text-xs text-center">Số lượng</th>
                                <th class="px-6 py-3 font-semibold text-xs text-right">Giá nhập (VNĐ)</th>
                                <th class="px-6 py-3 font-semibold text-xs text-right">Tổng (VNĐ)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            @foreach ($purchaseOrder->items as $item)
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
            {{-- Nút Sửa: Hiệu ứng button 3D nhẹ, màu nổi bật --}}
            <a href="{{ route('admin.purchase_orders.edit', $purchaseOrder->purchase_order_id) }}" 
               class="inline-flex items-center justify-center rounded-xl px-6 py-3 text-sm font-semibold text-white bg-green-600 shadow-md transition-all duration-300 transform hover:scale-[1.02] hover:bg-green-700 hover:shadow-lg focus:outline-none focus:ring-4 focus:ring-green-500/50">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                Sửa phiếu nhập
            </a>
            
            {{-- Nút Quay lại: Màu trung tính, hiệu ứng nhấn --}}
            <a href="{{ route('admin.purchase_orders') }}" 
               class="inline-flex items-center justify-center rounded-xl px-6 py-3 text-sm font-medium text-slate-700 dark:text-slate-200 bg-slate-100 dark:bg-slate-700/50 transition-colors duration-200 hover:bg-slate-200 dark:hover:bg-slate-700 focus:outline-none focus:ring-4 focus:ring-slate-300/50">
                Quay lại danh sách
            </a>
        </div>
    </div>
</div>
@endsection