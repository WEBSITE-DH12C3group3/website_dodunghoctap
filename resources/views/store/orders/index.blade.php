@extends('layouts.store')

@section('title', 'Đơn hàng của bạn')

@section('content')
<div class="max-w-[1320px] mx-auto px-4 py-6">
    <div class="grid grid-cols-1 md:grid-cols-[340px,1fr] gap-6">

        {{-- SIDEBAR (giữ nguyên) --}}
        <aside class="bg-white rounded-[10px] shadow border border-gray-200">
            <div class="py-6 flex flex-col items-center">
                @php
                use Illuminate\Support\Str;
                $name = auth()->user()->full_name ?: (auth()->user()->email ?: 'user');
                $initials = collect(preg_split('/\s+/', trim($name)))->filter()->map(fn($w)=>mb_substr($w,0,1))->take(2)->implode('');
                $initials = $initials ?: 'nn';
                @endphp
                <div class="h-24 w-24 rounded-full bg-[#F59E0B] flex items-center justify-center text-white text-[42px] font-extrabold leading-none">
                    {{ mb_strtolower($initials) }}
                </div>
                <div class="mt-3 text-gray-600 italic text-[18px]">
                    Xin chào, <span class="not-italic font-medium text-gray-800">{{ auth()->user()->full_name ?? 'người dùng' }}</span>
                </div>
            </div>

            <div class="px-4 pb-5">
                <nav class="rounded-lg overflow-hidden bg-[#1E4DE8]">
                    <a href="{{ route('profile.index') }}"
                        class="flex items-center gap-3 px-4 py-3 text-white/95 hover:bg-white/10">
                        <svg class="w-5 h-5 fill-white shrink-0" viewBox="0 0 20 20">
                            <path d="M10 10a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm-7 7c0-3.314 3.582-6 8-6s8 2.686 8 6v1H3v-1Z" />
                        </svg>
                        <span>Thông tin tài khoản</span>
                    </a>

                    <a href="{{ route('store.orders.index') }}"
                        class="flex items-center gap-3 px-4 py-3 bg-[#F59E0B] text-white font-medium">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-box2-heart-fill" viewBox="0 0 16 16">
                            <path d="M3.75 0a1 1 0 0 0-.8.4L.1 4.2a.5.5 0 0 0-.1.3V15a1 1 0 0 0 1 1h14a1 1 0 0 0 1-1V4.5a.5.5 0 0 0-.1-.3L13.05.4a1 1 0 0 0-.8-.4h-8.5ZM8.5 4h6l.5.667V5H1v-.333L1.5 4h6V1h1v3ZM8 7.993c1.664-1.711 5.825 1.283 0 5.132-5.825-3.85-1.664-6.843 0-5.132Z"></path>
                        </svg>
                        <span class="font-medium">Danh sách đơn hàng</span>
                    </a>

                    @php
                    $isAdmin = auth()->user()->role_id == 1;
                    @endphp
                    @if($isAdmin)
                    <a href="{{ route('dashboard') }}"
                        class="flex items-center gap-3 px-4 py-3 text-white/95 hover:bg-white/10">
                        <svg class="w-5 h-5 fill-white shrink-0" viewBox="0 0 20 20">
                            <path d="M3 3h14v2H3V3Zm0 4h14v2H3V7Zm0 4h14v2H3v-2Zm0 4h14v2H3v-2Z" />
                        </svg>
                        <span class="font-medium">Trang quản lý</span>
                    </a>
                    @endif

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="w-full text-left flex items-center gap-3 px-4 py-3 text-white/95 hover:bg-white/10">
                            <svg class="w-5 h-5 fill-white shrink-0" viewBox="0 0 20 20">
                                <path d="M7 3h6v2H7v10h6v2H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2Zm8.707 9.707-2-2a1 1 0 0 1 0-1.414l2-2L17.121 8.3 15.414 10l1.707 1.7-1.414 1.414Z" />
                            </svg>
                            <span class="font-medium">Đăng xuất</span>
                        </button>
                    </form>
                </nav>
            </div>
        </aside>

        {{-- MAIN --}}
        <section class="bg-white rounded-[10px] shadow border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-[16px] md:text-[20px] font-semibold tracking-wide text-gray-800">
                    ĐƠN HÀNG CỦA BẠN
                </h2>
            </div>

            @if($orders->isEmpty())
            <div class="p-6 text-gray-600">Không có đơn hàng nào.</div>
            @else
            <div class="overflow-x-auto p-6">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-gray-700">
                        <tr>
                            <th class="text-left py-3 px-4">Mã đơn hàng</th>
                            <th class="text-left py-3 px-4">Ngày đặt</th>
                            <th class="text-right py-3 px-4">Thành tiền</th>
                            <th class="text-left py-3 px-4">TT thanh toán</th>
                            <th class="text-left py-3 px-4">TT vận chuyển</th>
                            <th class="py-3 px-4"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach ($orders as $od)
                        @php
                        [$payText, $payClass] = $paymentBadge[$od->status] ?? ['Không rõ', 'bg-gray-100 text-gray-600'];
                        [$shipText, $shipClass] = $shippingBadge[$od->delivery_status] ?? ['Không rõ', 'bg-gray-100 text-gray-600'];
                        @endphp
                        <tr class="group">
                            <td class="py-3 px-4 font-medium text-gray-800">#{{ $od->order_id }}</td>
                            <td class="py-3 px-4 text-gray-600">{{ \Carbon\Carbon::parse($od->order_date)->format('d/m/Y H:i') }}</td>
                            <td class="py-3 px-4 text-right text-gray-600">{{ number_format($od->total_amount, 0, ',', '.') }}đ</td>
                            <td class="py-3 px-4">
                                <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold {{ $payClass }}">
                                    {{ $payText }}
                                </span>
                            </td>
                            <td class="py-3 px-4">
                                <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold {{ $shipClass }}">
                                    {{ $shipText }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-right">
                                <button onclick="toggleDetails(this)" class="text-[#1E4DE8] hover:text-[#143BC5] font-medium" data-order-id="{{ $od->order_id }}">
                                    Xem chi tiết
                                </button>
                            </td>
                        </tr>
                        <tr class="hidden detail-row-{{ $od->order_id }} bg-gray-50">
                            <td colspan="6" class="p-4">
                                <div class="space-y-2">
                                    <p class="text-sm font-medium text-gray-700">Thông tin chi tiết:</p>
                                    <p class="text-gray-600">Ngày đặt: {{ \Carbon\Carbon::parse($od->order_date)->format('d/m/Y H:i') }}</p>
                                    <p class="text-gray-600">Tổng tiền: {{ number_format($od->total_amount, 0, ',', '.') }}đ</p>
                                    @if(in_array($od->status, ['pending', 'pending_confirmation']))
                                        <form action="{{ route('store.orders.cancel', $od->order_id) }}" method="POST" class="mt-2 inline">
                                            @csrf
                                            @method('POST')
                                            <button type="submit" class="text-sm text-red-600 hover:underline">Hủy đơn hàng</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 border-t border-gray-200">
                {{ $orders->links() }}
            </div>
            @endif

            @if(session('ok'))
                <div class="p-6 text-center text-green-600">{{ session('ok') }}</div>
            @endif
            @if(session('error'))
                <div class="p-6 text-center text-red-600">{{ session('error') }}</div>
            @endif
        </section>

    </div>
</div>

<script>
    function toggleDetails(button) {
        const orderId = button.getAttribute('data-order-id');
        const detailRow = document.querySelector(`.detail-row-${orderId}`);
        if (detailRow.classList.contains('hidden')) {
            detailRow.classList.remove('hidden');
            button.textContent = 'Ẩn chi tiết';
        } else {
            detailRow.classList.add('hidden');
            button.textContent = 'Xem chi tiết';
        }
    }
</script>
@endsection