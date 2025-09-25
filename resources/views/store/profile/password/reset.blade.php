@extends('layouts.store')

@section('content')
<div class="max-w-xl mx-auto px-4 sm:px-0">
  <header class="mb-6">
    <h1 class="text-2xl font-semibold text-slate-900 dark:text-slate-100">Đặt mật khẩu mới</h1>
    <p class="mt-1 text-sm text-slate-600 dark:text-slate-400">
      Tạo mật khẩu mạnh để bảo vệ tài khoản của bạn.
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

  <form method="POST" action="{{ route('profile.password.reset.submit') }}"
        class="mt-4 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900 space-y-5">
    @csrf

    {{-- Password --}}
    <div>
      <label for="password" class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1">Mật khẩu mới</label>
      <div class="relative">
        <input id="password" type="password" name="password" required
               class="w-full rounded-xl border border-slate-300 px-3 py-2 text-slate-900 placeholder-slate-400 focus:ring-2 focus:ring-indigo-400 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100">
        <button type="button" data-toggle="#password"
                class="absolute inset-y-0 right-2 my-auto inline-flex h-8 w-8 items-center justify-center rounded-md text-slate-500 hover:bg-slate-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-300 dark:hover:bg-slate-700"
                aria-label="Hiện/ẩn mật khẩu">
          <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z" stroke="currentColor" stroke-width="1.5"/><circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="1.5"/></svg>
        </button>
      </div>
      <p id="pw-hint" class="mt-1 text-xs text-slate-500 dark:text-slate-400">
        Tối thiểu 6 ký tự. Dùng chữ hoa, chữ thường, số hoặc ký tự đặc biệt để mạnh hơn.
      </p>
      <div class="mt-2 h-1.5 w-full rounded-full bg-slate-200 dark:bg-slate-800">
        <div id="pw-meter" class="h-1.5 w-1/6 rounded-full bg-rose-500 transition-all"></div>
      </div>
    </div>

    {{-- Confirm --}}
    <div>
      <label for="password_confirmation" class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1">Nhập lại mật khẩu</label>
      <div class="relative">
        <input id="password_confirmation" type="password" name="password_confirmation" required
               class="w-full rounded-xl border border-slate-300 px-3 py-2 text-slate-900 placeholder-slate-400 focus:ring-2 focus:ring-indigo-400 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100">
        <button type="button" data-toggle="#password_confirmation"
                class="absolute inset-y-0 right-2 my-auto inline-flex h-8 w-8 items-center justify-center rounded-md text-slate-500 hover:bg-slate-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-300 dark:hover:bg-slate-700"
                aria-label="Hiện/ẩn mật khẩu">
          <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z" stroke="currentColor" stroke-width="1.5"/><circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="1.5"/></svg>
        </button>
      </div>
      <p id="pw-match" class="mt-1 text-xs text-slate-500 dark:text-slate-400">Phải trùng với mật khẩu mới.</p>
    </div>

    <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
      <button type="submit"
        class="inline-flex items-center justify-center gap-2 rounded-xl bg-indigo-600 px-4 py-2 font-medium text-white shadow-sm transition hover:bg-indigo-500 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-400">
        Cập nhật mật khẩu
      </button>
      <a href="{{ route('profile.index') }}"
         class="inline-flex items-center justify-center rounded-xl border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-400 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800">
        Quay lại hồ sơ
      </a>
    </div>
  </form>
</div>

{{-- JS: hiện/ẩn mật khẩu + meter cơ bản + xác nhận khớp --}}
<script>
  (function () {
    // Toggle password visibility
    document.querySelectorAll('[data-toggle]').forEach(btn => {
      btn.addEventListener('click', () => {
        const input = document.querySelector(btn.getAttribute('data-toggle'));
        if (!input) return;
        input.type = input.type === 'password' ? 'text' : 'password';
      });
    });

    // Strength meter (rất nhẹ nhàng)
    const pw = document.getElementById('password');
    const meter = document.getElementById('pw-meter');
    const hint = document.getElementById('pw-hint');
    const confirm = document.getElementById('password_confirmation');
    const matchText = document.getElementById('pw-match');

    const calcScore = (v) => {
      let s = 0;
      if (v.length >= 6) s++;
      if (/[a-z]/.test(v)) s++;
      if (/[A-Z]/.test(v)) s++;
      if (/\d/.test(v)) s++;
      if (/[^A-Za-z0-9]/.test(v)) s++;
      return Math.min(s, 5);
    };

    const updateMeter = () => {
      const score = calcScore(pw.value);
      const widths = ['1/6','2/6','3/6','4/6','5/6'];
      const colors = ['bg-rose-500','bg-orange-500','bg-yellow-500','bg-lime-500','bg-emerald-500'];
      meter.className = `h-1.5 rounded-full transition-all ${colors[Math.max(score-1,0)]}`;
      meter.style.width = (score ? (score/6*100+16) : 16) + '%';
      hint.textContent = score < 3
        ? 'Mật khẩu còn yếu. Thêm chữ hoa/thường, số hoặc ký tự đặc biệt để mạnh hơn.'
        : 'Mật khẩu khá ổn. Đừng dùng thông tin dễ đoán.';
    };

    const updateMatch = () => {
      if (!confirm.value) { matchText.textContent = 'Phải trùng với mật khẩu mới.'; return; }
      matchText.textContent = (pw.value === confirm.value)
        ? '✔ Khớp mật khẩu.'
        : '✘ Chưa khớp mật khẩu.';
    };

    pw?.addEventListener('input', () => { updateMeter(); updateMatch(); });
    confirm?.addEventListener('input', updateMatch);
    updateMeter();
  })();
</script>
@endsection
