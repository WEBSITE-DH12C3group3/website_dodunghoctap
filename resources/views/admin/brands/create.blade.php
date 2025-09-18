@extends('layouts.app')

@section('content')
<div class="bg-white/80 dark:bg-slate-900/50 backdrop-blur p-6 rounded-2xl shadow ring-1 ring-slate-900/5 dark:ring-white/10">
    <h2 class="text-xl font-semibold text-slate-800 dark:text-slate-100 mb-6">Thêm thương hiệu mới</h2>

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
    @if ($errors->any())
        <div class="mb-4 rounded-xl border border-red-200/60 dark:border-red-400/30 bg-red-50/80 dark:bg-red-900/30 px-4 py-3 text-red-700 dark:text-red-200 shadow-sm">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.brands.store') }}" method="POST" class="space-y-4">
        @csrf
        <div>
            <label for="brand_name" class="block text-sm font-medium text-slate-700 dark:text-slate-200">Tên thương hiệu <span class="text-red-500">*</span></label>
            <input type="text" name="brand_name" id="brand_name" value="{{ old('brand_name') }}" class="mt-1 block w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 p-2 text-slate-700 dark:text-slate-200 focus:border-brand-600 focus:ring focus:ring-brand-200 dark:focus:ring-brand-700" required>
        </div>
        <div>
            <label for="description" class="block text-sm font-medium text-slate-700 dark:text-slate-200">Mô tả</label>
            <textarea name="description" id="description" rows="4" class="mt-1 block w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 p-2 text-slate-700 dark:text-slate-200 focus:border-brand-600 focus:ring focus:ring-brand-200 dark:focus:ring-brand-700">{{ old('description') }}</textarea>
        </div>
        <div class="flex gap-4">
            <button type="submit" class="rounded-xl px-4 py-2 text-sm font-medium text-white bg-brand-600 hover:bg-brand-700 shadow-sm">Lưu</button>
            <a href="{{ route('admin.brands') }}" class="rounded-xl px-4 py-2 text-sm font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-200/60 dark:hover:bg-slate-800/60">Hủy</a>
        </div>
    </form>
</div>
@endsection