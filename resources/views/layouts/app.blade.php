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
                            <path d="M4 4h16v2H4V4zm2 4h12v12H6V8zm2 2v8h8v-8H8z" />
                        </svg>
                        <span>Quản lý sản phẩm</span>
                    </a>
                    @endif
                    @if(auth()->check() && auth()->user()->hasPermission('manage_categories'))
                    <a href="{{ route('admin.categories') }}" class="flex items-center gap-3 p-3 rounded-xl text-base font-medium text-slate-700 dark:text-slate-200 hover:bg-brand-600 hover:text-white dark:hover:bg-brand-700 transition-colors duration-200 {{ Route::is('admin.categories*') ? 'bg-brand-600 text-white dark:bg-brand-700' : '' }}">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M4 4h6v6H4V4zm0 10h6v6H4v-6zm10-10h6v6h-6V4zm0 10h6v6h-6v-6z" />
                        </svg>
                        <span>Quản lý danh mục</span>
                    </a>
                    @endif
                    <!-- @if(auth()->check() && auth()->user()->hasPermission('manage_users'))
                    <a href="{{ route('admin.users') }}" class="flex items-center gap-3 p-3 rounded-xl text-base font-medium text-slate-700 dark:text-slate-200 hover:bg-brand-600 hover:text-white dark:hover:bg-brand-700 transition-colors duration-200 {{ Route::is('admin.users') ? 'bg-brand-600 text-white dark:bg-brand-700' : '' }}">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 12a5 5 0 1 0-5-5 5 5 0 0 0 5 5zm-7 7a7 7 0 0 1 14 0H5z" />
                        </svg>
                        <span>Quản lý người dùng</span>
                    </a>
                    @endif -->
                    {{-- USERS --}}
                    @if(auth()->check() && auth()->user()->hasPermission('manage_users'))
                    <a href="{{ route('admin.users') }}"
                        class="flex items-center gap-3 p-3 rounded-xl text-base font-medium text-slate-700 dark:text-slate-200 hover:bg-brand-600 hover:text-white dark:hover:bg-brand-700 transition-colors duration-200 {{ Route::is('admin.users*') ? 'bg-brand-600 text-white dark:bg-brand-700' : '' }}">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 12a5 5 0 1 0-5-5 5 5 0 0 0 5 5zm-7 7a7 7 0 0 1 14 0H5z" />
                        </svg>
                        <span>Quản lý người dùng</span>
                    </a>

                    {{-- ROLES --}}
                    <a href="{{ route('admin.roles') }}"
                        class="mt-1 flex items-center gap-3 p-3 rounded-xl text-base font-medium text-slate-700 dark:text-slate-200 hover:bg-brand-600 hover:text-white dark:hover:bg-brand-700 transition-colors duration-200 {{ Route::is('admin.roles*') ? 'bg-brand-600 text-white dark:bg-brand-700' : '' }}">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 2l7 4v6c0 5-3.5 9-7 10-3.5-1-7-5-7-10V6l7-4z"/>
                        </svg>
                        <span>Nhóm quyền (Roles)</span>
                    </a>

                    {{-- PERMISSIONS --}}
                    <a href="{{ route('admin.permissions') }}"
                        class="mt-1 flex items-center gap-3 p-3 rounded-xl text-base font-medium text-slate-700 dark:text-slate-200 hover:bg-brand-600 hover:text-white dark:hover:bg-brand-700 transition-colors duration-200 {{ Route::is('admin.permissions*') ? 'bg-brand-600 text-white dark:bg-brand-700' : '' }}">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 1a5 5 0 00-5 5v2H6a2 2 0 00-2 2v9a2 2 0 002 2h12a2 2 0 002-2V10a2 2 0 00-2-2h-1V6a5 5 0 00-5-5zm-3 7V6a3 3 0 016 0v2H9z"/>
                        </svg>
                        <span>Quyền (Permissions)</span>
                    </a>

                    {{-- CUSTOMERS --}}
                    <a href="{{ route('admin.customers') }}"
                        class="mt-1 flex items-center gap-3 p-3 rounded-xl text-base font-medium text-slate-700 dark:text-slate-200 hover:bg-brand-600 hover:text-white dark:hover:bg-brand-700 transition-colors duration-200 {{ Route::is('admin.customers*') ? 'bg-brand-600 text-white dark:bg-brand-700' : '' }}">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M7 11a4 4 0 110-8 4 4 0 010 8zm10 0a4 4 0 110-8 4 4 0 010 8zM2 20a5 5 0 015-5h2a5 5 0 015 5v1H2v-1zm13 0a6.97 6.97 0 00-2.05-4.95A4.98 4.98 0 0120 20v1h-5v-1z"/>
                        </svg>
                        <span>Khách hàng</span>
                    </a>


                    @endif
                    @if(auth()->check() && auth()->user()->hasPermission('view_statistics'))
                    <a href="{{ route('admin.stats') }}" class="flex items-center gap-3 p-3 rounded-xl text-base font-medium text-slate-700 dark:text-slate-200 hover:bg-brand-600 hover:text-white dark:hover:bg-brand-700 transition-colors duration-200 {{ Route::is('admin.stats') ? 'bg-brand-600 text-white dark:bg-brand-700' : '' }}">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M4 16v4h4v-4H4zm6-8v12h4V8h-4zm6 4v8h4v-8h-4z" />
                        </svg>
                        <span>Thống kê</span>
                    </a>
                    @endif
                    @if(auth()->check() && auth()->user()->hasPermission('manage_orders'))
                    <a href="{{ route('admin.orders') }}" class="flex items-center gap-3 p-3 rounded-xl text-base font-medium text-slate-700 dark:text-slate-200 hover:bg-brand-600 hover:text-white dark:hover:bg-brand-700 transition-colors duration-200 {{ Route::is('admin.orders') ? 'bg-brand-600 text-white dark:bg-brand-700' : '' }}">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M3 3h18v2H3V3zm2 4h14v14H5V7zm2 2v10h10V9H7z" />
                        </svg>
                        <span>Đơn hàng</span>
                    </a>
                    @endif
                    @if(auth()->check() && auth()->user()->hasPermission('manage_purchases'))
                    <a href="{{ route('admin.purchase_orders') }}" class="flex items-center gap-3 p-3 rounded-xl text-base font-medium text-slate-700 dark:text-slate-200 hover:bg-brand-600 hover:text-white dark:hover:bg-brand-700 transition-colors duration-200 {{ Route::is('admin.purchase_orders') ? 'bg-brand-600 text-white dark:bg-brand-700' : '' }}">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M4 4h16v2H4V4zm2 4h12v12H6V8zm6 2l-4 4h3v4h2v-4h3l-4-4z" />
                        </svg>
                        <span>Quản lý nhập kho</span>
                    </a>
                    @endif
                    @if(auth()->check() && auth()->user()->hasPermission('manage_purchases'))
                    <a href="{{ route('admin.suppliers') }}" class="flex items-center gap-3 p-3 rounded-xl text-base font-medium text-slate-700 dark:text-slate-200 hover:bg-brand-600 hover:text-white dark:hover:bg-brand-700 transition-colors duration-200 {{ Route::is('admin.suppliers') ? 'bg-brand-600 text-white dark:bg-brand-700' : '' }}">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M3 7h18v2H3V7zm2 4h14l-1 10H6l-1-10zm8 2h2v6h-2v-6z" />
                        </svg>
                        <span>Quản lý nhà cung cấp</span>
                    </a>
                    @endif
                    @if(auth()->check() && auth()->user()->hasPermission('manage_brands'))
                    <a href="{{ route('admin.brands') }}" class="flex items-center gap-3 p-3 rounded-xl text-base font-medium text-slate-700 dark:text-slate-200 hover:bg-brand-600 hover:text-white dark:hover:bg-brand-700 transition-colors duration-200 {{ Route::is('admin.brands*') ? 'bg-brand-600 text-white dark:bg-brand-700' : '' }}">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M4 4h16v2H4V4zm2 4h12v12H6V8zm2 2h8v2H8v-2zm0 4h8v2H8v-2z" />
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
                    <!-- Theme Toggle / Home Button -->
                    <a href="{{ route('home') }}" class="flex items-center gap-3 p-3 rounded-xl text-base font-medium text-slate-700 dark:text-slate-200 hover:bg-brand-600 hover:text-white dark:hover:bg-brand-700 transition-colors duration-200">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 2.69l8.66 8.66a1 1 0 0 1-1.41 1.41L12 5.59l-7.25 7.25a1 1 0 0 1-1.41-1.41L12 2.69zM4 12v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8H4z" />
                        </svg>
                        <span>Trang Chủ</span>
                    </a>
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