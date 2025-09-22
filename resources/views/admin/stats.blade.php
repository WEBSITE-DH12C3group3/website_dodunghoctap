@extends('layouts.app')
@section('content')
<div class="p-6">
    <h1 class="text-2xl font-bold mb-6">Thống kê hệ thống</h1>

    <!-- Form chọn khoảng thời gian -->
    <form method="GET" class="mb-6 bg-white dark:bg-slate-800 p-4 rounded-xl shadow">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium">Từ ngày</label>
                <input type="date" name="start_date" value="{{ $start }}" class="w-full p-2 rounded border dark:bg-slate-700">
            </div>
            <div>
                <label class="block text-sm font-medium">Đến ngày</label>
                <input type="date" name="end_date" value="{{ $end }}" class="w-full p-2 rounded border dark:bg-slate-700">
            </div>
            <div class="flex items-end">
                <button type="submit" class="px-4 py-2 bg-brand-600 text-white rounded hover:bg-brand-700">Xem</button>
            </div>
        </div>
    </form>

    <!-- Tổng quan số liệu -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white dark:bg-slate-800 p-4 rounded-xl shadow">
            <h2 class="text-lg font-semibold">Danh mục</h2>
            <p class="text-2xl">{{ $totalCategories }}</p>
        </div>
        <div class="bg-white dark:bg-slate-800 p-4 rounded-xl shadow">
            <h2 class="text-lg font-semibold">Sản phẩm</h2>
            <p class="text-2xl">{{ $totalProducts }}</p>
        </div>
        <div class="bg-white dark:bg-slate-800 p-4 rounded-xl shadow">
            <h2 class="text-lg font-semibold">Người dùng</h2>
            <p class="text-2xl">{{ $totalUsers }} (Online: {{ $onlineUsers }})</p>
        </div>
        <div class="bg-white dark:bg-slate-800 p-4 rounded-xl shadow">
            <h2 class="text-lg font-semibold">Đơn hàng</h2>
            <p>Hôm nay: {{ $ordersToday }}</p>
            <p>Tuần này: {{ $ordersWeek }}</p>
            <p>Khoảng chọn: {{ $ordersInPeriod }}</p>
        </div>
    </div>

    <!-- Thống kê nhập/bán/lợi nhuận -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white dark:bg-slate-800 p-4 rounded-xl shadow">
            <h2 class="text-lg font-semibold">Hàng nhập kho</h2>
            <p class="text-2xl">{{ $purchases }} sản phẩm</p>
            <p>Chi phí: {{ number_format($cost) }} VNĐ</p>
        </div>
        <div class="bg-white dark:bg-slate-800 p-4 rounded-xl shadow">
            <h2 class="text-lg font-semibold">Hàng bán ra</h2>
            <p class="text-2xl">{{ $sales }} sản phẩm</p>
            <p>Doanh thu: {{ number_format($revenue) }} VNĐ</p>
        </div>
        <div class="bg-white dark:bg-slate-800 p-4 rounded-xl shadow">
            <h2 class="text-lg font-semibold">Lợi nhuận</h2>
            <p class="text-2xl">{{ number_format($profit) }} VNĐ</p>
        </div>
    </div>

    <!-- Top sản phẩm bán chạy -->
    <div class="bg-white dark:bg-slate-800 p-4 rounded-xl shadow mb-6">
        <h2 class="text-lg font-semibold mb-4">Top 3 sản phẩm bán chạy</h2>
        <ul class="space-y-2">
            @foreach ($topProducts as $index => $product)
                <li>{{ $index + 1 }}. {{ $product->product_name }} (Bán: {{ $product->total_sold }})</li>
            @endforeach
        </ul>
    </div>

    <!-- Nút xuất báo cáo -->
    <div class="mb-6">
        <button onclick="document.getElementById('exportModal').classList.remove('hidden')" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Xuất báo cáo doanh thu</button>
    </div>

    <!-- Modal chọn xuất báo cáo -->
    <div id="exportModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center">
        <div class="bg-white dark:bg-slate-800 p-6 rounded-xl shadow-lg w-full max-w-md">
            <h2 class="text-lg font-semibold mb-4">Xuất báo cáo</h2>
            <form method="POST" action="{{ route('admin.stats.export') }}">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium">Định dạng</label>
                    <select name="format" class="w-full p-2 rounded border dark:bg-slate-700">
                        <option value="pdf">PDF</option>
                        <option value="excel">Excel</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium">Khoảng thời gian</label>
                    <select name="period" class="w-full p-2 rounded border dark:bg-slate-700" onchange="toggleCustomDate(this)">
                        <option value="today">Hôm nay</option>
                        <option value="week">Tuần này</option>
                        <option value="month">Tháng này</option>
                        <option value="year">Năm nay</option>
                        <option value="custom">Tùy chỉnh</option>
                    </select>
                </div>
                <div id="customDate" class="hidden mb-4">
                    <label class="block text-sm font-medium">Từ ngày</label>
                    <input type="date" name="custom_start" class="w-full p-2 rounded border dark:bg-slate-700">
                    <label class="block text-sm font-medium mt-2">Đến ngày</label>
                    <input type="date" name="custom_end" class="w-full p-2 rounded border dark:bg-slate-700">
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" onclick="document.getElementById('exportModal').classList.add('hidden')" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">Hủy</button>
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Xuất</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Biểu đồ -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Biểu đồ cột: Doanh thu, chi phí, lợi nhuận -->
        <div class="bg-white dark:bg-slate-800 p-4 rounded-xl shadow">
            <h2 class="text-lg font-semibold mb-4">Tài chính</h2>
            <canvas id="financialChart"></canvas>
        </div>

        <!-- Biểu đồ đường: Doanh thu theo ngày -->
        <div class="bg-white dark:bg-slate-800 p-4 rounded-xl shadow">
            <h2 class="text-lg font-semibold mb-4">Doanh thu theo ngày</h2>
            <canvas id="revenueChart"></canvas>
        </div>

        <!-- Biểu đồ tròn: Phân bổ sản phẩm theo danh mục -->
        <div class="bg-white dark:bg-slate-800 p-4 rounded-xl shadow">
            <h2 class="text-lg font-semibold mb-4">Phân bổ sản phẩm theo danh mục</h2>
            <canvas id="categoryChart"></canvas>
        </div>

        <!-- Biểu đồ cột ngang: Top sản phẩm -->
        <div class="bg-white dark:bg-slate-800 p-4 rounded-xl shadow">
            <h2 class="text-lg font-semibold mb-4">Top sản phẩm bán chạy</h2>
            <canvas id="topProductsChart"></canvas>
        </div>
    </div>
