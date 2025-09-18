<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BrandController extends Controller
{
    public function index()
    {
        Log::info('BrandController@index called, fetching brands');
        $brands = Brand::withCount('products')->orderBy('brand_name', 'asc')->get();
        $totalBrands = $brands->count();
        Log::info('Brands fetched: ' . $totalBrands . ' items');
        return view('admin.brands.index', compact('brands', 'totalBrands'));
    }

    public function create()
    {
        Log::info('BrandController@create called');
        return view('admin.brands.create');
    }

    public function store(Request $request)
    {
        Log::info('BrandController@store called with data:', $request->all());
        
        $validated = $request->validate([
            'brand_name' => 'required|string|max:255|unique:brands,brand_name',
            'description' => 'nullable|string|max:1000',
        ], [
            'brand_name.required' => 'Vui lòng nhập tên thương hiệu.',
            'brand_name.unique' => 'Tên thương hiệu đã tồn tại.',
            'brand_name.max' => 'Tên thương hiệu không được vượt quá 255 ký tự.',
            'description.max' => 'Mô tả không được vượt quá 1000 ký tự.',
        ]);

        DB::beginTransaction();
        try {
            Brand::create($validated);
            DB::commit();
            Log::info('Brand created successfully: ' . $validated['brand_name']);
            return redirect()->route('admin.brands')->with('ok', 'Thêm thương hiệu thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating brand: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Lỗi khi thêm thương hiệu: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        Log::info('BrandController@show called for brand_id: ' . $id);
        $brand = Brand::with(['products.category'])->findOrFail($id);
        $categoryCounts = $brand->products->groupBy('category_id')->map(function ($group) {
            return [
                'category' => $group->first()->category,
                'product_count' => $group->count(),
                'products' => $group,
            ];
        });
        Log::info('Brand fetched: ', ['brand_id' => $brand->brand_id, 'category_counts' => $categoryCounts->toArray()]);
        return view('admin.brands.show', compact('brand', 'categoryCounts'));
    }

    public function destroy($id)
    {
        Log::info('BrandController@destroy called for brand_id: ' . $id);
        $brand = Brand::findOrFail($id);

        DB::beginTransaction();
        try {
            if ($brand->products()->count() > 0) {
                Log::warning('Attempt to delete brand with products: brand_id ' . $id);
                return back()->with('error', 'Không thể xóa thương hiệu vì vẫn còn sản phẩm liên quan.');
            }

            $brand->delete();
            DB::commit();
            Log::info('Brand deleted successfully: brand_id ' . $id);
            return redirect()->route('admin.brands')->with('ok', 'Xóa thương hiệu thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting brand: ' . $e->getMessage());
            return back()->with('error', 'Lỗi khi xóa thương hiệu: ' . $e->getMessage());
        }
    }
}