<?php

namespace App\Http\Controllers\Store;

use Illuminate\Support\Facades\Schema;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\Product;

class HomeController extends Controller
{
    public function index()
    {
        // Danh mục
        $categories = DB::table('categories')
            ->select('category_id', 'category_name')
            ->orderBy('category_name')
            ->limit(20)
            ->get();
        $ratingAgg = DB::table('comments')
            ->selectRaw('product_id, AVG(rating) AS avg_rating, COUNT(*) AS reviews_count')
            ->groupBy('product_id');
        // Sản phẩm mới: theo created_at (nếu thiếu thì fallback product_id)
        $newProducts = DB::table('products as p')
            ->leftJoinSub($ratingAgg, 'r', 'r.product_id', '=', 'p.product_id')
            ->select(
                'p.product_id',
                'p.product_name',
                'p.price',
                'p.image_url',
                'p.sold',
                DB::raw('ROUND(COALESCE(r.avg_rating,0),1) AS avg_rating'),
                DB::raw('COALESCE(r.reviews_count,0) AS reviews_count')
            )
            ->when(
                Schema::hasColumn('products', 'created_at'),
                fn($q) => $q->orderByDesc('p.created_at'),
                fn($q) => $q->orderByDesc('p.product_id')
            )
            ->limit(8)
            ->get();        // Bán chạy: sum(order_items.quantity)
        $bestSellers = DB::table('products as p')
            ->leftJoinSub($ratingAgg, 'r', 'r.product_id', '=', 'p.product_id')
            ->select(
                'p.product_id',
                'p.product_name',
                'p.price',
                'p.image_url',
                'p.sold',
                DB::raw('ROUND(COALESCE(r.avg_rating,0),1) AS avg_rating'),
                DB::raw('COALESCE(r.reviews_count,0) AS reviews_count')
            )
            ->orderByRaw('COALESCE(p.sold,0) DESC')
            ->limit(8)
            ->get();
        $featured = DB::table('products as p')
            ->leftJoin('comments as c', 'p.product_id', '=', 'c.product_id')
            ->select(
                'p.product_id',
                'p.product_name',
                'p.price',
                'p.image_url',
                'p.sold',
                // chỉ tính các rating 1..5
                DB::raw('ROUND(AVG(CASE WHEN c.rating BETWEEN 1 AND 5 THEN c.rating END), 1) as avg_rating'),
                DB::raw('SUM(CASE WHEN c.rating BETWEEN 1 AND 5 THEN 1 ELSE 0 END) as reviews_count')
            )
            ->where(function ($q) {
                $q->whereBetween('c.rating', [1, 5])
                    ->orWhereNull('c.rating'); // để LEFT JOIN không loại sp chưa có cmt
            })
            ->groupBy('p.product_id', 'p.product_name', 'p.price', 'p.image_url', 'p.sold')
            ->having('reviews_count', '>=', 2)
            ->orderByDesc('avg_rating')
            ->orderByDesc('reviews_count')
            ->limit(8)
            ->get();

        // Fallback: vẫn trả đủ field để Blade không lỗi
        if ($featured->isEmpty()) {
            $featured = DB::table('products as p')
                ->select(
                    'p.product_id',
                    'p.product_name',
                    'p.price',
                    'p.image_url',
                    'p.sold',
                    DB::raw('0 as avg_rating'),
                    DB::raw('0 as reviews_count')
                )
                ->when(
                    fn($q) => $q->orderByDesc('p.created_at'),
                    fn($q) => $q->orderByDesc('p.product_id')
                )
                ->limit(8)
                ->get();
        }
        return view('store.home', compact('categories', 'newProducts', 'bestSellers', 'featured'));
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
