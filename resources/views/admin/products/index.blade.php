@extends('layouts.app')

@section('content')
<div class="bg-white/80 dark:bg-slate-900/50 backdrop-blur p-6 rounded-2xl shadow ring-1 ring-slate-900/5 dark:ring-white/10">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-semibold text-slate-800 dark:text-slate-100">Danh sách sản phẩm</h2>
        <a href="{{ route('admin.products.create') }}" class="rounded-xl px-4 py-2 text-sm font-medium text-white bg-brand-600 hover:bg-brand-700 shadow-sm">
            Thêm sản phẩm mới
        </a>
    </div>

    @if (!isset($products) || !is_iterable($products))
        <p class="text-red-600">Lỗi: Không thể tải danh sách sản phẩm. Vui lòng thử lại hoặc liên hệ quản trị viên.</p>
        {{ Log::error('Variable $products is undefined or not iterable in admin.products.index') }}
    @elseif ($products->isEmpty())
        <p class="text-slate-600 dark:text-slate-300">Chưa có sản phẩm nào.</p>
    @else
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-700 dark:text-slate-200">
                <thead class="bg-slate-100 dark:bg-slate-800">
                    <tr>
                        <th class="px-4 py-3 font-semibold">ID</th>
                        <th class="px-4 py-3 font-semibold">Tên sản phẩm</th>
                        <th class="px-4 py-3 font-semibold">Danh mục</th>
                        <th class="px-4 py-3 font-semibold">Giá</th>
                        <th class="px-4 py-3 font-semibold">Số lượng tồn</th>
                        <th class="px-4 py-3 font-semibold">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($products as $product)
                    <tr class="border-b border-slate-200 dark:border-slate-700">
                        <td class="px-4 py-3">{{ $product->product_id }}</td>
                        <td class="px-4 py-3">{{ $product->product_name }}</td>
                        <td class="px-4 py-3">{{ $product->category ? $product->category->category_name : 'Chưa có danh mục' }}</td>
                        <td class="px-4 py-3">{{ number_format($product->price, 0, ',', '.') }} VNĐ</td>
                        <td class="px-4 py-3">{{ $product->stock_quantity }}</td>
                        <td class="px-4 py-3 flex gap-2">
                            <a href="{{ route('admin.products.edit', $product->product_id) }}" class="text-blue-600 hover:underline">Sửa</a>
                            <form action="{{ route('admin.products.destroy', $product->product_id) }}" method="POST" onsubmit="return confirm('Bạn có chắc muốn xóa sản phẩm này?');">
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