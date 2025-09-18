@extends('layouts.app')

@section('content')
<div class="bg-white/80 dark:bg-slate-900/50 backdrop-blur p-6 rounded-2xl shadow ring-1 ring-slate-900/5 dark:ring-white/10">
    <h2 class="text-xl font-semibold text-slate-800 dark:text-slate-100 mb-6">Chi tiết nhà cung cấp</h2>

    <div class="grid gap-4">
        <div>
            <span class="font-medium text-slate-700 dark:text-slate-200">ID:</span>
            <span class="text-slate-600 dark:text-slate-300">{{ $supplier->supplier_id }}</span>
        </div>
        <div>
            <span class="font-medium text-slate-700 dark:text-slate-200">Tên nhà cung cấp:</span>
            <span class="text-slate-600 dark:text-slate-300">{{ $supplier->supplier_name }}</span>
        </div>
        <div>
            <span class="font-medium text-slate-700 dark:text-slate-200">Thông tin liên hệ:</span>
            <span class="text-slate-600 dark:text-slate-300">{{ $supplier->contact_info ?? 'N/A' }}</span>
        </div>
        <div>
            <span class="font-medium text-slate-700 dark:text-slate-200">Ngày tạo:</span>
            <span class="text-slate-600 dark:text-slate-300">{{ $supplier->created_at->format('d/m/Y H:i') }}</span>
        </div>
        <div>
            <span class="font-medium text-slate-700 dark:text-slate-200">Ngày cập nhật:</span>
            <span class="text-slate-600 dark:text-slate-300">{{ $supplier->updated_at ? $supplier->updated_at->format('d/m/Y H:i') : 'N/A' }}</span>
        </div>
    </div>

    <div class="mt-6 flex gap-4">
        <a href="{{ route('admin.suppliers.edit', $supplier->supplier_id) }}" class="rounded-xl px-4 py-2 text-sm font-medium text-white bg-brand-600 hover:bg-brand-700 shadow-sm">Sửa</a>
        <form action="{{ route('admin.suppliers.destroy', $supplier->supplier_id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa nhà cung cấp này?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="rounded-xl px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 shadow-sm">Xóa</button>
        </form>
        <a href="{{ route('admin.suppliers') }}" class="rounded-xl px-4 py-2 text-sm font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-200/60 dark:hover:bg-slate-800/60">Quay lại</a>
    </div>
</div>
@endsection