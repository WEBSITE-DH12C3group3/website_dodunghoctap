@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">
  {{-- Page header --}}
  <div class="flex items-start justify-between gap-3 mb-6">
    <div>
      <h1 class="text-2xl font-semibold text-slate-800 dark:text-slate-100">Nhóm quyền (Roles)</h1>
      <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
        Quản lý nhóm quyền & gán quyền sử dụng hệ thống.
      </p>
    </div>
    <a href="{{ route('admin.roles.create') }}"
       class="inline-flex items-center gap-2 rounded-xl px-4 py-2 text-sm font-medium
              bg-brand-600 text-white hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-brand-400">
      <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M11 11V5h2v6h6v2h-6v6h-2v-6H5v-2z"/></svg>
      Tạo role
    </a>
  </div>

  {{-- Flash messages --}}
  @if(session('ok'))
    <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 p-3 text-emerald-800">
      {{ session('ok') }}
    </div>
  @endif
  @if($errors->any())
    <div class="mb-4 rounded-xl border border-rose-200 bg-rose-50 p-3 text-rose-800">
      {{ $errors->first() }}
    </div>
  @endif

  {{-- Filters --}}
  <form method="GET" class="mb-4">
    <div class="flex items-center gap-2">
      <div class="relative flex-1">
        <input
          type="text" name="search" value="{{ request('search') }}"
          placeholder="Tìm theo tên role…"
          class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800
                 px-4 py-2 pl-10 text-sm text-slate-800 dark:text-slate-100 placeholder-slate-400
                 focus:outline-none focus:ring-2 focus:ring-brand-400"
        />
        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" viewBox="0 0 24 24" fill="currentColor">
          <path d="M15.5 14h-.79l-.28-.27A6.471 6.471 0 0016 9.5 6.5 6.5 0 109.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 5 1.5-1.5-5-5zM9.5 14C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/>
        </svg>
      </div>
      <button
        class="rounded-xl px-4 py-2 text-sm font-medium border border-slate-300 dark:border-slate-700
               hover:bg-slate-50 dark:hover:bg-slate-800">
        Tìm
      </button>
      @if(request()->has('search') && request('search')!=='')
        <a href="{{ route('admin.roles') }}"
           class="rounded-xl px-3 py-2 text-sm text-slate-600 dark:text-slate-300 hover:underline">
          Xoá lọc
        </a>
      @endif
    </div>
  </form>

  {{-- Table card --}}
  <div class="overflow-hidden rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900">
    <div class="overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead class="bg-slate-50 dark:bg-slate-800/60">
          <tr class="text-left text-slate-600 dark:text-slate-300">
            <th class="px-4 py-3 font-medium">#</th>
            <th class="px-4 py-3 font-medium">Tên role</th>
            <th class="px-4 py-3 font-medium">Số người dùng</th>
            <th class="px-4 py-3 font-medium">Quyền</th>
            <th class="px-4 py-3"></th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
          @forelse($roles as $r)
            <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-800/40">
              <td class="px-4 py-3 text-slate-500 dark:text-slate-400">{{ $r->role_id }}</td>
              <td class="px-4 py-3 font-medium text-slate-800 dark:text-slate-100">
                {{ $r->role_name }}
              </td>
              <td class="px-4 py-3">
                <span class="inline-flex items-center rounded-lg bg-slate-100 dark:bg-slate-800 px-2 py-1 text-xs font-medium">
                  {{ $r->users_count }}
                </span>
              </td>
              <td class="px-4 py-3">
                @php
                  $permList = $r->permissions->pluck('permission_name')->all();
                  $permText = implode(', ', $permList);
                  $preview = \Illuminate\Support\Str::limit($permText, 80);
                @endphp
                <span class="text-slate-600 dark:text-slate-300" title="{{ $permText }}">
                  {{ $preview ?: '—' }}
                </span>
              </td>
              <td class="px-4 py-3">
                <div class="flex items-center gap-2">
                  <a href="{{ route('admin.roles.edit',$r->role_id) }}"
                     class="inline-flex items-center gap-1 rounded-lg px-3 py-1.5 text-xs font-medium
                            border border-slate-300 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04a1.004 1.004 0 000-1.42l-2.34-2.34a1.004 1.004 0 00-1.42 0l-1.83 1.83 3.75 3.75 1.84-1.82z"/></svg>
                    Sửa
                  </a>
                  <form method="POST" action="{{ route('admin.roles.destroy',$r->role_id) }}"
                        onsubmit="return confirm('Xóa role này? Hành động không thể hoàn tác.');">
                    @csrf @method('DELETE')
                    <button
                      class="inline-flex items-center gap-1 rounded-lg px-3 py-1.5 text-xs font-medium
                             border border-rose-300 text-rose-700 hover:bg-rose-50
                             dark:border-rose-500/40 dark:text-rose-300 dark:hover:bg-rose-900/20">
                      <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M6 7h12v2H6zm2 3h8l-1 9H9L8 10zm3-7h2l1 1h4v2H4V4h4l1-1z"/></svg>
                      Xóa
                    </button>
                  </form>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="5" class="px-4 py-8 text-center text-slate-500 dark:text-slate-400">
                Chưa có role nào. Hãy tạo role đầu tiên.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    {{-- Footer / Pagination --}}
    <div class="flex items-center justify-between px-4 py-3 bg-slate-50 dark:bg-slate-800/60">
      <p class="text-xs text-slate-500 dark:text-slate-400">
        Hiển thị {{ $roles->firstItem() ?? 0 }}–{{ $roles->lastItem() ?? 0 }} trong tổng {{ $roles->total() }} role
      </p>
      <div class="text-sm">
        {{ $roles->withQueryString()->links() }}
      </div>
    </div>
  </div>
</div>
@endsection
