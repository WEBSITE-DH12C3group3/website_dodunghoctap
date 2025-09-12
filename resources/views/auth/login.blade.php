@extends('layouts.app')
@section('content')
<div class="min-h-[70vh] grid place-items-center">
    <div class="w-full max-w-lg rounded-2xl bg-white/80 dark:bg-slate-900/50 backdrop-blur p-6 md:p-8 shadow-xl ring-1 ring-slate-900/5 dark:ring-white/10">
        <div class="mb-6 flex items-center gap-3">
            <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-brand-600 to-brand-700 text-white grid place-items-center shadow">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 17a2 2 0 0 0 2-2v-1h-4v1a2 2 0 0 0 2 2Zm6-6V9a6 6 0 1 0-12 0v2H4v10h16V11h-2Zm-8-2a4 4 0 1 1 8 0v2H10V9Z" />
                </svg>
            </div>
            <div>
                <h1 class="text-xl font-semibold">Đăng nhập</h1>
                <p class="text-sm text-slate-600 dark:text-slate-300">Truy cập khu vực quản trị & cửa hàng</p>
            </div>
        </div>

        <form method="POST" action="{{ route('login.post') }}" class="space-y-4">
            @csrf
            <div class="relative">
                <label class="block text-sm mb-1">Email</label>
                <span class="pointer-events-none absolute left-3 top-[42px] -translate-y-1/2 text-slate-400">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M2 6a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v.35l-10 5-10-5V6Zm0 3.7V18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9.7l-10 5-10-5Z" />
                    </svg>
                </span>
                <input type="email" name="email" value="{{ old('email') }}" required
                    class="w-full rounded-xl border border-slate-300/60 dark:border-slate-700 bg-white/70 dark:bg-slate-900/60 pl-10 pr-3 py-2.5 shadow-inner focus:outline-none focus:ring-2 focus:ring-brand-600/50">
            </div>

            <div class="relative">
                <label class="block text-sm mb-1">Mật khẩu</label>
                <span class="pointer-events-none absolute left-3 top-[42px] -translate-y-1/2 text-slate-400">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 1a5 5 0 0 0-5 5v3H6a2 2 0 0 0-2 2v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8a2 2 0 0 0-2-2h-1V6a5 5 0 0 0-5-5Zm3 8H9V6a3 3 0 1 1 6 0v3Z" />
                    </svg>
                </span>
                <input id="pwd" type="password" name="password" required
                    class="w-full rounded-xl border border-slate-300/60 dark:border-slate-700 bg-white/70 dark:bg-slate-900/60 pl-10 pr-10 py-2.5 shadow-inner focus:outline-none focus:ring-2 focus:ring-brand-600/50">
                <button type="button" id="togglePwd" class="absolute right-3 top-[42px] -translate-y-1/2 text-slate-500 hover:text-slate-700 dark:hover:text-slate-300">
                    <svg id="eye" class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 5C7 5 2.73 8.11 1 12c1.73 3.89 6 7 11 7s9.27-3.11 11-7c-1.73-3.89-6-7-11-7Zm0 12a5 5 0 1 1 0-10 5 5 0 0 1 0 10Z" />
                    </svg>
                </button>
            </div>

            <button class="mt-2 w-full rounded-xl bg-gradient-to-br from-brand-600 to-brand-700 text-white py-2.5 font-medium shadow hover:opacity-95">
                Đăng nhập
            </button>
        </form>

        <p class="mt-4 text-center text-sm text-slate-600 dark:text-slate-300">
            Chưa có tài khoản?
            <a class="font-medium text-brand-700 dark:text-brand-600 hover:underline" href="{{ route('register') }}">Đăng ký ngay</a>
        </p>
    </div>
</div>

<script>
    const btn = document.getElementById('togglePwd');
    const input = document.getElementById('pwd');
    btn?.addEventListener('click', () => {
        input.type = input.type === 'password' ? 'text' : 'password';
    });
</script>
@endsection