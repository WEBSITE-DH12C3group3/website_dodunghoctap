@extends('layouts.app')

@section('content')
<div class="p-4 sm:p-6 lg:p-8 max-w-7xl mx-auto bg-gradient-to-br from-[#1E4DE8]/10 to-[#143BC5]/10 min-h-screen">
    <!-- Tiêu đề -->
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-6 flex items-center">
        <i class="fas fa-chart-line mr-2 text-blue-600 dark:text-blue-400"></i> Thống kê hệ thống
    </h1>


    <!-- Tổng quan số liệu -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white dark:bg-slate-800 p-4 rounded-xl shadow-lg border border-gray-200 dark:border-slate-700">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Danh mục</h2>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $totalCategories ?? 0 }}</p>
        </div>
        <div class="bg-white dark:bg-slate-800 p-4 rounded-xl shadow-lg border border-gray-200 dark:border-slate-700">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Sản phẩm</h2>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $totalProducts ?? 0 }}</p>
        </div>
        <div class="bg-white dark:bg-slate-800 p-4 rounded-xl shadow-lg border border-gray-200 dark:border-slate-700">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Người dùng</h2>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $totalUsers ?? 0 }} <span class="text-sm font-normal text-gray-500 dark:text-gray-400">(Online: {{ $onlineUsers ?? 0 }})</span></p>
        </div>
        <div class="bg-white dark:bg-slate-800 p-4 rounded-xl shadow-lg border border-gray-200 dark:border-slate-700">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Đơn hàng</h2>
            <p class="text-sm text-gray-600 dark:text-gray-400">Hôm nay: <span class="font-bold text-gray-900 dark:text-white">{{ $ordersToday ?? 0 }}</span></p>
            <p class="text-sm text-gray-600 dark:text-gray-400">Tuần này: <span class="font-bold text-gray-900 dark:text-white">{{ $ordersWeek ?? 0 }}</span></p>
            <p class="text-sm text-gray-600 dark:text-gray-400">Khoảng chọn: <span class="font-bold text-blue-600 dark:text-blue-400">{{ $ordersInPeriod ?? 0 }}</span></p>
        </div>
    </div>

    <!-- Top sản phẩm bán chạy -->
    <div class="bg-white dark:bg-slate-800 p-4 rounded-xl shadow-lg border border-gray-200 dark:border-slate-700 mb-6">
        <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">Top 3 sản phẩm bán chạy</h2>
        <ul class="space-y-2">
            @forelse ($topProducts ?? [] as $index => $product)
                <li class="flex items-center p-3 bg-gray-50 dark:bg-slate-700 rounded-lg">
                    <span class="text-lg font-bold text-blue-600 dark:text-blue-400 mr-4 w-6 text-center">{{ $index + 1 }}.</span>
                    <span class="flex-grow text-gray-700 dark:text-gray-200">{{ $product->product_name ?? 'N/A' }}</span>
                    <span class="text-sm font-semibold text-green-600 dark:text-green-400">Đã bán: {{ $product->total_sold ?? 0 }}</span>
                </li>
            @empty
                <li class="text-sm text-gray-500 dark:text-gray-400">Không có dữ liệu sản phẩm bán chạy.</li>
            @endforelse
        </ul>
    </div>

    <!-- Nút xuất báo cáo -->
    <div class="mb-6 text-center">
        <button onclick="document.getElementById('exportModal').classList.remove('hidden')"
                class="px-6 py-2.5 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 shadow-md transition-all">
            <i class="fas fa-file-export mr-2"></i> Xuất báo cáo doanh thu
        </button>
    </div>

    <!-- Modal chọn xuất báo cáo -->
    <div id="exportModal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4">
        <div class="bg-white dark:bg-slate-800 p-6 rounded-xl shadow-2xl w-full max-w-md border border-gray-200 dark:border-slate-700">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4 border-b pb-2">Xuất báo cáo</h2>
            <form method="POST" action="{{ route('admin.stats.export') }}">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Định dạng</label>
                    <select name="format" class="w-full p-2.5 rounded-lg border border-gray-300 dark:bg-slate-700 dark:border-slate-600 focus:ring-2 focus:ring-blue-500">
                        <option value="pdf">PDF</option>
                        <option value="excel">Excel</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Khoảng thời gian</label>
                    <select name="period" class="w-full p-2.5 rounded-lg border border-gray-300 dark:bg-slate-700 dark:border-slate-600 focus:ring-2 focus:ring-blue-500" onchange="toggleCustomDate(this)">
                        <option value="today">Hôm nay</option>
                        <option value="week">Tuần này</option>
                        <option value="month">Tháng này</option>
                        <option value="year">Năm nay</option>
                        <option value="custom">Tùy chỉnh</option>
                    </select>
                </div>
                <div id="customDate" class="hidden mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Từ ngày</label>
                    <input type="date" name="custom_start" class="w-full p-2.5 rounded-lg border border-gray-300 dark:bg-slate-700 dark:border-slate-600">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mt-2 mb-1">Đến ngày</label>
                    <input type="date" name="custom_end" class="w-full p-2.5 rounded-lg border border-gray-300 dark:bg-slate-700 dark:border-slate-600">
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" onclick="document.getElementById('exportModal').classList.add('hidden')"
                            class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-all">Hủy</button>
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-all">Xuất</button>
                </div>
            </form>
        </div>
    </div>

     <!-- Form chọn khoảng thời gian -->
