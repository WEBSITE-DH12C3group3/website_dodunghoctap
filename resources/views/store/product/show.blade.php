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
            <a href="{{ $product->category_id ? route('store.category', $product->category->category_id) : '#' }}" class="hover:text-blue-600">
                {{ $product->category->category_name ?? 'Danh mục' }}
            </a>
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
                                        @if($offText)
                                        <p class="font-semibold text-gray-900 leading-6 truncate">{{ $offText }}</p>
                                        @endif
                                    </div>

                                    <div class="mt-1 text-xs text-gray-600 flex flex-wrap gap-x-4 gap-y-1">
                                        @if($title)
                                        <span class="inline-flex items-center gap-1">
                                            <span class="px-1.5 py-0.5 rounded bg-rose-50 border border-rose-200 text-rose-600">
                                                {{ $title }}
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
                                            Mã: <span class="font-bold ">{{ $code }}</span>
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

        {{-- ĐÁNH GIÁ --}}
        <div class="mt-6 bg-white rounded-2xl shadow-lg p-6">
            <h2 class="text-xl font-semibold mb-4">Đánh giá sản phẩm</h2>


            <div class="grid lg:grid-cols-[340px,1fr] gap-6">
                {{-- TỔNG QUAN (trái) --}}
                <div class="flex items-start  gap-5 justify-self-start">
                    <div class="text-5xl font-bold text-yellow-500 flex items-center justify-center min-h-[100px]">{{ number_format($avg ?? 0,1) }}</div>
                    <div class="flex-1">
                        @php $t = $total ?? 0; @endphp

                        @for($row=5; $row>=1; $row--)
                        @php
                        $count = (int)($dist[$row] ?? 0);
                        $percent = $t ? round($count * 100 / $t) : 0;
                        @endphp

                        <div class="flex items-center gap-3 text-sm mb-2">
                            {{-- 5 sao bên trái: đầy = vàng, rỗng = viền xám --}}
                            <div class="flex w-[110px] shrink-0">
                                @for($s=1; $s<=5; $s++)
                                    @php $filled=$s <=$row; @endphp
                                    <svg class="w-4 h-4 mr-1 {{ $filled ? 'text-amber-400' : 'text-gray-300' }}" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                    <path d="M12 17.3l-5.46 3.2 1.46-6.04L3 9.76l6.17-.52L12 3.5l2.83 5.74 6.17.52-5 4.7 1.46 6.04z" />
                                    </svg>
                                    @endfor
                            </div>

                            {{-- thanh %: nền xám luôn hiển thị; vàng theo % --}}
                            @php
                            $percent = $t ? ($count * 100 / $t) : 0; // không round cứng
                            $percent = max(0, min(100, $percent)); // clamp 0..100
                            $percentCss = number_format($percent, 2, '.', ''); // "50.00"
                            @endphp

                            <div class="relative flex-1 min-w-[160px] h-2 rounded-full bg-gray-200/90 overflow-hidden">
                                @if($percent > 0)
                                <div class="absolute left-0 top-0 h-full
                    bg-gradient-to-b from-amber-300 to-amber-400
                    shadow-[inset_0_0_0_1px_rgba(251,191,36,.35)]"
                                    style="width: {{ $percentCss }}%"></div>
                                @endif
                            </div>



                            {{-- số lượt --}}
                            <div class="w-8 text-right text-gray-600 tabular-nums">{{ $count }}</div>
                        </div>
                        @endfor

                        <div class="text-xs text-gray-500 mt-1">{{ number_format($t) }} đánh giá</div>
                    </div>
                </div>

                {{-- CỤM "ĐÁNH GIÁ SẢN PHẨM" (phải) --}}
                <div class="flex items-center justify-between rounded-2xl border border-gray-200 p-4
                    w-full lg:w-auto lg:justify-self-end">
                    <div class="text-sm text-gray-700 font-medium mr-3">Đánh giá sản phẩm</div>
                    <div id="rv-star-group" class="flex items-center gap-2">
                        @for($i=1;$i<=5;$i++)
                            <button
                            type="button"
                            class="rv-star inline-grid place-items-center w-10 h-10 rounded border border-gray-500 text-gray-500 transition-colors"
                            data-open-review="true"
                            data-star="{{ $i }}"
                            aria-label="{{ $i }} sao">★</button>
                            @endfor
                    </div>
                </div>
            </div>

            {{-- DANH SÁCH BÌNH LUẬN --}}
            <div class="mt-6 space-y-6">
                @forelse(($comments ?? collect()) as $cmt)
                <div class="border-t pt-4 first:border-t-0 first:pt-0">
                    <div class="flex items-center gap-2 text-sm">
                        <span class="font-medium">{{ $cmt->user->name ?? 'Khách' }}</span>
                        <span class="text-gray-400">•</span>
                        <span class="text-gray-500">{{ \Carbon\Carbon::parse($cmt->comment_date)->diffForHumans() }}</span>
                    </div>
                    <div class="text-yellow-500 mt-1">
                        {{ str_repeat('★', (int)$cmt->rating) }}<span class="text-gray-300">{{ str_repeat('★', 5-(int)$cmt->rating) }}</span>
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

        {{-- ===== POPUP VIẾT ĐÁNH GIÁ ===== --}}
        @auth
        <div id="rv-modal" class="fixed inset-0 z-[70] hidden">
            {{-- overlay --}}
            <div class="absolute inset-0 bg-black/40"></div>

            {{-- modal --}}
            <div class="relative max-w-xl w-[90%] mx-auto mt-20 bg-white rounded-2xl shadow-2xl overflow-hidden">
                <div class="bg-[#144591] text-white font-semibold text-center py-3">ĐÁNH GIÁ SẢN PHẨM</div>

                <form id="rv-form" action="{{ route('store.product.review.store', $product->product_id) }}"
                    method="POST" enctype="multipart/form-data" class="p-5">
                    @csrf
                    <input type="hidden" name="rating" id="rv-rating" value="5">

                    {{-- Header sản phẩm + Chọn sao (giữa popup) --}}
                    <div class="mb-4">
                        {{-- Header sản phẩm: căn giữa --}}
                        <div class="flex flex-col items-center text-center gap-2 mb-3">
                            <img class="w-14 h-14 object-contain"
                                src="{{ Str::startsWith($product->image_url, ['http://','https://']) ? $product->image_url : asset('storage/'.$product->image_url) }}"
                                alt="">
                            <div class="font-medium max-w-[90%]">{{ $product->product_name }}</div>
                        </div>

                        {{-- Chọn sao: giống cụm bên ngoài, hover tô vàng liên tiếp --}}
                        <div class="flex flex-col items-center">
                            <div id="rvm-star-group" class="flex items-center gap-2 mb-2">
                                @for($i=1;$i<=5;$i++)
                                    <button type="button"
                                    class="rvm-star inline-grid place-items-center w-10 h-10 rounded border
                               border-gray-300 text-gray-500 transition-colors select-none"
                                    data-star="{{ $i }}" aria-label="{{ $i }} sao">★</button>
                                    @endfor
                            </div>
                            <div id="rvm-label" class="text-sm text-gray-600">Hài lòng</div>
                        </div>
                    </div>

                    {{-- Viết đánh giá --}}
                    <label class="block text-sm text-gray-700 mb-1">Viết đánh giá*</label>
                    <textarea name="comment" id="rvm-comment" rows="4" maxlength="500"
                        placeholder="Hãy chia sẻ đánh giá của bạn về sản phẩm"
                        class="w-full border rounded-lg p-3 outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                    <div class="text-xs text-gray-500 text-right" id="rvm-count">0/500 ký tự</div>

                    {{-- Ảnh đánh giá (UI mô phỏng, có thể lưu hoặc bỏ qua phía server) --}}
                    <div class="mt-3">
                        <div class="text-sm text-gray-700 mb-1">Hình ảnh đánh giá <span class="text-xs text-gray-500">(jpg, jpeg, png)</span></div>
                        <label class="inline-flex items-center justify-center w-16 h-16 border-2 border-dashed rounded-lg cursor-pointer hover:bg-gray-50">
                            <span class="text-2xl">+</span>
                            <input type="file" name="images[]" accept=".jpg,.jpeg,.png" class="hidden" multiple>
                        </label>
                    </div>

                    <div class="mt-5 flex items-center justify-end gap-3">
                        <button type="button" id="rv-cancel" class="px-4 py-2 rounded-lg border">Hủy</button>
                        <button class="px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700">Gửi đánh giá</button>
                    </div>
                </form>

                <button type="button" id="rv-close"
                    class="absolute right-3 top-2 text-white/90 hover:text-white text-xl leading-none">×</button>
            </div>
        </div>
        @else
        {{-- nếu chưa đăng nhập: click sao sẽ chuyển tới login --}}
        <form id="rv-login-redirect" action="{{ route('login') }}" method="GET"></form>
        @endauth

        {{-- JS nhỏ cho popup & sao --}}
        <script>
            (function() {
                const openBtns = document.querySelectorAll('[data-open-review]');
                const modal = document.getElementById('rv-modal');
                const closeBtns = [document.getElementById('rv-close'), document.getElementById('rv-cancel')];
                const body = document.body;

                // ======= CỤM SAO Ở BÊN NGOÀI (trên trang) =======
                const starGroup = document.getElementById('rv-star_group') || document.getElementById('rv-star-group'); // fallback
                if (starGroup) {
                    const rvStars = starGroup.querySelectorAll('.rv-star');

                    const paintOutside = (n = 0) => {
                        rvStars.forEach(s => {
                            const v = parseInt(s.dataset.star, 10);
                            const filled = v <= n;
                            s.classList.toggle('bg-amber-400', filled);
                            s.classList.toggle('border-amber-400', filled);
                            s.classList.toggle('text-white', filled);
                            s.classList.toggle('text-gray-500', !filled);
                            s.classList.toggle('border-gray-300', !filled);
                        });
                    };

                    rvStars.forEach(s => {
                        s.addEventListener('mouseenter', () => paintOutside(parseInt(s.dataset.star, 10)));
                    });
                    starGroup.addEventListener('mouseleave', () => paintOutside(0));
                }

                // ======= SAO TRONG POPUP =======
                const ratingInput = document.getElementById('rv-rating'); // hidden input
                const rvmGroup = document.getElementById('rvm-star-group'); // <div id="rvm-star-group">...</div>
                const rvmStars = rvmGroup ? rvmGroup.querySelectorAll('.rvm-star') : [];
                const rvmLabel = document.getElementById('rvm-label');
                const labels = {
                    1: 'Tệ',
                    2: 'Không hài lòng',
                    3: 'Bình thường',
                    4: 'Hài lòng',
                    5: 'Tuyệt vời'
                };

                let selected = parseInt(ratingInput?.value || '5', 10);

                function paintModal(n) {
                    rvmStars.forEach(btn => {
                        const v = parseInt(btn.dataset.star, 10);
                        const filled = v <= n;
                        btn.classList.toggle('bg-amber-400', filled);
                        btn.classList.toggle('border-amber-400', filled);
                        btn.classList.toggle('text-white', filled);
                        btn.classList.toggle('text-gray-500', !filled);
                        btn.classList.toggle('border-gray-300', !filled);
                    });
                    if (rvmLabel) rvmLabel.textContent = labels[n] || 'Hài lòng';
                }

                function applyModalRating(n) {
                    selected = n;
                    if (ratingInput) ratingInput.value = n;
                    paintModal(selected);
                }

                // hover + click trong popup
                rvmStars.forEach(btn => {
                    const v = parseInt(btn.dataset.star, 10);
                    btn.addEventListener('mouseenter', () => paintModal(v)); // preview
                    btn.addEventListener('click', () => applyModalRating(v)); // chọn
                });
                if (rvmGroup) {
                    rvmGroup.addEventListener('mouseleave', () => paintModal(selected));
                }
                // khởi tạo lần đầu
                paintModal(selected);

                // ======= MỞ / ĐÓNG POPUP =======
                openBtns.forEach(btn => {
                    btn.addEventListener('click', () => {
                        @auth
                        const preset = parseInt(btn.dataset.star || '5', 10);
                        applyModalRating(preset); // preset đúng số sao
                        modal.classList.remove('hidden');
                        body.style.overflow = 'hidden';
                        @else
                        document.getElementById('rv-login-redirect').submit();
                        @endauth
                    });
                });

                closeBtns.forEach(b => b && b.addEventListener('click', () => {
                    modal.classList.add('hidden');
                    body.style.overflow = '';
                }));
                if (modal) {
                    modal.addEventListener('click', e => {
                        if (e.target === modal) {
                            modal.classList.add('hidden');
                            body.style.overflow = '';
                        }
                    });
                }

                // ======= Đếm ký tự =======
                const ta = document.getElementById('rvm-comment');
                const cnt = document.getElementById('rvm-count');
                if (ta && cnt) {
                    ta.addEventListener('input', () => cnt.textContent = (ta.value.length || 0) + '/500 ký tự');
                }
            })();
        </script>

    </div>
</div>
@endsection