@extends('layouts.app')

@section('content')
<div class="bg-white/80 dark:bg-slate-900/50 backdrop-blur p-6 rounded-2xl shadow ring-1 ring-slate-900/5 dark:ring-white/10">
    <h2 class="text-xl font-semibold text-slate-800 dark:text-slate-100 mb-6">Sửa nhà cung cấp</h2>

    @if (session('error'))
        <div class="mb-4 rounded-xl border border-red-200/60 dark:border-red-400/30 bg-red-50/80 dark:bg-red-900/30 px-4 py-3 text-red-700 dark:text-red-200 shadow-sm">
            {{ session('error') }}
        </div>
    @endif

    <form action="{{ route('admin.suppliers.update', $supplier->supplier_id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="grid gap-6 md:grid-cols-2">
            <div>
                <label for="supplier_name" class="block text-sm font-medium text-slate-700 dark:text-slate-200">Tên nhà cung cấp</label>
                <input type="text" name="supplier_name" id="supplier_name" value="{{ old('supplier_name', $supplier->supplier_name) }}" class="mt-1 block w-full rounded-xl border-slate-300 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 focus:ring-brand-600 focus:border-brand-600" required>
                @error('supplier_name')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="contact_info" class="block text-sm font-medium text-slate-700 dark:text-slate-200">Thông tin liên hệ</label>
                <input type="text" name="contact_info" id="contact_info" value="{{ old('contact_info', $supplier->contact_info) }}" class="mt-1 block w-full rounded-xl border-slate-300 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 focus:ring-brand-600 focus:border-brand-600">
                @error('contact_info')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="mt-6 flex gap-4">
            <button type="submit" class="rounded-xl px-4 py-2 text-sm font-medium text-white bg-brand-600 hover:bg-brand-700 shadow-sm">Cập nhật</button>
            <a href="{{ route('admin.suppliers') }}" class="rounded-xl px-4 py-2 text-sm font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-200/60 dark:hover:bg-slate-800/60">Hủy</a>
        </div>
    </form>
</div>
@endsection