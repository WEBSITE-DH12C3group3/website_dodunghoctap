<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->get('q', ''));
        $priceStr = $request->get('price'); // "min-max"
        $sort     = $request->get('sort', 'newest');
        $absMin = 1;
        $absMax = 15_000_000;
        // Parse "min-max"
        $min = $absMin;
        $max = $absMax;
        if ($priceStr) {
            [$pm, $px] = array_pad(explode('-', $priceStr, 2), 2, null);
            if (is_numeric($pm)) $min = max($absMin, (int)$pm);
            if (is_numeric($px)) $max = min($absMax, (int)$px);
            if ($min > $max) {
                $min = $absMin;
                $max = $absMax;
            }
        }

        $query = DB::table('products as p')
            ->select([
                'p.product_id',
                'p.product_name',
                'p.price',
                'p.image_url',
                'p.sold',
                // Điểm trung bình
                DB::raw('(SELECT ROUND(AVG(cm.rating),1)
                  FROM comments cm
                  WHERE cm.product_id = p.product_id) AS avg_rating'),
                // Số đánh giá
                DB::raw('(SELECT COUNT(*)
                  FROM comments cm2
                  WHERE cm2.product_id = p.product_id) AS rating_count'),
            ])
            ->when($q !== '', function ($w) use ($q) {
                $w->where(function ($x) use ($q) {
                    $x->where('p.product_name', 'like', "%{$q}%")
                        ->orWhere('p.description', 'like', "%{$q}%");
                });
            })
            ->when($min !== null && $max !== null, function ($w) use ($min, $max) {
                $w->whereBetween('p.price', [$min, $max]);
            });
        // ->orderBy('p.created_at', 'desc');
        switch ($sort) {
            case 'price_asc':
                $query->orderBy('p.price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('p.price', 'desc');
                break;
            default:
                $query->orderBy('p.created_at', 'desc'); // newest
        }
        $products = $query->paginate(16)->appends(['q' => $q, 'price' => "{$min}-{$max}", 'sort' => $sort]);

        // Thêm dòng này để tạo biến $base
        $base = ['q' => $q, 'price' => "{$min}-{$max}"];

        return view('store.product.search', [
            'products' => $products,
            'q'        => $q,
            'absMin'   => $absMin,
            'absMax'   => $absMax,
            'min'      => $min,
            'max'      => $max,
            'sort'     => $sort,
            // Thêm dòng này để truyền biến $base sang view
            'base'     => $base,
        ]);
    }
}
