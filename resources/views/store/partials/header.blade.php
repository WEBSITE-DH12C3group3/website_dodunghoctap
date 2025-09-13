{{-- resources/views/store/partials/header.blade.php --}}
@php
// Fallback an toàn khi partial được include mà chưa truyền biến
$categories = $categories ?? collect();
$cartCount = $cartCount ?? collect(session('cart') ?? [])->sum('qty'); // nếu dùng session cart
$activeCategoryId = $activeCategoryId ?? null;
@endphp

<header class="shadow">
    {{-- Hàng trên: logo + tìm kiếm + account + cart --}}
    {{-- Hàng trên: logo + search + hotline/đăng nhập/giỏ 1 HÀNG --}}
    <div class="bg-[#144591] text-white">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-[auto,1fr,auto] items-center gap-4 md:gap-6 py-3">

                {{-- Logo --}}
                <a href="{{ url('/') }}" class="flex items-center gap-3 shrink-0">
                    <img src="{{ asset('images/logo.svg') }}" alt="Logo" class="h-8 w-auto md:h-9">
                    <span class="sr-only">Trang chủ</span>
                </a>

                {{-- SEARCH: giới hạn width + ẩn nút X mặc định --}}
                <form action="{{ route('store.search') }}" method="GET" class="min-w-0 justify-self-center w-full">
                    <div class="mx-auto w-full sm:max-w-[520px] lg:max-w-[620px] xl:max-w-[700px]">
                        <div class="flex h-11 md:h-12 w-full">
                            <div class="relative flex-1 bg-white rounded-l-full">
                                <input id="headerSearchInput" name="q" type="search" value="{{ request('q') }}"
                                    placeholder="Tìm kiếm sản phẩm..."
                                    class="w-full h-full rounded-l-full border-0 pl-4 pr-10 text-gray-900 placeholder-gray-500
                            focus:ring-2 focus:ring-blue-300 outline-none" />
                                <button type="button" id="headerSearchClear"
                                    class="absolute right-2 top-1/2 -translate-y-1/2 hidden
                             text-[#1f4aa9] hover:text-[#0b2a5d]" aria-label="Xoá">
                                    <svg viewBox="0 0 24 24" class="h-5 w-5 fill-current">
                                        <path d="M18.3 5.7a1 1 0 0 0-1.4 0L12 10.6 7.1 5.7a1 1 0 1 0-1.4 1.4L10.6 12l-4.9 4.9a1 1 0 1 0 1.4 1.4L12 13.4l4.9 4.9a1 1 0 0 0 1.4-1.4L13.4 12l4.9-4.9a1 1 0 0 0 0-1.4z" />
                                    </svg>
                                </button>
                            </div>
                            <button type="submit"
                                class="shrink-0 w-11 md:w-12 grid place-items-center rounded-r-full
                           bg-[#0D2E69] hover:bg-[#0b2a5d] text-white">
                                <svg viewBox="0 0 24 24" class="h-5 w-5 fill-current">
                                    <path d="M10 2a8 8 0 105.293 14.293l4.707 4.707 1.414-1.414-4.707-4.707A8 8 0 0010 2zm0 2a6 6 0 110 12A6 6 0 0110 4z" />
                                </svg>
                                <span class="sr-only">Tìm kiếm</span>
                            </button>
                        </div>
                    </div>
                </form>

                {{-- NHÓM NÚT BÊN PHẢI: giữ 1 hàng --}}
                <div class="flex items-center gap-6 md:gap-8 whitespace-nowrap shrink-0">

                    {{-- Hotline --}}
                    <a href="tel:1900866819" class="flex items-center gap-3 text-white/95 hover:text-white">
                        <span class="inline-grid place-items-center h-9 w-9 rounded-full bg-[#0D2E69] ring-1 ring-white/10 shadow">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-telephone-fill" viewBox="0 0 16 16">
                                <path fill-rule="evenodd" d="M1.885.511a1.745 1.745 0 0 1 2.61.163L6.29 2.98c.329.423.445.974.315 1.494l-.547 2.19a.68.68 0 0 0 .178.643l2.457 2.457a.68.68 0 0 0 .644.178l2.189-.547a1.75 1.75 0 0 1 1.494.315l2.306 1.794c.829.645.905 1.87.163 2.611l-1.034 1.034c-.74.74-1.846 1.065-2.877.702a18.6 18.6 0 0 1-7.01-4.42 18.6 18.6 0 0 1-4.42-7.009c-.362-1.03-.037-2.137.703-2.877z" />
                            </svg>
                        </span>
                        <span class="leading-4">
                            <span class="block font-extrabold tracking-wide text-[15px]">1900 866 819</span>
                            <span class="block text-[12px] opacity-90 -mt-0.5">Hỗ trợ khách hàng</span>
                        </span>
                    </a>

                    {{-- Đăng nhập / Đăng ký --}}
                    @guest
                    <a href="{{ route('login') }}" class="flex items-center gap-3 group text-white/95 hover:text-white">
                        <span class="inline-grid place-items-center h-9 w-9 rounded-full bg-[#0D2E69] ring-1 ring-white/10 shadow">
                            <svg viewBox="0 0 16 16" class="h-5 w-5 fill-white">
                                <path d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1H3Z" />
                                <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z" />
                            </svg>
                        </span>
                        <span class="leading-4">
                            <span class="block font-extrabold text-[15px]">Đăng nhập</span>

                        </span>
                    </a>
                    @endguest

                    @auth
                    <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 text-white/95 hover:text-white">
                        <span class="inline-grid place-items-center h-9 w-9 rounded-full bg-[#0D2E69] ring-1 ring-white/10 shadow">
                            <svg viewBox="0 0 16 16" class="h-5 w-5 fill-white">
                                <path d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1H3Z" />
                                <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z" />
                            </svg>
                        </span>
                        <span class="leading-4">
                            <span class="block font-extrabold text-[15px] max-w-[160px] truncate">{{ auth()->user()->full_name ?? auth()->user()->name }}</span>
                            <span class="block text-[12px] opacity-90 -mt-0.5">Tài khoản của tôi</span>
                        </span>
                    </a>
                    @endauth

                    {{-- Giỏ hàng --}}
                    <a href="" class="relative flex items-center gap-2 hover:opacity-90">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                d="M2.25 3h1.386l.383 1.437M7.5 14.25h9.03a1.5 1.5 0 001.47-1.2l1.2-6a1.125 1.125 0 00-1.115-1.35H5.108" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M7.5 14.25L4.5 5.25" />
                            <circle cx="9" cy="19.5" r="1" />
                            <circle cx="17.25" cy="19.5" r="1" />
                        </svg>
                        <span class="hidden sm:inline">Giỏ hàng</span>
                        @if(($cartCount ?? 0) > 0)
                        <span class="absolute -top-2 -right-3 bg-red-500 text-white text-xs rounded-full h-5 min-w-[20px] px-1 flex items-center justify-center">{{ $cartCount }}</span>
                        @endif
                    </a>
                </div>

            </div>
        </div>
    </div>

    {{-- Ẩn nút X mặc định & JS hiển thị nút clear --}}
    <style>
        input[type="search"]::-webkit-search-cancel-button {
            display: none;
        }

        input[type="search"]::-webkit-search-decoration,
        input[type="search"]::-webkit-search-results-button,
        input[type="search"]::-webkit-search-results-decoration {
            display: none;
        }

        input[type="search"]::-ms-clear {
            display: none;
            width: 0;
            height: 0;
        }
    </style>
    <script>
        (function() {
            const i = document.getElementById('headerSearchInput');
            const c = document.getElementById('headerSearchClear');
            if (!i || !c) return;

            function t() {
                c.classList.toggle('hidden', !(i.value && i.value.trim().length))
            }
            t();
            i.addEventListener('input', t);
            c.addEventListener('click', () => {
                i.value = '';
                i.focus();
                t();
            });
        })();
    </script>


    {{-- Hàng dưới: thanh danh mục --}}
    <nav class="bg-blue-50 border-t border-blue-100">
        <div class="container mx-auto px-4">
            <ul class="flex gap-6 overflow-x-auto py-3 text-sm">
                @foreach($categories as $cat)
                @php
                $id = $cat->category_id ?? $cat['category_id'] ?? null;
                $name = $cat->category_name ?? $cat['category_name'] ?? '';
                $active = (string)($activeCategoryId ?? '') === (string)$id;
                @endphp
                <li class="group relative shrink-0">
                    <a href="{{ route('store.category', $id) }}"
                        class="flex items-center gap-2 font-medium {{ $active ? 'text-blue-700' : 'text-gray-800 hover:text-blue-700' }}">
                        <span class="text-lg">•</span>
                        <span>{{ $name }}</span>
                        <svg class="h-4 w-4 opacity-70" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </a>

                    {{-- Dropdown demo: thay bằng dữ liệu thực nếu có subcategory --}}
                    <div class="absolute left-0 top-full hidden group-hover:block bg-white shadow-lg rounded-xl p-4 min-w-[240px] z-20">
                        <div class="grid gap-2">
                            <a href="{{ route('store.category', $id) }}" class="hover:text-blue-700">Tất cả {{ $name }}</a>
                            <a href="#" class="hover:text-blue-700">Bán chạy</a>
                            <a href="#" class="hover:text-blue-700">Khuyến mãi</a>
                        </div>
                    </div>
                </li>
                @endforeach
            </ul>
        </div>
    </nav>
</header>