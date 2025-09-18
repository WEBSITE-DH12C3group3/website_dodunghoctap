<!doctype html>
<html lang="vi" class="h-full antialiased">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'S3 — Stationery Shop' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
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
        // Dark mode toggle (remember choice)
        const theme = localStorage.getItem('theme');
        if (theme === 'dark' || (!theme && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        }
    </script>
</head>

<body class="h-full bg-gradient-to-br from-sky-50 to-indigo-100 dark:from-slate-900 dark:to-slate-800 text-slate-800 dark:text-slate-100">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <aside id="sidebar" class="w-72 bg-white dark:bg-slate-900/90 backdrop-blur-sm border-r-2 border-slate-300 dark:border-slate-600 shadow-2xl z-10 md:sticky md:top-0 md:h-screen transform -translate-x-full md:translate-x-0 transition-transform duration-300 ease-in-out">
            <div class="p-6 flex flex-col h-full">
                <!-- Mobile menu toggle (top of sidebar for mobile) -->
                <div class="flex items-center justify-between mb-6 md:hidden">
                    <button id="sidebarToggle" class="p-2 rounded-xl hover:bg-slate-200/60 dark:hover:bg-slate-800/60">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>
                <!-- Logo and Title -->
                <a href="{{ route('dashboard') }}" class="flex items-center gap-2 font-semibold tracking-tight mb-6">
                    <span class="inline-flex h-8 w-8 items-center justify-center rounded-xl bg-gradient-to-br from-brand-600 to-brand-700 text-white shadow-sm">S3</span>
                    <span>S3 — Stationery Shop</span>
                </a>
                <!-- Navigation -->
                <nav class="space-y-2 flex-1">
                    @if(auth()->check() && auth()->user()->hasPermission('manage_products'))
                    <a href="{{ route('admin.products') }}" class="flex items-center gap-3 p-3 rounded-xl text-base font-medium text-slate-700 dark:text-slate-200 hover:bg-brand-600 hover:text-white dark:hover:bg-brand-700 transition-colors duration-200 {{ Route::is('admin.products*') ? 'bg-brand-600 text-white dark:bg-brand-700' : '' }}">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M3 4h18v2H3V4Zm2 4h14l-1 12H6L5 8Zm4 2v8h2v-8H9Zm4 0v8h2v-8h-2Z" />
                        </svg>
                        <span>Quản lý sản phẩm</span>
                    </a>
                    @endif
                    @if(auth()->check() && auth()->user()->hasPermission('manage_categories'))
                    <a href="{{ route('admin.categories') }}" class="flex items-center gap-3 p-3 rounded-xl text-base font-medium text-slate-700 dark:text-slate-200 hover:bg-brand-600 hover:text-white dark:hover:bg-brand-700 transition-colors duration-200 {{ Route::is('admin.categories*') ? 'bg-brand-600 text-white dark:bg-brand-700' : '' }}">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M3 4h18v2H3V4Zm2 4h14l-1 12H6L5 8Zm4 2v8h2v-8H9Zm4 0v8h2v-8h-2Z" />
                        </svg>
                        <span>Quản lý danh mục</span>
                    </a>
                    @endif
                    @if(auth()->check() && auth()->user()->hasPermission('manage_users'))
                    <a href="{{ route('admin.users') }}" class="flex items-center gap-3 p-3 rounded-xl text-base font-medium text-slate-700 dark:text-slate-200 hover:bg-brand-600 hover:text-white dark:hover:bg-brand-700 transition-colors duration-200 {{ Route::is('admin.users') ? 'bg-brand-600 text-white dark:bg-brand-700' : '' }}">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 12a5 5 0 1 0-5-5 5 5 0 0 0 5 5Zm-9 9a9 9 0 0 1 18 0Z" />
                        </svg>
                        <span>Quản lý người dùng</span>
                    </a>
                    @endif
                    @if(auth()->check() && auth()->user()->hasPermission('view_statistics'))
                    <a href="{{ route('admin.stats') }}" class="flex items-center gap-3 p-3 rounded-xl text-base font-medium text-slate-700 dark:text-slate-200 hover:bg-brand-600 hover:text-white dark:hover:bg-brand-700 transition-colors duration-200 {{ Route::is('admin.stats') ? 'bg-brand-600 text-white dark:bg-brand-700' : '' }}">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M3 3h2v18H3V3Zm4 10h2v8H7v-8Zm4-6h2v14h-2V7Zm4 4h2v10h-2V11Zm4-6h2v16h-2V5Z" />
                        </svg>
                        <span>Thống kê</span>
                    </a>
                    @endif
                    @if(auth()->check() && auth()->user()->hasPermission('manage_orders'))
                    <a href="{{ route('admin.orders') }}" class="flex items-center gap-3 p-3 rounded-xl text-base font-medium text-slate-700 dark:text-slate-200 hover:bg-brand-600 hover:text-white dark:hover:bg-brand-700 transition-colors duration-200 {{ Route::is('admin.orders') ? 'bg-brand-600 text-white dark:bg-brand-700' : '' }}">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M3 6h18v2H3V6Zm2 4h14l-1 8H6l-1-8Zm5 2v4h2v-4H10Zm4 0v4h2v-4h-2Z" />
                        </svg>
                        <span>Đơn hàng</span>
                    </a>
                    @endif
                     @if(auth()->check() && auth()->user()->hasPermission('manage_purchases'))
                    <a href="{{ route('admin.purchase_orders') }}" class="flex items-center gap-3 p-3 rounded-xl text-base font-medium text-slate-700 dark:text-slate-200 hover:bg-brand-600 hover:text-white dark:hover:bg-brand-700 transition-colors duration-200 {{ Route::is('admin.purchase_orders') ? 'bg-brand-600 text-white dark:bg-brand-700' : '' }}">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M3 6h18v2H3V6Zm2 4h14l-1 8H6l-1-8Zm5 2v4h2v-4H10Zm4 0v4h2v-4h-2Z" />
                        </svg>
                        <span>Quản lý nhập kho</span>
                    </a>
                    @endif

                    @if(auth()->check() && auth()->user()->hasPermission('manage_purchases'))
                    <a href="{{ route('admin.suppliers') }}" class="flex items-center gap-3 p-3 rounded-xl text-base font-medium text-slate-700 dark:text-slate-200 hover:bg-brand-600 hover:text-white dark:hover:bg-brand-700 transition-colors duration-200 {{ Route::is('admin.suppliers') ? 'bg-brand-600 text-white dark:bg-brand-700' : '' }}">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M3 6h18v2H3V6Zm2 4h14l-1 8H6l-1-8Zm5 2v4h2v-4H10Zm4 0v4h2v-4h-2Z" />
                        </svg>
                        <span>Quản lý nhà cung cấp</span>
                    </a>
                    @endif  
                    @if(auth()->check() && auth()->user()->hasPermission('manage_brands'))
                    <a href="{{ route('admin.brands') }}" class="flex items-center gap-3 p-3 rounded-xl text-base font-medium text-slate-700 dark:text-slate-200 hover:bg-brand-600 hover:text-white dark:hover:bg-brand-700 transition-colors duration-200 {{ Route::is('admin.brands*') ? 'bg-brand-600 text-white dark:bg-brand-700' : '' }}">
        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="currentColor">
            <path d="M3 4h18v2H3V4Zm2 4h14l-1 12H6L5 8Zm4 2v8h2v-8H9Zm4 0v8h2v-8h-2Z" />
        </svg>
        <span>Quản lý thương hiệu</span>
    </a>
@endif
                </nav>
                <!-- User Info and Actions -->
                <div class="mt-auto border-t border-slate-200 dark:border-slate-700 pt-4">
                    @auth
                    <div class="flex items-center gap-3 mb-4">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-slate-800 dark:text-slate-100">{{ auth()->user()->full_name }}</p>
                            <p class="text-xs text-slate-600 dark:text-slate-300">
                                <span class="rounded-lg px-2 py-0.5 bg-slate-100 dark:bg-slate-800">
                                    {{ optional(auth()->user()->role)->role_name }}
                                </span>
                            </p>
                        </div>
                    </div>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button class="w-full flex items-center gap-3 p-3 rounded-xl text-base font-medium text-slate-700 dark:text-slate-200 hover:bg-red-600 hover:text-white dark:hover:bg-red-700 transition-colors duration-200">
                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h3a3 3 0 013 3v1" />
                            </svg>
                            <span>Đăng xuất</span>
                        </button>
                    </form>
                    @else
                    <a href="{{ route('login') }}" class="flex items-center gap-3 p-3 rounded-xl text-base font-medium text-slate-700 dark:text-slate-200 hover:bg-brand-600 hover:text-white dark:hover:bg-brand-700 transition-colors duration-200">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                        </svg>
                        <span>Đăng nhập</span>
                    </a>
                    <a href="{{ route('register') }}" class="flex items-center gap-3 p-3 rounded-xl text-base font-medium text-slate-700 dark:text-slate-200 hover:bg-brand-600 hover:text-white dark:hover:bg-brand-700 transition-colors duration-200">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                        </svg>
                        <span>Đăng ký</span>
                    </a>
                    @endauth
                    <!-- Theme Toggle -->
                    <button id="themeToggle" class="w-full flex items-center gap-3 p-3 rounded-xl text-base font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-200/60 dark:hover:bg-slate-800/60 transition-colors duration-200" title="Dark/Light">
                        <svg id="sun" class="h-6 w-6 hidden dark:block" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 18a6 6 0 1 0 0-12 6 6 0 0 0 0 12Zm0 4a1 1 0 0 1-1-1v-1.1a1 1 0 1 1 2 0V21a1 1 0 0 1-1 1Zm0-18a1 1 0 0 1-1-1V3a1 1 0 1 1 2 0v1a1 1 0 0 1-1 1Zm9 7h-1.1a1 1 0 1 1 0-2H21a1 1 0 1 1 0 2ZM4.1 11a1 1 0 1 1 0 2H3a1 1 0 0 1 0-2h1.1ZM18.364 5.636a1 1 0 1 1-1.414-1.414l.778-.778a1 1 0 0 1 1.414 1.414l-.778.778ZM6.272 17.728a1 1 0 0 1-1.414 0l-.778-.778A1 1 0 0 1 5.494 15.5l.778.778a1 1 0 0 1 0 1.414ZM5.636 5.636l-.778-.778A1 1 0 1 1 6.272 3.444l.778.778A1 1 0 0 1 5.636 5.636Zm12.728 12.728-.778.778a1 1 0 1 1-1.414-1.414l.778-.778a1 1 0 1 1 1.414 1.414Z" />
                        </svg>
                        <svg id="moon" class="h-6 w-6 dark:hidden" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79Z" />
                        </svg>
                        <span>Chuyển đổi giao diện</span>
                    </button>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 relative ml-4">
            <div class="absolute inset-0 -z-10 bg-[radial-gradient(24rem_24rem_at_120%_-10%,rgba(79,70,229,0.25),transparent)] dark:bg-[radial-gradient(24rem_24rem_at_120%_-10%,rgba(79,70,229,0.35),transparent)]"></div>
            <div class="max-w-6xl mx-auto p-4 md:p-6">
                @if (session('ok'))
                <div class="mb-4 rounded-xl border border-green-200/60 dark:border-green-400/30 bg-green-50/80 dark:bg-green-900/30 px-4 py-3 text-green-700 dark:text-green-200 shadow-sm">
                    {{ session('ok') }}
                </div>
                @endif

                @if ($errors->any())
                <div class="mb-4 rounded-xl border border-red-200/60 dark:border-red-400/30 bg-red-50/80 dark:bg-red-900/30 px-4 py-3 text-red-700 dark:text-red-200 shadow-sm">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
                    </ul>
                </div>
                @endif

                {{ $slot ?? '' }}
                @yield('content')
            </div>
        </main>
    </div>

    <script>
        // Theme toggle
        document.getElementById('themeToggle')?.addEventListener('click', () => {
            const root = document.documentElement;
            const dark = root.classList.toggle('dark');
            localStorage.setItem('theme', dark ? 'dark' : 'light');
        });

        // Sidebar toggle for mobile
        document.getElementById('sidebarToggle')?.addEventListener('click', () => {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('-translate-x-full');
        });
    </script>
</body>

</html>