</div>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Toggle custom date inputs
    function toggleCustomDate(select) {
        const customDate = document.getElementById('customDate');
        customDate.classList.toggle('hidden', select.value !== 'custom');
    }

    // Biểu đồ cột: Tài chính
    new Chart(document.getElementById('financialChart'), {
        type: 'bar',
        data: {
            labels: {!! json_encode($financialLabels) !!},
            datasets: [{
                label: 'VNĐ',
                data: {!! json_encode($financialValues) !!},
                backgroundColor: ['#4f46e5', '#ef4444', '#22c55e']
            }]
        },
        options: { scales: { y: { beginAtZero: true } } }
    });

    // Biểu đồ đường: Doanh thu theo ngày
    new Chart(document.getElementById('revenueChart'), {
        type: 'line',
        data: {
            labels: {!! json_encode($revenueLabels) !!},
            datasets: [{
                label: 'Doanh thu (VNĐ)',
                data: {!! json_encode($revenueValues) !!},
                borderColor: '#4f46e5',
                fill: false
            }]
        },
        options: { scales: { y: { beginAtZero: true } } }
    });

    // Biểu đồ tròn: Phân bổ danh mục
    new Chart(document.getElementById('categoryChart'), {
        type: 'pie',
        data: {
            labels: {!! json_encode($categoryLabels) !!},
            datasets: [{
                data: {!! json_encode($categoryValues) !!},
                backgroundColor: ['#4f46e5', '#22c55e', '#ef4444', '#eab308', '#3b82f6']
            }]
        }
    });

    // Biểu đồ cột ngang: Top sản phẩm
    new Chart(document.getElementById('topProductsChart'), {
        type: 'bar',
        data: {
            labels: {!! json_encode($topLabels) !!},
            datasets: [{
                label: 'Số lượng bán',
                data: {!! json_encode($topValues) !!},
                backgroundColor: '#4f46e5'
            }]
        },
        options: { indexAxis: 'y', scales: { x: { beginAtZero: true } } }
    });
</script>
@endsection