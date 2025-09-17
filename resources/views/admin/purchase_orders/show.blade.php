@extends('layouts.app')

@section('content')
<div class="bg-white/80 dark:bg-slate-900/50 backdrop-blur p-6 rounded-2xl shadow ring-1 ring-slate-900/5 dark:ring-white/10">
    <h2 class="text-xl font-semibold text-slate-800 dark:text-slate-100 mb-6">Chi tiết phiếu nhập kho #{{ $purchaseOrder->code }}</h2>

    <div class="grid gap-6 md:grid-cols-2">
        <div>
            <h3 class="font-semibold mb-2">Thông tin phiếu nhập</h3>
            <p><strong>Mã phiếu:</strong> {{ $purchaseOrder->code }}</p>
            <p><strong>Tổng tiền:</strong> {{ number_format($purchaseOrder->total_amount, 0, ',', '.') }} VNĐ</p>
            <p><strong>Ngày nhập:</strong> {{ date('d/m/Y', strtotime($purchaseOrder->order_date)) }}</p>
            <p><strong>Người nhập:</strong> {{ $purchaseOrder->user ? $purchaseOrder->user->full_name : 'Không xác định' }}</p>
            <p><strong>Nhà cung cấp:</strong> {{ $purchaseOrder->supplier ? $purchaseOrder->supplier->supplier_name : 'Không xác định' }}</p>
        </div>

        <div>
            <h3 class="font-semibold mb-2">Thông tin nhà cung cấp</h3>
            @if ($purchaseOrder->supplier)
                <p><strong>Tên:</strong> {{ $purchaseOrder->supplier->supplier_name }}</p>
                <p><strong>Thông tin liên hệ:</strong> {{ $purchaseOrder->supplier->contact_info ?? 'Không có' }}</p>
            @else
                <p class="text-red-600">Không có thông tin nhà cung cấp.</p>
            @endif
        </div>
    </div>

    <div class="mt-6">
        <h3 class="font-semibold mb-2">Sản phẩm nhập kho</h3>
        @if ($purchaseOrder->items->isEmpty())
            <p class="text-slate-600 dark:text-slate-300">Chưa có sản phẩm nào.</p>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-700 dark:text-slate-200">
                    <thead class="bg-slate-100 dark:bg-slate-800">
                        <tr>
                            <th class="px-4 py-3 font-semibold">Sản phẩm</th>
                            <th class="px-4 py-3 font-semibold">Số lượng</th>
                            <th class="px-4 py-3 font-semibold">Giá nhập (VNĐ)</th>
                            <th class="px-4 py-3 font-semibold">Tổng (VNĐ)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($purchaseOrder->items as $item)
                        <tr class="border-b border-slate-200 dark:border-slate-700">
                            <td class="px-4 py-3">{{ $item->product ? $item->product->product_name : 'Sản phẩm đã xóa' }}</td>
                            <td class="px-4 py-3">{{ $item->quantity }}</td>
                            <td class="px-4 py-3">{{ number_format($item->price, 0, ',', '.') }}</td>
                            <td class="px-4 py-3">{{ number_format($item->quantity * $item->price, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <div class="mt-6 flex gap-4">
        <a href="{{ route('admin.purchase_orders.edit', $purchaseOrder->purchase_order_id) }}" class="rounded-xl px-4 py-2 text-sm font-medium text-white bg-green-600 hover:bg-green-700 shadow-sm">Sửa</a>
        <a href="{{ route('admin.purchase_orders') }}" class="rounded-xl px-4 py-2 text-sm font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-200/60 dark:hover:bg-slate-800/60">Quay lại</a>
    </div>
</div>
@endsection