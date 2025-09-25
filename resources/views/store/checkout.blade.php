@extends('layouts.store')

@section('title', 'Tiến hành đặt hàng')

@section('content')
<div class="max-w-6xl mx-auto p-4 md:p-6 grid md:grid-cols-12 gap-6">
    {{-- Cột trái: Thông tin + phương thức --}}
    <form action="{{ route('checkout.store') }}" method="POST"
        class="md:col-span-7 bg-white rounded-2xl shadow-sm ring-1 ring-gray-100 p-6 space-y-6">
        @csrf

        {{-- Thông tin giao hàng --}}
        <div>
            <h2 class="text-xl font-semibold mb-4">Thông tin giao hàng</h2>
            <div class="grid sm:grid-cols-2 gap-3">
                <input name="fullname" value="{{ old('fullname', auth()->user()->full_name ?? '') }}"
                    class="w-full border rounded-lg px-3 h-11" placeholder="Họ và tên" required>
                <input name="phone" value="{{ old('phone', auth()->user()->phone ?? '') }}"
                    class="w-full border rounded-lg px-3 h-11" placeholder="Số điện thoại" required>
                <div class="sm:col-span-2">
                    <input name="address" value="{{ old('address', auth()->user()->address ?? '') }}"
                        class="w-full border rounded-lg px-3 h-11" placeholder="Địa chỉ" required>
                </div>
            </div>
        </div>

        {{-- Phương thức vận chuyển (mock như ảnh: Miễn phí) --}}
        <div>
            <h2 class="text-xl font-semibold mb-3">Phương thức vận chuyển</h2>
            <label class="flex items-center justify-between border rounded-xl px-4 py-3">
                <div class="flex items-center gap-3">
                    <span class="inline-flex w-4 h-4 rounded-full ring-2 ring-blue-600 bg-blue-600"></span>
                    <span>Miễn phí</span>
                </div>
                <span>0đ</span>
            </label>
        </div>

        {{-- Phương thức thanh toán --}}
        <div>
            <h2 class="text-xl font-semibold mb-3">Phương thức thanh toán</h2>
            <div class="space-y-3">
                <label class="flex items-center gap-3 border rounded-xl px-4 py-3">
                    <input type="radio" name="payment_method" value="cod" class="size-4" checked>
                    <span>Thanh toán khi giao hàng (COD)</span>
                </label>
                <label class="flex items-center gap-3 border rounded-xl px-4 py-3">
                    <input type="radio" name="payment_method" value="vietqr" class="size-4">
                    <span>Thanh toán qua cổng VietQR</span>
                </label>
                <label class="flex items-center gap-3 border rounded-xl px-4 py-3">
                    <input type="radio" name="payment_method" value="payos" class="size-4">
                    <span>Thanh toán qua cổng PayOS</span>
                </label>

            </div>
        </div>

        {{-- Coupon (nhập tại đây, submit cùng form) --}}
        <div class="grid sm:grid-cols-3 gap-3">
            <div class="sm:col-span-2">
                <input name="coupon" value="{{ old('coupon', $couponCode) }}"
                    class="w-full border rounded-lg px-3 h-11" placeholder="Mã giảm giá">
            </div>
            <button type="submit"
                class="h-11 rounded-lg bg-gray-900 text-white font-semibold hover:bg-black/80">
                Áp dụng / Cập nhật
            </button>
        </div>

        {{-- Nút hoàn tất --}}
        <button type="submit"
            class="w-full h-12 rounded-full bg-indigo-600 text-white font-semibold shadow-sm hover:bg-indigo-700">
            Hoàn tất đơn hàng
        </button>
    </form>

    {{-- Cột phải: Tóm tắt + tổng tiền --}}
    <aside class="md:col-span-5 bg-white rounded-2xl shadow-sm ring-1 ring-gray-100 p-6">
        <h3 class="text-lg font-semibold mb-4">Giỏ hàng</h3>
        <ul class="divide-y">
            @foreach($cart as $item)
            <li class="py-3 flex items-start justify-between gap-3">
                <div class="min-w-0">
                    <p class="font-medium truncate">{{ $item['name'] }}</p>
                    <p class="text-sm text-gray-500">x {{ $item['qty'] }}</p>
                </div>
                <div class="whitespace-nowrap">
                    {{ number_format($item['price'] * $item['qty'], 0, ',', '.') }}đ
                </div>
            </li>
            @endforeach
        </ul>

        <dl class="mt-4 space-y-2 text-sm">
            <div class="flex justify-between">
                <dt>Tạm tính</dt>
                <dd>{{ number_format($subTotal,0,',','.') }}đ</dd>
            </div>
            <div class="flex justify-between">
                <dt>Phí vận chuyển</dt>
                <dd>{{ number_format($shipping,0,',','.') }}đ</dd>
            </div>
            <div class="flex justify-between">
                <dt>Giảm giá</dt>
                <dd>-{{ number_format($discount,0,',','.') }}đ</dd>
            </div>
        </dl>
        <div class="mt-3 pt-3 border-t flex justify-between text-base font-semibold">
            <span>Tổng cộng</span>
            <span class="text-indigo-600">{{ number_format($grandTotal,0,',','.') }}đ</span>
        </div>
    </aside>
</div>
@endsection