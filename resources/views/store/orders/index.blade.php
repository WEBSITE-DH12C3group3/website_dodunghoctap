@extends('layouts.store')

@section('title', 'Đơn hàng của bạn')

@section('content')
<div class="max-w-6xl mx-auto p-4 md:p-6 grid md:grid-cols-12 gap-6">

    {{-- Sidebar tài khoản (đơn giản, tùy bạn thay bằng partial có sẵn) --}}
    <aside class="md:col-span-4 lg:col-span-3">
        <div class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-100 p-6">
            <div class="w-24 h-24 rounded-full bg-amber-500 mx-auto grid place-items-center text-white text-3xl font-bold">
                {{ Str::of(auth()->user()->full_name ?? auth()->user()->name)->trim()->explode(' ')->last()->substr(0,2)->lower() }}
            </div>
            <p class="text-center mt-3 text-gray-600">Xin chào, <b>{{ auth()->user()->full_name ?? auth()->user()->name }}</b></p>

            <nav class="mt-6 space-y-2">
                <a href="{{ route('profile.index') }}" class="block px-3 py-2 rounded-lg hover:bg-gray-50">
                    Tổng quan tài khoản
                </a>
                <a href="{{ route('profile.index') }}#info" class="block px-3 py-2 rounded-lg hover:bg-gray-50">
                    Thông tin tài khoản
                </a>
                <a href="{{ route('store.orders.index') }}" class="block px-3 py-2 rounded-lg bg-indigo-600 text-white">
                    Danh sách đơn hàng
                </a>
                <a href="{{ route('profile.index') }}#address" class="block px-3 py-2 rounded-lg hover:bg-gray-50">
                    Sổ địa chỉ
                </a>
                <a href="{{ route('logout') }}"
                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                    class="block px-3 py-2 rounded-lg hover:bg-gray-50">
                    Đăng xuất
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">@csrf</form>
            </nav>
        </div>
    </aside>

    {{-- Danh sách đơn --}}
    <section class="md:col-span-8 lg:col-span-9">
        <div class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-100">
            <div class="px-6 py-4 border-b">
                <h2 class="text-lg font-semibold text-gray-900">ĐƠN HÀNG CỦA BẠN</h2>
            </div>

            @if($orders->isEmpty())
            <div class="p-6 text-gray-600">Không có đơn hàng nào.</div>
            @else
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 text-gray-700">
                        <tr>
                            <th class="text-left py-3 px-6">Mã đơn hàng</th>
                            <th class="text-left py-3 px-6">Ngày đặt</th>
                            <th class="text-right py-3 px-6">Thành tiền</th>
                            <th class="text-left py-3 px-6">TT thanh toán</th>
                            <th class="text-left py-3 px-6">TT vận chuyển</th>
                            <th class="py-3 px-6"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @foreach ($orders as $od)
                        @php
                        [$payText, $payClass] = $paymentBadge[$od->status] ?? ['Không rõ','bg-gray-100 text-gray-600'];
                        [$shipText, $shipClass] = $shippingBadge[$od->delivery_status] ?? ['Không rõ','bg-gray-100 text-gray-600'];
                        @endphp
                        <tr>
                            <td class="py-3 px-6 font-medium">#{{ $od->order_id }}</td>
                            <td class="py-3 px-6">{{ \Carbon\Carbon::parse($od->order_date)->format('d/m/Y H:i') }}</td>
                            <td class="py-3 px-6 text-right">{{ number_format($od->total_amount,0,',','.') }}đ</td>
                            <td class="py-3 px-6">
                                <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold {{ $payClass }}">
                                    {{ $payText }}
                                </span>
                            </td>
                            <td class="py-3 px-6">
                                <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold {{ $shipClass }}">
                                    {{ $shipText }}
                                </span>
                            </td>
                            <td class="py-3 px-6 text-right">
                                {{-- nếu có trang chi tiết, đổi route bên dưới --}}
                                <a href="{{ url('/orders/'.$od->order_id) }}"
                                    class="text-indigo-600 hover:text-indigo-800">Xem chi tiết</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4">{{ $orders->links() }}</div>
            @endif
        </div>
    </section>
</div>
@endsection