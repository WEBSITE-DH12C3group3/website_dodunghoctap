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
    <!-- NAV -->
    <nav class="sticky top-0 z-40 border-b border-white/30 dark:border-slate-700/60 backdrop-blur bg-white/70 dark:bg-slate-900/60">
        <div class="max-w-6xl mx-auto px-4 py-3 flex items-center justify-between">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2 font-semibold tracking-tight">
                <span class="inline-flex h-8 w-8 items-center justify-center rounded-xl bg-gradient-to-br from-brand-600 to-brand-700 text-white shadow-sm">S3</span>
                <span>S3 — Stationery Shop</span>
            </a>
            <div class="flex items-center gap-3">
                @auth
                <span class="hidden sm:block text-sm/6 text-slate-600 dark:text-slate-300">
                    {{ auth()->user()->full_name }}
                    <span class="ml-2 rounded-lg px-2 py-0.5 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-200">
                        {{ optional(auth()->user()->role)->role_name }}
                    </span>
                </span>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button class="rounded-xl px-3 py-1.5 text-sm font-medium text-white bg-slate-900 dark:bg-brand-600 hover:opacity-90 shadow-sm">
                        Đăng xuất
                    </button>
                </form>
                @else
                <a class="text-sm hover:underline" href="{{ route('login') }}">Đăng nhập</a>
                <a class="rounded-xl px-3 py-1.5 text-sm font-medium text-white bg-brand-600 hover:bg-brand-700 shadow-sm" href="{{ route('register') }}">Đăng ký</a>
                @endauth
                <button id="themeToggle" class="ml-2 rounded-xl p-2 hover:bg-slate-200/60 dark:hover:bg-slate-800/60" title="Dark/Light">
                    <!-- sun/moon -->
                    <svg id="sun" class="h-5 w-5 hidden dark:block" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 18a6 6 0 1 0 0-12 6 6 0 0 0 0 12Zm0 4a1 1 0 0 1-1-1v-1.1a1 1 0 1 1 2 0V21a1 1 0 0 1-1 1Zm0-18a1 1 0 0 1-1-1V3a1 1 0 1 1 2 0v1a1 1 0 0 1-1 1Zm9 7h-1.1a1 1 0 1 1 0-2H21a1 1 0 1 1 0 2ZM4.1 11a1 1 0 1 1 0 2H3a1 1 0 0 1 0-2h1.1ZM18.364 5.636a1 1 0 1 1-1.414-1.414l.778-.778a1 1 0 0 1 1.414 1.414l-.778.778ZM6.272 17.728a1 1 0 0 1-1.414 0l-.778-.778A1 1 0 0 1 5.494 15.5l.778.778a1 1 0 0 1 0 1.414ZM5.636 5.636l-.778-.778A1 1 0 1 1 6.272 3.444l.778.778A1 1 0 0 1 5.636 5.636Zm12.728 12.728-.778.778a1 1 0 1 1-1.414-1.414l.778-.778a1 1 0 1 1 1.414 1.414Z" />
                    </svg>
                    <svg id="moon" class="h-5 w-5 dark:hidden" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79Z" />
                    </svg>
                </button>
            </div>
        </div>
    </nav>

    <main class="relative">
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

    <script>
        document.getElementById('themeToggle')?.addEventListener('click', () => {
            const root = document.documentElement;
            const dark = root.classList.toggle('dark');
            localStorage.setItem('theme', dark ? 'dark' : 'light');
        });
    </script>
</body>

</html>