@extends('layouts.app')

@section('content')
<div class="bg-white/80 dark:bg-slate-900/50 backdrop-blur p-6 rounded-2xl shadow ring-1 ring-slate-900/5 dark:ring-white/10">
    <h2 class="text-xl font-semibold text-slate-800 dark:text-slate-100 mb-6">Tạo phiếu nhập kho</h2>

    @if (session('error'))
        <div class="mb-4 rounded-xl border border-red-200/60 dark:border-red-400/30 bg-red-50/80 dark:bg-red-900/30 px-4 py-3 text-red-700 dark:text-red-200 shadow-sm">
            {{ session('error') }}
        </div>
    @endif

    <form action="{{ route('admin.purchase_orders.store') }}" method="POST">
        @csrf

        <div class="grid gap-6 md:grid-cols-2">
            <div>
                <label for="order_date" class="block text-sm font-medium text-slate-700 dark:text-slate-200">Ngày nhập</label>
                <input type="date" name="order_date" id="order_date" value="{{ old('order_date', date('Y-m-d')) }}" class="mt-1 block w-full rounded-xl border-slate-300 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 focus:ring-brand-600 focus:border-brand-600" required>
                @error('order_date')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="supplier_id" class="block text-sm font-medium text-slate-700 dark:text-slate-200">Nhà cung cấp</label>
                <select name="supplier_id" id="supplier_id" class="mt-1 block w-full rounded-xl border-slate-300 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 focus:ring-brand-600 focus:border-brand-600" required>
                    <option value="" disabled selected>Chọn nhà cung cấp</option>
                    @foreach ($suppliers as $supplier)
                        <option value="{{ $supplier->supplier_id }}" {{ old('supplier_id') == $supplier->supplier_id ? 'selected' : '' }}>{{ $supplier->supplier_name }}</option>
                    @endforeach
                </select>
                @error('supplier_id')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="mt-6">
            <h3 class="font-semibold mb-2">Sản phẩm nhập kho</h3>
            <div id="items" class="space-y-4">
                @if (old('items'))
                    @foreach (old('items') as $index => $item)
                    <div class="item grid gap-4 md:grid-cols-4 items-end">
                        <div>
                            <label for="items[{{ $index }}][product_id]" class="block text-sm font-medium text-slate-700 dark:text-slate-200">Sản phẩm</label>
                            <select name="items[{{ $index }}][product_id]" class="mt-1 block w-full rounded-xl border-slate-300 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 focus:ring-brand-600 focus:border-brand-600" required>
                                <option value="" disabled>Chọn sản phẩm</option>
                                @foreach ($products as $product)
                                    <option value="{{ $product->product_id }}" {{ old('items.' . $index . '.product_id') == $product->product_id ? 'selected' : '' }}>{{ $product->product_name }} (Giá bán: {{ number_format($product->price, 0, ',', '.') }} VNĐ)</option>
                                @endforeach
                            </select>
                            @error('items.' . $index . '.product_id')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="items[{{ $index }}][quantity]" class="block text-sm font-medium text-slate-700 dark:text-slate-200">Số lượng</label>
                            <input type="number" name="items[{{ $index }}][quantity]" min="1" value="{{ old('items.' . $index . '.quantity', 1) }}" class="mt-1 block w-full rounded-xl border-slate-300 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 focus:ring-brand-600 focus:border-brand-600" required>
                            @error('items.' . $index . '.quantity')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="items[{{ $index }}][price]" class="block text-sm font-medium text-slate-700 dark:text-slate-200">Giá nhập (VNĐ)</label>
                            <input type="number" name="items[{{ $index }}][price]" min="0" step="0.01" value="{{ old('items.' . $index . '.price', 0) }}" class="mt-1 block w-full rounded-xl border-slate-300 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 focus:ring-brand-600 focus:border-brand-600" required>
                            @error('items.' . $index . '.price')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <button type="button" class="remove-item text-red-600 hover:text-red-800 dark:hover:text-red-400 text-sm">Xóa</button>
                        </div>
                    </div>
                    @endforeach
                @else
                    <div class="item grid gap-4 md:grid-cols-4 items-end">
                        <div>
                            <label for="items[0][product_id]" class="block text-sm font-medium text-slate-700 dark:text-slate-200">Sản phẩm</label>
                            <select name="items[0][product_id]" class="mt-1 block w-full rounded-xl border-slate-300 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 focus:ring-brand-600 focus:border-brand-600" required>
                                <option value="" disabled selected>Chọn sản phẩm</option>
                                @foreach ($products as $product)
                                    <option value="{{ $product->product_id }}">{{ $product->product_name }} (Giá bán: {{ number_format($product->price, 0, ',', '.') }} VNĐ)</option>
                                @endforeach
                            </select>
                            @error('items.0.product_id')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="items[0][quantity]" class="block text-sm font-medium text-slate-700 dark:text-slate-200">Số lượng</label>
                            <input type="number" name="items[0][quantity]" min="1" value="{{ old('items.0.quantity', 1) }}" class="mt-1 block w-full rounded-xl border-slate-300 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 focus:ring-brand-600 focus:border-brand-600" required>
                            @error('items.0.quantity')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="items[0][price]" class="block text-sm font-medium text-slate-700 dark:text-slate-200">Giá nhập (VNĐ)</label>
                            <input type="number" name="items[0][price]" min="0" step="0.01" value="{{ old('items.0.price', 0) }}" class="mt-1 block w-full rounded-xl border-slate-300 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 focus:ring-brand-600 focus:border-brand-600" required>
                            @error('items.0.price')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <button type="button" class="remove-item text-red-600 hover:text-red-800 dark:hover:text-red-400 text-sm">Xóa</button>
                        </div>
                    </div>
                @endif
            </div>
            <button type="button" id="add-item" class="mt-2 text-sm text-blue-600 hover:underline">Thêm sản phẩm</button>
        </div>

        <div class="mt-6 flex gap-4">
            <button type="submit" class="rounded-xl px-4 py-2 text-sm font-medium text-white bg-brand-600 hover:bg-brand-700 shadow-sm">Tạo phiếu nhập</button>
            <a href="{{ route('admin.purchase_orders') }}" class="rounded-xl px-4 py-2 text-sm font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-200/60 dark:hover:bg-slate-800/60">Hủy</a>
        </div>
    </form>
