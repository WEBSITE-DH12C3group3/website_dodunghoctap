@extends('layouts.store')
@section('title', 'Đăng nhập')
@section('content')
<section class="relative overflow-hidden">
    <div id="sky" class="absolute inset-0 animated-gradient overflow-hidden"></div>

    <style>
        input[type=password]::-ms-reveal,
        input[type=password]::-ms-clear {
            display: none;
        }


        .animated-gradient {
            background: linear-gradient(270deg, #000c6aff, #172160ff, #2e356aff, #37327eff, #344ba7ff, #3067a5ff);
            background-size: 800% 800%;
            animation: gradientAnimation 30s ease infinite;
        }

        @keyframes gradientAnimation {
            0% {
                background-position: 0% 50%
            }

            50% {
                background-position: 100% 50%
            }

            100% {
                background-position: 0% 50%
            }
        }

        /* SAO */
        .star {
            position: absolute;
            top: -10px;
            /* xuất phát trên đỉnh */
            width: 4px;
            height: 4px;
            background: #fff;
            border-radius: 50%;
            opacity: .6;
            box-shadow: 0 0 6px #fff;
            pointer-events: none;
            z-index: 2;
            will-change: transform, opacity;
        }

        /* rơi tốc độ “chuẩn” – duration/ delay sẽ được gán bằng CSS variables */
        .falling {
            animation:
                fallStar var(--dur, 2s) linear var(--delay, 0s) infinite,
                twinkle var(--twinkle, 2s) ease-in-out var(--delay, 0s) infinite alternate;
        }

        /* một số sao dùng quỹ đạo/nhịp rơi khác (không theo tốc độ chung) */
        .falling.odd {
            /* đổi keyframe + timing để tạo đoạn chậm rồi tăng tốc */
            animation-name: fallStarAlt, twinkle;
            animation-duration: var(--dur, 2s), var(--twinkle, 2s);
            animation-timing-function: cubic-bezier(.25, .1, .25, 1), ease-in-out;
            /* ease tuỳ biến */
            animation-delay: var(--delay, 0s), var(--delay, 0s);
            animation-iteration-count: infinite, infinite;
        }

        /* Keyframes */
        @keyframes fallStar {
            0% {
                transform: translateY(0);
                opacity: .6
            }

            70% {
                opacity: 1
            }

            100% {
                transform: translateY(110vh);
                opacity: 0
            }
        }

        /* Rơi “khác thường”: khựng nhẹ ở 30–40% rồi tăng tốc mạnh */
        @keyframes fallStarAlt {
            0% {
                transform: translateY(0);
                opacity: .6
            }

            30% {
                transform: translateY(12vh);
                opacity: .9
            }

            40% {
                transform: translateY(14vh);
                opacity: 1
            }

            /* chậm lại một nhịp */
            70% {
                transform: translateY(65vh);
                opacity: 1
            }

            /* tăng tốc */
            100% {
                transform: translateY(110vh);
                opacity: 0
            }
        }

        /* nhấp nháy nhẹ cho đẹp */
        @keyframes twinkle {
            from {
                opacity: .5
            }

            to {
                opacity: 1
            }
        }

        /* tôn trọng người dùng không thích hiệu ứng động */
        @media (prefers-reduced-motion: reduce) {
            .star {
                animation: none !important
            }
        }
    </style>

    <script>
        (function() {
            const container = document.getElementById('sky');

            const STAR_COUNT = 120; // nhiều sao hơn
            const MAX_DELAY = 5; // < 3.5s như yêu cầu

            for (let i = 0; i < STAR_COUNT; i++) {
                const s = document.createElement('span');
                s.className = 'star falling';

                // Vị trí ngang & kích thước sao
                const left = Math.random() * 100; // 0–100%
                const size = 2 + Math.random() * 3; // 2–5px
                s.style.left = left.toFixed(2) + '%';
                s.style.width = s.style.height = size.toFixed(1) + 'px';

                // Delay luôn < 3.5s
                const delay = Math.random() * MAX_DELAY; // 0–3.4
                s.style.setProperty('--delay', delay.toFixed(2) + 's');

                // Duration đa dạng: 1.6–8.0s
                const dur = 1.6 + Math.random() * 3;
                s.style.setProperty('--dur', dur.toFixed(2) + 's');

                // Twinkle riêng cho từng sao: 1–3s
                const tw = 1 + Math.random() * 2;
                s.style.setProperty('--twinkle', tw.toFixed(2) + 's');

                // ~30% sao “không theo tốc độ chung” (keyframe Alt khác nhịp)
                // if (Math.random() < 0.30) {
                //     s.classList.add('odd');
                // }

                container.appendChild(s);
            }
        })();
    </script>

    {{-- Vùng chứa form ở giữa màn hình --}}
    <div class="relative mx-auto max-w-screen-2xl px-4 py-16 md:py-24 min-h-[calc(100vh-200px)] grid place-items-center">
        <div class="w-full max-w-xl rounded-2xl bg-[#000000]/40 p-6 md:p-10 ring-1 ring-white/10 shadow-2xl backdrop-blur-sm">
            <h1 class="text-center text-white text-2xl md:text-3xl font-semibold tracking-wide">ĐĂNG NHẬP</h1>

            {{-- Thông báo lỗi (nếu có) --}}
            @if ($errors->any())
            <div class="mt-4 rounded-lg bg-red-500/10 border border-red-400/40 text-red-50 px-4 py-3 text-sm">
                <ul class="list-disc pl-5 space-y-1">
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="mt-6 space-y-6">
                @csrf

                {{-- Email --}}
                <div>
                    <label class="block text-white/90 font-semibold">Email <span class="text-red-300">*</span></label>
                    <div class="border-b border-white/30 focus-within:border-white transition">
                        <input type="email" name="email" value="{{ old('email') }}" autofocus
                            placeholder="Nhập Email"
                            class="w-full bg-transparent text-white placeholder-white/60 py-3 outline-none" />
                    </div>
                </div>

                {{-- Mật khẩu --}}
                <div>
                    <label class="block text-white/90 font-semibold">Mật khẩu <span class="text-red-300">*</span></label>
                    <div class="border-b border-white/30 focus-within:border-white transition">
                        <input type="password" name="password"
                            placeholder="Nhập Mật khẩu"
                            class="w-full bg-transparent text-white placeholder-white/60 py-3 outline-none" />
                    </div>
                </div>

                {{-- Quên mật khẩu --}}
                <div class="text-center text-white/90">
                    Quên mật khẩu? Nhấn vào
                    <a href="{{ route('password.request') }}" class="font-semibold text-yellow-300 hover:text-yellow-200 ">
                        đây
                    </a>
                </div>

                {{-- Nút Đăng nhập (vàng) --}}
                <button type="submit"
                    class="w-full h-12 rounded-full bg-[#F6D34B] hover:bg-[#efd034] active:translate-y-[1px]
                       text-[#242528] font-semibold text-lg shadow-[0_8px_18px_rgba(0,0,0,0.15)] transition">
                    Đăng nhập
                </button>
            </form>

            {{-- Đăng ký --}}
            <p class="mt-4 text-center text-white/90">
                Bạn chưa có tài khoản
                <a href="{{ route('register') }}" class="font-semibold text-yellow-300 hover:text-yellow-200 ">
                    Đăng ký tại đây
                </a>
            </p>

            {{-- Đăng nhập mạng xã hội --}}
            <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 gap-3">
                <a href="{{ route('google.redirect') }}"
                    class="inline-flex items-center justify-center gap-2 rounded-lg bg-[#C6262C] px-4 py-3 text-white font-medium hover:brightness-110 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-google" viewBox="0 0 16 16">
                        <path d="M15.545 6.558a9.4 9.4 0 0 1 .139 1.626c0 2.434-.87 4.492-2.384 5.885h.002C11.978 15.292 10.158 16 8 16A8 8 0 1 1 8 0a7.7 7.7 0 0 1 5.352 2.082l-2.284 2.284A4.35 4.35 0 0 0 8 3.166c-2.087 0-3.86 1.408-4.492 3.304a4.8 4.8 0 0 0 0 3.063h.003c.635 1.893 2.405 3.301 4.492 3.301 1.078 0 2.004-.276 2.722-.764h-.003a3.7 3.7 0 0 0 1.599-2.431H8v-3.08z" />
                    </svg>
                    Google
                </a>

                <a href="{{ url('auth/facebook') }}"
                    class="inline-flex items-center justify-center gap-2 rounded-lg bg-[#072B47] px-4 py-3 text-white font-medium
                  hover:brightness-110 transition">
                    {{-- Facebook icon --}}
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-facebook" viewBox="0 0 16 16">
                        <path d="M16 8.049c0-4.446-3.582-8.05-8-8.05C3.58 0-.002 3.603-.002 8.05c0 4.017 2.926 7.347 6.75 7.951v-5.625h-2.03V8.05H6.75V6.275c0-2.017 1.195-3.131 3.022-3.131.876 0 1.791.157 1.791.157v1.98h-1.009c-.993 0-1.303.621-1.303 1.258v1.51h2.218l-.354 2.326H9.25V16c3.824-.604 6.75-3.934 6.75-7.951" />
                    </svg>
                    Facebook
                </a>
            </div>
        </div>
    </div>
</section>
@endsection