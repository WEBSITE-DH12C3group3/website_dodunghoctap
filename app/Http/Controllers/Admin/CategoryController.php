<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\QueryException;

class CategoryController extends Controller
{
    public function index()
    {
        Log::info('CategoryController@index called, fetching categories');
        $categories = Category::all();
        Log::info('Categories fetched: ' . $categories->count() . ' items');
        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        Log::info('CategoryController@create called');
        return view('admin.categories.create');
    }

    public function store(Request $request)
    {
        Log::info('CategoryController@store called with data: ' . json_encode($request->all()));

        try {
            $validated = $request->validate([
                'category_name' => 'required|string|max:255',
            ]);

            // Tạo category_id thủ công
            $lastCategory = Category::orderBy('category_id', 'desc')->first();
            $nextCategoryId = $lastCategory ? $lastCategory->category_id + 1 : 1;
            $validated['category_id'] = $nextCategoryId;

            Category::create($validated);

            Log::info('Category created with category_id: ' . $nextCategoryId . ', redirecting to admin.categories');
            return redirect()->route('admin.categories')->with('ok', 'Thêm danh mục thành công!');
        } catch (\Exception $e) {
            Log::error('Error in CategoryController@store: ' . $e->getMessage());
            return redirect()->route('admin.categories.create')->with('error', 'Lỗi khi thêm danh mục: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $category = Category::findOrFail($id);
        return view('admin.categories.edit', compact('category'));
    }

    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        $validated = $request->validate([
            'category_name' => 'required|string|max:255',
        ]);

        $category->update($validated);

        return redirect()->route('admin.categories')->with('ok', 'Cập nhật danh mục thành công!');
    }

    public function destroy($id)
    {
        try {
            $category = Category::findOrFail($id);

            // Kiểm tra ràng buộc khóa ngoại
            if ($category->products()->count() > 0) {
                return redirect()->route('admin.categories')->with('error', 'Không thể xóa danh mục vì có sản phẩm liên quan.');
            }

            $category->delete();

            return redirect()->route('admin.categories')->with('ok', 'Xóa danh mục thành công!');
        } catch (\Exception $e) {
            Log::error('Error in CategoryController@destroy: ' . $e->getMessage());
            return redirect()->route('admin.categories')->with('error', 'Lỗi khi xóa danh mục: ' . $e->getMessage());
        }
    }
}