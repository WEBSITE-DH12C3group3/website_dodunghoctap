@extends('layouts.store')
@section('title', $product->product_name)

@section('content')
<div class="bg-gradient-to-b from-blue-50/40 to-white">
    {{-- 1536px + dư địa 2xl ~ 1680px --}}
    <div class="max-w-screen-2xl 2xl:max-w-[1680px] mx-auto px-2 sm:px-4 lg:px-6 2xl:px-8 py-6">

        {{-- Breadcrumb --}}
        <nav class="text-sm text-gray-500 mb-4">
            <a href="{{ url('/') }}" class="hover:text-blue-600">Trang chủ</a>
            <span class="mx-2">/</span>
            <a href="{{ route('store.categories.index') }}" class="hover:text-blue-600">Danh mục</a>
            <span class="mx-2">/</span>
            <span class="mx-2">{{ $product->product_name }}</span>
        </nav>

        {{-- CARD LỚN: ẢNH + THÔNG TIN + COUPON (cùng 1 div) --}}
        <div class="bg-white rounded-2xl shadow-lg p-4 md:p-6">
            {{-- Lưới 3 cột trên desktop, 1 cột trên mobile --}}
            <div class="grid gap-6 grid-cols-1
            xl:grid-cols-[minmax(0,700px)_minmax(0,560px)_380px]
            2xl:grid-cols-[minmax(0,760px)_minmax(0,600px)_400px]">


                {{-- ẢNH (mobile: trên cùng) --}}
                <section class="order-1 lg:order-1 bg-white rounded-xl p-4 2xl:p-6">
                    <div class="aspect-[4/3] w-full rounded-lg overflow-hidden flex items-center justify-center bg-gray-50">
                        <img
                            src="{{ Str::startsWith($product->image_url, ['http://','https://']) ? $product->image_url : asset('storage/'.$product->image_url) }}"
                            alt="{{ $product->product_name }}"
                            class="h-full object-contain">
                    </div>
                    {{-- Nếu có nhiều ảnh, thêm thumbnails bên dưới --}}
                </section>

                {{-- THÔNG TIN + MUA (mobile: giữa) --}}
                <section class="order-2 lg:order-2 bg-white rounded-xl p-5">
                    <h1 class="text-2xl md:text-3xl font-bold text-gray-900 leading-tight">
                        {{ $product->product_name }}
                    </h1>

                    <div class="mt-2 grid gap-1 text-sm text-gray-600">
                        <span>Thương hiệu:
                            <span class="font-medium text-blue-700">{{ $product->brand->brand_name ?? 'Không rõ' }}</span>
                        </span>
                        <span>Tình trạng:
                            @if($product->stock_quantity > 0)
                            <span class="text-blue-700 font-medium">Còn hàng</span>
                            @else
                            <span class="text-rose-600 font-medium">Hết hàng</span>
                            @endif
                        </span>
                        <span>Mã sản phẩm: <span class="font-medium text-blue-700">{{ $product->product_id }}</span></span>
                    </div>

                    {{-- Giá (không giảm) --}}
                    <div class="mt-4">
                        <div class="text-3xl font-bold text-blue-700">
                            {{ number_format($product->price, 0, ',', '.') }}đ
                        </div>
                    </div>

                    {{-- Số lượng + Nút --}}
                    <form action="{{ route('cart.add') }}" method="POST" class="mt-5 space-y-4">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->product_id }}">

                        <div class="flex items-center gap-3">
                            <label class="text-sm text-gray-600">Số lượng</label>
                            <div class="flex items-center rounded-full border border-gray-300 overflow-hidden">
                                <button type="button"
                                    class="w-10 h-10 grid place-items-center text-gray-600"
                                    onclick="const i=this.nextElementSibling;i.value=Math.max(1,(+i.value||1)-1)">−</button>
                                <input name="qty" value="1" class="w-12 text-center outline-none" />
                                <button type="button"
                                    class="w-10 h-10 grid place-items-center text-gray-600"
                                    onclick="const i=this.previousElementSibling;i.value=(+i.value||1)+1">+</button>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <button type="submit"
                                class="rounded-full h-12 px-6 bg-blue-600 text-white font-semibold hover:bg-blue-700 transition flex items-center justify-center gap-2 shadow-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25h9.673a1.5 1.5 0 0 0 1.463-1.183l1.29-6A1.5 1.5 0 0 0 18.46 5.25H5.106m2.394 9l-2.25-9m2.25 9L6 8.25m1.5 6L18 8.25M9 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm10.5 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" />
                                </svg>
                                THÊM VÀO GIỎ
                            </button>

                            <button type="submit" name="buy_now" value="1"
                                class="rounded-full h-12 px-6 bg-indigo-700 text-white font-semibold hover:bg-indigo-800 transition shadow-lg">
                                MUA NGAY
                            </button>
                        </div>

                        {{-- Cam kết --}}
                        <ul class="mt-6 grid sm:grid-cols-2 gap-3 text-sm text-gray-700">
                            <li class="flex items-center gap-2"><span class="text-blue-600">✔</span> Hàng chính hãng</li>
                            <li class="flex items-center gap-2"><span class="text-blue-600">✔</span> Đổi trả 7 ngày</li>
                            <li class="flex items-center gap-2"><span class="text-blue-600">✔</span> Giao nhanh toàn quốc</li>
                            <li class="flex items-center gap-2"><span class="text-blue-600">✔</span> Hỗ trợ 8:00–22:00</li>
                        </ul>
                    </form>
                </section>
                <style>
                    /* Màu dùng cho nền ngoài và hiệu ứng vé */
                    .coupon-wrap {
                        --pv-primary: #2b6cff;
                        --pv-bg-soft: #eaf3ff;
                    }

                    /* khối ngoài để “khuyết tròn” thấy rõ */
                    .coupon-wrap {
                        background: var(--pv-bg-soft);
                        padding: 8px;
                        border-radius: 14px;
                    }

                    /* đường đứt + 2 khuyết tròn (notches) tại vị trí left-16 (64px) */
                    .coupon-perf {
                        border-left: 2px dashed rgba(43, 108, 255, .35);
                        position: absolute;
                        width: 0;
                    }

                    .coupon-card {
                        position: relative;
                    }

                    /* hover nâng cấp nhẹ */
                    .coupon-card:hover {
                        box-shadow: 0 14px 36px rgba(2, 23, 64, .14);
                    }
                </style>

                {{-- CSS cho vé coupon --}}
                {{-- COUPON – kiểu “vé” Thiên Long --}}
                <section class="order-3 lg:order-3">
                    @if(($coupons ?? collect())->count())
                    <div class="space-y-3 coupon-wrap"> {{-- nền xanh nhạt + bo góc ngoài --}}
                        @foreach($coupons as $cp)
                        @php
                        $code = $cp->code ?? ($cp['code'] ?? '');
                        $title = $cp->description ?: ($cp->display_off ?? 'Ưu đãi');
                        $validTo = $cp->valid_to ?? ($cp['expire'] ?? null);
                        $validToText = $validTo ? \Carbon\Carbon::parse($validTo)->format('d/m/Y') : null;
                        $validFrom = $cp->valid_from ?? null;
                        $validFromText = $validFrom ? \Carbon\Carbon::parse($validFrom)->format('d/m/Y') : null;

                        $off = [];
                        if (!empty($cp->discount_percent)) $off[] = (int)$cp->discount_percent.'%';
                        if (!empty($cp->discount_amount)) $off[] = number_format($cp->discount_amount,0,',','.').'đ';
                        $offText = count($off) ? 'Giảm '.implode(' / ', $off) : null;
                        @endphp

                        <div class="group relative">
                            <div class="coupon-card relative isolate flex gap-4 rounded-2xl bg-white p-4 pr-5 ring-1 ring-blue-200/70
                        transition duration-200 ease-out hover:scale-[1.01] hover:shadow-lg hover:ring-2 hover:ring-blue-300/80">

                                {{-- Cột trái: icon + vạch ngăn --}}
                                <div class="relative shrink-0 self-stretch"> <!-- thêm self-stretch -->
                                    {{-- Cột icon cố định 64px, căn giữa dọc bằng flex --}}
                                    <div class="w-16 h-full flex items-center justify-center">
                                        <div class="w-16 h-16 rounded-full bg-[#2659f3] text-white grid place-items-center shadow-sm ring-1 ring-blue-200/60">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"
                                                class="w-8 h-8" aria-hidden="true" focusable="false">
                                                <g transform="matrix(-1,-1.2246467991473532e-16,1.2246467991473532e-16,-1,512,512.0000610351562)">
                                                    <path d="m312.091 173.988v67.026l-7.139-5.212a34.979 34.979 0 0 1 0-56.6l7.138-5.21zm48.518 130.518a35 35 0 0 1 -14.423 28.3l-7.139 5.211v-67.027l7.138 5.209a35.012 35.012 0 0 1 14.424 28.307zm-97.035-97.012a62.116 62.116 0 0 0 45.243 59.667l3.274.922v69.96l-7.126-5.163a34.922 34.922 0 0 1 -12.783-17.737 13.474 13.474 0 1 0 -25.68 8.169 61.716 61.716 0 0 0 42.3 40.892l3.286.916v7.127a13.478 13.478 0 0 0 26.956 0v-7.164l3.275-.921a61.962 61.962 0 0 0 0-119.312l-3.275-.921v-69.959l7.126 5.162a34.9 34.9 0 0 1 12.784 17.726 13.474 13.474 0 1 0 25.68-8.17 61.685 61.685 0 0 0 -42.3-40.881l-3.287-.915v-7.127a13.478 13.478 0 0 0 -26.956 0v7.153l-3.275.921a62.108 62.108 0 0 0 -45.242 59.655zm248.426 196.035v-295.058h-372.887v295.058zm-509.5-75.281a81.383 81.383 0 0 0 28.852-23.882 79.776 79.776 0 0 0 16.148-48.366 79.805 79.805 0 0 0 -16.143-48.367 81.378 81.378 0 0 0 -28.857-23.871l-2.5-1.237v-74.054h112.17v295.058h-112.17v-74.044z"
                                                        fill-rule="evenodd" fill="currentColor" />
                                                </g>
                                            </svg>
                                        </div>
                                    </div>

                                    {{-- Vạch ngăn: bám full chiều cao cột trái, đặt đúng mép phải 64px --}}
                                    <div class="absolute top-0 bottom-0 left-20 border-l-2 border-dashed border-blue-400 pointer-events-none"></div>
                                </div>

                                {{-- nội dung --}}
                                <div class="min-w-0 flex-1 pl-4">
                                    <div class="flex items-start gap-2">
                                        <p class="font-semibold text-gray-900 leading-6 truncate">{{ $title }}</p>


                                    </div>

                                    <div class="mt-1 text-xs text-gray-600 flex flex-wrap gap-x-4 gap-y-1">
                                        @if($offText)
                                        <span class="inline-flex items-center gap-1">
                                            <span class="px-1.5 py-0.5 rounded bg-rose-50 border border-rose-200 text-rose-600">
                                                {{ $offText }}
                                            </span>
                                        </span>
                                        @endif

                                        @if($validFrom || $validToText)
                                        <span>HSD:
                                            <!-- @if($validFrom) {{ $validFromText }} @endif -->
                                            @if($validToText) {{ $validToText }} @endif
                                        </span>
                                        @endif
                                    </div>

                                    <div class="mt-2 flex items-center gap-2">
                                        <div class="text-xs bg-white border border-gray-200 rounded px-2 py-0.5">
                                            Mã: <span class="font-mono">{{ $code }}</span>
                                        </div>
                                        <button type="button"
                                            onclick="window.copyCoupon('{{ $code }}')"
                                            class="ml-auto inline-flex items-center rounded-full bg-blue-600 text-white
                                       text-xs px-3 py-1.5 hover:bg-blue-700 shadow-sm">Sao chép mã</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="px-5 py-4 text-sm text-gray-500 bg-white rounded-2xl shadow-lg">
                        Hiện chưa có mã áp dụng.
                    </div>
                    @endif
                </section>


                {{-- Toast mini báo đã sao chép --}}
                <script>
                    window.copyCoupon = async function(code) {
                        try {
                            await navigator.clipboard.writeText(code);
                        } catch (e) {}
                        const t = document.createElement('div');
                        t.className = 'fixed z-[60] bottom-6 left-1/2 -translate-x-1/2 px-3 py-2 text-sm rounded-full bg-gray-900 text-white/90 shadow-lg';
                        t.textContent = 'Đã sao chép: ' + code;
                        document.body.appendChild(t);
                        setTimeout(() => {
                            t.remove();
                        }, 1500);
                    };
                </script>

            </div>
        </div>

        {{-- Mô tả --}}
        <div class="mt-6 bg-white rounded-2xl shadow-lg p-6">
            <h2 class="text-xl font-semibold mb-3">Mô tả sản phẩm</h2>
            <div class="prose max-w-none">
                {!! nl2br(e($product->description)) !!}
            </div>
        </div>

        {{-- Sản phẩm cùng danh mục --}}
        @if(($related ?? collect())->count())
        <div class="mt-6 bg-white rounded-2xl shadow-lg p-6">
            <h2 class="text-xl font-semibold mb-4">Sản phẩm cùng loại</h2>
            <div class="grid sm:grid-cols-2 lg:grid-cols-5 gap-4">
                @foreach($related as $p)
                @include('store.partials.product-card', ['product' => $p])
                @endforeach
            </div>
        </div>
        @endif

        {{-- Đánh giá --}}
        <div class="mt-6 bg-white rounded-2xl shadow-lg p-6">
            <h2 class="text-xl font-semibold mb-4">Đánh giá sản phẩm</h2>

            <div class="grid lg:grid-cols-[340px,1fr] gap-6">
                {{-- Tổng quan --}}
                <div class="flex items-center gap-5 bg-white rounded-xl">
                    <div class="text-5xl font-bold text-yellow-500">{{ number_format($avg ?? 0,1) }}</div>
                    <div class="flex-1">
                        @php $t = $total ?? 0; @endphp
                        @for($star=5;$star>=1;$star--)
                        @php
                        $count = $dist[$star] ?? 0;
                        $percent = $t ? round($count*100/$t) : 0;
                        @endphp
                        <div class="flex items-center gap-3 text-sm mb-2">
                            <div class="w-12 text-gray-600">{{ $star }} <span class="text-yellow-500">★</span></div>
                            <div class="flex-1 h-2 bg-gray-100 rounded-full overflow-hidden">
                                <div class="h-2 bg-yellow-400" style="width: {{ $percent }}%"></div>
                            </div>
                            <div class="w-10 text-right text-gray-600">{{ $count }}</div>
                        </div>
                        @endfor
                        <div class="text-xs text-gray-500 mt-1">{{ number_format($t) }} đánh giá</div>
                    </div>
                </div>

                {{-- Form + danh sách --}}
                <div class="space-y-6">
                    @auth
                    <form action="{{ route('store.product.review.store', $product->product_id) }}"
                        method="POST" class="border border-gray-100 rounded-xl p-4 shadow-lg">
                        @csrf
                        <div class="mb-3">
                            <label class="block text-sm text-gray-600 mb-1">Chọn số sao</label>
                            <div class="flex gap-2">
                                @for($i=1;$i<=5;$i++)
                                    <label class="cursor-pointer">
                                    <input type="radio" name="rating" value="{{ $i }}" class="sr-only"
                                        onclick="document.getElementById('starV').innerText={{$i}};">
                                    <span class="inline-flex items-center justify-center w-9 h-9 border rounded text-yellow-500">★</span>
                                    </label>
                                    @endfor
                            </div>
                            <div class="text-xs text-gray-500 mt-1">Đã chọn: <span id="starV">0</span> sao</div>
                            @error('rating')<div class="text-rose-600 text-sm">{{ $message }}</div>@enderror
                        </div>
                        <textarea name="comment" rows="3" placeholder="Chia sẻ cảm nhận của bạn..."
                            class="w-full border rounded-lg p-3"></textarea>
                        @error('comment')<div class="text-rose-600 text-sm">{{ $message }}</div>@enderror
                        <div class="mt-3">
                            <button class="h-10 px-5 rounded-full bg-blue-600 text-white font-medium shadow-lg">Gửi đánh giá</button>
                        </div>
                    </form>
                    @else
                    <div class="text-sm text-gray-600">
                        <a class="text-blue-600 underline" href="{{ route('login') }}">Đăng nhập</a> để viết đánh giá.
                    </div>
                    @endauth

                    @forelse(($comments ?? collect()) as $cmt)
                    <div class="border-t pt-4 first:border-t-0 first:pt-0">
                        <div class="flex items-center gap-2 text-sm">
                            <span class="font-medium">{{ $cmt->user->name ?? 'Khách' }}</span>
                            <span class="text-gray-400">•</span>
                            <span class="text-gray-500">
                                {{ \Carbon\Carbon::parse($cmt->comment_date)->diffForHumans() }}
                            </span>
                        </div>
                        <div class="text-yellow-500 mt-1">
                            {{ str_repeat('★', (int)$cmt->rating) }}
                            <span class="text-gray-300">{{ str_repeat('★', 5-(int)$cmt->rating) }}</span>
                        </div>
                        @if($cmt->comment)
                        <div class="mt-1 text-gray-800">{{ $cmt->comment }}</div>
                        @endif
                    </div>
                    @empty
                    <div class="text-sm text-gray-500">Chưa có đánh giá nào.</div>
                    @endforelse
                </div>
            </div>
        </div>

    </div>
</div>
@endsection