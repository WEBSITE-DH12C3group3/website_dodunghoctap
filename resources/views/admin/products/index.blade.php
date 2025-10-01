@extends('layouts.app')

@section('content')
<div class="bg-white/80 dark:bg-slate-900/50 backdrop-blur p-6 rounded-2xl shadow ring-1 ring-slate-900/5 dark:ring-white/10">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-semibold text-slate-800 dark:text-slate-100">Danh sách sản phẩm</h2>
        <a href="{{ route('admin.products.create') }}" class="rounded-xl px-4 py-2 text-sm font-medium text-white bg-brand-600 hover:bg-brand-700 shadow-sm">Thêm sản phẩm mới</a>
    </div>

    <!-- Form tìm kiếm + lọc -->
    <form action="{{ route('admin.products') }}" method="GET" class="mb-6 grid grid-cols-1 md:grid-cols-6 gap-2">
        <!-- Tìm kiếm -->
        <input
            name="search"
            value="{{ request('search') }}"
            placeholder="Tìm kiếm theo tên, danh mục hoặc thương hiệu..."
            class="md:col-span-2 rounded-xl px-4 py-2 text-sm text-slate-700 dark:text-slate-200 
                   bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 
                   focus:outline-none focus:ring-2 focus:ring-brand-500">

        <!-- Lọc danh mục -->
        <select name="category_id" class="rounded-xl px-2 py-2 text-sm bg-slate-100 dark:bg-slate-800 border">
            <option value="">-- Danh mục --</option>
            @foreach($categories as $cat)
                <option value="{{ $cat->category_id }}" {{ request('category_id') == $cat->category_id ? 'selected' : '' }}>
                    {{ $cat->category_name }}
                </option>
            @endforeach
        </select>

        <!-- Lọc thương hiệu -->
        <select name="brand_id" class="rounded-xl px-2 py-2 text-sm bg-slate-100 dark:bg-slate-800 border">
            <option value="">-- Thương hiệu --</option>
            @foreach($brands as $brand)
                <option value="{{ $brand->brand_id }}" {{ request('brand_id') == $brand->brand_id ? 'selected' : '' }}>
                    {{ $brand->brand_name }}
                </option>
            @endforeach
        </select>

        <!-- Lọc giá -->
        <div class="flex gap-1">
            <input type="number" name="price_min" value="{{ request('price_min') }}" placeholder="Giá từ"
                class="w-1/2 rounded-xl px-2 py-2 text-sm bg-slate-100 dark:bg-slate-800 border">
            <input type="number" name="price_max" value="{{ request('price_max') }}" placeholder="Giá đến"
                class="w-1/2 rounded-xl px-2 py-2 text-sm bg-slate-100 dark:bg-slate-800 border">
        </div>

        <!-- Lọc tồn kho -->
        <div class="flex gap-1">
            <input type="number" name="stock_min" value="{{ request('stock_min') }}" placeholder="Tồn từ"
                class="w-1/2 rounded-xl px-2 py-2 text-sm bg-slate-100 dark:bg-slate-800 border">
            <input type="number" name="stock_max" value="{{ request('stock_max') }}" placeholder="Tồn đến"
                class="w-1/2 rounded-xl px-2 py-2 text-sm bg-slate-100 dark:bg-slate-800 border">
        </div>

        <!-- Buttons -->
        <div class="md:col-span-6 flex gap-2">
            <button class="rounded-xl px-4 py-2 text-sm font-medium text-white bg-brand-600 hover:bg-brand-700 shadow-sm">
                Lọc
            </button>
            <a href="{{ route('admin.products') }}" class="text-slate-500 hover:text-red-500 text-sm">Xóa lọc</a>
        </div>
    </form>

    <!-- Flash messages -->
    @foreach (['ok' => 'green', 'error' => 'red'] as $flash => $color)
        @if (session($flash))
            <div class="mb-4 rounded-xl border border-{{ $color }}-200/60 dark:border-{{ $color }}-400/30 
                        bg-{{ $color }}-50/80 dark:bg-{{ $color }}-900/30 
                        px-4 py-3 text-{{ $color }}-700 dark:text-{{ $color }}-200 shadow-sm">
                {{ session($flash) }}
            </div>
        @endif
    @endforeach

    @php $hasSearch = filled(request('search')) || request()->except('page'); @endphp

    <!-- Danh sách sản phẩm -->
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

            <!-- Phân trang -->
            @if ($products->hasPages())
                <div class="mt-4">{{ $products->links() }}</div>
            @endif
        @endif
    @empty
        <p class="text-slate-600 dark:text-slate-300">
            {{ $hasSearch ? 'Không tìm thấy sản phẩm nào phù hợp với điều kiện lọc.' : 'Chưa có sản phẩm nào.' }}
        </p>
    @endforelse
</div>
@endsection
