<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ProductListController extends Controller
{
    /** Build base query + sidebar data, apply single-select filters/sort/pagination */
    private function buildListing(Request $r, $baseScope = null)
    {
        $hasCreated = Schema::hasColumn('products', 'created_at');

        // --- Subquery rating ---
        $ratingAgg = DB::table('comments')
            ->selectRaw('product_id, AVG(rating) avg_rating, COUNT(*) reviews_count')
            ->groupBy('product_id');

        // --- Base query ---
        $q = DB::table('products as p')
            ->leftJoinSub($ratingAgg, 'r', 'r.product_id', '=', 'p.product_id')
            ->select(
                'p.product_id',
                'p.product_name',
                'p.price',
                'p.image_url',
                'p.sold',
                DB::raw('ROUND(COALESCE(r.avg_rating,0),1) AS avg_rating'),
                DB::raw('COALESCE(r.reviews_count,0) AS reviews_count'),
                'p.category_id',
                'p.brand_id'
            );

        // --- Áp scope ---
        if ($baseScope === 'best') $q->orderByRaw('COALESCE(p.sold,0) DESC');
        if ($baseScope === 'new' && $hasCreated) $q->orderByDesc('p.created_at');
        if ($baseScope === 'featured') $q->where('p.is_featured', 1)->orderByDesc('p.product_id');
        if (is_array($baseScope) && isset($baseScope['category_id'])) $q->where('p.category_id', $baseScope['category_id']);

        // --- Đọc tham số lọc (đơn chọn) ---
        // Nếu client submit mảng (brand[]=..), ta vẫn lấy phần tử đầu để đảm bảo "đơn chọn"
        $brand = is_array($r->brand) ? ($r->brand[0] ?? null) : $r->brand;
        $category = is_array($r->category) ? ($r->category[0] ?? null) : $r->category;
        $price = is_array($r->price) ? ($r->price[0] ?? null) : $r->price;

        if ($brand) $q->where('p.brand_id', (int)$brand);
        if ($category) $q->where('p.category_id', (int)$category);

        // Mức giá: chuỗi dạng "min-max" hoặc "max+"
        if ($price) {
            if (str_ends_with($price, '+')) {
                $min = (int) rtrim($price, '+');
                $q->where('p.price', '>=', $min);
            } else {
                [$min, $max] = array_map('intval', explode('-', $price));
                $q->whereBetween('p.price', [$min, $max]);
            }
        }

        // --- Sắp xếp ---
        // name_asc, name_desc, price_asc, price_desc, newest
        $sort = $r->get('sort');
        match ($sort) {
            'name_asc' => $q->orderBy('p.product_name', 'asc'),
            'name_desc' => $q->orderBy('p.product_name', 'desc'),
            'price_asc' => $q->orderBy('p.price', 'asc'),
            'price_desc' => $q->orderBy('p.price', 'desc'),
            'newest' => $hasCreated ? $q->orderByDesc('p.created_at') : $q->orderByDesc('p.product_id'),
            default => null, // giữ thứ tự scope đã đặt
        };

        // --- Sidebar data ---
        $brands = DB::table('brands')->select('brand_id', 'brand_name')->orderBy('brand_name')->get();
        $categories = DB::table('categories')->select('category_id', 'category_name')->orderBy('category_name')->get();

        // --- Phân trang ---
        //      $products = $q->paginate(16)->appends($r->query());
        $products = $q->paginate(16)->appends(collect($r->query())->except('page')->all());

        // Các khoảng giá mẫu
        $priceRanges = [
            ['label' => 'Giá dưới 100.000đ', 'value' => '0-100000'],
            ['label' => '100.000đ - 300.000đ', 'value' => '100000-300000'],
            ['label' => '300.000đ - 500.000đ', 'value' => '300000-500000'],
            ['label' => '500.000đ - 700.000đ', 'value' => '500000-700000'],
            ['label' => '700.000đ - 1.000.000đ', 'value' => '700000-1000000'],
            ['label' => 'Giá trên 1.000.000đ', 'value' => '1000000+'],
        ];

        return compact('products', 'brands', 'categories', 'priceRanges', 'brand', 'category', 'price', 'sort');
    }

    public function index(Request $r)
    {
        $data = $this->buildListing($r, null);
        return view('store.product.index', $data);
    }
    public function new(Request $r)
    {
        $data = $this->buildListing($r, 'new');
        return view('store.product.index', $data);
    }
    public function best(Request $r)
    {
        $data = $this->buildListing($r, 'best');
        return view('store.product.index', $data);
    }
    public function featured(Request $r)
    {
        $data = $this->buildListing($r, 'featured');
        return view('store.product.index', $data);
    }
    public function category(Request $r, $id)
    {
        $id = (int) $id;

        // Nếu URL như /category/13?category=11 -> chuyển về /category/11 (giữ brand/price/sort)
        if ($r->filled('category') && (int)$r->category !== $id) {
            $q = $r->query();
            unset($q['category'], $q['page']); // bỏ category trùng & trang cũ
            return redirect()->route('store.category', ['id' => (int)$r->category] + $q);
        }

        // Không cho buildListing áp dụng thêm query category lần nữa
        $r->query->remove('category');

        $data = $this->buildListing($r, ['category_id' => $id]);
        return view('store.product.index', $data);
    }
}
