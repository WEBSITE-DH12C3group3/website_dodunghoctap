@extends('layouts.store')
@section('title', 'Giỏ hàng')
@section('content')
<div class="max-w-5xl mx-auto px-4 py-6">
    <h1 class="text-2xl font-bold mb-4">Giỏ hàng</h1>

    @if(empty($cart))
    <p>Chưa có sản phẩm nào trong giỏ.</p>
    @else
    <div class="bg-white rounded-2xl shadow-sm divide-y">
        @foreach($cart as $item)
        <div class="p-4 flex items-center gap-4">
            <img src="{{ asset('storage/'.$item['image']) }}" class="w-16 h-16 object-contain bg-gray-50 rounded" />
            <div class="flex-1">
                <div class="font-medium">{{ $item['name'] }}</div>
                <div class="text-blue-700 font-semibold">{{ number_format($item['price'],0,',','.') }}đ</div>
            </div>
            <form action="{{ route('cart.update') }}" method="POST" class="flex items-center gap-2">
                @csrf
                <input type="hidden" name="id" value="{{ $item['id'] }}">
                <input name="qty" value="{{ $item['qty'] }}" class="w-16 text-center border rounded" />
                <button class="px-3 py-2 rounded bg-gray-800 text-white">Cập nhật</button>
            </form>
            <form action="{{ route('cart.remove', $item['id']) }}" method="POST" class="ml-2">
                @csrf @method('DELETE')
                <button class="px-3 py-2 rounded bg-rose-600 text-white">Xóa</button>
            </form>
        </div>
        @endforeach
    </div>

    <div class="mt-4 flex items-center justify-between">
        <div class="text-lg">
            Tổng: <span class="font-bold text-blue-700">{{ number_format($total,0,',','.') }}đ</span>
        </div>

        <form action="{{ route('checkout.vnpay.start') }}" method="POST">
            @csrf
            <button class="h-12 px-6 rounded-full bg-indigo-700 text-white grid place-items-center">
                Thanh toán VNPAY
            </button>
        </form>
    </div>

    @endif
</div>
@endsection