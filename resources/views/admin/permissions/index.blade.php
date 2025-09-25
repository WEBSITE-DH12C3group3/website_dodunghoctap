@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">
  {{-- Header --}}
  <div class="flex items-start justify-between gap-3 mb-6">
    <div>
      <h1 class="text-2xl font-semibold text-slate-800 dark:text-slate-100">Quyền (Permissions)</h1>
      <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
        Tạo, cập nhật và xoá quyền sử dụng hệ thống.
      </p>
    </div>
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

  {{-- Search --}}
  <form method="GET" class="mb-4">
    <div class="flex items-center gap-2">
      <div class="relative flex-1">
        <input
          type="text" name="search" value="{{ request('search') }}"
          placeholder="Tìm theo tên quyền…"
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
      @if(request()->filled('search'))
        <a href="{{ route('admin.permissions') }}"
           class="rounded-xl px-3 py-2 text-sm text-slate-600 dark:text-slate-300 hover:underline">Xoá lọc</a>
      @endif
    </div>
  </form>

  {{-- Create --}}
  <form method="POST" action="{{ route('admin.permissions.store') }}"
        class="mb-6 rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-4">
    @csrf
    <div class="grid grid-cols-1 md:grid-cols-12 gap-3">
      <div class="md:col-span-4">
        <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 mb-1">permission_name</label>
        <input name="permission_name" required
               class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2 text-sm">
      </div>
      <div class="md:col-span-6">
        <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 mb-1">Mô tả (tuỳ chọn)</label>
        <input name="description"
               class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2 text-sm">
      </div>
      <div class="md:col-span-2 flex items-end">
        <button
          class="w-full md:w-auto inline-flex items-center justify-center gap-2 rounded-xl px-4 py-2 text-sm font-medium
                 bg-brand-600 text-white hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-brand-400">
          <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M11 11V5h2v6h6v2h-6v6h-2v-6H5v-2z"/></svg>
          Thêm quyền
        </button>
      </div>
    </div>
  </form>

  {{-- Table --}}
  <div class="overflow-hidden rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900">
    <div class="overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead class="bg-slate-50 dark:bg-slate-800/60">
          <tr class="text-left text-slate-600 dark:text-slate-300">
            <th class="px-4 py-3 font-medium">#</th>
            <th class="px-4 py-3 font-medium">Thông tin quyền</th>
            <th class="px-4 py-3 font-medium">Mô tả</th>
            <th class="px-4 py-3"></th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
          @forelse($permissions as $p)
            <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-800/40">
              <td class="px-4 py-3 text-slate-500 dark:text-slate-400">{{ $p->permission_id }}</td>

              {{-- Inline edit giữ nguyên logic hiện tại --}}
              <td class="px-4 py-3">
                <form method="POST" action="{{ route('admin.permissions.update',$p->permission_id) }}"
                      class="grid grid-cols-1 md:grid-cols-12 gap-2 items-center">
                  @csrf @method('PUT')
                  <input name="permission_name"
                         value="{{ $p->permission_name }}"
                         class="md:col-span-6 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-1.5 text-sm" />
                  <input name="description"
                         value="{{ $p->description }}"
                         class="md:col-span-5 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-1.5 text-sm" />
                  <div class="md:col-span-1">
                    <button
                      class="w-full inline-flex items-center justify-center gap-1 rounded-lg px-3 py-1.5 text-xs font-medium
                             border border-slate-300 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800">
                      <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04a1.004 1.004 0 000-1.42l-2.34-2.34a1.004 1.004 0 00-1.42 0l-1.83 1.83 3.75 3.75 1.84-1.82z"/></svg>
                      Lưu
                    </button>
                  </div>
                </form>
              </td>

              <td class="px-4 py-3 text-slate-700 dark:text-slate-200">
                {{ $p->description ?: '—' }}
              </td>

              <td class="px-4 py-3">
                <form method="POST" action="{{ route('admin.permissions.destroy',$p->permission_id) }}"
                      onsubmit="return confirm('Xóa quyền này? Hành động không thể hoàn tác.');">
                  @csrf @method('DELETE')
                  <button
                    class="inline-flex items-center gap-1 rounded-lg px-3 py-1.5 text-xs font-medium
                           border border-rose-300 text-rose-700 hover:bg-rose-50
                           dark:border-rose-500/40 dark:text-rose-300 dark:hover:bg-rose-900/20">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M6 7h12v2H6zm2 3h8l-1 9H9L8 10zm3-7h2l1 1h4v2H4V4h4l1-1z"/></svg>
                    Xóa
                  </button>
                </form>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="4" class="px-4 py-8 text-center text-slate-500 dark:text-slate-400">
                Chưa có quyền nào. Hãy thêm quyền ở biểu mẫu phía trên.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    {{-- Footer / Pagination --}}
    <div class="flex items-center justify-between px-4 py-3 bg-slate-50 dark:bg-slate-800/60">
      <p class="text-xs text-slate-500 dark:text-slate-400">
        Hiển thị {{ $permissions->firstItem() ?? 0 }}–{{ $permissions->lastItem() ?? 0 }} / {{ $permissions->total() }} quyền
      </p>
      <div class="text-sm">
        {{ $permissions->withQueryString()->links() }}
      </div>
    </div>
  </div>
</div>
@endsection
