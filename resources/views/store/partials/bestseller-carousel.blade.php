@php $uid = 'bs_'.uniqid(); @endphp

<section class="mt-10 overflow-visible">
    <div class=" flex items-center gap-2 mb-4">
        <span class="inline-block h-7 w-1.5 rounded-full bg-blue-600"></span>
        <h2 class="text-xl font-bold tracking-wide">SẢN PHẨM BÁN CHẠY</h2>
    </div>

    <div id="{{ $uid }}" class="relative  overflow-visible">
        {{-- Nút trái --}}
        <button type="button"
            class="hidden md:flex absolute left-2 top-1/2 -translate-y-1/2 z-10 h-10 w-10 items-center justify-center rounded-full bg-white/90 shadow ring-1 ring-black/5 hover:bg-white transition
                   group-hover/card:flex"
            data-dir="-1" aria-label="Prev">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
        </button>

        {{-- Nút phải --}}
        <button type="button"
            class="hidden md:flex absolute right-2 top-1/2 -translate-y-1/2 z-10 h-10 w-10 items-center justify-center rounded-full bg-white/90 shadow ring-1 ring-black/5 hover:bg-white transition
                   group-hover/card:flex"
            data-dir="1" aria-label="Next">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
        </button>

        {{-- Khung cuộn --}}
        <div class="scroller overflow-x-hidden overflow-y-visible pt-1 pb-4">
            <div class="track flex  items-stretch gap-4 snap-x snap-proximity">
                @foreach($products as $p)
                <div class="slide snap-start shrink-0 w-[260px] sm:w-[280px] md:w-[300px]">
                    <a href="{{ route('store.product.show', $p->product_id) }}"
                        class="group/card block bg-white rounded-2xl ring-1 ring-blue-100 shadow-[0_1px_0_#e6f0ff,0_8px_24px_rgba(33,82,255,.06)]
            overflow-visible transition-transform duration-300
          hover:-translate-y-1 hover:ring-blue-300 hover:shadow-[0_2px_0_#e6f0ff,0_18px_48px_rgba(38,89,243,.28)]">

                        {{-- Ảnh (KHÔNG overflow-hidden để không “cắt” cảm giác mép trên) --}}
                        <div class="p-4 pt-6"> {{-- pt-5 để có thêm không gian phía trên --}}
                            <div class="relative rounded-xl">
                                <img src="{{ asset('storage/'.$p->image) }}"
                                    alt="{{ $p->product_name }}"
                                    class="w-full h-40 object-contain bg-[#F7FAFF] rounded-[18px]
                    transition-transform duration-300 group-hover/card:scale-[1.02]" />
                            </div>

                            {{-- Badges đặt RA NGOÀI khung ảnh để không bị cảm giác cắt mép --}}
                            <div class="mt-2 -mb-1 flex items-center gap-1 justify-start">
                                <span class="px-2 h-6 leading-6 rounded-full text-xs font-semibold
                     bg-rose-50 text-rose-600 ring-1 ring-rose-200">New</span>
                                @if(isset($p->sold))
                                <span class="px-2 h-6 leading-6 rounded-full text-xs font-semibold
                       bg-blue-50 text-blue-600 ring-1 ring-blue-200">Đã bán {{ (int)$p->sold }}</span>
                                @endif
                            </div>
                        </div>

                        {{-- Tên / Giá / Nút: căn giữa --}}
                        <div class="px-4 pb-4">
                            <div class="text-center font-medium line-clamp-2 min-h-[44px]  group-hover/card:text-blue-600 ">
                                {{ $p->product_name }}
                            </div>

                            <div class="mt-2 flex flex-col items-center gap-1">
                                <div class="text-[20px] font-bold text-[#2659f3] leading-none">
                                    {{ number_format($p->price,0,',','.') }}<span class="align-top text-[13px] font-semibold">đ</span>
                                </div>
                                @if(!empty($p->compare_price) && $p->compare_price > $p->price)
                                <div class="text-sm text-gray-400 line-through">
                                    {{ number_format($p->compare_price,0,',','.') }}đ
                                </div>
                                @endif
                            </div>

                            <div class="mt-3 flex justify-center">
                                <span class="inline-flex items-center justify-center h-10 px-6 rounded-full text-[13px] font-semibold
                      border border-[#98B2FF] text-[#2659f3] bg-white
                      transition-colors duration-200 group-hover/card:bg-[#F0F4FF]">
                                    Xem chi tiết
                                </span>
                            </div>
                        </div>
                    </a>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Fade mép trái/phải --}}
        <!-- <div class="pointer-events-none absolute inset-y-0 left-0 w-10 bg-gradient-to-r from-white to-transparent"></div>
        <div class="pointer-events-none absolute inset-y-0 right-0 w-10 bg-gradient-to-l from-white to-transparent"></div> -->
    </div>
</section>

<script>
    (() => {
        const root = document.getElementById(@json($uid));
        if (!root) return;

        const scroller = root.querySelector('.scroller');
        const track = root.querySelector('.track');
        const slides = root.querySelectorAll('.slide');

        function stepWidth() {
            const first = slides[0];
            if (!first) return 300;
            const style = getComputedStyle(track);
            const gap = parseFloat(style.columnGap || style.gap || 16);
            return Math.ceil(first.getBoundingClientRect().width + gap);
        }

        function scrollByDir(dir = 1) {
            const w = scroller.clientWidth;
            const amount = Math.max(stepWidth(), Math.floor(w * 0.9));
            scroller.scrollBy({
                left: dir * amount,
                behavior: 'smooth'
            });

            // loop mượt
            const nearEnd = scroller.scrollLeft + scroller.clientWidth >= (scroller.scrollWidth - 2);
            if (dir > 0 && nearEnd) {
                setTimeout(() => scroller.scrollTo({
                    left: 0,
                    behavior: 'smooth'
                }), 300);
            }
            if (dir < 0 && scroller.scrollLeft <= 2) {
                setTimeout(() => scroller.scrollTo({
                    left: scroller.scrollWidth,
                    behavior: 'instant'
                }), 80);
            }
        }

        // Nút điều hướng
        root.querySelectorAll('button[data-dir]').forEach(btn => {
            btn.addEventListener('click', () => scrollByDir(parseInt(btn.dataset.dir, 10)));
        });

        // Auto play
        let timer = null;
        const start = () => {
            timer = setInterval(() => scrollByDir(1), 3000);
        };
        const stop = () => {
            if (timer) clearInterval(timer);
            timer = null;
        };
        start();

        // Pause khi hover
        root.addEventListener('mouseenter', stop);
        root.addEventListener('mouseleave', start);

        // Ẩn nút nếu không cần cuộn
        const toggleArrows = () => {
            const arrows = root.querySelectorAll('button[data-dir]');
            const hide = scroller.scrollWidth <= scroller.clientWidth + 4;
            arrows.forEach(a => a.style.display = hide ? 'none' : '');
        };
        window.addEventListener('resize', toggleArrows);
        setTimeout(toggleArrows, 0);
    })();
</script>