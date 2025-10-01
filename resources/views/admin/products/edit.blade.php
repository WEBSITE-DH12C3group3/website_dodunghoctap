@extends('layouts.app')

@section('content')
<div class="bg-white/80 dark:bg-slate-900/50 backdrop-blur p-6 rounded-2xl shadow ring-1 ring-slate-900/5 dark:ring-white/10">
    <h2 class="text-xl font-semibold text-slate-800 dark:text-slate-100 mb-6">Chỉnh sửa sản phẩm</h2>

    <form action="{{ route('admin.products.update', $product->product_id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="grid gap-6 md:grid-cols-2">
            <div>
                <label for="product_name" class="block text-sm font-medium text-slate-700 dark:text-slate-200">Tên sản phẩm</label>
                <input type="text" name="product_name" id="product_name" value="{{ old('product_name', $product->product_name) }}" class="mt-1 block w-full rounded-xl border-slate-300 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 focus:ring-brand-600 focus:border-brand-600" required>
                @error('product_name')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="category_id" class="block text-sm font-medium text-slate-700 dark:text-slate-200">Danh mục</label>
                <select name="category_id" id="category_id" class="mt-1 block w-full rounded-xl border-slate-300 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 focus:ring-brand-600 focus:border-brand-600" required>
                    @foreach ($categories as $category)
                        <option value="{{ $category->category_id }}" {{ old('category_id', $product->category_id) == $category->category_id ? 'selected' : '' }}>{{ $category->category_name }}</option>
                    @endforeach
                </select>
                @error('category_id')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="price" class="block text-sm font-medium text-slate-700 dark:text-slate-200">Giá (VNĐ)</label>
                <input type="number" name="price" id="price" value="{{ old('price', $product->price) }}" step="0.01" class="mt-1 block w-full rounded-xl border-slate-300 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 focus:ring-brand-600 focus:border-brand-600" required>
                @error('price')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="stock_quantity" class="block text-sm font-medium text-slate-700 dark:text-slate-200">Số lượng tồn</label>
                <input type="number" name="stock_quantity" id="stock_quantity" value="{{ old('stock_quantity', $product->stock_quantity) }}" class="mt-1 block w-full rounded-xl border-slate-300 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 focus:ring-brand-600 focus:border-brand-600" required>
                @error('stock_quantity')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
           <div class="md:col-span-2">
    <label for="image_url" class="block text-sm font-medium text-slate-700 dark:text-slate-200">Hình ảnh sản phẩm</label>
    <!-- Hiển thị ảnh hiện tại (nếu có) -->
    @if($product->image_url)
        <div class="mt-2">
            <img src="{{ asset('storage/' . $product->image_url) }}" alt="{{ $product->product_name }}" class="h-32 w-auto object-contain rounded-lg">
        </div>
    @endif
    <!-- Input file để upload ảnh mới -->
    <input type="file" name="image_url" id="image_url" class="mt-2 block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-brand-50 file:text-brand-700 hover:file:bg-brand-100">
    <!-- Hiển thị đường dẫn hiện tại (nếu có) -->
    @if($product->image_url)
        <p class="mt-1 text-xs text-slate-500">Đường dẫn hiện tại: {{ $product->image_url }}</p>
    @endif
    @error('image_url')
        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
    @enderror
</div>

        <div class="mt-6 flex gap-4">
            <button type="submit" class="rounded-xl px-4 py-2 text-sm font-medium text-white bg-brand-600 hover:bg-brand-700 shadow-sm">Lưu thay đổi</button>
            <a href="{{ route('admin.products') }}" class="rounded-xl px-4 py-2 text-sm font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-200/60 dark:hover:bg-slate-800/60">Hủy</a>
        </div>
    </form>
</div>
@endsection