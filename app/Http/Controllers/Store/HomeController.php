<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\Product;

class HomeController extends Controller
{
    public function index()
    {
        $products = Product::query()
            ->latest('created_at')
            ->paginate(12);

        // Danh mục
        $categories = DB::table('categories')
            ->select('category_id', 'category_name')
            ->orderBy('category_name')
            ->limit(20)
            ->get();

        // Sản phẩm mới: theo created_at (nếu thiếu thì fallback product_id)
        $hasCreated = $this->hasColumn('products', 'created_at');
        $newProducts = DB::table('products')
            ->select('product_id', 'product_name', 'price', 'image_url')
            ->when(
                $hasCreated,
                fn($q) => $q->orderByDesc('created_at'),
                fn($q) => $q->orderByDesc('product_id')
            )
            ->limit(10)->get();

        // Bán chạy: sum(order_items.quantity)
        $bestSellers = DB::table('order_items as oi')
            ->select('p.product_id', 'p.product_name', 'p.price', 'p.image_url', DB::raw('SUM(oi.quantity) as total_qty'))
            ->join('products as p', 'p.product_id', '=', 'oi.product_id')
            ->groupBy('p.product_id', 'p.product_name', 'p.price', 'p.image_url')
            ->orderByDesc('total_qty')
            ->limit(10)->get();

        // Nổi bật: theo rating trung bình từ comments (>=2 đánh giá), fallback created_at
        $featured = DB::table('comments as c')
            ->select(
                'p.product_id',
                'p.product_name',
                'p.price',
                'p.image_url',
                DB::raw('AVG(c.rating) as avg_rating'),
                DB::raw('COUNT(*) as rating_count')
            )
            ->join('products as p', 'p.product_id', '=', 'c.product_id')
            ->groupBy('p.product_id', 'p.product_name', 'p.price', 'p.image_url')
            ->havingRaw('COUNT(*) >= 2')
            ->orderByDesc('avg_rating')
            ->orderByDesc('rating_count')
            ->limit(10)->get();

        // Nếu bảng comments trống -> fallback mới nhất
        if ($featured->isEmpty()) {
            $featured = DB::table('products')
                ->select('product_id', 'product_name', 'price', 'image_url')
                ->when(
                    $hasCreated,
                    fn($q) => $q->orderByDesc('created_at'),
                    fn($q) => $q->orderByDesc('product_id')
                )
                ->limit(10)->get();
        }

        return view('store.home', compact('products', 'categories', 'newProducts', 'bestSellers', 'featured'));
    }

    private function hasColumn(string $table, string $column): bool
    {
        try {
            $col = DB::select("SHOW COLUMNS FROM `$table` LIKE ?", [$column]);
            return !empty($col);
        } catch (\Throwable $e) {
            return false;
        }
    }
}
