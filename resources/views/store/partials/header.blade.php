{{-- resources/views/store/partials/header.blade.php --}}
@php
// Fallback an toàn khi partial được include mà chưa truyền biến
$categories = $categories ?? collect();
$cartCount = $cartCount ?? collect(session('cart') ?? [])->sum('qty'); // nếu dùng session cart
$activeCategoryId = $activeCategoryId ?? null;
$brandLogo = $brandLogo ?? asset('images/logoo.svg');
@endphp

<header class="shadow">
    <div class="bg-[#144591] text-white">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 min-[1030px]:grid-cols-[auto,1fr,auto] min-[1030px]:items-center gap-3 min-[1030px]:gap-6 py-3">

                <a href="{{ url('/') }}"
                    class="flex items-center gap-3 shrink-0 justify-self-center min-[1030px]:justify-self-start">
                    <img src="{{ $brandLogo }}" alt="Logo" class="h-8 w-auto min-[1030px]:h-9">
                    <span class="sr-only">Trang chủ</span>
                </a>

                <form action="{{ route('store.search') }}" method="GET"
                    class="min-w-0 w-full justify-self-stretch min-[1030px]:justify-self-center">
                    <div class="mx-0 sm:mx-auto w-full sm:max-w-[520px] lg:max-w-[620px] xl:max-w-[700px]">
                        <div class="flex h-11 min-[1030px]:h-12 w-full">
                            <div class="relative flex-1 bg-white rounded-l-full">
                                <input id="headerSearchInput" name="q" type="search" value="{{ request('q') }}"
                                    placeholder="Tìm kiếm sản phẩm..."
                                    class="w-full h-full rounded-l-full border-0 pl-4 pr-10 text-gray-900 placeholder-gray-500 focus:ring-2 focus:ring-blue-300 outline-none" />
                                <button type="button" id="headerSearchClear"
                                    class="absolute right-2 top-1/2 -translate-y-1/2 hidden text-[#1f4aa9] hover:text-[#0b2a5d]"
                                    aria-label="Xoá">
                                    <svg viewBox="0 0 24 24" class="h-5 w-5 fill-current">
                                        <path d="M18.3 5.7a1 1 0 0 0-1.4 0L12 10.6 7.1 5.7a1 1 0 1 0-1.4 1.4L10.6 12l-4.9 4.9a1 1 0 1 0 1.4 1.4L12 13.4l4.9 4.9a1 1 0 0 0 1.4-1.4L13.4 12l4.9-4.9a1 1 0 0 0 0-1.4z" />
                                    </svg>
                                </button>
                            </div>
                            <button type="submit"
                                class="shrink-0 w-11 min-[1030px]:w-12 grid place-items-center rounded-r-full bg-[#0D2E69] hover:bg-[#0b2a5d] text-white">
                                <svg viewBox="0 0 24 24" class="h-5 w-5 fill-current">
                                    <path d="M10 2a8 8 0 105.293 14.293l4.707 4.707 1.414-1.414-4.707-4.707A8 8 0 0010 2zm0 2a6 6 0 110 12A6 6 0 0110 4z" />
                                </svg>
                                <span class="sr-only">Tìm kiếm</span>
                            </button>
                        </div>
                    </div>
                </form>

                <div class="flex items-center gap-6 min-[1030px]:gap-8 whitespace-nowrap shrink-0
              w-full min-[1030px]:w-auto justify-between min-[1030px]:justify-end">

                    {{-- Hotline --}}
                    <a href="tel:1900866819" class="flex items-center gap-3 text-white/95 hover:text-white">
                        <span class="inline-grid place-items-center h-9 w-9 rounded-full bg-[#0D2E69] ring-1 ring-white/10 shadow">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-telephone-fill" viewBox="0 0 16 16">
                                <path fill-rule="evenodd" d="M1.885.511a1.745 1.745 0 0 1 2.61.163L6.29 2.98c.329.423.445.974.315 1.494l-.547 2.19a.68.68 0 0 0 .178.643l2.457 2.457a.68.68 0 0 0 .644.178l2.189-.547a1.75 1.75 0 0 1 1.494.315l2.306 1.794c.829.645.905 1.87.163 2.611l-1.034 1.034c-.74.74-1.846 1.065-2.877.702a18.6 18.6 0 0 1-7.01-4.42 18.6 18.6 0 0 1-4.42-7.009c-.362-1.03-.037-2.137.703-2.877z" />
                            </svg>
                        </span>
                        <span class="leading-4">
                            <span class="block font-extrabold tracking-wide text-[15px]">1900 866 819</span>
                            <span class="block text-[12px] opacity-90 -mt-0.5 b">Hỗ trợ khách hàng</span>
                        </span>
                    </a>

                    {{-- Đăng nhập / Đăng ký --}}
                    @guest
                    <a href="{{ route('login') }}" class="flex items-center gap-3 text-white/95 hover:text-white">
                        <span class="inline-grid place-items-center h-9 w-9 rounded-full bg-[#0D2E69] ring-1 ring-white/10 shadow">
                            <svg viewBox="0 0 16 16" class="h-5 w-5 fill-white">
                                <path d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1H3Z" />
                                <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z" />
                            </svg>
                        </span>
                        <span class="leading-4">
                            <span class="block font-extrabold text-[15px]">Đăng nhập</span>
                            <form action="{{ route('register') }}" method="GET" class="block text-[12px] opacity-90 -mt-0.5 b">
                                <button type="submit">Đăng ký</button>
                            </form>
                        </span>
                    </a>
                    @endguest
                    @auth
                    <a href="{{ route('profile.index') }}" class="flex items-center gap-3 text-white/95 hover:text-white">
                        <span class="inline-grid place-items-center h-9 w-9 rounded-full bg-[#0D2E69] ring-1 ring-white/10 shadow">
                            <svg viewBox="0 0 16 16" class="h-5 w-5 fill-white">
                                <path d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1H3Z" />
                                <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z" />
                            </svg>
                        </span>
                        <span class="leading-4">
                            <span class="block font-extrabold text-[15px] max-w-[160px] truncate">Hi, {{ auth()->user()->full_name ?? auth()->user()->name }}</span>
                            <form action="{{ route('logout') }}" method="POST" class="block text-[12px] opacity-90 -mt-0.5 b">
                                @csrf
                                <button type="submit" class="hover:underline">Đăng xuất</button>
                            </form>
                        </span>
                    </a>
                    @endauth

                    {{-- Giỏ hàng --}}
                    <a href="{{ route('cart.index') }}" class="flex items-center gap-3 text-white/95 hover:text-white">
                        <span class="inline-grid place-items-center h-9 w-9 rounded-full bg-[#0D2E69] ring-1 ring-white/10 shadow">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-bag-fill" viewBox="0 0 16 16">
                                <path d="M8 1a2.5 2.5 0 0 1 2.5 2.5V4h-5v-.5A2.5 2.5 0 0 1 8 1m3.5 3v-.5a3.5 3.5 0 1 0-7 0V4H1v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V4z" />
                            </svg>
                        </span>
                        <span class="hidden sm:inline block font-extrabold text-[15px] ">Giỏ hàng</span>
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

        .b {
            margin-top: 5px;
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


    {{-- Hàng dưới: thanh danh mục (đẹp & tự wrap nhiều dòng) --}}
    <nav class="bg-blue-50 border-t border-blue-100">
        <div class="container mx-auto px-4">
            <ul class="flex flex-wrap items-center gap-x-3 gap-y-2 md:gap-x-4 md:gap-y-3 py-3">
                @foreach($categories as $cat)
                @php
                $id = $cat->category_id ?? $cat['category_id'] ?? null;
                $name = $cat->category_name ?? $cat['category_name'] ?? '';
                $active = (string)($activeCategoryId ?? '') === (string)$id;
                @endphp
                <li class="shrink-0">
                    <a href="{{ $id ? route('store.category', $id) : '#' }}"
                        class="inline-flex items-center gap-2 rounded-full px-3.5 py-2
                    text-[13px] md:text-sm leading-none ring-1 transition
                    {{ $active
                        ? 'bg-white text-blue-700 ring-blue-200 shadow-sm'
                        : 'bg-white/90 text-gray-800 ring-gray-200 hover:bg-white hover:text-blue-700 hover:ring-blue-200 hover:shadow-sm' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-caret-down-fill" viewBox="0 0 16 16">
                            <path d="M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z" />
                        </svg>
                        <span class="whitespace-nowrap">{{ $name }}</span>
                    </a>
                </li>
                @endforeach
            </ul>
        </div>
    </nav>
</header>