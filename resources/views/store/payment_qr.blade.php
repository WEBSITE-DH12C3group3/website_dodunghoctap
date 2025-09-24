@extends('layouts.store')

@section('title', 'Thanh toán VietQR')

@section('content')
<div class="max-w-lg mx-auto bg-white rounded-2xl shadow-sm ring-1 ring-gray-100 p-6 text-center">
    <h2 class="text-xl font-semibold">Quét mã để thanh toán</h2>
    <p class="text-gray-600 mt-1">Đơn hàng #{{ $order->order_id }}</p>

    <img src="{{ $vietqrUrl }}" alt="VietQR" class="mx-auto my-6 w-72 h-72 object-contain">

    <div class="text-lg font-bold">
        Số tiền: <span class="text-indigo-600">{{ number_format($grandTotal,0,',','.') }}đ</span>
    </div>

    <p class="text-sm text-gray-500 mt-3">
        Vui lòng chuyển khoản đúng nội dung. Sau khi nhận tiền, hệ thống sẽ xác nhận đơn.
    </p>

    <a href="{{ route('store.orders.index') }}" class="inline-block mt-6 text-indigo-700 hover:underline">
        Xem đơn hàng của tôi
    </a>
</div>
@endsection