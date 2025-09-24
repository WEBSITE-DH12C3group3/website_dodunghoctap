@extends('layouts.store')
@section('title', 'Tìm kiếm')

@section('content')
<style>
    /* Ẩn track mặc định để dùng track custom */
    .range-thumb::-webkit-slider-runnable-track {
        -webkit-appearance: none;
        background: transparent;
        height: 0;
    }

    .range-thumb::-moz-range-track {
        -moz-appearance: none;
        background: transparent;
        height: 0;
    }

    /* Tùy chỉnh nút kéo (thumb) cho Chrome/Safari */
    .range-thumb::-webkit-slider-thumb {
        -webkit-appearance: none;
        appearance: none;
        width: 18px;
        height: 18px;
        border-radius: 9999px;
        background: #FACC15;
        /* vàng */
        border: 3px solid #fff;
        box-shadow: 0 1px 3px rgba(0, 0, 0, .25);
        cursor: pointer;
        margin-top: -8px;
        /* căn chỉnh vị trí dọc */
        position: relative;
        z-index: 3;
    }

    /* Tùy chỉnh nút kéo (thumb) cho Firefox */
    .range-thumb::-moz-range-thumb {
        -moz-appearance: none;
        width: 18px;
        height: 18px;
        border-radius: 9999px;
        background: #FACC15;
        border: 3px solid #fff;
        box-shadow: 0 1px 3px rgba(0, 0, 0, .25);
        cursor: pointer;
        z-index: 3;
    }
</style>

