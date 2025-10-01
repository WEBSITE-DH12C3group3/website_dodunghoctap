@extends('layouts.app')

@section('content')
<div class="bg-white/80 dark:bg-slate-900/50 backdrop-blur p-6 rounded-2xl shadow ring-1 ring-slate-900/5 dark:ring-white/10">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-semibold text-slate-800 dark:text-slate-100">Danh sách đơn hàng</h2>
        <div class="relative">
            <button id="exportButton" 
                    class="inline-block rounded-xl px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 shadow-sm">
                Xuất báo cáo
            </button>
            <div id="exportDropdown" class="hidden absolute right-0 mt-2 w-48 bg-white dark:bg-slate-800 rounded-xl shadow-lg z-10">
                <a href="{{ route('admin.orders.export', array_merge(['format' => 'pdf'], request()->query())) }}" 
                   class="block px-4 py-2 text-sm text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700">
                    Xuất PDF
                </a>
                <a href="{{ route('admin.orders.export', array_merge(['format' => 'excel'], request()->query())) }}" 
                   class="block px-4 py-2 text-sm text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700">
                    Xuất Excel
                </a>
            </div>
        </div>
    </div>

    {{-- Form lọc --}}
    <form action="{{ route('admin.orders') }}" method="GET" class="mb-6 grid grid-cols-1 md:grid-cols-6 gap-2">
        <!-- Tìm kiếm -->
        <input type="text" name="search" placeholder="Tìm ID, tên KH, trạng thái..." 
               class="md:col-span-2 rounded-xl px-4 py-2 text-sm text-slate-700 dark:text-slate-200 
                      bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 
                      focus:outline-none focus:ring-2 focus:ring-brand-500"
               value="{{ request('search') }}">

        <!-- Ngày đặt từ / đến -->
        <div class="flex gap-1">
            <input type="date" name="date_from" value="{{ request('date_from') }}"
                   class="w-1/2 rounded-xl border px-2 py-2 text-sm bg-slate-100 dark:bg-slate-800">
            <input type="date" name="date_to" value="{{ request('date_to') }}"
                   class="w-1/2 rounded-xl border px-2 py-2 text-sm bg-slate-100 dark:bg-slate-800">
        </div>

        <!-- Tổng tiền từ / đến -->
        <div class="flex gap-1">
            <input type="number" name="amount_min" value="{{ request('amount_min') }}" placeholder="Tổng từ"
                   class="w-1/2 rounded-xl border px-2 py-2 text-sm bg-slate-100 dark:bg-slate-800">
            <input type="number" name="amount_max" value="{{ request('amount_max') }}" placeholder="Tổng đến"
                   class="w-1/2 rounded-xl border px-2 py-2 text-sm bg-slate-100 dark:bg-slate-800">
        </div>

        <!-- Lọc theo phương thức thanh toán -->
        <select name="payment_method" class="rounded-xl border px-2 py-2 text-sm bg-slate-100 dark:bg-slate-800">
            <option value="">-- Thanh toán --</option>
            <option value="cod" {{ request('payment_method')=='cod' ? 'selected' : '' }}>Thanh toán khi nhận hàng</option>
            <option value="bank_transfer" {{ request('payment_method')=='bank_transfer' ? 'selected' : '' }}>Chuyển khoản ngân hàng</option>
            <option value="momo" {{ request('payment_method')=='momo' ? 'selected' : '' }}>Momo</option>
        </select>

        <!-- Lọc theo trạng thái -->
        <select name="status" class="rounded-xl border px-2 py-2 text-sm bg-slate-100 dark:bg-slate-800">
            <option value="">-- Trạng thái --</option>
            <option value="pending" {{ request('status')=='pending' ? 'selected' : '' }}>Chờ xử lý</option>
            <option value="confirmed" {{ request('status')=='confirmed' ? 'selected' : '' }}>Đã xác nhận</option>
            <option value="delivered" {{ request('status')=='delivered' ? 'selected' : '' }}>Đã giao</option>
            <option value="cancelled" {{ request('status')=='cancelled' ? 'selected' : '' }}>Đã hủy</option>
        </select>

        <!-- Buttons -->
        <div class="md:col-span-6 flex gap-2">
            <button type="submit"
                class="rounded-xl px-4 py-2 text-sm font-medium text-white bg-brand-600 hover:bg-brand-700 shadow-sm">
                Lọc
            </button>
            @if(request()->except('page'))
                <a href="{{ route('admin.orders') }}" class="text-slate-500 hover:text-red-500 text-sm">Xóa lọc</a>
            @endif
        </div>
    </form>

    {{-- Flash message --}}
    @if (session('ok'))
        <div class="mb-4 rounded-xl border border-green-200/60 dark:border-green-400/30 bg-green-50/80 dark:bg-green-900/30 px-4 py-3 text-green-700 dark:text-green-200 shadow-sm">
            {{ session('ok') }}
        </div>
    @endif
    @if (session('error'))
        <div class="mb-4 rounded-xl border border-red-200/60 dark:border-red-400/30 bg-red-50/80 dark:bg-red-900/30 px-4 py-3 text-red-700 dark:text-red-200 shadow-sm">
            {{ session('error') }}
        </div>
    @endif

    {{-- Table --}}
    @if ($orders->isEmpty())
        <p class="text-slate-600 dark:text-slate-300">
            {{ request()->anyFilled(['search','date_from','date_to','amount_min','amount_max','payment_method','status'])
                ? 'Không tìm thấy đơn hàng nào phù hợp.'
                : 'Chưa có đơn hàng nào.' }}
        </p>
    @else
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-700 dark:text-slate-200">
                <thead class="bg-slate-100 dark:bg-slate-800">
                    <tr>
                        <th class="px-4 py-3 font-semibold">ID</th>
                        <th class="px-4 py-3 font-semibold">Khách hàng</th>
                        <th class="px-4 py-3 font-semibold">Ngày đặt</th>
                        <th class="px-4 py-3 font-semibold">Tổng tiền</th>
                        <th class="px-4 py-3 font-semibold">Thanh toán</th>
                        <th class="px-4 py-3 font-semibold">Trạng thái</th>
                        <th class="px-4 py-3 font-semibold">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $statusMap = [
                            'pending' => 'Chờ xử lý',
                            'confirmed' => 'Đã xác nhận',
                            'cancelled' => 'Đã hủy',
                            'delivered' => 'Đã giao',
                        ];
                        $paymentMethodMap = [
                            'cod' => 'Thanh toán khi nhận hàng',
                            'bank_transfer' => 'Chuyển khoản ngân hàng',
                            'momo' => 'Momo',
                        ];
                    @endphp
                    @foreach ($orders as $order)
                    <tr class="border-b border-slate-200 dark:border-slate-700">
                        <td class="px-4 py-3">{{ $order->order_id }}</td>
                        <td class="px-4 py-3">{{ $order->user ? $order->user->full_name : 'Khách vãng lai' }}</td>
                        <td class="px-4 py-3">{{ $order->order_date }}</td>
                        <td class="px-4 py-3">{{ number_format($order->total_amount, 0, ',', '.') }} VNĐ</td>
                        <td class="px-4 py-3">{{ $paymentMethodMap[$order->payment_method] ?? ucfirst($order->payment_method) }}</td>
                        <td class="px-4 py-3">{{ $statusMap[$order->status] ?? ucfirst($order->status) }}</td>
                        <td class="px-4 py-3 flex gap-2">
                            <a href="{{ route('admin.orders.show', $order->order_id) }}" class="text-blue-600 hover:underline">Xem</a>
                            <a href="{{ route('admin.orders.edit', $order->order_id) }}" class="text-green-600 hover:underline">Cập nhật</a>
                            <form action="{{ route('admin.orders.destroy', $order->order_id) }}" method="POST" onsubmit="return confirm('Bạn có chắc muốn hủy đơn hàng này?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline">Hủy</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if ($orders->hasPages())
            <div class="mt-4">
                {{ $orders->links() }}
            </div>
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