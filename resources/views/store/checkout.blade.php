@extends('layouts.store')

@section('title', 'Tiến hành đặt hàng')

@section('content')
<div class="max-w-7xl mx-auto p-4 md:p-8">

    <div class="grid lg:grid-cols-12 gap-8">

        {{-- Cột trái: Thông tin & Phương thức (Order Details) --}}
        <form action="{{ route('checkout.store') }}" method="POST"
            class="lg:col-span-7 bg-white rounded-2xl p-6 md:p-8 shadow-xl ring-1 ring-gray-100 h-fit ">
            @csrf

            {{-- 1. Thông tin giao hàng --}}
            <section>
                <h2 class="text-2xl font-bold text-gray-800 border-b pb-3 mb-5 flex items-center gap-2">
                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.828 0l-4.243-4.243a8 8 0 1111.314 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    Thông tin giao hàng
                </h2>
                <div class="grid sm:grid-cols-2 gap-4">

                    {{-- Họ và tên --}}
                    <div>
                        <label for="fullname" class="block text-sm font-medium text-gray-700 mb-1">Họ và tên<span class="text-red-500">*</span></label>
                        <input id="fullname" name="fullname" value="{{ old('fullname', auth()->user()->full_name ?? '') }}"
                            class="@error('fullname') border-red-500 @enderror w-full border border-gray-300 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 rounded-xl px-4 h-12 transition duration-150" placeholder="Nguyễn Văn A" required>
                    </div>

                    {{-- Số điện thoại --}}
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Số điện thoại<span class="text-red-500">*</span></label>
                        <input id="phone" name="phone" value="{{ old('phone', auth()->user()->phone ?? '') }}"
                            class="@error('phone') border-red-500 @enderror w-full border border-gray-300 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 rounded-xl px-4 h-12 transition duration-150" placeholder="0901 234 567" required>
                    </div>

                    {{-- Địa chỉ --}}
                    <div class="sm:col-span-2">
                        <label for="address" class="block text-sm font-medium text-gray-700 mb-1">Địa chỉ chi tiết<span class="text-red-500">*</span></label>
                        <input id="address" name="address" value="{{ old('address', auth()->user()->address ?? '') }}"
                            class="@error('address') border-red-500 @enderror w-full border border-gray-300 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 rounded-xl px-4 h-12 transition duration-150" placeholder="Số nhà, đường, phường/xã" required>
                    </div>

                    {{-- Ghi chú --}}
                    <textarea id="note" name="note" rows="3"
                        class="@error('note') border-red-500 @enderror sm:col-span-2 w-full rounded-xl border border-gray-300 px-4 py-3 leading-relaxed placeholder:text-gray-400 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 resize-y transition duration-150"
                        placeholder="Ghi chú cho đơn hàng (Ví dụ: Giao giờ hành chính, gọi trước khi giao...)">{{ old('note', request('note')) }}</textarea>

                    @error('note') <p class="text-red-500 text-sm mt-1 sm:col-span-2">{{ $message }}</p> @enderror
                </div>
            </section>



            {{-- 2. Phương thức vận chuyển --}}
            <section class="mt-8">
                <h2 class="text-2xl font-bold text-gray-800 border-b pb-3 mb-5 flex items-center gap-2">
                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Phương thức vận chuyển
                </h2>
                <label class="flex items-center justify-between border border-indigo-500 bg-indigo-50 rounded-xl px-4 py-4 cursor-pointer shadow-sm">
                    <div class="flex items-center gap-3">
                        <input type="radio" name="shipping_method" value="free" class="hidden" checked>
                        <span class="inline-flex items-center justify-center w-5 h-5 rounded-full ring-2 ring-indigo-600 bg-indigo-600 text-white">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </span>
                        <span class="font-medium text-gray-800">Giao hàng tiêu chuẩn (Miễn phí)</span>
                    </div>
                    <span class="font-semibold text-green-600">0đ</span>
                </label>
                <p class="text-sm text-gray-500 mt-2 ml-8">Dự kiến giao hàng trong 3-5 ngày làm việc.</p>
            </section>



            {{-- 3. Phương thức thanh toán --}}
            <section class="mt-8">
                <h2 class="text-2xl font-bold text-gray-800 border-b pb-3 mb-5 flex items-center gap-2">
                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                    </svg>
                    Phương thức thanh toán
                </h2>
                <div class="space-y-3">
                    @php
                    $paymentMethods = [
                    'cod' => ['label' => 'Thanh toán khi giao hàng (COD)', 'icon' => '💵'],
                    'vietqr' => ['label' => 'Thanh toán qua cổng VietQR', 'icon' => '📱'],
                    'payos' => ['label' => 'Thanh toán qua cổng PayOS', 'icon' => '💳'],
                    ];
                    $selectedPayment = old('payment_method', 'cod');
                    @endphp

                    @foreach($paymentMethods as $value => $method)
                    <label data-pay
                        class="flex items-center gap-3 p-3 border rounded-lg cursor-pointer transition duration-200 ease-in-out
                                hover:shadow-md focus-within:ring-2 focus-within:ring-indigo-400
                                border-gray-300 hover:border-indigo-400
                                {{ $selectedPayment === $value ? 'border-indigo-600 bg-indigo-50 shadow-lg ring-2 ring-indigo-300' : '' }}">
                        {{-- Input Radio Button: ĐÃ GIẢM KÍCH THƯỚC (size-4) --}}
                        <input type="radio" name="payment_method" value="{{ $value }}" class="
                                size-4 text-indigo-600 border-gray-300 rounded-full appearance-none transition duration-150 ease-in-out 
                                checked:bg-indigo-600 checked:border-indigo-600 checked:ring-2 checked:ring-offset-2 checked:ring-indigo-600 
                                focus:ring-2 focus:ring-offset-2 focus:ring-indigo-200 
                                cursor-pointer"
                            {{ $selectedPayment === $value ? 'checked' : '' }}>

                        {{-- Content: Icon and Label --}}
                        <div class="flex items-center gap-2">
                            <span class="text-xl text-gray-700">{{ $method['icon'] }}</span>
                            <span class="text-gray-800 font-medium">{{ $method['label'] }}</span>
                        </div>
                    </label>
                    <script>
                        document.addEventListener('DOMContentLoaded', () => {
                            const ACTIVE = ['border-indigo-600', 'bg-indigo-50', 'shadow-lg', 'ring-2', 'ring-indigo-300'];
                            const labels = Array.from(document.querySelectorAll('label[data-pay]'));
                            const update = () => {
                                labels.forEach(lb => lb.classList.remove(...ACTIVE));
                                const checked = document.querySelector('input[name="payment_method"]:checked');
                                if (!checked) return;
                                checked.closest('label')?.classList.add(...ACTIVE);
                            };
                            document.querySelectorAll('input[name="payment_method"]').forEach(r => {
                                r.addEventListener('change', update);
                            });
                            update(); // chạy lần đầu
                        });
                    </script>

                    @endforeach
                </div>
            </section>



            {{-- 4. Mã giảm giá --}}
            <section class="mt-8">
                <h2 class="text-2xl font-bold text-gray-800 border-b pb-3 mb-5 flex items-center gap-2">
                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c1.657 0 3 .895 3 2s-1.343 2-3 2-3 .895-3 2 1.343 2 3 2m-3 0h6m-9 0h12a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                    </svg>
                    Mã giảm giá
                </h2>
                <div class="grid sm:grid-cols-3 gap-3">
                    <div class="sm:col-span-2">
                        <input name="coupon" value="{{ old('coupon', $couponCode) }}"
                            class="w-full border border-gray-300 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 rounded-xl px-4 h-12 transition duration-150"
                            placeholder="Nhập mã giảm giá (nếu có)">
                    </div>
                    <button type="submit" name="action" value="apply_coupon"
                        class="h-12 rounded-xl bg-gray-900 text-white font-semibold hover:bg-black transition duration-150 shadow-md">
                        Áp dụng / Cập nhật
                    </button>
                </div>
            </section>



            {{-- Nút hoàn tất --}}
            <button type="submit" name="action" value="complete_order"
                class="w-full h-14 rounded-full bg-indigo-600 text-white font-extrabold text-lg shadow-xl hover:bg-indigo-700 transition duration-300 transform hover:scale-[1.01] focus:outline-none focus:ring-4 focus:ring-indigo-500/50 mt-4">
                Hoàn tất đơn hàng và Thanh toán
            </button>
        </form>

        {{-- Cột phải: Tóm tắt + Tổng tiền (Order Summary) --}}
        <aside class="lg:col-span-5">
            {{-- Đã thay đổi top-4 thành top-24 (hoặc giá trị phù hợp với chiều cao header của bạn) --}}
            <div class="sticky top-48 bg-white rounded-2xl p-6 md:p-8 shadow-xl ring-1 ring-gray-100">
                <h3 class="text-2xl font-bold text-gray-800 mb-5 flex items-center gap-2">
                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    Tóm tắt đơn hàng
                </h3>

                {{-- Danh sách sản phẩm --}}
                <div class="max-h-60 overflow-y-auto pr-2 mb-4">
                    <ul class="divide-y divide-gray-100">
                        @foreach($cart as $item)
                        <li class="py-3 flex items-start justify-between gap-3 group">
                            <div class="min-w-0 flex items-center gap-3">
                                <span class="text-sm font-semibold text-gray-500">x{{ $item['qty'] }}</span>
                                <p class="font-medium text-gray-800 truncate group-hover:text-indigo-600 transition">{{ $item['name'] }}</p>
                            </div>
                            <div class="whitespace-nowrap font-medium text-gray-700">
                                {{ number_format($item['price'] * $item['qty'], 0, ',', '.') }}đ
                            </div>
                        </li>
                        @endforeach
                    </ul>
                </div>

                {{-- Chi tiết tính toán --}}
                <dl class="mt-4 space-y-3 text-base text-gray-600 border-t pt-4">
                    <div class="flex justify-between">
                        <dt>Tạm tính</dt>
                        <dd class="font-medium text-gray-800">{{ number_format($subTotal,0,',','.') }}đ</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt>Phí vận chuyển</dt>
                        <dd class="font-medium text-green-600">{{ number_format($shipping,0,',','.') }}đ</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt>Giảm giá Coupon</dt>
                        <dd class="font-medium text-red-500">-{{ number_format($discount,0,',','.') }}đ</dd>
                    </div>
                </dl>

                {{-- Tổng cộng --}}
                <div class="mt-5 pt-4 border-t-2 border-dashed border-gray-200 flex justify-between items-center text-xl font-bold">
                    <span>Tổng cộng</span>
                    <span class="text-indigo-600">{{ number_format($grandTotal,0,',','.') }}đ</span>
                </div>
            </div>
        </aside>
    </div>
</div>
@endsection