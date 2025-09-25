@extends('layouts.store')

@section('title', 'Thanh toán VietQR')

@section('content')
<div class="max-w-lg mx-auto bg-white rounded-2xl shadow-sm ring-1 ring-gray-100 p-6 text-center">
    <h2 class="text-xl font-semibold">Quét VietQR để thanh toán</h2>
    <p class="text-gray-600 mt-1">Đơn hàng #{{ $order->order_id }}</p>

    <img src="{{ $qrUrl }}" alt="VietQR" class="mx-auto my-6 w-72 h-72 object-contain">

    <div class="text-lg font-bold">
        Số tiền: <span class="text-indigo-600">{{ number_format($amount,0,',','.') }}đ</span>
    </div>

    <div class="mt-3 text-sm text-gray-600">
        Ngân hàng (BIN): <b>{{ $bankBin }}</b> • STK: <b>{{ $accountNo }}</b><br>
        Tên tài khoản: <b>{{ $accName }}</b><br>
        Nội dung: <b>Thanh toan don hang #{{ $order->order_id }}</b>
    </div>

    <div class="mt-6 flex justify-center gap-3">
        <a href="{{ route('store.orders.index') }}" class="px-5 h-11 inline-flex items-center rounded-lg bg-gray-100 hover:bg-gray-200">
            Xem đơn hàng của tôi
        </a>
        <a href="{{ url('/') }}" class="px-5 h-11 inline-flex items-center rounded-lg bg-indigo-600 text-white hover:bg-indigo-700">
            Tiếp tục mua sắm
        </a>
    </div>
</div>
@endsection