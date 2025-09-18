@extends('layouts.app')

@section('content')
<div class="bg-white/80 dark:bg-slate-900/50 backdrop-blur p-6 rounded-2xl shadow ring-1 ring-slate-900/5 dark:ring-white/10">
    <h2 class="text-xl font-semibold text-slate-800 dark:text-slate-100 mb-6">Chi tiết thương hiệu "{{ $brand->brand_name }}"</h2>

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

    <div class="grid gap-6 md:grid-cols-2">
        <div>
            <h3 class="font-semibold mb-2">Thông tin thương hiệu</h3>
            <p><strong>ID:</strong> {{ $brand->brand_id }}</p>
            <p><strong>Tên:</strong> {{ $brand->brand_name }}</p>
            <p><strong>Mô tả:</strong> {{ $brand->description ?? 'N/A' }}</p>
            <p><strong>Tổng sản phẩm:</strong> {{ $brand->products->count() }}</p>
        </div>

        <div>
            <h3 class="font-semibold mb-2">Danh mục và sản phẩm</h3>
            @if ($categoryCounts->isEmpty())
                <p class="text-slate-600 dark:text-slate-300">Không có danh mục nào.</p>
            @else
                @foreach ($categoryCounts as $categoryGroup)
                    <div class="mb-4 p-4 bg-slate-50 dark:bg-slate-800 rounded-xl">
                        <h4 class="font-semibold mb-2">
                            {{ optional($categoryGroup['category'])->category_name ?? 'Danh mục không xác định' }} ({{ $categoryGroup['product_count'] }} sản phẩm)
                        </h4>
                        <ul class="list-disc list-inside space-y-1">
                            @foreach ($categoryGroup['products'] as $product)
                                <li>{{ $product->product_name }} (Số lượng: {{ $product->stock_quantity }})</li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach
            @endif
        </div>
    </div>

    <div class="mt-6 flex gap-4">
        <a href="{{ route('admin.brands') }}" class="rounded-xl px-4 py-2 text-sm font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-200/60 dark:hover:bg-slate-800/60">Quay lại danh sách</a>
    </div>
</div>
@endsection