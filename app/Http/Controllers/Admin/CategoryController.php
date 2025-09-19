<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\QueryException;

class CategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:manage_categories');
    }

    private function normalizeVietnamese($string)
    {
        $accents = [
            'à','á','ạ','ả','ã','â','ầ','ấ','ậ','ẩ','ẫ','ă','ằ','ắ','ặ','ẳ','ẵ',
            'è','é','ẹ','ẻ','ẽ','ê','ề','ế','ệ','ể','ễ',
            'ì','í','ị','ỉ','ĩ',
            'ò','ó','ọ','ỏ','õ','ô','ồ','ố','ộ','ổ','ỗ','ơ','ờ','ớ','ợ','ở','ỡ',
            'ù','ú','ụ','ủ','ũ','ư','ừ','ứ','ự','ử','ữ',
            'ỳ','ý','ỵ','ỷ','ỹ',
            'đ','À','Á','Ạ','Ả','Ã','Â','Ầ','Ấ','Ậ','Ẩ','Ẫ','Ă','Ằ','Ắ','Ặ','Ẳ','Ẵ',
            'È','É','Ẹ','Ẻ','Ẽ','Ê','Ề','Ế','Ệ','Ể','Ễ',
            'Ì','Í','Ị','Ỉ','Ĩ',
            'Ò','Ó','Ọ','Ỏ','Õ','Ô','Ồ','Ố','Ộ','Ổ','Ỗ','Ơ','Ờ','Ớ','Ợ','Ở','Ỡ',
            'Ù','Ú','Ụ','Ủ','Ũ','Ư','Ừ','Ứ','Ự','Ử','Ữ',
            'Ỳ','Ý','Ỵ','Ỷ','Ỹ',
            'Đ'
        ];
        $noAccents = [
            'a','a','a','a','a','a','a','a','a','a','a','a','a','a','a','a','a',
            'e','e','e','e','e','e','e','e','e','e','e',
            'i','i','i','i','i',
            'o','o','o','o','o','o','o','o','o','o','o','o','o','o','o','o','o',
            'u','u','u','u','u','u','u','u','u','u','u',
            'y','y','y','y','y',
            'd','A','A','A','A','A','A','A','A','A','A','A','A','A','A','A','A','A',
            'E','E','E','E','E','E','E','E','E','E','E',
            'I','I','I','I','I',
            'O','O','O','O','O','O','O','O','O','O','O','O','O','O','O','O','O',
            'U','U','U','U','U','U','U','U','U','U','U',
            'Y','Y','Y','Y','Y',
            'D'
        ];
        return str_replace($accents, $noAccents, $string);
    }

    public function index(Request $request)
    {
        Log::info('CategoryController@index called, fetching categories');

        try {
            $query = Category::select('category_id', 'category_name');

            if ($request->filled('search')) {
                $searchTerm = $request->input('search');
                $normalizedSearch = $this->normalizeVietnamese($searchTerm);
                $query->whereRaw('LOWER(category_name) LIKE ?', ['%' . strtolower($searchTerm) . '%'])
                      ->orWhereRaw('LOWER(category_name) LIKE ?', ['%' . strtolower($normalizedSearch) . '%']);
                Log::info('Category search: ' . $searchTerm);
            }

            $categories = $query->orderBy('category_name', 'asc')
                                ->paginate(10)
                                ->withQueryString();

            Log::info('Categories fetched (page): ' . $categories->count() . ', total: ' . $categories->total());
            Log::debug('Categories object type: ' . get_class($categories));
            Log::debug('Categories data: ' . json_encode($categories->items(), JSON_UNESCAPED_UNICODE));

            if (!($categories instanceof \Illuminate\Pagination\LengthAwarePaginator)) {
                Log::error('Categories is not LengthAwarePaginator, type: ' . get_class($categories));
                throw new \Exception('Invalid paginator type');
            }

            return view('admin.categories.index', compact('categories'));
        } catch (QueryException $e) {
            Log::error('Error in CategoryController@index: ' . $e->getMessage());
            return redirect()->route('admin.categories')->with('error', 'Lỗi khi tải danh sách danh mục: ' . $e->getMessage());
        } catch (\Exception $e) {
            Log::error('Unexpected error in CategoryController@index: ' . $e->getMessage());
            return redirect()->route('admin.categories')->with('error', 'Lỗi không xác định: ' . $e->getMessage());
        }
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
                'category_name' => 'required|string|max:255|unique:categories,category_name',
            ], [
                'category_name.required' => 'Vui lòng nhập tên danh mục.',
                'category_name.max' => 'Tên danh mục không được vượt quá 255 ký tự.',
                'category_name.unique' => 'Tên danh mục đã tồn tại.',
            ]);

            $lastCategory = Category::orderBy('category_id', 'desc')->first();
            $nextCategoryId = $lastCategory ? $lastCategory->category_id + 1 : 1;
            $validated['category_id'] = $nextCategoryId;

            Category::create($validated);

            Log::info('Category created with category_id: ' . $nextCategoryId);
            return redirect()->route('admin.categories')->with('ok', 'Thêm danh mục thành công!');
        } catch (\Exception $e) {
            Log::error('Error in CategoryController@store: ' . $e->getMessage());
            return redirect()->route('admin.categories.create')->withInput()->with('error', 'Lỗi khi thêm danh mục: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        try {
            $category = Category::findOrFail($id);
            return view('admin.categories.edit', compact('category'));
        } catch (\Exception $e) {
            Log::error('Error in CategoryController@edit: ' . $e->getMessage());
            return redirect()->route('admin.categories')->with('error', 'Lỗi khi tải danh mục: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $category = Category::findOrFail($id);

            $validated = $request->validate([
                'category_name' => 'required|string|max:255|unique:categories,category_name,' . $id . ',category_id',
            ], [
                'category_name.required' => 'Vui lòng nhập tên danh mục.',
                'category_name.max' => 'Tên danh mục không được vượt quá 255 ký tự.',
                'category_name.unique' => 'Tên danh mục đã tồn tại.',
            ]);

            $category->update($validated);

            return redirect()->route('admin.categories')->with('ok', 'Cập nhật danh mục thành công!');
        } catch (\Exception $e) {
            Log::error('Error in CategoryController@update: ' . $e->getMessage());
            return redirect()->route('admin.categories.edit', $id)->withInput()->with('error', 'Lỗi khi cập nhật danh mục: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $category = Category::findOrFail($id);

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