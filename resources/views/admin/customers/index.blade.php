@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">
  {{-- Header --}}
  <div class="flex items-start justify-between gap-3 mb-6">
    <div>
      <h1 class="text-2xl font-semibold text-slate-800 dark:text-slate-100">Khách hàng</h1>
      <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
        Danh sách khách hàng (role = customer), kèm số đơn và tổng chi tiêu.
      </p>
    </div>
  </div>

  {{-- Flash (nếu có) --}}
  @if(session('ok'))
    <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 p-3 text-emerald-800">
      {{ session('ok') }}
    </div>
  @endif

 {{-- Tìm kiếm + lọc --}}
<form method="GET" class="mb-4 grid grid-cols-1 md:grid-cols-5 gap-2">
  <!-- Ô tìm kiếm -->
  <input
    type="text" name="search" value="{{ request('search') }}"
    placeholder="Tên, email, SĐT…"
    class="md:col-span-2 w-full rounded-xl border border-slate-300 dark:border-slate-700
           bg-white dark:bg-slate-800 px-4 py-2 text-sm text-slate-800 dark:text-slate-100
           placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-400" />

  <!-- Số đơn hàng từ / đến -->
  <div class="flex gap-1">
    <input type="number" name="orders_min" value="{{ request('orders_min') }}" placeholder="Đơn từ"
      class="w-1/2 rounded-xl border px-2 py-2 text-sm bg-slate-100 dark:bg-slate-800">
    <input type="number" name="orders_max" value="{{ request('orders_max') }}" placeholder="Đơn đến"
      class="w-1/2 rounded-xl border px-2 py-2 text-sm bg-slate-100 dark:bg-slate-800">
  </div>

  <!-- Tổng chi từ / đến -->
  <div class="flex gap-1">
    <input type="number" name="spend_min" value="{{ request('spend_min') }}" placeholder="Chi từ"
      class="w-1/2 rounded-xl border px-2 py-2 text-sm bg-slate-100 dark:bg-slate-800">
    <input type="number" name="spend_max" value="{{ request('spend_max') }}" placeholder="Chi đến"
      class="w-1/2 rounded-xl border px-2 py-2 text-sm bg-slate-100 dark:bg-slate-800">
  </div>

  <!-- Mốc nhanh cho tổng chi -->
  <select name="spend_quick" onchange="if(this.value) { this.form.spend_min=this.value; this.form.submit(); }"
    class="rounded-xl border px-2 py-2 text-sm bg-slate-100 dark:bg-slate-800">
    <option value="">-- Mốc tổng chi --</option>
    <option value="1000000" {{ request('spend_min')==1000000 ? 'selected':'' }}>Trên 1 triệu</option>
    <option value="5000000" {{ request('spend_min')==5000000 ? 'selected':'' }}>Trên 5 triệu</option>
    <option value="10000000" {{ request('spend_min')==10000000 ? 'selected':'' }}>Trên 10 triệu</option>
  </select>

  <!-- Buttons -->
  <div class="flex gap-2">
    <button class="rounded-xl px-4 py-2 text-sm font-medium border border-slate-300 dark:border-slate-700
                   hover:bg-slate-50 dark:hover:bg-slate-800">
      Lọc
    </button>
    @if(request()->except('page'))
      <a href="{{ route('admin.customers') }}"
         class="rounded-xl px-3 py-2 text-sm text-slate-600 dark:text-slate-300 hover:underline">
        Xoá lọc
      </a>
    @endif
  </div>
</form>


  {{-- Bảng danh sách --}}
  <div class="overflow-hidden rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900">
    <div class="overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead class="bg-slate-50 dark:bg-slate-800/60">
          <tr class="text-left text-slate-600 dark:text-slate-300">
            <th class="px-4 py-3 font-medium">#</th>
            <th class="px-4 py-3 font-medium">Họ tên</th>
            <th class="px-4 py-3 font-medium">Email</th>
            <th class="px-4 py-3 font-medium">SĐT</th>
            <th class="px-4 py-3 font-medium">Đơn hàng</th>
            <th class="px-4 py-3 font-medium">Tổng chi</th>
            <th class="px-4 py-3"></th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
          @forelse($customers as $c)
            <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-800/40">
              <td class="px-4 py-3 text-slate-500 dark:text-slate-400">{{ $c->user_id }}</td>
              <td class="px-4 py-3 font-medium text-slate-800 dark:text-slate-100">
                {{ $c->full_name }}
              </td>
              <td class="px-4 py-3">
                <span class="text-slate-700 dark:text-slate-200">{{ $c->email }}</span>
              </td>
              <td class="px-4 py-3 text-slate-600 dark:text-slate-300">
                {{ $c->phone ?: '—' }}
              </td>
              <td class="px-4 py-3">
                <span class="inline-flex items-center rounded-lg bg-slate-100 dark:bg-slate-800 px-2 py-1 text-xs font-medium">
                  {{ $c->orders_count }}
                </span>
              </td>
              <td class="px-4 py-3 font-semibold text-slate-800 dark:text-slate-100">
                {{ number_format($c->orders_sum, 0, ',', '.') }}đ
              </td>
              <td class="px-4 py-3">
                <a href="{{ route('admin.customers.show',$c->user_id) }}"
                   class="inline-flex items-center gap-1 rounded-lg px-3 py-1.5 text-xs font-medium
                          border border-slate-300 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800">
                  <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M12 5c-7 0-10 7-10 7s3 7 10 7 10-7 10-7-3-7-10-7zm0 12a5 5 0 110-10 5 5 0 010 10z"/></svg>
                  Xem
                </a>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="7" class="px-4 py-8 text-center text-slate-500 dark:text-slate-400">
                Chưa có khách hàng nào.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    {{-- Footer / Pagination --}}
    <div class="flex items-center justify-between px-4 py-3 bg-slate-50 dark:bg-slate-800/60">
      <p class="text-xs text-slate-500 dark:text-slate-400">
        Hiển thị {{ $customers->firstItem() ?? 0 }}–{{ $customers->lastItem() ?? 0 }} / {{ $customers->total() }} khách hàng
      </p>
      <div class="text-sm">
        {{ $customers->withQueryString()->links() }}
      </div>
    </div>
  </div>
</div>
@endsection
