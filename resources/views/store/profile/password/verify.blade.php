@extends('layouts.store')

@section('content')
<div class="max-w-xl mx-auto px-4 sm:px-0">
  <header class="mb-6">
    <h1 class="text-2xl font-semibold text-slate-900 dark:text-slate-100">Nhập mã OTP</h1>
    <p class="mt-1 text-sm text-slate-600 dark:text-slate-400">
      Mã gồm 6 chữ số. Nếu chưa nhận, hãy kiểm tra Spam/Quảng cáo.
    </p>
  </header>

  {{-- Alerts --}}
  <div class="space-y-3" aria-live="polite">
    @if(session('ok'))
      <div class="flex items-start gap-3 rounded-xl border border-emerald-200/60 bg-emerald-50 p-3 text-emerald-900 dark:border-emerald-900/40 dark:bg-emerald-950/40 dark:text-emerald-100">
        <svg class="mt-0.5 h-5 w-5 shrink-0" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
          <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 10-1.414 1.414L9 13.414l4.707-4.707z" clip-rule="evenodd"/>
        </svg>
        <p>{{ session('ok') }}</p>
      </div>
    @endif

    @if($errors->any())
      <div class="flex items-start gap-3 rounded-xl border border-rose-200/60 bg-rose-50 p-3 text-rose-900 dark:border-rose-900/40 dark:bg-rose-950/40 dark:text-rose-100">
        <svg class="mt-0.5 h-5 w-5 shrink-0" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
          <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9 5h2v6H9V5zm0 8h2v2H9v-2z" clip-rule="evenodd"/>
        </svg>
        <p>{{ $errors->first() }}</p>
      </div>
    @endif
  </div>

  <form method="POST" action="{{ route('profile.password.verify.submit') }}"
        class="mt-4 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900 space-y-5">
    @csrf

    <div>
      <label for="otp" class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1">Mã OTP</label>
      <input id="otp" name="code" inputmode="numeric" pattern="[0-9]*" maxlength="6" required
             class="w-full rounded-xl border border-slate-300 px-3 py-2 font-mono tracking-widest text-lg text-slate-900 placeholder-slate-400 focus:ring-2 focus:ring-indigo-400 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100"
             placeholder="••••••" autofocus>
      <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Mã gồm 6 chữ số, hiệu lực 10 phút.</p>
    </div>

    <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
      <button type="submit"
        class="inline-flex items-center justify-center gap-2 rounded-xl bg-indigo-600 px-4 py-2 font-medium text-white shadow-sm transition hover:bg-indigo-500 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-400">
        Xác thực
      </button>
      <a href="{{ route('profile.password.request') }}"
         class="inline-flex items-center justify-center rounded-xl border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-400 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800">
        Gửi lại mã
      </a>
    </div>
  </form>
</div>
@endsection
