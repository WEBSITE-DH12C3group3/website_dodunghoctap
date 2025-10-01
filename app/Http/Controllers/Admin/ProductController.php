<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
public function index(Request $request)
{
    Log::info('ProductController@index called, fetching products');

    try {
        $query = Product::with(['category', 'brand']);

        // Tìm kiếm theo tên / danh mục / thương hiệu
        if ($request->filled('search')) {
            $searchTerm = $request->input('search');
            $query->where(function ($q) use ($searchTerm) {
                $q->where('product_name', 'LIKE', "%{$searchTerm}%")
                  ->orWhereHas('category', function ($q) use ($searchTerm) {
                      $q->where('category_name', 'LIKE', "%{$searchTerm}%");
                  })
                  ->orWhereHas('brand', function ($q) use ($searchTerm) {
                      $q->where('brand_name', 'LIKE', "%{$searchTerm}%");
                  });
            });
            Log::info('Product search: ' . $searchTerm);
        }

        // Lọc theo danh mục
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Lọc theo thương hiệu
        if ($request->filled('brand_id')) {
            $query->where('brand_id', $request->brand_id);
        }

        // Lọc theo số lượng tồn
        if ($request->filled('stock_min')) {
            $query->where('stock_quantity', '>=', (int) $request->stock_min);
        }
        if ($request->filled('stock_max')) {
            $query->where('stock_quantity', '<=', (int) $request->stock_max);
        }

        // Lọc theo khoảng giá
        if ($request->filled('price_min')) {
            $query->where('price', '>=', (int) $request->price_min);
        }
        if ($request->filled('price_max')) {
            $query->where('price', '<=', (int) $request->price_max);
        }

        $products = $query->orderBy('product_id', 'desc')
                         ->paginate(10)
                         ->withQueryString();

        // Lấy data cho dropdown lọc (bắt buộc để view không lỗi)
        $categories = Category::all();
        $brands = Brand::all();

        Log::info('Products fetched (page): ' . $products->count() . ', total: ' . $products->total());

        return view('admin.products.index', compact('products', 'categories', 'brands'));
    } catch (\Exception $e) {
        Log::error('Error in ProductController@index: ' . $e->getMessage());
        return redirect()->route('admin.products')->with('error', 'Lỗi khi tải danh sách sản phẩm: ' . $e->getMessage());
    }
}


    public function create()
    {
        Log::info('ProductController@create called');
        $categories = Category::all();
        $brands = Brand::all();
        return view('admin.products.create', compact('categories', 'brands'));
    }

    public function store(Request $request)
    {
        Log::info('ProductController@store called with data:', $request->all());
        
        $validated = $request->validate([
            'product_name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,category_id',
            'brand_id' => 'nullable|exists:brands,brand_id',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'image_url' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'product_name.required' => 'Vui lòng nhập tên sản phẩm.',
            'category_id.required' => 'Vui lòng chọn danh mục.',
            'brand_id.exists' => 'Thương hiệu không tồn tại.',
            'price.required' => 'Vui lòng nhập giá.',
            'stock_quantity.required' => 'Vui lòng nhập số lượng tồn kho.',
            'image_url.image' => 'File phải là hình ảnh.',
            'image_url.max' => 'Kích thước hình ảnh không được vượt quá 2MB.',
        ]);

        DB::beginTransaction();
        try {
            $dataToCreate = $validated;

            if ($request->hasFile('image_url')) {
                $imageName = time() . '_' . $request->file('image_url')->getClientOriginalName(); // Tạo tên duy nhất
                $imagePath = $request->file('image_url')->storeAs('products', $imageName, 'public'); // Lưu vào storage/app/public/products
                if (!$imagePath) {
                    throw new \Exception('Lỗi khi lưu hình ảnh.');
                }
                $dataToCreate['image_url'] = $imagePath; // Lưu path: products/filename.jpg
            } else {
                $dataToCreate['image_url'] = null; // Nếu không upload ảnh, để null
            }

            Product::create($dataToCreate);
            DB::commit();
            Log::info('Product created successfully: ' . $validated['product_name']);
            return redirect()->route('admin.products')->with('ok', 'Thêm sản phẩm thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating product: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Lỗi khi thêm sản phẩm: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        Log::info('ProductController@edit called for product_id: ' . $id);
        $product = Product::with(['category', 'brand'])->findOrFail($id);
        $categories = Category::all();
        $brands = Brand::all();
        return view('admin.products.edit', compact('product', 'categories', 'brands'));
    }

    public function update(Request $request, $id)
    {
        Log::info('ProductController@update called with data:', $request->all());
        $product = Product::findOrFail($id);

        $validated = $request->validate([
            'product_name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,category_id',
            'brand_id' => 'nullable|exists:brands,brand_id',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'image_url' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'product_name.required' => 'Vui lòng nhập tên sản phẩm.',
            'category_id.required' => 'Vui lòng chọn danh mục.',
            'brand_id.exists' => 'Thương hiệu không tồn tại.',
            'price.required' => 'Vui lòng nhập giá.',
            'stock_quantity.required' => 'Vui lòng nhập số lượng tồn kho.',
            'image_url.image' => 'File phải là hình ảnh.',
            'image_url.max' => 'Kích thước hình ảnh không được vượt quá 2MB.',
        ]);

        DB::beginTransaction();
        try {
            $dataToUpdate = $validated;

            if ($request->hasFile('image_url')) {
                // Xóa ảnh cũ nếu tồn tại
               if ($product->image_url && Storage::disk('public')->exists($product->image_url)) {
                Storage::disk('public')->delete($product->image_url);
                }

                $imageName = time() . '_' . $request->file('image_url')->getClientOriginalName();
                $imagePath = $request->file('image_url')->storeAs('products', $imageName, 'public');
                if (!$imagePath) {
                    throw new \Exception('Lỗi khi lưu hình ảnh.');
                }
                $dataToUpdate['image_url'] = $imagePath;
            }

            $product->update($dataToUpdate);
            DB::commit();
            Log::info('Product updated successfully: ' . $validated['product_name']);
            return redirect()->route('admin.products')->with('ok', 'Cập nhật sản phẩm thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating product: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Lỗi khi cập nhật sản phẩm: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        Log::info('ProductController@destroy called for product_id: ' . $id);
        $product = Product::findOrFail($id);

        DB::beginTransaction();
        try {
            if ($product->image_url && Storage::exists($product->image_url)) {
                Storage::delete($product->image_url);
            }
            $product->delete();
            DB::commit();
            Log::info('Product deleted successfully: product_id ' . $id);
            return redirect()->route('admin.products')->with('ok', 'Xóa sản phẩm thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting product: ' . $e->getMessage());
            return back()->with('error', 'Lỗi khi xóa sản phẩm: ' . $e->getMessage());
        }
    }
}