@extends('layouts.app')
@section('content')
<div class="min-h-[70vh] grid place-items-center">
    <div class="w-full max-w-2xl rounded-2xl bg-white/80 dark:bg-slate-900/50 backdrop-blur p-6 md:p-8 shadow-xl ring-1 ring-slate-900/5 dark:ring-white/10">
        <div class="mb-6 flex items-center gap-3">
            <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-brand-600 to-brand-700 text-white grid place-items-center shadow">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 12a5 5 0 1 0-5-5 5 5 0 0 0 5 5Zm-8 9a8 8 0 1 1 16 0Z" />
                </svg>
            </div>
            <div>
                <h1 class="text-xl font-semibold">Tạo tài khoản</h1>
                <p class="text-sm text-slate-600 dark:text-slate-300">Mua sắm & quản lý đơn hàng dễ dàng</p>
            </div>
        </div>

        <form method="POST" action="{{ route('register.post') }}" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @csrf
            <div class="md:col-span-2">
                <label class="block text-sm mb-1">Họ tên</label>
                <input type="text" name="full_name" value="{{ old('full_name') }}" required
                    class="w-full rounded-xl border border-slate-300/60 dark:border-slate-700 bg-white/70 dark:bg-slate-900/60 px-3 py-2.5 shadow-inner focus:outline-none focus:ring-2 focus:ring-brand-600/50">
            </div>

            <div>
                <label class="block text-sm mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required
                    class="w-full rounded-xl border border-slate-300/60 dark:border-slate-700 bg-white/70 dark:bg-slate-900/60 px-3 py-2.5 shadow-inner focus:outline-none focus:ring-2 focus:ring-brand-600/50">
            </div>
            <div>
                <label class="block text-sm mb-1">Số điện thoại</label>
                <input type="text" name="phone" value="{{ old('phone') }}"
                    class="w-full rounded-xl border border-slate-300/60 dark:border-slate-700 bg-white/70 dark:bg-slate-900/60 px-3 py-2.5 shadow-inner focus:outline-none focus:ring-2 focus:ring-brand-600/50">
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm mb-1">Địa chỉ</label>
                <input type="text" name="address" value="{{ old('address') }}"
                    class="w-full rounded-xl border border-slate-300/60 dark:border-slate-700 bg-white/70 dark:bg-slate-900/60 px-3 py-2.5 shadow-inner focus:outline-none focus:ring-2 focus:ring-brand-600/50">
            </div>

            <div>
                <label class="block text-sm mb-1">Mật khẩu</label>
                <input type="password" name="password" required
                    class="w-full rounded-xl border border-slate-300/60 dark:border-slate-700 bg-white/70 dark:bg-slate-900/60 px-3 py-2.5 shadow-inner focus:outline-none focus:ring-2 focus:ring-brand-600/50">
            </div>
            <div>
                <label class="block text-sm mb-1">Nhập lại mật khẩu</label>
                <input type="password" name="password_confirmation" required
                    class="w-full rounded-xl border border-slate-300/60 dark:border-slate-700 bg-white/70 dark:bg-slate-900/60 px-3 py-2.5 shadow-inner focus:outline-none focus:ring-2 focus:ring-brand-600/50">
            </div>

            <div class="md:col-span-2">
                <button class="mt-2 w-full rounded-xl bg-gradient-to-br from-brand-600 to-brand-700 text-white py-2.5 font-medium shadow hover:opacity-95">
                    Tạo tài khoản
                </button>
            </div>
        </form>

        <p class="mt-4 text-center text-sm text-slate-600 dark:text-slate-300">
            Đã có tài khoản? <a class="font-m