@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">
  {{-- Header --}}
  <div class="flex items-start justify-between gap-3 mb-6">
    <div>
      <h1 class="text-2xl font-semibold text-slate-800 dark:text-slate-100">
        Khách hàng #{{ $customer->user_id }}
      </h1>
      <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
        Thông tin chi tiết và 20 đơn hàng gần nhất.
      </p>
    </div>
  </div>

  {{-- Customer card --}}
  <div class="mb-6 rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-5">
    <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
      <div class="md:col-span-6">
        <div class="text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400 mb-1">Họ tên</div>
        <div class="text-slate-800 dark:text-slate-100 font-medium">{{ $customer->full_name ?: '—' }}</div>
      </div>
      <div class="md:col-span-3">
        <div class="text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400 mb-1">Email</div>
        <div class="text-slate-800 dark:text-slate-100">
          @if($customer->email)
            <a href="mailto:{{ $customer->email }}" class="hover:underline">{{ $customer->email }}</a>
          @else
            —
          @endif
        </div>
      </div>
      <div class="md:col-span-3">
        <div class="text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400 mb-1">SĐT</div>
        <div class="text-slate-800 dark:text-slate-100">{{ $customer->phone ?: '—' }}</div>
      </div>

      <div class="md:col-span-9">
        <div class="text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400 mb-1">Địa chỉ</div>
        <div class="text-slate-800 dark:text-slate-100">{{ $customer->address ?: '—' }}</div>
      </div>
      <div class="md:col-span-3">
        <div class="text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400 mb-1">Trạng thái</div>
        <div>
          @if($customer->is_active)
            <span class="inline-flex items-center rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200 px-2 py-1 text-xs font-medium">
              ● Active
            </span>
          @else
            <span class="inline-flex items-center rounded-lg bg-slate-100 text-slate-700 border border-slate-300 px-2 py-1 text-xs font-medium">
              ● Inactive
            </span>
          @endif
        </div>
      </div>
    </div>
  </div>

  {{-- Orders table --}}
  <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 overflow-hidden">
    <div class="px-5 pt-5 pb-3 flex items-center justify-between">
      <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-200">20 đơn gần nhất</h3>
      <span class="text-xs text-slate-500 dark:text-slate-400">Tối đa 20 bản ghi</span>
    </div>

    <div class="overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead class="bg-slate-50 dark:bg-slate-800/60">
          <tr class="text-left text-slate-600 dark:text-slate-300">
            <th class="px-4 py-3 font-medium">Mã</th>
            <th class="px-4 py-3 font-medium">Ngày</th>
            <th class="px-4 py-3 font-medium">Trạng thái</th>
            <th class="px-4 py-3 font-medium text-right">Tổng tiền</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
          @forelse($orders as $o)
            <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-800/40">
              <td class="px-4 py-3 text-slate-700 dark:text-slate-200">{{ $o->order_id }}</td>
              <td class="px-4 py-3 text-slate-600 dark:text-slate-300">
                {{ \Illuminate\Support\Str::of($o->order_date)->replace('T',' ') }}
              </td>
              <td class="px-4 py-3">
                @php
                  $status = strtolower((string)$o->status);
                  $map = [
                    'pending'   => ['bg' => 'bg-amber-50',   'bd'=>'border-amber-200', 'tx'=>'text-amber-700', 'label'=>'Pending'],
                    'paid'      => ['bg' => 'bg-emerald-50', 'bd'=>'border-emerald-200','tx'=>'text-emerald-700','label'=>'Paid'],
                    'shipped'   => ['bg' => 'bg-sky-50',     'bd'=>'border-sky-200',   'tx'=>'text-sky-700',   'label'=>'Shipped'],
                    'completed' => ['bg' => 'bg-emerald-50', 'bd'=>'border-emerald-200','tx'=>'text-emerald-700','label'=>'Completed'],
                    'cancelled' => ['bg' => 'bg-rose-50',    'bd'=>'border-rose-200',  'tx'=>'text-rose-700',  'label'=>'Cancelled'],
                  ];
                  $c = $map[$status] ?? ['bg'=>'bg-slate-100','bd'=>'border-slate-300','tx'=>'text-slate-700','label'=>ucfirst($status ?: 'Unknown')];
                @endphp
                <span class="inline-flex items-center rounded-lg px-2 py-1 text-xs font-medium border {{ $c['bg'] }} {{ $c['bd'] }} {{ $c['tx'] }}">
                  {{ $c['label'] }}
                </span>
              </td>
              <td class="px-4 py-3 text-right font-semibold text-slate-800 dark:text-slate-100">
                {{ number_format($o->total_amount ?? 0, 0, ',', '.') }}đ
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="4" class="px-4 py-8 text-center text-slate-500 dark:text-slate-400">
                Khách hàng này chưa có đơn hàng nào.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