</div>

<script>
    let itemCount = {{ old('items') ? count(old('items')) : 1 }};
    document.getElementById('add-item').addEventListener('click', () => {
        const items = document.getElementById('items');
        const newItem = document.createElement('div');
        newItem.className = 'item grid gap-4 md:grid-cols-4 items-end';
        newItem.innerHTML = `
            <div>
                <label for="items[${itemCount}][product_id]" class="block text-sm font-medium text-slate-700 dark:text-slate-200">Sản phẩm</label>
                <select name="items[${itemCount}][product_id]" class="mt-1 block w-full rounded-xl border-slate-300 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 focus:ring-brand-600 focus:border-brand-600" required>
                    <option value="" disabled selected>Chọn sản phẩm</option>
                    @foreach ($products as $product)
                        <option value="{{ $product->product_id }}">{{ $product->product_name }} (Giá bán: {{ number_format($product->price, 0, ',', '.') }} VNĐ)</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="items[${itemCount}][quantity]" class="block text-sm font-medium text-slate-700 dark:text-slate-200">Số lượng</label>
                <input type="number" name="items[${itemCount}][quantity]" min="1" value="1" class="mt-1 block w-full rounded-xl border-slate-300 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 focus:ring-brand-600 focus:border-brand-600" required>
            </div>
            <div>
                <label for="items[${itemCount}][price]" class="block text-sm font-medium text-slate-700 dark:text-slate-200">Giá nhập (VNĐ)</label>
                <input type="number" name="items[${itemCount}][price]" min="0" step="0.01" value="0" class="mt-1 block w-full rounded-xl border-slate-300 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 focus:ring-brand-600 focus:border-brand-600" required>
            </div>
            <div>
                <button type="button" class="remove-item text-red-600 hover:text-red-800 dark:hover:text-red-400 text-sm">Xóa</button>
            </div>
        `;
        items.appendChild(newItem);
        itemCount++;
        attachRemoveListeners();
    });

    function attachRemoveListeners() {
        document.querySelectorAll('.remove-item').forEach(button => {
            button.addEventListener('click', () => {
                if (document.querySelectorAll('.item').length > 1) {
                    button.closest('.item').remove();
                }
            });
        });
    }

    attachRemoveListeners();
</script>
@endsection