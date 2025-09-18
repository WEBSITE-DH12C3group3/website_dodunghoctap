@extends('layouts.app')

@section('content')
<div class="bg-white/80 dark:bg-slate-900/50 backdrop-blur p-6 rounded-2xl shadow ring-1 ring-slate-900/5 dark:ring-white/10">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-semibold text-slate-800 dark:text-slate-100">Danh sách nhà cung cấp</h2>
        <a href="{{ route('admin.suppliers.create') }}" class="rounded-xl px-4 py-2 text-sm font-medium text-white bg-brand-600 hover:bg-brand-700 shadow-sm">Thêm nhà cung cấp</a>
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

    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left text-slate-700 dark:text-slate-200">
            <thead class="text-xs text-slate-700 uppercase bg-slate-50 dark:bg-slate-800 dark:text-slate-200">
                <tr>
                    <th scope="col" class="px-6 py-3">ID</th>
                    <th scope="col" class="px-6 py-3">Tên nhà cung cấp</th>
                    <th scope="col" class="px-6 py-3">Thông tin liên hệ</th>
                    <th scope="col" class="px-6 py-3">Hành động</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($suppliers as $supplier)
                    <tr class="border-b dark:border-slate-700">
                        <td class="px-6 py-4">{{ $supplier->supplier_id }}</td>
                        <td class="px-6 py-4">{{ $supplier->supplier_name }}</td>
                        <td class="px-6 py-4">{{ $supplier->contact_info ?? 'N/A' }}</td>
                        <td class="px-6 py-4 flex gap-2">
                            <a href="{{ route('admin.suppliers.show', $supplier->supplier_id) }}" class="text-blue-600 hover:underline">Xem</a>
                            <a href="{{ route('admin.suppliers.edit', $supplier->supplier_id) }}" class="text-blue-600 hover:underline">Sửa</a>
                            <form action="{{ route('admin.suppliers.destroy', $supplier->supplier_id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa nhà cung cấp này?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline">Xóa</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-center">Không có nhà cung cấp nào.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection