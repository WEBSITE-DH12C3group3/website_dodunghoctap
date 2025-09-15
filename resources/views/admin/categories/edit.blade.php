@extends('layouts.app')

@section('content')
<div class="bg-white/80 dark:bg-slate-900/50 backdrop-blur p-6 rounded-2xl shadow ring-1 ring-slate-900/5 dark:ring-white/10">
    <h2 class="text-xl font-semibold text-slate-800 dark:text-slate-100 mb-6">Sửa danh mục</h2>

    @if (session('error'))
        <div class="mb-6 p-4 bg-red-100 dark:bg-red-900/50 text-red-800 dark:text-red-200 rounded-xl">
            {{ session('error') }}
        </div>
    @endif

    <form action="{{ route('admin.categories.update', $category->category_id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="grid gap-6">
            <div>
                <label for="category_name" class="block text-sm font-medium text-slate-700 dark:text-slate-200">Tên danh mục</label>
                <input type="text" name="category_name" id="category_name" value="{{ old('category_name', $category->category_name) }}" class="mt-1 block w-full rounded-xl border-slate-300 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 focus:ring-brand-600 focus:border-brand-600" required>
                @error('category_name')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="mt-6 flex gap-4">
            <button type="submit" class="rounded-xl px-4 py-2 text-sm font-medium text-white bg-brand-600 hover:bg-brand-700 shadow-sm">Cập nhật</button>
            <a href="{{ route('admin.categories') }}" class="rounded-xl px-4 py-2 text-sm font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-200/60 dark:hover:bg-slate-800/60">Hủy</a>
        </div>
    </form>
</div>
@endsection