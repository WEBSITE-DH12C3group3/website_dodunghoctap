<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductReviewController extends Controller
{
    public function store($id, Request $request)
    {
        $product = Product::findOrFail($id);

        $data = $request->validate([
            'rating'  => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:2000',
        ]);

        Comment::create([
            'product_id'   => $product->product_id,
            'user_id'      => $request->user()->id,
            'rating'       => $data['rating'],
            'comment'      => $data['comment'] ?? null,
            'comment_date' => now(),
        ]);

        return back()->with('success', 'Cảm ơn bạn đã đánh giá sản phẩm!');
    }
}
