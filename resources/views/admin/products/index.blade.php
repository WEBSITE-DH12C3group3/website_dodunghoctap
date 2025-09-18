@extends('layouts.app')

@section('content')
<div class="bg-white/80 dark:bg-slate-900/50 backdrop-blur p-6 rounded-2xl shadow ring-1 ring-slate-900/5 dark:ring-white/10">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-xl font-semibold text-slate-800 dark:text-slate-100">Danh sách sản phẩm</h2>
            <p class="text-sm text-slate-600 dark:text-slate-300">Tổng số sản phẩm: {{ $products->count() }}</p>
        </div>
        <a href="{{ route('admin.products.create') }}" class="rounded-xl px-4 py-2 text-sm font-medium text-white bg-brand-600 hover:bg-brand-700 shadow-sm">Thêm sản phẩm</a>
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

    @if ($products->isEmpty())
        <p class="text-slate-600 dark:text-slate-300">Chưa có sản phẩm nào.</p>
    @else
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-700 dark:text-slate-200">
                <thead class="bg-slate-100 dark:bg-slate-800">
                    <tr>
                        <th class="px-4 py-3 font-semibold">ID</th>
                        <th class="px-4 py-3 font-semibold">Tên sản phẩm</th>
                        <th class="px-4 py-3 font-semibold">Thương hiệu</th>
                        <th class="px-4 py-3 font-semibold">Danh mục</th>
                        <th class="px-4 py-3 font-semibold">Số lượng</th>
                        <th class="px-4 py-3 font-semibold">Giá</th>
                        <th class="px-4 py-3 font-semibold">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($products as $product)
                    <tr class="border-b border-slate-200 dark:border-slate-700">
                        <td class="px-4 py-3">{{ $product->product_id }}</td>
                        <td class="px-4 py-3">{{ $product->product_name }}</td>
                        <td class="px-4 py-3">{{ optional($product->brand)->brand_name ?? 'N/A' }}</td>
                        <td class="px-4 py-3">{{ optional($product->category)->category_name ?? 'N/A' }}</td>
                        <td class="px-4 py-3">{{ $product->stock_quantity }}</td>
                        <td class="px-4 py-3">{{ number_format($product->price, 0, ',', '.') }} đ</td>
                        <td class="px-4 py-3 flex gap-2">
                            <a href="{{ route('admin.products.edit', $product->product_id) }}" class="text-sm px-3 py-1 rounded bg-slate-100 dark:bg-slate-800">Sửa</a>
                            <form action="{{ route('admin.products.destroy', $product->product_id) }}" method="POST" onsubmit="return confirm('Bạn có muốn xóa?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-sm px-3 py-1 rounded bg-red-600 text-white">Xóa</button>
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
