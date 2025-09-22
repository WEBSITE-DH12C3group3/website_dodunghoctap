<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\User;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SalesReportExport;

class StatsController extends Controller
{
    public function index(Request $request)
    {
        Log::info('StatsController@index called, fetching stats');

        try {
            // Tổng số liệu cơ bản
            $totalCategories = Category::count();
            $totalProducts = Product::count();
            $totalUsers = User::count();
            $onlineUsers = User::where('last_activity', '>', now()->subMinutes(5))->count();

            // Lấy khoảng thời gian từ request (mặc định tháng hiện tại)
            $start = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
            $end = $request->input('end_date', now()->endOfMonth()->format('Y-m-d'));

            $start = Carbon::parse($start);
            $end = Carbon::parse($end);

            // Tổng đơn hàng trong ngày
            $ordersToday = Order::whereDate('order_date', now())->count();

            // Tổng đơn hàng trong tuần
            $ordersWeek = Order::whereBetween('order_date', [now()->startOfWeek(), now()->endOfWeek()])->count();

            // Tổng đơn hàng trong khoảng thời gian
            $ordersInPeriod = Order::whereBetween('order_date', [$start, $end])->count();

            // Hàng nhập kho (tổng quantity từ purchase_order_items)
            $purchases = PurchaseOrderItem::join('purchase_orders', 'purchase_order_items.purchase_order_id', '=', 'purchase_orders.purchase_order_id')
                ->whereBetween('purchase_orders.order_date', [$start, $end])
                ->sum('purchase_order_items.quantity');

            // Chi phí nhập (tổng total_amount từ purchase_orders)
            $cost = PurchaseOrder::whereBetween('order_date', [$start, $end])->sum('total_amount');

            // Hàng bán ra (tổng quantity từ order_items)
            $sales = OrderItem::join('orders', 'order_items.order_id', '=', 'orders.order_id')
                ->whereBetween('orders.order_date', [$start, $end])
                ->sum('order_items.quantity');

            // Doanh thu (tổng total_amount từ orders)
            $revenue = Order::whereBetween('order_date', [$start, $end])->sum('total_amount');

            // Lợi nhuận
            $profit = $revenue - $cost;

            // Top 3 sản phẩm bán chạy
            $topProducts = OrderItem::select('product_id', DB::raw('SUM(quantity) as total_sold'))
                ->groupBy('product_id')
                ->orderByDesc('total_sold')
                ->limit(3)
                ->get()
                ->map(function ($item) {
                    $product = Product::find($item->product_id);
                    $item->product_name = $product ? $product->product_name : 'Unknown';
                    return $item;
                });

            // Dữ liệu cho biểu đồ đường: Doanh thu theo ngày
            $revenueData = Order::selectRaw('DATE(order_date) as date, SUM(total_amount) as total')
                ->whereBetween('order_date', [$start, $end])
                ->groupBy('date')
                ->get();

            $revenueLabels = $revenueData->pluck('date');
            $revenueValues = $revenueData->pluck('total');

            // Dữ liệu cho biểu đồ tròn: Phân bổ sản phẩm theo danh mục
            $categoryData = Product::selectRaw('categories.category_name, COUNT(products.product_id) as count')
                ->join('categories', 'products.category_id', '=', 'categories.category_id')
                ->groupBy('categories.category_name')
                ->get();

            $categoryLabels = $categoryData->pluck('category_name');
            $categoryValues = $categoryData->pluck('count');

            // Dữ liệu cho biểu đồ cột ngang: Top sản phẩm
            $topLabels = $topProducts->pluck('product_name');
            $topValues = $topProducts->pluck('total_sold');

            // Dữ liệu cho biểu đồ cột: Doanh thu, chi phí, lợi nhuận
            $financialLabels = ['Doanh thu', 'Chi phí', 'Lợi nhuận'];
            $financialValues = [$revenue, $cost, $profit];

            Log::info('Stats fetched: Categories ' . $totalCategories . ', Products ' . $totalProducts . ', Users ' . $totalUsers . ', Online ' . $onlineUsers . ', Orders in period ' . $ordersInPeriod . ', Purchases ' . $purchases . ', Sales ' . $sales . ', Revenue ' . $revenue . ', Cost ' . $cost . ', Profit ' . $profit);

            return view('admin.stats', compact(
                'totalCategories', 'totalProducts', 'totalUsers', 'onlineUsers',
                'ordersToday', 'ordersWeek', 'ordersInPeriod',
                'purchases', 'sales', 'revenue', 'cost', 'profit',
                'topProducts', 'start', 'end',
                'revenueLabels', 'revenueValues',
                'categoryLabels', 'categoryValues',
                'topLabels', 'topValues',
                'financialLabels', 'financialValues'
            ));
        } catch (\Exception $e) {
            Log::error('Error in StatsController@index: ' . $e->getMessage());
            return redirect()->route('admin.stats')->with('error', 'Lỗi khi tải thống kê: ' . $e->getMessage());
        }
    }

