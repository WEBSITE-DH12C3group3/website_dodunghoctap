@extends('layouts.app')

@section('content')
<div class="bg-white/80 dark:bg-slate-900/50 backdrop-blur p-6 rounded-2xl shadow ring-1 ring-slate-900/5 dark:ring-white/10">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-semibold text-slate-800 dark:text-slate-100">Danh sách phiếu nhập kho</h2>
        <div class="flex gap-4">
            <a href="{{ route('admin.purchase_orders.create') }}" 
               class="inline-block rounded-xl px-4 py-2 text-sm font-medium text-white bg-brand-600 hover:bg-brand-700 shadow-sm">
                Tạo phiếu nhập mới
            </a>
            <!-- Export Button with Dropdown -->
            <div class="relative">
                <button id="exportButton" 
                        class="inline-block rounded-xl px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 shadow-sm">
                    Xuất báo cáo
                </button>
                <div id="exportDropdown" class="hidden absolute right-0 mt-2 w-48 bg-white dark:bg-slate-800 rounded-xl shadow-lg z-10">
                    <a href="{{ route('admin.purchase_orders.export', array_merge(['format' => 'pdf'], request()->query())) }}" 
                       class="block px-4 py-2 text-sm text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700">
                        Xuất PDF
                    </a>
                    <a href="{{ route('admin.purchase_orders.export', array_merge(['format' => 'excel'], request()->query())) }}" 
                       class="block px-4 py-2 text-sm text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700">
                        Xuất Excel
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Form lọc --}}
    <form action="{{ route('admin.purchase_orders') }}" method="GET" 
          class="grid grid-cols-1 md:grid-cols-4 gap-3 mb-6 bg-slate-50 dark:bg-slate-800/40 p-4 rounded-xl">
        
        {{-- Tìm kiếm chung --}}
        <div>
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Mã phiếu, người nhập, nhà cung cấp..."
                   class="w-full rounded-xl px-3 py-2 text-sm border border-slate-300 dark:border-slate-700 
                          bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-200">
        </div>

        {{-- Ngày nhập từ --}}
        <div>
            <input type="date" name="date_from" value="{{ request('date_from') }}"
                   class="w-full rounded-xl px-3 py-2 text-sm border border-slate-300 dark:border-slate-700 
                          bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-200">
        </div>

        {{-- Ngày nhập đến --}}
        <div>
            <input type="date" name="date_to" value="{{ request('date_to') }}"
                   class="w-full rounded-xl px-3 py-2 text-sm border border-slate-300 dark:border-slate-700 
                          bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-200">
        </div>

        {{-- Tổng tiền min --}}
        <div>
            <input type="number" name="amount_min" value="{{ request('amount_min') }}" placeholder="Tổng tiền từ..."
                   class="w-full rounded-xl px-3 py-2 text-sm border border-slate-300 dark:border-slate-700 
                          bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-200">
        </div>

        {{-- Tổng tiền max --}}
        <div>
            <input type="number" name="amount_max" value="{{ request('amount_max') }}" placeholder="Tổng tiền đến..."
                   class="w-full rounded-xl px-3 py-2 text-sm border border-slate-300 dark:border-slate-700 
                          bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-200">
        </div>

        {{-- Người nhập --}}
        <div>
            <select name="user_id" class="w-full rounded-xl px-3 py-2 text-sm border border-slate-300 dark:border-slate-700 
                                         bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-200">
                <option value="">-- Người nhập --</option>
                @foreach($users as $u)
                    <option value="{{ $u->user_id }}" {{ request('user_id') == $u->user_id ? 'selected' : '' }}>
                        {{ $u->full_name }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Nhà cung cấp --}}
        <div>
            <select name="supplier_id" class="w-full rounded-xl px-3 py-2 text-sm border border-slate-300 dark:border-slate-700 
                                             bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-200">
                <option value="">-- Nhà cung cấp --</option>
                @foreach($suppliers as $s)
                    <option value="{{ $s->supplier_id }}" {{ request('supplier_id') == $s->supplier_id ? 'selected' : '' }}>
                        {{ $s->supplier_name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="flex items-center gap-2 col-span-full">
            <button type="submit" class="rounded-xl px-4 py-2 text-sm font-medium text-white bg-brand-600 hover:bg-brand-700 shadow-sm">
                Lọc
            </button>
            @if(request()->anyFilled(['search','date_from','date_to','amount_min','amount_max','user_id','supplier_id']))
                <a href="{{ route('admin.purchase_orders') }}" class="text-slate-500 hover:text-red-500">Xóa lọc</a>
            @endif
        </div>
    </form>

    {{-- Flash messages --}}
    @if (session('ok'))
        <div class="mb-4 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-green-700 shadow-sm">
            {{ session('ok') }}
        </div>
    @endif
    @if (session('error'))
        <div class="mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-red-700 shadow-sm">
            {{ session('error') }}
        </div>
    @endif

    {{-- Bảng --}}
    @if ($purchaseOrders->isEmpty())
        <p class="text-slate-600 dark:text-slate-300">Không có phiếu nhập nào.</p>
    @else
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-100 dark:bg-slate-800">
                    <tr>
                        <th class="px-4 py-3 font-semibold">Mã phiếu</th>
                        <th class="px-4 py-3 font-semibold">Tổng tiền</th>
                        <th class="px-4 py-3 font-semibold">Ngày nhập</th>
                        <th class="px-4 py-3 font-semibold">Người nhập</th>
                        <th class="px-4 py-3 font-semibold">Nhà cung cấp</th>
                        <th class="px-4 py-3 font-semibold">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($purchaseOrders as $purchaseOrder)
                    <tr class="border-b border-slate-200 dark:border-slate-700">
                        <td class="px-4 py-3">{{ $purchaseOrder->code }}</td>
                        <td class="px-4 py-3">{{ number_format($purchaseOrder->total_amount, 0, ',', '.') }} VNĐ</td>
                        <td class="px-4 py-3">{{ date('d/m/Y', strtotime($purchaseOrder->order_date)) }}</td>
                        <td class="px-4 py-3">{{ $purchaseOrder->user?->full_name ?? 'Không xác định' }}</td>
                        <td class="px-4 py-3">{{ $purchaseOrder->supplier?->supplier_name ?? 'Không xác định' }}</td>
                        <td class="px-4 py-3 flex gap-2">
                            <a href="{{ route('admin.purchase_orders.show', $purchaseOrder->purchase_order_id) }}" class="text-blue-600 hover:underline">Xem</a>
                            <a href="{{ route('admin.purchase_orders.edit', $purchaseOrder->purchase_order_id) }}" class="text-green-600 hover:underline">Sửa</a>
                            <form action="{{ route('admin.purchase_orders.destroy', $purchaseOrder->purchase_order_id) }}" method="POST" 
                                  onsubmit="return confirm('Bạn có chắc muốn xóa phiếu nhập này?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline">Xóa</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if ($purchaseOrders->hasPages())
            <div class="mt-4">{{ $purchaseOrders->links() }}</div>
        @endif
    @endif
</div>

<script>
    // JavaScript to toggle the export dropdown
    document.getElementById('exportButton').addEventListener('click', function() {
        const dropdown = document.getElementById('exportDropdown');
        dropdown.classList.toggle('hidden');
    });

    // Close dropdown when clicking outside
    document.addEventListener('click', function(event) {
        const button = document.getElementById('exportButton');
        const dropdown = document.getElementById('exportDropdown');
        if (!button.contains(event.target) && !dropdown.contains(event.target)) {
            dropdown.classList.add('hidden');
        }
    });
</script>
@endsection