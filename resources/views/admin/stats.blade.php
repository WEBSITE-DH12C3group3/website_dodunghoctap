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
        <button onclick="alert('Chức năng xuất báo cáo đang được phát triển.')" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Xuất báo cáo doanh thu</button>
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