    public function exportReport(Request $request)
    {
        try {
            $format = $request->input('format', 'pdf');
            $period = $request->input('period', 'month');

            // Xác định khoảng thời gian
            switch ($period) {
                case 'today':
                    $start = now()->startOfDay();
                    $end = now()->endOfDay();
                    $periodText = 'Hôm nay (' . now()->format('d/m/Y') . ')';
                    break;
                case 'week':
                    $start = now()->startOfWeek();
                    $end = now()->endOfWeek();
                    $periodText = 'Tuần này (' . $start->format('d/m/Y') . ' - ' . $end->format('d/m/Y') . ')';
                    break;
                case 'month':
                    $start = now()->startOfMonth();
                    $end = now()->endOfMonth();
                    $periodText = 'Tháng ' . now()->format('m/Y');
                    break;
                case 'year':
                    $start = now()->startOfYear();
                    $end = now()->endOfYear();
                    $periodText = 'Năm ' . now()->format('Y');
                    break;
                case 'custom':
                    $start = Carbon::parse($request->input('custom_start'));
                    $end = Carbon::parse($request->input('custom_end'));
                    $periodText = $start->format('d/m/Y') . ' - ' . $end->format('d/m/Y');
                    break;
                default:
                    $start = now()->startOfMonth();
                    $end = now()->endOfMonth();
                    $periodText = 'Tháng ' . now()->format('m/Y');
            }

            // Tính toán số liệu báo cáo
            $ordersCount = Order::whereBetween('order_date', [$start, $end])->count();
            $sales = OrderItem::join('orders', 'order_items.order_id', '=', 'orders.order_id')
                ->whereBetween('orders.order_date', [$start, $end])
                ->sum('order_items.quantity');
            $revenue = Order::whereBetween('order_date', [$start, $end])->sum('total_amount');
            $cost = PurchaseOrder::whereBetween('order_date', [$start, $end])->sum('total_amount');
            $profit = $revenue - $cost;

            $data = [
                'periodText' => $periodText,
                'userName' => auth()->user()->full_name,
                'ordersCount' => $ordersCount,
                'sales' => $sales,
                'revenue' => $revenue,
                'cost' => $cost,
                'profit' => $profit,
                'date' => now()->format('d/m/Y H:i'),
            ];

            // Xuất PDF
        if ($format === 'pdf') {
    $pdf = Pdf::loadView('admin.reports.sales', $data);
    return $pdf->download('bao-cao-doanh-thu-' . now()->format('YmdHis') . '.pdf');
}

            // Xuất Excel
            return Excel::download(new SalesReportExport($data), 'bao-cao-doanh-thu-' . now()->format('YmdHis') . '.xlsx');
        } catch (\Exception $e) {
            Log::error('Error in StatsController@exportReport: ' . $e->getMessage());
            return redirect()->route('admin.stats')->with('error', 'Lỗi khi xuất báo cáo: ' . $e->getMessage());
        }
    }
}