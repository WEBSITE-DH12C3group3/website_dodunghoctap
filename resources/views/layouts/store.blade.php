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

<body class="font-sans antialiased">
    {{-- sticky nếu muốn dính trên cùng --}}
    <div class="sticky top-0 z-40">
        @include('store.partials.header', [
        'categories' => $categories ?? [],
        'cartCount' => $cartCount ?? 0,
        'activeCategoryId' => $activeCategoryId ?? null,
        ])
    </div>

    @yield('content')
</body>
@include('store.partials.footer', [
// có thể truyền các biến để tuỳ biến nhanh
// 'brandName' => 'Ecommerce Study Tools',
// 'siteName' => 'e-tools.vn',
])

</html>