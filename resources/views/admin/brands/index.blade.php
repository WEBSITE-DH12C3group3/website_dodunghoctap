@extends('layouts.app')

@section('content')
<div class="bg-white/80 dark:bg-slate-900/50 backdrop-blur p-6 rounded-2xl shadow ring-1 ring-slate-900/5 dark:ring-white/10">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-xl font-semibold text-slate-800 dark:text-slate-100">Danh sách thương hiệu</h2>
            <p class="text-sm text-slate-600 dark:text-slate-300">Tổng số thương hiệu: {{ $totalBrands }}</p>
        </div>
        <a href="{{ route('admin.brands.create') }}" class="rounded-xl px-4 py-2 text-sm font-medium text-white bg-brand-600 hover:bg-brand-700 shadow-sm">Thêm thương hiệu</a>
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

    @if ($brands->isEmpty())
        <p class="text-slate-600 dark:text-slate-300">Chưa có thương hiệu nào.</p>
    @else
        <div class="overflow-x-auto">
<div class="mb-4">
    <form action="{{ route('admin.brands') }}" method="GET" class="flex items-center space-x-2">
        <input type="text" name="search" placeholder="Tìm kiếm thương hiệu..." 
               class="flex-1 rounded-xl px-4 py-2 text-sm text-slate-700 dark:text-slate-200 
                      bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 
                      focus:outline-none focus:ring-2 focus:ring-brand-500"
               value="{{ request('search') }}">
        <button type="submit" class="rounded-xl px-4 py-2 text-sm font-medium text-white 
                                     bg-brand-600 hover:bg-brand-700 shadow-sm">
            Tìm kiếm
        </button>
        @if(request('search'))
            <a href="{{ route('admin.brands') }}" class="text-slate-500 hover:text-red-500">
                Xóa tìm kiếm
            </a>
        @endif
    </form>
</div>
            <table class="w-full text-left text-sm text-slate-700 dark:text-slate-200">
                <thead class="bg-slate-100 dark:bg-slate-800">
                    <tr>
                        <th class="px-4 py-3 font-semibold">ID</th>
                        <th class="px-4 py-3 font-semibold">Tên thương hiệu</th>
                        <th class="px-4 py-3 font-semibold">Số sản phẩm</th>
                        <th class="px-4 py-3 font-semibold">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($brands as $brand)
                    <tr class="border-b border-slate-200 dark:border-slate-700">
                        <td class="px-4 py-3">{{ $brand->brand_id }}</td>
                        <td class="px-4 py-3">{{ $brand->brand_name }}</td>
                        <td class="px-4 py-3">{{ $brand->products_count }}</td>
                        <td class="px-4 py-3 flex gap-2">
                            <a href="{{ route('admin.brands.show', $brand->brand_id) }}" class="text-blue-600 hover:underline">Xem chi tiết</a>
                            <form action="{{ route('admin.brands.destroy', $brand->brand_id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa thương hiệu này?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline">Xóa</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
     <div class="mt-4">
    {{ $brands->links() }}
</div>

        </div>
    @endif
</div>
@endsection