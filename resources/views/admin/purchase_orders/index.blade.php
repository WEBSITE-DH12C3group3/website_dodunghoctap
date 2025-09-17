@extends('layouts.app')

@section('content')
<div class="bg-white/80 dark:bg-slate-900/50 backdrop-blur p-6 rounded-2xl shadow ring-1 ring-slate-900/5 dark:ring-white/10">
    <h2 class="text-xl font-semibold text-slate-800 dark:text-slate-100 mb-6">Danh sách phiếu nhập kho</h2>

    <a href="{{ route('admin.purchase_orders.create') }}" class="mb-4 inline-block rounded-xl px-4 py-2 text-sm font-medium text-white bg-brand-600 hover:bg-brand-700 shadow-sm">Tạo phiếu nhập mới</a>

    @if ($purchaseOrders->isEmpty())
        <p class="text-slate-600 dark:text-slate-300">Chưa có phiếu nhập kho nào.</p>
    @else
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-700 dark:text-slate-200">
                <thead class="bg-slate-100 dark:bg-slate-800">
                    <tr>
                        <th class="px-4 py-3 font-semibold">Mã phiếu</th>
                        <th class="px-4 py-3 font-semibold">Tổng tiền</th>
                        <th class="px-4 py-3 font-semibold">Ngày nhập</th>
                        <th class="px-4 py-3 font-semibold">Người nhập</th>
                        <th class="px-4 py-3 font-semibold">Nhà cung cấp</th>
                        <th class="px-4 py-3 font-semibold">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($purchaseOrders as $purchaseOrder)
                    <tr class="border-b border-slate-200 dark:border-slate-700">
                        <td class="px-4 py-3">{{ $purchaseOrder->code }}</td>
                        <td class="px-4 py-3">{{ number_format($purchaseOrder->total_amount, 0, ',', '.') }} VNĐ</td>
                        <td class="px-4 py-3">{{ date('d/m/Y', strtotime($purchaseOrder->order_date)) }}</td>
                        <td class="px-4 py-3">{{ $purchaseOrder->user ? $purchaseOrder->user->full_name : 'Không xác định' }}</td>
                        <td class="px-4 py-3">{{ $purchaseOrder->supplier ? $purchaseOrder->supplier->supplier_name : 'Không xác định' }}</td>
                        <td class="px-4 py-3 flex gap-2">
                            <a href="{{ route('admin.purchase_orders.show', $purchaseOrder->purchase_order_id) }}" class="text-blue-600 hover:underline">Xem</a>
                            <a href="{{ route('admin.purchase_orders.edit', $purchaseOrder->purchase_order_id) }}" class="text-green-600 hover:underline">Sửa</a>
                            <form action="{{ route('admin.purchase_orders.destroy', $purchaseOrder->purchase_order_id) }}" method="POST" onsubmit="return confirm('Bạn có chắc muốn xóa phiếu nhập này?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline">Xóa</button>
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