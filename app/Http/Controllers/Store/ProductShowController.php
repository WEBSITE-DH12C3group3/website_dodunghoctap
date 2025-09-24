<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Comment;
use App\Models\Coupon;
use App\Models\Favourite;
use Illuminate\Support\Facades\DB;

class ProductShowController extends Controller
{
    public function show($id)
    {
        $product = Product::with(['brand'])->findOrFail($id);

        $isFav = false;
        if (auth()->check()) {
            $isFav = Favourite::where('user_id', auth()->id())
                ->where('product_id', $product->product_id)
                ->exists();
        }
        $favouritesCount = $product->favourites()->count();
        // Sản phẩm cùng danh mục
        $related = Product::query()
            ->where('category_id', $product->category_id)
            ->where('product_id', '!=', $product->product_id)
            ->latest('created_at')
            ->take(10)->get();

        // Lấy đánh giá
        $comments = Comment::with('user')
            ->where('product_id', $product->product_id)
            ->latest('comment_date')
            ->get();

        // Thống kê rating
        $counts = Comment::where('product_id', $product->product_id)
            ->selectRaw('rating, COUNT(*) as c')
            ->groupBy('rating')
            ->pluck('c', 'rating'); // [5=>...,4=>...]

        $total = $counts->sum() ?: 0;
        $avg = $total ? round(
            Comment::where('product_id', $product->product_id)->avg('rating'),
            1
        ) : 0;
        $coupons = Coupon::active()
            ->orderByRaw('CASE WHEN valid_to IS NULL THEN 1 ELSE 0 END, valid_to ASC') // gần hết hạn lên trước
            ->take(6)
            ->get();
        $dist = collect([5, 4, 3, 2, 1])->mapWithKeys(fn($r) => [$r => (int)($counts[$r] ?? 0)]);

        return view('store.product.show', compact('product', 'related', 'comments', 'avg', 'total', 'coupons', 'dist', 'isFav', 'favouritesCount'));
    }
}
