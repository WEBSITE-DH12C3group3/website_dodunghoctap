<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class HomeController extends Controller
{
    public function index(Request $req)
    {
        $q   = trim($req->get('q', ''));
        $cat = $req->get('cat');   // category_id nếu có
        $brand = $req->get('brand');

        $products = Product::query();

        if ($q !== '') {
            $products->where(function ($qq) use ($q) {
                $qq->where('name', 'like', "%$q%")
                    ->orWhere('product_name', 'like', "%$q%")
                    ->orWhere('title', 'like', "%$q%");
            });
        }
        if ($cat && Schema::hasColumn('products', 'category_id')) {
            $products->where('category_id', $cat);
        }
        if ($brand && Schema::hasColumn('products', 'brand_id')) {
            $products->where('brand_id', $brand);
        }

        $products = $products->orderByDesc('created_at')->paginate(12)->withQueryString();

        // Lấy danh mục/brand nếu có bảng
        $categories = Schema::hasTable('categories')
            ? DB::table('categories')->select('category_id', 'category_name')->get()
            : collect();
        $brands = Schema::hasTable('brands')
            ? DB::table('brands')->select('brand_id', 'brand_name')->get()
            : collect();

        return view('store.home', compact('products', 'q', 'categories', 'brands', 'cat', 'brand'));
    }

    public function show($id)
    {
        $p = Product::findOrFail($id);
        return view('store.product_show', compact('p'));
    }
}
