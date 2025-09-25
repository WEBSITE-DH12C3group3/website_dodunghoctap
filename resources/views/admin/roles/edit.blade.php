@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">
  {{-- Header --}}
  <div class="flex items-start justify-between gap-3 mb-6">
    <div>
      <h1 class="text-2xl font-semibold text-slate-800 dark:text-slate-100">
        Sửa role: <span class="text-brand-600">{{ $role->role_name }}</span>
      </h1>
      <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
        Cập nhật thông tin và quản lý các quyền đang gán cho role này.
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

  {{-- Form role info --}}
  <form method="POST" action="{{ route('admin.roles.update',$role->role_id) }}"
        class="mb-8 rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-5">
    @csrf @method('PUT')

    <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
      <div class="md:col-span-6">
        <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 mb-1">Tên role</label>
        <input name="role_name" required
               value="{{ old('role_name',$role->role_name) }}"
               class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2 text-sm">
      </div>
      <div class="md:col-span-12">
        <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 mb-1">Mô tả</label>
        <textarea name="description" rows="3"
          class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2 text-sm">{{ old('description',$role->description) }}</textarea>
      </div>
    </div>

    <div class="mt-4 flex items-center gap-3">
      <button
        class="inline-flex items-center gap-2 rounded-xl px-4 py-2 text-sm font-medium
               bg-brand-600 text-white hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-brand-400">
        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04a1.004 1.004 0 000-1.42l-2.34-2.34a1.004 1.004 0 00-1.42 0l-1.83 1.83 3.75 3.75 1.84-1.82z"/></svg>
        Cập nhật
      </button>
      <span class="text-xs text-slate-500 dark:text-slate-400">
        ID: {{ $role->role_id }}
      </span>
    </div>
  </form>

  {{-- Permission manager --}}
  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    {{-- Attach (Available) --}}
    <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900">
      <div class="px-5 pt-5">
        <div class="flex items-center justify-between">
          <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-200">Thêm quyền</h3>
          <span class="text-xs text-slate-500 dark:text-slate-400">
            Tổng: {{ $allPerms->count() }}
          </span>
        </div>
        <div class="relative mt-3">
          <input id="perm-search"
                 placeholder="Tìm quyền…"
                 class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 pl-10 px-3 py-2 text-sm">
          <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" viewBox="0 0 24 24" fill="currentColor">
            <path d="M15.5 14h-.79l-.28-.27A6.471 6.471 0 0016 9.5 6.5 6.5 0 109.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 5 1.5-1.5-5-5zM9.5 14C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/>
          </svg>
        </div>
      </div>

      <form method="POST" action="{{ route('admin.roles.permissions.attach',$role->role_id) }}" class="p-5">
        @csrf
        <div class="rounded-xl border border-slate-200 dark:border-slate-800">
          <select id="perm-select"
                  name="permission_ids[]" multiple size="10"
                  class="w-full bg-transparent px-3 py-2 text-sm focus:outline-none"
                  style="min-height: 240px">
            @foreach($allPerms as $p)
              <option value="{{ $p->permission_id }}">{{ $p->permission_name }}</option>
            @endforeach
          </select>
        </div>
        <div class="mt-3 flex items-center justify-between">
          <p class="text-xs text-slate-500 dark:text-slate-400">Giữ Ctrl/Cmd để chọn nhiều.</p>
          <button
            class="inline-flex items-center gap-2 rounded-xl px-4 py-2 text-sm font-medium
                   bg-emerald-600 text-white hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-400">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M11 11V5h2v6h6v2h-6v6h-2v-6H5v-2z"/></svg>
            Gán quyền
          </button>
        </div>
      </form>
    </div>

    {{-- Detach (Assigned) --}}
    <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900">
      <div class="px-5 pt-5">
        <div class="flex items-center justify-between">
          <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-200">Bỏ quyền đã có</h3>
          <span class="text-xs text-slate-500 dark:text-slate-400">
            Đang có: {{ $role->permissions->count() }}
          </span>
        </div>
      </div>

      @if($role->permissions->count())
        <form method="POST" action="{{ route('admin.roles.permissions.detach',$role->role_id) }}" class="p-5">
          @csrf
          <div class="rounded-xl border border-slate-200 dark:border-slate-800">
            <select name="permission_ids[]" multiple size="10"
                    class="w-full bg-transparent px-3 py-2 text-sm focus:outline-none"
                    style="min-height: 240px">
              @foreach($role->permissions as $p)
                <option value="{{ $p->permission_id }}">{{ $p->permission_name }}</option>
              @endforeach
            </select>
          </div>
          <div class="mt-3 flex items-center justify-between">
            <p class="text-xs text-slate-500 dark:text-slate-400">Chọn quyền cần bỏ, có thể chọn nhiều.</p>
            <button
              class="inline-flex items-center gap-2 rounded-xl px-4 py-2 text-sm font-medium
                     border border-rose-300 text-rose-700 hover:bg-rose-50
                     dark:border-rose-500/40 dark:text-rose-300 dark:hover:bg-rose-900/20">
              <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M6 7h12v2H6zm2 3h8l-1 9H9L8 10zm3-7h2l1 1h4v2H4V4h4l1-1z"/></svg>
              Bỏ quyền
            </button>
          </div>
        </form>
      @else
        <div class="px-5 pb-6">
          <div class="rounded-xl border border-dashed border-slate-300 dark:border-slate-700 p-4 text-center text-sm text-slate-500 dark:text-slate-400">
            Role này chưa có quyền nào.
          </div>
        </div>
      @endif
    </div>
  </div>
</div>

{{-- Tiny JS: filter danh sách quyền bên trái (không đổi logic) --}}
@push('scripts')
<script>
  (function(){
    const input = document.getElementById('perm-search');
    const select = document.getElementById('perm-select');
    if(!input || !select) return;
    input.addEventListener('input', function(){
      const q = this.value.toLowerCase();
      Array.from(select.options).forEach(opt => {
        opt.hidden = q && !opt.text.toLowerCase().includes(q);
      });
    });
  })();
</script>
@endpush
@endsection