<form id="statsFilterForm" method="GET" class="mb-6 bg-white dark:bg-slate-800 p-4 rounded-xl shadow">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <label class="block text-sm font-medium">Từ ngày</label>
            <input id="start_date" type="date" name="start_date" value="{{ $start }}" class="w-full p-2 rounded border dark:bg-slate-700">
        </div>
        <div>
            <label class="block text-sm font-medium">Đến ngày</label>
            <input id="end_date" type="date" name="end_date" value="{{ $end }}" class="w-full p-2 rounded border dark:bg-slate-700">
        </div>
        <div class="flex items-end">
            <button type="submit" id="filterSubmit" class="px-4 py-2 bg-brand-600 text-white rounded hover:bg-brand-700">Xem</button>
        </div>
    </div>

    <!-- lỗi ngày (hiển thị dưới form) -->
    <div id="dateError" class="mt-3 text-sm text-red-500" role="alert" aria-live="polite"></div>
</form>

    <!-- Thống kê nhập/bán/lợi nhuận -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white dark:bg-slate-800 p-4 rounded-xl shadow-lg border border-gray-200 dark:border-slate-700">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-2 flex items-center">
                <i class="fas fa-arrow-down mr-2 text-red-500"></i> Hàng nhập kho
            </h2>
            <p class="text-2xl font-bold text-red-600 dark:text-red-400">{{ $purchases ?? 0 }} sản phẩm</p>
            <p class="text-sm text-gray-600 dark:text-gray-400">Chi phí: <span class="font-bold">{{ number_format($cost ?? 0) }} VNĐ</span></p>
        </div>
        <div class="bg-white dark:bg-slate-800 p-4 rounded-xl shadow-lg border border-gray-200 dark:border-slate-700">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-2 flex items-center">
                <i class="fas fa-arrow-up mr-2 text-blue-500"></i> Hàng bán ra
            </h2>
            <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $sales ?? 0 }} sản phẩm</p>
            <p class="text-sm text-gray-600 dark:text-gray-400">Doanh thu: <span class="font-bold">{{ number_format($revenue ?? 0) }} VNĐ</span></p>
        </div>
        <div class="bg-white dark:bg-slate-800 p-4 rounded-xl shadow-lg border border-gray-200 dark:border-slate-700">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-2 flex items-center">
                <i class="fas fa-hand-holding-usd mr-2 text-green-500"></i> Lợi nhuận
            </h2>
            <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ number_format($profit ?? 0) }} VNĐ</p>
        </div>
    </div>

    <!-- Biểu đồ -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white dark:bg-slate-800 p-4 rounded-xl shadow-lg border border-gray-200 dark:border-slate-700">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4 flex items-center">
                <i class="fas fa-chart-bar mr-2 text-blue-500"></i> Tài chính
            </h2>
            <canvas id="financialChart" class="w-full h-80"></canvas>
            <p id="financialChartError" class="hidden mt-2 text-sm text-red-600 dark:text-red-400">Không có dữ liệu để hiển thị biểu đồ.</p>
        </div>
        <div class="bg-white dark:bg-slate-800 p-4 rounded-xl shadow-lg border border-gray-200 dark:border-slate-700">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4 flex items-center">
                <i class="fas fa-chart-line mr-2 text-blue-500"></i> Doanh thu theo ngày
            </h2>
            <canvas id="revenueChart" class="w-full h-80"></canvas>
            <p id="revenueChartError" class="hidden mt-2 text-sm text-red-600 dark:text-red-400">Không có dữ liệu để hiển thị biểu đồ.</p>
        </div>
        <div class="bg-white dark:bg-slate-800 p-4 rounded-xl shadow-lg border border-gray-200 dark:border-slate-700">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4 flex items-center">
                <i class="fas fa-chart-pie mr-2 text-blue-500"></i> Phân bổ sản phẩm theo danh mục
            </h2>
            <canvas id="categoryChart" class="w-full h-80"></canvas>
            <p id="categoryChartError" class="hidden mt-2 text-sm text-red-600 dark:text-red-400">Không có dữ liệu để hiển thị biểu đồ.</p>
        </div>
        <div class="bg-white dark:bg-slate-800 p-4 rounded-xl shadow-lg border border-gray-200 dark:border-slate-700">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4 flex items-center">
                <i class="fas fa-sort-amount-up-alt mr-2 text-blue-500"></i> Top sản phẩm bán chạy
            </h2>
            <canvas id="topProductsChart" class="w-full h-80"></canvas>
            <p id="topProductsChartError" class="hidden mt-2 text-sm text-red-600 dark:text-red-400">Không có dữ liệu để hiển thị biểu đồ.</p>
        </div>
    </div>





    <script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('statsFilterForm');
    const startInput = document.getElementById('start_date');
    const endInput = document.getElementById('end_date');
    const errorBox = document.getElementById('dateError');

    // Khởi tạo min/max nếu đã có giá trị từ server
    function initBounds() {
        if (startInput.value) {
            endInput.min = startInput.value;
        } else {
            endInput.removeAttribute('min');
        }

        if (endInput.value) {
            startInput.max = endInput.value;
        } else {
            startInput.removeAttribute('max');
        }
    }

    // Kiểm tra hợp lệ: start <= end
    function validateDates() {
        errorBox.textContent = '';
        if (startInput.value && endInput.value) {
            // so sánh dưới dạng chuỗi YYYY-MM-DD thì so sánh ký tự hoạt động đúng
            if (startInput.value > endInput.value) {
                errorBox.textContent = 'Ngày bắt đầu không được lớn hơn ngày kết thúc.';
                return false;
            }
        }
        return true;
    }

    // Khi người dùng thay đổi input
    startInput.addEventListener('input', function () {
        if (startInput.value) {
            endInput.min = startInput.value;
        } else {
            endInput.removeAttribute('min');
        }
        validateDates();
    });

    endInput.addEventListener('input', function () {
        if (endInput.value) {
            startInput.max = endInput.value;
        } else {
            startInput.removeAttribute('max');
        }
        validateDates();
    });

    // Trước khi submit form
    form.addEventListener('submit', function (e) {
        if (!validateDates()) {
            e.preventDefault();
            // fokus vào field gây lỗi
            if (startInput.value > endInput.value) {
                startInput.focus();
            }
            return false;
        }
    });

    // Khởi tạo lần đầu khi trang load
    initBounds();
});
</script>
    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Hàm làm sạch dữ liệu
        function cleanData(values) {
            return values.map(value => {
                if (value === null || isNaN(value) || !isFinite(value)) return 0;
                return Number(value);
            });
        }

        // Hàm tính giá trị tối đa cho trục y
        function getSuggestedMax(values) {
            const validValues = cleanData(values).filter(v => v > 0);
            if (validValues.length === 0) return 1000; // Giá trị mặc định
            const max = Math.max(...validValues);
            return max * 1.2; // Thêm 20% đệm
        }

        // Hàm kiểm tra dữ liệu
        function validateChartData(labels, values, chartId) {
            if (!Array.isArray(labels) || !Array.isArray(values) || labels.length === 0 || values.length === 0) {
                console.warn(`Dữ liệu không hợp lệ cho biểu đồ ${chartId}:`, { labels, values });
                document.getElementById(`${chartId}Error`).classList.remove('hidden');
                return false;
            }
            return true;
        }

        // Debug dữ liệu
        console.log('Financial Data:', {
            labels: {!! json_encode($financialLabels ?? []) !!},
            values: {!! json_encode($financialValues ?? []) !!}
        });
        console.log('Revenue Data:', {
            labels: {!! json_encode($revenueLabels ?? []) !!},
            values: {!! json_encode($revenueValues ?? []) !!}
        });
        console.log('Category Data:', {
            labels: {!! json_encode($categoryLabels ?? []) !!},
            values: {!! json_encode($categoryValues ?? []) !!}
        });
        console.log('Top Products Data:', {
            labels: {!! json_encode($topLabels ?? []) !!},
            values: {!! json_encode($topValues ?? []) !!}
        });

        // Dữ liệu từ Laravel
        const laravelData = {
            financialLabels: {!! json_encode($financialLabels ?? []) !!},
            financialValues: cleanData({!! json_encode($financialValues ?? []) !!}),
            revenueLabels: {!! json_encode($revenueLabels ?? []) !!},
            revenueValues: cleanData({!! json_encode($revenueValues ?? []) !!}),
            categoryLabels: {!! json_encode($categoryLabels ?? []) !!},
            categoryValues: cleanData({!! json_encode($categoryValues ?? []) !!}),
            topLabels: {!! json_encode($topLabels ?? []) !!},
            topValues: cleanData({!! json_encode($topValues ?? []) !!})
        };

        // Toggle custom date inputs
        function toggleCustomDate(select) {
            document.getElementById('customDate').classList.toggle('hidden', select.value !== 'custom');
        }

        // Biểu đồ cột: Tài chính
        if (validateChartData(laravelData.financialLabels, laravelData.financialValues, 'financialChart')) {
            new Chart(document.getElementById('financialChart'), {
                type: 'bar',
                data: {
                    labels: laravelData.financialLabels,
                    datasets: [{
                        label: 'VNĐ',
                        data: laravelData.financialValues,
                        backgroundColor: ['#4f46e5', '#ef4444', '#22c55e']
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true,
                            suggestedMax: getSuggestedMax(laravelData.financialValues)
                        }
                    }
                }
            });
        }

        // Biểu đồ đường: Doanh thu theo ngày
        if (validateChartData(laravelData.revenueLabels, laravelData.revenueValues, 'revenueChart')) {
            new Chart(document.getElementById('revenueChart'), {
                type: 'line',
                data: {
                    labels: laravelData.revenueLabels,
                    datasets: [{
                        label: 'Doanh thu (VNĐ)',
                        data: laravelData.revenueValues,
                        borderColor: '#4f46e5',
                        fill: false
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true,
                            suggestedMax: getSuggestedMax(laravelData.revenueValues)
                        }
                    }
                }
            });
        }

        // Biểu đồ tròn: Phân bổ danh mục
        if (validateChartData(laravelData.categoryLabels, laravelData.categoryValues, 'categoryChart')) {
            new Chart(document.getElementById('categoryChart'), {
                type: 'pie',
                data: {
                    labels: laravelData.categoryLabels,
                    datasets: [{
                        data: laravelData.categoryValues,
                        backgroundColor: ['#4f46e5', '#22c55e', '#ef4444', '#eab308', '#3b82f6']
                    }]
                }
            });
        }

        // Biểu đồ cột ngang: Top sản phẩm
        if (validateChartData(laravelData.topLabels, laravelData.topValues, 'topProductsChart')) {
            new Chart(document.getElementById('topProductsChart'), {
                type: 'bar',
                data: {
                    labels: laravelData.topLabels,
                    datasets: [{
                        label: 'Số lượng bán',
                        data: laravelData.topValues,
                        backgroundColor: '#4f46e5'
                    }]
                },
                options: {
                    indexAxis: 'y',
                    scales: {
                        x: {
                            beginAtZero: true,
                            suggestedMax: getSuggestedMax(laravelData.topValues)
                        }
                    }
                }
            });
        }
    </script>
</div>
@endsection