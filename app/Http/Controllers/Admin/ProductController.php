<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    public function index()
    {
        Log::info('ProductController@index called, fetching products');
        $products = Product::with('category')->get();
        Log::info('Products fetched: ' . $products->count() . ' items');
        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        Log::info('ProductController@create called');
        $categories = Category::all();
        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        Log::info('ProductController@store called with data: ' . json_encode($request->all()));

        try {
            $validated = $request->validate([
                'product_name' => 'required|string|max:255',
                'category_id' => 'required|exists:categories,category_id',
                'price' => 'required|numeric|min:0',
                'stock_quantity' => 'required|integer|min:0',
                'description' => 'nullable|string',
                'image_url' => 'nullable|string|max:255',
            ]);

            // Tạo product_id thủ công
            $lastProduct = Product::orderBy('product_id', 'desc')->first();
            $nextProductId = $lastProduct ? $lastProduct->product_id + 1 : 14; // Bắt đầu từ 14 nếu bảng rỗng
            $validated['product_id'] = $nextProductId;

            Product::create($validated);

            Log::info('Product created with product_id: ' . $nextProductId . ', redirecting to admin.products');
            return redirect()->route('admin.products')->with('ok', 'Thêm sản phẩm thành công!');
        } catch (\Exception $e) {
            Log::error('Error in ProductController@store: ' . $e->getMessage());
            return redirect()->route('admin.products.create')->with('error', 'Lỗi khi thêm sản phẩm: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $product = Product::findOrFail($id);
        $categories = Category::all();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $validated = $request->validate([
            'product_name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,category_id',
            'price' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'description' => 'nullable|string',
            'image_url' => 'nullable|string|max:255',
        ]);

        $product->update($validated);

        return redirect()->route('admin.products')->with('ok', 'Cập nhật sản phẩm thành công!');
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();

        return redirect()->route('admin.products')->with('ok', 'Xóa sản phẩm thành công!');
    }
}