<div class="mx-auto max-w-[1440px] xl:max-w-[1560px] px-3 md:px-6 xl:px-8 py-6">

    {{-- HEADER: Khoảng giá + Sắp xếp (không phá grid bên dưới) --}}
    <div class="mb-5 rounded-2xl bg-white/90 border border-gray-100 shadow-sm px-5 py-4">
        <div class="text-[15px] md:text-base font-semibold text-[#2F65F6]">
            CÓ {{ number_format($products->total()) }} KẾT QUẢ TÌM KIẾM PHÙ HỢP
        </div>
        @php
        $base = ['q' => $q, 'price' => "{$min}-{$max}"];
        @endphp
        <br>
        <div class="grid grid-cols-1 lg:grid-cols-[1fr,auto] gap-4 items-start">
            <form id="priceForm" action="{{ route('store.product.search') }}" method="GET" class="min-w-0">
                <input type="hidden" name="q" value="{{ $q }}">
                <input type="hidden" name="sort" value="{{ $sort ?? '' }}">
                <input type="hidden" id="priceHidden" name="price" value="{{ $min }}-{{ $max }}">

                <div class="flex items-center gap-3">
                    <h4 class="text-[15px] font-medium text-gray-800 shrink-0">Khoảng giá:</h4>
                    <span id="valMin" class="shrink-0 inline-block px-3 py-1 rounded-md bg-[#2F65F6] text-white text-xs font-semibold w-[110px] text-center">
                        {{ number_format($min,0,',','.') }}₫
                    </span>
                    <div id="priceSlider" class="relative h-5 w-full max-w-[780px] rounded-full bg-gray-200 select-none">
                        {{-- fill chọn khoảng --}}
                        <div id="rangeFill"
                            class="pointer-events-none absolute inset-y-0 h-2 top-1/2 -translate-y-1/2 bg-[#2F65F6] rounded-full"
                            style="left:0;right:0"></div>
                        {{-- Hai thanh trượt thực sự --}}
                        <input type="range" id="minRange"
                            min="{{ $absMin }}" max="{{ $absMax }}"
                            value="{{ $min }}" step="1000"
                            class="absolute inset-y-0 w-full h-full appearance-none bg-transparent pointer-events-none range-thumb z-20">
                        <input type="range" id="maxRange"
                            min="{{ $absMin }}" max="{{ $absMax }}"
                            value="{{ $max }}" step="1000"
                            class="absolute inset-y-0 w-full h-full appearance-none bg-transparent pointer-events-none range-thumb z-20">
                    </div>
                    <span id="valMax" class="shrink-0 inline-block px-3 py-1 rounded-md bg-[#2F65F6] text-white text-xs font-semibold w-[110px] text-center">
                        {{ number_format($max,0,',','.') }}₫
                    </span>
                </div>
            </form>

            @php
            $q = request()->query(); // tất cả query hiện có
            $sort = request('sort', 'newest'); // sort hiện tại
            $base = $q; unset($base['page']); // bỏ page khi đổi sort
            $urlAsc = url()->current() . '?' . http_build_query(array_replace($base, ['sort' => 'price_asc']));
            $urlDes = url()->current() . '?' . http_build_query(array_replace($base, ['sort' => 'price_desc']));
            $urlNew = url()->current() . '?' . http_build_query(array_replace($base, ['sort' => 'newest']));
            @endphp

            <div class="flex items-center gap-3 text-sm justify-end mt-4">
                <span class="text-gray-700">Sắp xếp:</span>

                <a href="{{ $urlAsc }}"
                    class="{{ $sort==='price_asc' ? 'text-blue-600 font-semibold' : 'text-gray-500 hover:text-gray-700' }}">
                    Giá tăng dần
                </a>

                <a href="{{ $urlDes }}"
                    class="{{ $sort==='price_desc' ? 'text-blue-600 font-semibold' : 'text-gray-500 hover:text-gray-700' }}">
                    Giá giảm dần
                </a>

                <a href="{{ $urlNew }}"
                    class="{{ $sort==='newest' ? 'text-blue-600 font-semibold' : 'text-gray-500 hover:text-gray-700' }}">
                    Mới nhất
                </a>
            </div>

        </div>
    </div>
    <script>
        (function() {
            const minRange = document.getElementById('minRange');
            const maxRange = document.getElementById('maxRange');
            const valMin = document.getElementById('valMin');
            const valMax = document.getElementById('valMax');
            const rangeFill = document.getElementById('rangeFill');
            const form = document.getElementById('priceForm');
            const priceHiddenInput = document.getElementById('priceHidden');
            const sliderContainer = document.getElementById('priceSlider');

            const STEP = 1000;
            const GAP = STEP; // khoảng cách tối thiểu giữa 2 đầu

            const toNumber = v => +v;
            const fmt = n => Number(n).toLocaleString('vi-VN') + '₫';

            function updateRange() {
                const minVal = +minRange.value;
                const maxVal = +maxRange.value;
                const totalRange = maxRange.max - minRange.min;
                const minPercent = ((minVal - minRange.min) / totalRange) * 100;
                const maxPercent = ((maxVal - minRange.min) / totalRange) * 100;

                rangeFill.style.left = `${minPercent}%`;
                rangeFill.style.right = `${100 - maxPercent}%`;
                valMin.textContent = fmt(minVal);
                valMax.textContent = fmt(maxVal);
                priceHiddenInput.value = `${minVal}-${maxVal}`;
            }

            let t;

            function autoSubmit() {
                clearTimeout(t);
                t = setTimeout(() => form.submit(), 300);
            }

            // ---- DRAG ----
            let isDragging = false;
            let activeHandle = null;

            function handleDrag(e) {
                if (!isDragging || !activeHandle) return;
                e.preventDefault();
                setFromClick(e.clientX, /*submit*/ false); // kéo thì chỉ render, submit khi mouseup
            }

            function startDrag(e, handleType) {
                isDragging = true;
                activeHandle = handleType;
                e.preventDefault();
                document.addEventListener('mousemove', handleDrag);
                document.addEventListener('mouseup', stopDrag);
            }

            function stopDrag() {
                isDragging = false;
                activeHandle = null;
                document.removeEventListener('mousemove', handleDrag);
                document.removeEventListener('mouseup', stopDrag);
                autoSubmit(); // submit khi dừng kéo
            }

            // ---- CLICK TRÊN TRACK: đặt giá trị NGAY lập tức ----
            function valueFromClientX(clientX) {
                const rect = sliderContainer.getBoundingClientRect();
                let ratio = (clientX - rect.left) / rect.width; // 0..1
                ratio = Math.max(0, Math.min(1, ratio));
                const minAbs = +minRange.min,
                    maxAbs = +maxRange.max;
                let v = minAbs + ratio * (maxAbs - minAbs);
                // làm tròn theo STEP:
                v = Math.round(v / STEP) * STEP;
                // kẹp biên:
                v = Math.max(minAbs, Math.min(v, maxAbs));
                return v;
            }

            function setFromClick(clientX, submitNow = true) {
                const v = valueFromClientX(clientX);
                // chọn handle gần hơn vị trí click (nếu đang kéo thì ưu tiên handle đang active)
                const dMin = Math.abs(v - (+minRange.value));
                const dMax = Math.abs(v - (+maxRange.value));
                const which = activeHandle ?? (dMin <= dMax ? 'min' : 'max');

                if (which === 'min') {
                    const maxAllowed = +maxRange.value - GAP;
                    minRange.value = Math.min(Math.max(v, +minRange.min), maxAllowed);
                } else {
                    const minAllowed = +minRange.value + GAP;
                    maxRange.value = Math.max(Math.min(v, +maxRange.max), minAllowed);
                }

                updateRange();
                if (submitNow) autoSubmit();
            }

            // --- Events ---
            // kéo bằng chuột trên thumb
            minRange.addEventListener('mousedown', (e) => startDrag(e, 'min'));
            maxRange.addEventListener('mousedown', (e) => startDrag(e, 'max'));

            // click trên track: cập nhật NGAY và cho phép kéo nếu giữ chuột
            sliderContainer.addEventListener('mousedown', function(e) {
                // nếu click thẳng vào thumb thì đã có listener riêng ở trên
                if (e.target === minRange || e.target === maxRange) return;
                // đặt giá trị ngay tại vị trí click
                // đồng thời xác định handle gần nhất để kéo tiếp nếu giữ chuột
                const rect = this.getBoundingClientRect();
                const minX = ((+minRange.value - +minRange.min) / (+maxRange.max - +minRange.min)) * rect.width + rect.left;
                const maxX = ((+maxRange.value - +minRange.min) / (+maxRange.max - +minRange.min)) * rect.width + rect.left;
                activeHandle = (Math.abs(e.clientX - minX) <= Math.abs(e.clientX - maxX)) ? 'min' : 'max';

                setFromClick(e.clientX, /*submitNow*/ true); // <-- cập nhật NGAY + debounce submit
                // nếu người dùng giữ chuột và kéo tiếp
                isDragging = true;
                document.addEventListener('mousemove', handleDrag);
                document.addEventListener('mouseup', stopDrag);
            });

            // init
            updateRange();
        })();
    </script>

    {{-- GRID 4x4 --}}
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-4 xl:grid-cols-4 gap-3 md:gap-4 xl:gap-5">
        @forelse ($products as $p)
        <a href="{{ route('store.product.show', $p->product_id) }}"
            class="group relative bg-white rounded-3xl border border-gray-100 shadow-sm
              overflow-hidden flex flex-col transition
              hover:-translate-y-1 hover:shadow-lg hover:ring-1 hover:ring-blue-100">

            {{-- Ảnh --}}
            <div class="aspect-[4/5] bg-gray-50/80">
                <img src="{{ $p->image_url ? asset('storage/' . $p->image_url) : asset('images/placeholder.webp') }}"
                    alt="{{ $p->product_name }}"
                    class="h-full w-full object-contain transition-transform duration-300 group-hover:scale-105"
                    onerror="this.src='{{ asset('images/placeholder.webp') }}'; this.onerror=null;" />
            </div>

            {{-- Nội dung --}}
            <div class="p-3 md:p-4">
                {{-- Tên sp: đổi xanh khi hover --}}
                <h3 class="text-sm md:text-[15px] font-medium text-gray-800 group-hover:text-blue-700 transition-colors line-clamp-2 min-h-[40px]">
                    {{ $p->product_name }}
                </h3>

                {{-- Rating + Đã bán: đổi xanh khi hover --}}
                <div class="mt-2 flex items-center gap-2 text-[12px] text-gray-500 transition-colors">
                    @php $rating = (int) round($p->avg_rating ?? 0); @endphp
                    <div class="flex">
                        @for ($i=1; $i<=5; $i++)
                            <svg class="w-4 h-4 {{ $i <= $rating ? 'text-amber-400' : 'text-gray-300 ' }}"
                            viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.2 3.685a1 1 0 00.95.69h3.873c.969 0 1.371 1.24.588 1.81l-3.135 2.279a1 1 0 00-.364 1.118l1.2 3.685c.3.921-.755 1.688-1.54 1.118L10 15.347l-3.273 2.065c-.784.57-1.838-.197-1.539-1.118l1.2-3.685a1 1 0 00-.364-1.118L2.89 9.112c-.783-.57-.38-1.81.588-1.81h3.873a1 1 0 00.95-.69l1.2-3.685z" />
                            </svg>
                            @endfor
                    </div>
                    <span>({{ $p->rating_count ?? 0 }})</span>
                    <span class="ml-auto">Đã bán {{ number_format($p->sold ?? 0, 0, ',', '.') }}</span>
                </div>

                {{-- Giá: đổi xanh đậm khi hover --}}
                <div class="mt-2 flex items-baseline gap-2">
                    <div class="text-blue-600 group-hover:text-blue-700 transition-colors font-semibold text-[15px] md:text-[16px]">
                        {{ number_format($p->price, 0, ',', '.') }}đ
                    </div>
                    @if(!empty($p->old_price) && $p->old_price > $p->price)
                    <div class="text-xs text-gray-400 group-hover:text-blue-500 transition-colors line-through">
                        {{ number_format($p->old_price,0,',','.') }}đ
                    </div>
                    <span class="text-[11px] text-rose-500 bg-rose-50 border border-rose-100 px-1.5 py-0.5 rounded-full">
                        -{{ max(1, round( (1 - $p->price/$p->old_price)*100 )) }}%
                    </span>
                    @endif
                </div>
            </div>
        </a>
        @empty
        <div class="col-span-2 md:col-span-4 text-center py-16 text-gray-500">Không tìm thấy sản phẩm phù hợp.</div>
        @endforelse
    </div>

    {{-- Phân trang --}}
    <div class="mt-6">
        {{ $products->onEachSide(1)->links() }}
    </div>
</div>
@endsection