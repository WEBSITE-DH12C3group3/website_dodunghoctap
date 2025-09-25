@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto">
  {{-- Header --}}
  <div class="mb-6">
    <h1 class="text-2xl font-semibold text-slate-800 dark:text-slate-100">Tạo role</h1>
    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Thiết lập nhóm quyền mới cho hệ thống.</p>
  </div>

  {{-- Flash / Errors --}}
  @if(session('ok'))
    <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 p-3 text-emerald-800">
      {{ session('ok') }}
    </div>
  @endif
  @if ($errors->any())
    <div class="mb-4 rounded-xl border border-rose-200 bg-rose-50 p-3 text-rose-800">
      {{ $errors->first() }}
    </div>
  @endif

  <form method="POST" action="{{ route('admin.roles.store') }}"
        class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-5 space-y-4">
    @csrf

    {{-- Role name --}}
    <div>
      <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 mb-1">
        Tên role <span class="text-rose-500">*</span>
      </label>
      <input
        name="role_name"
        value="{{ old('role_name') }}"
        required
        placeholder="Ví dụ: admin, employee, customer…"
        class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800
               px-3 py-2 text-sm text-slate-800 dark:text-slate-100 placeholder-slate-400
               focus:outline-none focus:ring-2 focus:ring-brand-400">
      <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
        Tên ngắn, không dấu, dùng để gán cho người dùng.
      </p>
      @error('role_name')
        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
      @enderror
    </div>

    {{-- Description --}}
    <div>
      <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 mb-1">Mô tả</label>
      <textarea
        name="description"
        rows="3"
        placeholder="Ghi chú về phạm vi sử dụng, trách nhiệm… (tuỳ chọn)"
        class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800
               px-3 py-2 text-sm text-slate-800 dark:text-slate-100 placeholder-slate-400
               focus:outline-none focus:ring-2 focus:ring-brand-400">{{ old('description') }}</textarea>
      @error('description')
        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
      @enderror
    </div>

    {{-- Actions --}}
    <div class="pt-2 flex items-center gap-3">
      <button
        class="inline-flex items-center gap-2 rounded-xl px-4 py-2 text-sm font-medium
               bg-brand-600 text-white hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-brand-400">
        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M11 11V5h2v6h6v2h-6v6h-2v-6H5v-2z"/></svg>
        Lưu
      </button>
      <a href="{{ route('admin.roles') }}"
         class="text-sm text-slate-600 dark:text-slate-300 hover:underline">
        Huỷ
      </a>
    </div>
  </form>
</div>
@endsection
