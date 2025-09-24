@extends('layouts.app')

@section('content')
<div class="bg-white/80 dark:bg-slate-900/50 backdrop-blur p-6 rounded-2xl shadow ring-1 ring-slate-900/5 dark:ring-white/10">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-semibold text-slate-800 dark:text-slate-100">Danh sách sản phẩm</h2>
        <a href="{{ route('admin.products.create') }}" class="rounded-xl px-4 py-2 text-sm font-medium text-white bg-brand-600 hover:bg-brand-700 shadow-sm">Thêm sản phẩm mới</a>
    </div>

    <form action="{{ route('admin.products') }}" method="GET" class="mb-4 flex items-center gap-2">
        <input
            name="search"
            value="{{ request('search') }}"
            placeholder="Tìm kiếm theo tên, danh mục hoặc thương hiệu..."
            class="flex-1 rounded-xl px-4 py-2 text-sm text-slate-700 dark:text-slate-200 bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 focus:outline-none focus:ring-2 focus:ring-brand-500">
        <button class="rounded-xl px-4 py-2 text-sm font-medium text-white bg-brand-600 hover:bg-brand-700 shadow-sm">Tìm kiếm</button>
        @if(request('search'))
            <a href="{{ route('admin.products') }}" class="text-slate-500 hover:text-red-500">Xóa tìm kiếm</a>
        @endif
    </form>

    @foreach (['ok' => 'green', 'error' => 'red'] as $flash => $color)
        @if (session($flash))
            <div class="mb-4 rounded-xl border border-{{ $color }}-200/60 dark:border-{{ $color }}-400/30 bg-{{ $color }}-50/80 dark:bg-{{ $color }}-900/30 px-4 py-3 text-{{ $color }}-700 dark:text-{{ $color }}-200 shadow-sm">
                {{ session($flash) }}
            </div>
        @endif
    @endforeach

    @php $hasSearch = filled(request('search')); @endphp

    @forelse ($products as $product)
        @if ($loop->first)
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-700 dark:text-slate-200">
                    <thead class="bg-slate-100 dark:bg-slate-800">
                        <tr>
                            <th class="px-4 py-3 font-semibold">ID</th>
                            <th class="px-4 py-3 font-semibold">Tên sản phẩm</th>
                            <th class="px-4 py-3 font-semibold">Danh mục</th>
                            <th class="px-4 py-3 font-semibold">Thương hiệu</th>
                            <th class="px-4 py-3 font-semibold">Giá</th>
                            <th class="px-4 py-3 font-semibold">Tồn</th>
                            <th class="px-4 py-3 font-semibold">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
        @endif

                        <tr class="border-b border-slate-200 dark:border-slate-700">
                            <td class="px-4 py-3">{{ $product->product_id }}</td>
                            <td class="px-4 py-3">{{ $product->product_name }}</td>
                            <td class="px-4 py-3">{{ $product->category->category_name ?? 'Chưa phân loại' }}</td>
                            <td class="px-4 py-3">{{ $product->brand->brand_name ?? 'Chưa có' }}</td>
                            <td class="px-4 py-3">{{ number_format($product->price, 0, ',', '.') }} VNĐ</td>
                            <td class="px-4 py-3">{{ $product->stock_quantity }}</td>
                            <td class="px-4 py-3">
                                <div class="flex gap-2">
                                    <a href="{{ route('admin.products.edit', $product->product_id) }}" class="text-green-600 hover:underline">Sửa</a>
                                    <form action="{{ route('admin.products.destroy', $product->product_id) }}" method="POST" onsubmit="return confirm('Bạn có chắc muốn xóa sản phẩm này?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:underline">Xóa</button>
                                    </form>
                                </div>
                            </td>
                        </tr>

        @if ($loop->last)
                    </tbody>
                </table>
            </div>

            @if ($products->hasPages())
                <div class="mt-4">{{ $products->links() }}</div>
            @endif
        @endif
    @empty
        <p class="text-slate-600 dark:text-slate-300">
            {{ $hasSearch ? 'Không tìm thấy sản phẩm nào phù hợp với "' . e(request('search')) . '".' : 'Chưa có sản phẩm nào.' }}
        </p>
    @endforelse
</div>
@endsection
