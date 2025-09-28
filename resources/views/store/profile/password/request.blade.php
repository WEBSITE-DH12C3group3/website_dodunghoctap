@extends('layouts.store')

@section('content')
<div class="max-w-[1000px] mx-auto px-4 py-6">
  <header class="mb-6">
    <button
      type="button"
      onclick="history.back()"
      class="inline-flex items-center gap-2 text-sm font-medium text-slate-700 hover:text-slate-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-400 ">
      <svg class="h-6 w-6" viewBox="0 0 20 20" fill="none" aria-hidden="true">
        <path d="M12 16L6 10L12 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
      </svg>
      <span>Quay lại</span>
    </button>
    <h1 class="text-2xl font-semibold text-slate-900 ">Đổi mật khẩu qua OTP</h1>
    <p class="mt-1 text-sm text-slate-600 ">
      Mã sẽ gửi đến email của bạn và có hiệu lực trong <strong>10 phút</strong>.
    </p>
  </header>

  {{-- Alerts --}}
  <div class="space-y-3" aria-live="polite">
    @if(session('ok'))
    <div class="flex items-start gap-3 rounded-xl border border-emerald-200/60 bg-emerald-50 p-3 text-emerald-900">
      <svg class="mt-0.5 h-5 w-5 shrink-0" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 10-1.414 1.414L9 13.414l4.707-4.707z" clip-rule="evenodd" />
      </svg>
      <p>{{ session('ok') }}</p>
    </div>
    @endif

    @if($errors->any())
    <div class="flex items-start gap-3 rounded-xl border border-rose-200/60 bg-rose-50 p-3 text-rose-900 ">
      <svg class="mt-0.5 h-5 w-5 shrink-0" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9 5h2v6H9V5zm0 8h2v2H9v-2z" clip-rule="evenodd" />
      </svg>
      <p>{{ $errors->first() }}</p>
    </div>
    @endif
  </div>

  {{-- Card --}}
  <section class="mt-4 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm ">
    <div class="mb-4">
      <div class="text-sm text-slate-600 ">Email của bạn</div>
      <div class="mt-1 inline-flex items-center gap-2 rounded-lg bg-slate-50 px-3 py-1.5 text-slate-900 ring-1 ring-inset ring-slate-200 ">
        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
          <path d="M3 7l9 6 9-6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
          <rect x="3" y="5" width="18" height="14" rx="2" stroke="currentColor" stroke-width="1.5" />
        </svg>
        <strong class="font-medium">{{ $user->email }}</strong>
      </div>
    </div>

    <p class="text-sm text-slate-600 ">
      Nhấn <em>Gửi mã OTP</em> để nhận mã xác thực. Không chia sẻ mã cho bất kỳ ai.
    </p>

    <form
      id="send-otp-form"
      method="POST"
      action="{{ route('profile.password.otp.send') }}"
      class="mt-5 flex flex-col gap-3 sm:flex-row sm:items-center"
      data-cooldown-until="{{ $canSendAt ?? '' }}">
      @csrf

      <button
        id="send-otp-btn"
        type="submit"
        class="inline-flex items-center justify-center gap-2 rounded-xl bg-indigo-600 px-4 py-2 font-medium text-white shadow-sm transition hover:bg-indigo-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-400 disabled:cursor-not-allowed disabled:opacity-60">
        <svg id="send-otp-spinner" class="hidden h-4 w-4 animate-spin" viewBox="0 0 24 24" aria-hidden="true">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" fill="none"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v3a5 5 0 00-5 5H4z"></path>
        </svg>
        <span id="send-otp-label">Gửi mã OTP</span>
        <span id="send-otp-countdown" class="hidden tabular-nums"></span>
      </button>

      <a
        href="{{ route('profile.password.verify.form') }}"
        class="inline-flex items-center justify-center rounded-xl border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-gray-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-400 ">
        Tôi đã có mã, nhập OTP
      </a>
    </form>
  </section>

  <aside class="mt-6 text-xs text-slate-500 ">
    <ul class="list-disc pl-5 space-y-1">
      <li>Kiểm tra cả hộp thư <strong>Spam/Quảng cáo</strong> nếu chưa thấy email.</li>
      <li>Nếu vừa yêu cầu mã, có thể cần chờ trước khi gửi lại.</li>
    </ul>
  </aside>
</div>

{{-- JS: chống double submit + cooldown --}}
<script>
  (function() {
    const form = document.getElementById('send-otp-form');
    const btn = document.getElementById('send-otp-btn');
    const label = document.getElementById('send-otp-label');
    const spinner = document.getElementById('send-otp-spinner');
    const countdownEl = document.getElementById('send-otp-countdown');

    form?.addEventListener('submit', function() {
      btn.disabled = true;
      spinner.classList.remove('hidden');
      label.textContent = 'Đang gửi...';
    });

    const until = form?.dataset?.cooldownUntil;
    if (until) {
      const end = Number(until) * 1000;
      const tick = () => {
        const now = Date.now();
        const remain = Math.max(0, Math.floor((end - now) / 1000));
        if (remain > 0) {
          btn.disabled = true;
          label.textContent = 'Chờ gửi lại';
          countdownEl.classList.remove('hidden');
          const s = String(remain % 60).padStart(2, '0');
          const m = Math.floor(remain / 60);
          countdownEl.textContent = ` (${m}:${s})`;
          requestAnimationFrame(tick);
        } else {
          btn.disabled = false;
          countdownEl.classList.add('hidden');
          label.textContent = 'Gửi mã OTP';
        }
      };
      tick();
    }
  })();
</script>
@endsection