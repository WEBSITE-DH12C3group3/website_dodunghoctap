@extends('layouts.app')

@section('content')
<div class="bg-white/80 dark:bg-slate-900/50 backdrop-blur p-6 rounded-2xl shadow ring-1 ring-slate-900/5 dark:ring-white/10">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-semibold text-slate-800 dark:text-slate-100">Danh sách danh mục</h2>
        <a href="{{ route('admin.categories.create') }}" class="rounded-xl px-4 py-2 text-sm font-medium text-white bg-brand-600 hover:bg-brand-700 shadow-sm">
            Thêm danh mục mới
        </a>
    </div>

    @if (session('ok'))
        <div class="mb-6 p-4 bg-green-100 dark:bg-green-900/50 text-green-800 dark:text-green-200 rounded-xl">
            {{ session('ok') }}
        </div>
    @endif
    @if (session('error'))
        <div class="mb-6 p-4 bg-red-100 dark:bg-red-900/50 text-red-800 dark:text-red-200 rounded-xl">
            {{ session('error') }}
        </div>
    @endif

    @if ($categories->isEmpty())
        <p class="text-slate-600 dark:text-slate-300">Chưa có danh mục nào.</p>
    @else
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-700 dark:text-slate-200">
                <thead class="bg-slate-100 dark:bg-slate-800">
                    <tr>
                        <th class="px-4 py-3 font-semibold">ID</th>
                        <th class="px-4 py-3 font-semibold">Tên danh mục</th>
                        <th class="px-4 py-3 font-semibold">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($categories as $category)
                    <tr class="border-b border-slate-200 dark:border-slate-700">
                        <td class="px-4 py-3">{{ $category->category_id }}</td>
                        <td class="px-4 py-3">{{ $category->category_name }}</td>
                        <td class="px-4 py-3 flex gap-2">
                            <a href="{{ route('admin.categories.edit', $category->category_id) }}" class="text-blue-600 hover:underline">Sửa</a>
                            <form action="{{ route('admin.categories.destroy', $category->category_id) }}" method="POST" onsubmit="return confirm('Bạn có chắc muốn xóa danh mục này?');">
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