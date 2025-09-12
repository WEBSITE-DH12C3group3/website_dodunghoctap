<!doctype html>
<html lang="vi" class="h-full antialiased">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'S3 — Stationery Shop' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'ui-sans-serif', 'system-ui']
                    },
                    colors: {
                        brand: {
                            600: '#4f46e5',
                            700: '#4338ca'
                        }
                    }
                }
            }
        }
        const theme = localStorage.getItem('theme');
        if (theme === 'dark' || (!theme && matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>

<body class="min-h-screen bg-slate-50 dark:bg-slate-900 text-slate-800 dark:text-slate-100">
    <!-- HEADER -->
    <header class="sticky top-0 z-40 bg-white/80 dark:bg-slate-900/70 backdrop-blur border-b border-slate-200/60 dark:border-slate-700/60">
        <div class="max-w-7xl mx-auto px-4 py-3 flex items-center gap-4">
            <a href="{{ route('home') }}" class="flex items-center gap-2 font-semibold">
                <span class="inline-grid place-items-center h-8 w-8 rounded-xl bg-gradient-to-br from-brand-600 to-brand-700 text-white">S3</span>
                <span>S3 — Stationery Shop</span>
            </a>

            <form action="{{ route('home') }}" method="get" class="flex-1 hidden md:flex">
                <div class="flex w-full rounded-xl border border-slate-300/70 dark:border-slate-700 bg-white dark:bg-slate-800 overflow-hidden">
                    <input type="text" name="q" value="{{ $q ?? '' }}" placeholder="Tìm bút, tập, thước…"
                        class="flex-1 px-3 py-2 bg-transparent outline-none">
                    <button class="px-3 py-2 bg-slate-900 dark:bg-brand-600 text-white">Tìm</button>
                </div>
            </form>

            <nav class="ml-auto flex items-center gap-3">
                @auth
                <a href="{{ route('dashboard') }}" class="text-sm hover:underline">Quản trị</a>
                <form action="{{ route('logout') }}" method="POST">@csrf
                    <button class="rounded-lg bg-slate-900 dark:bg-brand-600 text-white px-3 py-1.5 text-sm">Đăng xuất</button>
                </form>
                @else
                <a class="text-sm hover:underline" href="{{ route('login') }}">Đăng nhập</a>
                <a class="rounded-lg bg-brand-600 text-white px-3 py-1.5 text-sm" href="{{ route('register') }}">Đăng ký</a>
                @endauth

                <button id="themeToggle" class="p-2 rounded-lg hover:bg-slate-200/50 dark:hover:bg-slate-800/60" title="Dark/Light">
                    <svg class="h-5 w-5 dark:hidden" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79Z" />
                    </svg>
                    <svg class="h-5 w-5 hidden dark:block" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 18a6 6 0 1 0 0-12 6 6 0 0 0 0 12Z" />
                    </svg>
                </button>
            </nav>
        </div>
    </header>

    <!-- CONTENT -->
    <main class="max-w-7xl mx-auto px-4 py-6">
        @yield('content')
    </main>

    <!-- FOOTER -->
    <footer class="mt-auto border-t border-slate-200/60 dark:border-slate-700/60 bg-white/50 dark:bg-slate-900/70">
        <div class="max-w-7xl mx-auto px-4 py-8 grid gap-6 md:grid-cols-4">
            <div>
                <div class="flex items-center gap-2 font-semibold mb-2">
                    <span class="inline-grid place-items-center h-8 w-8 rounded-xl bg-gradient-to-br from-brand-600 to-brand-700 text-white">S3</span>
                    <span>S3 — Stationery Shop</span>
                </div>
                <p class="text-sm text-slate-600 dark:text-slate-300">Cửa hàng đồ dùng học tập. Giao nhanh – giá tốt.</p>
            </div>
            <div>
                <h4 class="font-semibold mb-2">Hỗ trợ</h4>
                <ul class="text-sm space-y-1 text-slate-600 dark:text-slate-300">
                    <li><a href="#" class="hover:underline">Chính sách đổi trả</a></li>
                    <li><a href="#" class="hover:underline">Vận chuyển</a></li>
                    <li><a href="#" class="hover:underline">Thanh toán</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-semibold mb-2">Danh mục</h4>
                <ul class="text-sm space-y-1 text-slate-600 dark:text-slate-300">
                    <li><a href="{{ route('home') }}" class="hover:underline">Tất cả sản phẩm</a></li>
                    {{-- Có categories thì loop ở trang home --}}
                </ul>
            </div>
            <div>
                <h4 class="font-semibold mb-2">Liên hệ</h4>
                <p class="text-sm text-slate-600 dark:text-slate-300">Email: support@s3.shop</p>
            </div>
        </div>
        <div class="text-center text-xs text-slate-500 dark:text-slate-400 py-4">© {{ date('Y') }} S3. All rights reserved.</div>
    </footer>

    <script>
        document.getElementById('themeToggle')?.addEventListener('click', () => {
            const root = document.documentElement;
            const dark = root.classList.toggle('dark');
            localStorage.setItem('theme', dark ? 'dark' : 'light');
        });
    </script>
</body>

</html>