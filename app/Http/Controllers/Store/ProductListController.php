<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ProductListController extends Controller
{
    /** Build base query + sidebar data, apply single-select filters/sort/pagination */
    // app/Http/Controllers/Store/ProductListController.php

    private function buildListing(Request $r, ?string $scope = null)
    {
        $hasCreated = Schema::hasColumn('products', 'created_at');

        // ---- rating agg
        $ratingAgg = DB::table('comments')
            ->selectRaw('product_id, AVG(rating) avg_rating, COUNT(*) reviews_count')
            ->groupBy('product_id');

        $q = DB::table('products as p')
            ->leftJoinSub($ratingAgg, 'r', 'r.product_id', '=', 'p.product_id')
            ->select(
                'p.product_id',
                'p.product_name',
                'p.price',
                'p.image_url',
                'p.sold',
                'p.category_id',
                'p.brand_id',
                DB::raw('ROUND(COALESCE(r.avg_rating,0),1) AS avg_rating'),
                DB::raw('COALESCE(r.reviews_count,0) AS reviews_count')
            );

        // ---- scope (giữ được khi từ /newProduct chuyển sang /products)
        $scope = $scope ?? $r->get('scope');
        if ($scope === 'best')      $q->orderByRaw('COALESCE(p.sold,0) DESC');
        elseif ($scope === 'new')   $hasCreated ? $q->orderByDesc('p.created_at') : $q->orderByDesc('p.product_id');
        elseif ($scope === 'feat' || $scope === 'featured') $q->where('p.is_featured', 1);

        // ---- filters từ query
        if ($r->filled('category')) $q->where('p.category_id', (int)$r->category);
        if ($r->filled('brand'))    $q->where('p.brand_id', (int)$r->brand);

        if ($r->filled('price')) {
            $price = $r->price;
            if (str_ends_with($price, '+')) {
                $min = (int) rtrim($price, '+');
                $q->where('p.price', '>=', $min);
            } else {
                [$min, $max] = array_map('intval', explode('-', $price));
                $q->whereBetween('p.price', [$min, $max]);
            }
        }

        // ---- sort
        $sort = $r->get('sort');
        match ($sort) {
            'name_asc'   => $q->orderBy('p.product_name', 'asc'),
            'name_desc'  => $q->orderBy('p.product_name', 'desc'),
            'price_asc'  => $q->orderBy('p.price', 'asc'),
            'price_desc' => $q->orderBy('p.price', 'desc'),
            'newest'     => $hasCreated ? $q->orderByDesc('p.created_at') : $q->orderByDesc('p.product_id'),
            default      => null,
        };

        $products   = $q->paginate(16)->appends(collect($r->query())->except('page')->all());
        $brands     = DB::table('brands')->select('brand_id', 'brand_name')->orderBy('brand_name')->get();
        $categories = DB::table('categories')->select('category_id', 'category_name')->orderBy('category_name')->get();
        $priceRanges = [
            ['label' => 'Giá dưới 100.000đ', 'value' => '0-100000'],
            ['label' => '100.000đ - 300.000đ', 'value' => '100000-300000'],
            ['label' => '300.000đ - 500.000đ', 'value' => '300000-500000'],
            ['label' => '500.000đ - 700.000đ', 'value' => '500000-700000'],
            ['label' => '700.000đ - 1.000.000đ', 'value' => '700000-1000000'],
            ['label' => 'Giá trên 1.000.000đ', 'value' => '1000000+'],
        ];

        // biến để Blade/JS biết scope hiện tại
        return compact('products', 'brands', 'categories', 'priceRanges', 'sort') + [
            'brand'    => $r->brand,
            'category' => $r->category,
            'price'    => $r->price,
            'scope'    => $scope,
        ];
    }

    public function index(Request $r)
    {
        return $this->renderList($r, null);
    }
    public function new(Request $r)
    {
        return $this->renderList($r, 'new');
    }
    public function best(Request $r)
    {
        return $this->renderList($r, 'best');
    }
    public function featured(Request $r)
    {
        return $this->renderList($r, 'feat');
    }

    private function renderList(Request $r, ?string $scope)
    {
        $data = $this->buildListing($r, $scope);

        // Khi là AJAX → trả fragment grid
        if ($r->ajax()) {
            return response()->json([
                'html' => view('store.product._grid', $data)->render(),
            ]);
        }
        return view('store.product.index', $data);
    }
}
