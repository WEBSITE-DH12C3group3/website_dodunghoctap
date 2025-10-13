@extends('layouts.app')

@section('content')
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm p-6">
        <h1 class="text-2xl font-semibold text-slate-800 dark:text-slate-100 mb-6">Thêm mã giảm giá</h1>

        <form action="{{ route('admin.discounts.store') }}" method="POST">
            @csrf
            <div class="grid gap-6">
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Mã giảm giá</label>
                    <input type="text" name="code"
                           value="{{ old('code') }}"
                           class="mt-1 block w-full rounded-xl border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-100 focus:ring-brand-600 focus:border-brand-600"
                           required>
                    @error('code')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Mô tả</label>
                    <textarea name="description"
                              class="mt-1 block w-full rounded-xl border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-100 focus:ring-brand-600 focus:border-brand-600">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Loại giảm giá</label>
                    <select name="discount_type" id="discount_type" 
                            class="mt-1 block w-full rounded-xl border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-100 focus:ring-brand-600 focus:border-brand-600"
                            onchange="toggleDiscountInput()">
                        <option value="amount" {{ old('discount_type') == 'amount' ? 'selected' : '' }}>Số tiền giảm</option>
                        <option value="percent" {{ old('discount_type') == 'percent' ? 'selected' : '' }}>Phần trăm giảm</option>
                    </select>
                    @error('discount_type')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div id="discount_amount_container" style="display: {{ old('discount_type', 'amount') == 'amount' ? 'block' : 'none' }};">
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Số tiền giảm</label>
                    <input type="number" name="discount_amount"
                           value="{{ old('discount_amount') }}"
                           class="mt-1 block w-full rounded-xl border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-100 focus:ring-brand-600 focus:border-brand-600">
                    @error('discount_amount')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div id="discount_percent_container" style="display: {{ old('discount_type') == 'percent' ? 'block' : 'none' }};">
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Phần trăm giảm</label>
                    <input type="number" name="discount_percent"
                           value="{{ old('discount_percent') }}"
                           class="mt-1 block w-full rounded-xl border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-100 focus:ring-brand-600 focus:border-brand-600">
                    @error('discount_percent')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Hiệu lực từ</label>
                        <input type="date" name="valid_from"
                               value="{{ old('valid_from') }}"
                               class="mt-1 block w-full rounded-xl border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-100 focus:ring-brand-600 focus:border-brand-600">
                        @error('valid_from')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Hiệu lực đến</label>
                        <input type="date" name="valid_to"
                               value="{{ old('valid_to') }}"
                               class="mt-1 block w-full rounded-xl border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-100 focus:ring-brand-600 focus:border-brand-600">
                        @error('valid_to')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Giới hạn sử dụng</label>
                    <input type="number" name="usage_limit"
                           value="{{ old('usage_limit') }}"
                           class="mt-1 block w-full rounded-xl border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-100 focus:ring-brand-600 focus:border-brand-600">
                    @error('usage_limit')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            <div class="mt-6">
                <button type="submit"
                        class="px-4 py-2 bg-brand-600 text-white rounded-xl hover:bg-brand-700 transition-colors">
                    Lưu
                </button>
                <a href="{{ route('admin.discounts') }}"
                   class="px-4 py-2 bg-slate-200 dark:bg-slate-600 rounded-xl hover:bg-slate-300 dark:hover:bg-slate-500 transition-colors">
                    Hủy
                </a>
            </div>
        </form>
    </div>

    <script>
        function toggleDiscountInput() {
            const discountType = document.getElementById('discount_type').value;
            const amountContainer = document.getElementById('discount_amount_container');
            const percentContainer = document.getElementById('discount_percent_container');
            
            amountContainer.style.display = discountType === 'amount' ? 'block' : 'none';
            percentContainer.style.display = discountType === 'percent' ? 'block' : 'none';
            
            // Clear input values when switching types
            if (discountType === 'amount') {
                document.getElementsByName('discount_percent')[0].value = '';
            } else {
                document.getElementsByName('discount_amount')[0].value = '';
            }
        }
    </script>
@endsection