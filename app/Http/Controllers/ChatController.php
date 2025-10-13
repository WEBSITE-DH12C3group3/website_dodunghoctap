<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ChatbotRule;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Supplier;
use App\Models\Coupon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ChatController extends Controller
{
    private $apiKey = '';

    public function index()
    {
        return view('chat.index');
    }

    public function chat(Request $request)
    {
        $request->validate(['message' => 'required|string']);
        $userMessage = $request->input('message');
        $message = strtolower(Str::ascii($userMessage)); 

        // 1. Luật tĩnh từ DB (chatbot_rules)
        try {
            $rules = ChatbotRule::all();
            foreach ($rules as $rule) {
                if (Str::contains($message, strtolower(Str::ascii($rule->keyword)))) {
                    return response()->json([
                        'reply' => $rule->answer,
                        'source' => 'DB_RULE'
                    ]);
                }
            }
        } catch (\Exception $e) {
            Log::error("Lỗi ChatbotRule: " . $e->getMessage());
        }

        // 2. Sản phẩm bán chạy nhất (order_items)
        $bestSellerKeywords = ['san pham ban chay', 'ban tot nhat', 'top ban', 'san pham hot'];
        if (Str::contains($message, $bestSellerKeywords)) {
            try {
                $topProducts = DB::table('products')
                    ->join('order_items', 'products.product_id', '=', 'order_items.product_id')
                    ->select('products.product_id', 'products.product_name', 'products.price', DB::raw('SUM(order_items.quantity) as total_sold'))
                    ->groupBy('products.product_id', 'products.product_name', 'products.price')
                    ->orderByDesc('total_sold')
                    ->limit(5)
                    ->get();

                if ($topProducts->isEmpty()) {
                    $reply = "Hiện tại chưa có dữ liệu sản phẩm bán chạy.";
                    return response()->json(['reply' => $reply, 'source' => 'DB_QUERY_BEST_SELLER']);
                } else {
                    $products = $topProducts->map(function ($p) {
                        return [
                            'product_id' => $p->product_id,
                            'product_name' => $p->product_name,
                            'price' => $p->price
                        ];
                    })->toArray();
                    $list = $topProducts->map(function ($p, $i) {
                        return ($i + 1) . ". " . $p->product_name . " (Đã bán: " . $p->total_sold . ")";
                    })->implode("\n");
                    $reply = "Top 5 sản phẩm bán chạy:\n\n" . $list;
                    return response()->json(['reply' => $reply, 'products' => $products, 'source' => 'DB_QUERY_BEST_SELLER']);
                }
            } catch (\Exception $e) {
                Log::error("Lỗi SQL bán chạy: " . $e->getMessage());
                return response()->json(['reply' => 'Lỗi dữ liệu khi tìm sản phẩm bán chạy.', 'source' => 'DB_ERROR']);
            }
        }

        // 3. Sản phẩm giá thấp nhất
        $lowPriceKeywords = ['gia thap nhat', 're nhat', 'mat hang re', 'gia thap'];
        if (Str::contains($message, $lowPriceKeywords)) {
            try {
                $lowPriceProducts = Product::select('product_id', 'product_name', 'price')
                    ->orderBy('price', 'asc')
                    ->limit(5)
                    ->get();

                if ($lowPriceProducts->isEmpty()) {
                    $reply = "Không tìm thấy sản phẩm nào.";
                    return response()->json(['reply' => $reply, 'source' => 'DB_QUERY_LOW_PRICE']);
                } else {
                    $products = $lowPriceProducts->map(function ($p) {
                        return [
                            'product_id' => $p->product_id,
                            'product_name' => $p->product_name,
                            'price' => $p->price
                        ];
                    })->toArray();
                    $list = $lowPriceProducts->map(function ($p, $i) {
                        return ($i + 1) . ". " . $p->product_name . " (Giá: " . number_format($p->price, 0, ',', '.') . " VNĐ)";
                    })->implode("\n");
                    $reply = "Top 5 sản phẩm giá rẻ:\n\n" . $list;
                    return response()->json(['reply' => $reply, 'products' => $products, 'source' => 'DB_QUERY_LOW_PRICE']);
                }
            } catch (\Exception $e) {
                Log::error("Lỗi SQL giá thấp: " . $e->getMessage());
                return response()->json(['reply' => 'Lỗi dữ liệu khi tìm sản phẩm giá thấp.', 'source' => 'DB_ERROR']);
            }
        }

        // 4. Danh mục sản phẩm
        $categoryKeywords = ['danh muc san pham', 'cac loai hang', 'shop co gi'];
        if (Str::contains($message, $categoryKeywords)) {
            try {
                $categories = Category::select('category_name')->get();

                if ($categories->isEmpty()) {
                    $reply = "Hiện chưa có danh mục sản phẩm.";
                } else {
                    $list = $categories->map(function ($c, $i) {
                        return ($i + 1) . ". " . $c->category_name;
                    })->implode("\n");
                    $reply = "Các danh mục chính:\n\n" . $list;
                }
                return response()->json(['reply' => $reply, 'source' => 'DB_QUERY_CATEGORIES']);
            } catch (\Exception $e) {
                Log::error("Lỗi SQL danh mục: " . $e->getMessage());
                return response()->json(['reply' => 'Lỗi dữ liệu khi tìm danh mục.', 'source' => 'DB_ERROR']);
            }
        }

        // 5. Coupon/Khuyến mãi (coupons)
        $couponKeywords = ['ma giam gia', 'khuyen mai', 'coupon', 'chuong trinh khuyen mai'];
        if (Str::contains($message, $couponKeywords)) {
            try {
                $today = Carbon::now()->toDateString();

                $activeCoupons = Coupon::where('valid_from', '<=', $today)
                    ->where('valid_to', '>=', $today)
                    ->get();

                if ($activeCoupons->isEmpty()) {
                    $reply = "Hiện chưa có mã giảm giá nào.";
                } else {
                    $list = $activeCoupons->map(function ($c, $i) {
                        $discount = $c->discount_amount 
                            ? number_format($c->discount_amount, 0, ',', '.') . ' VNĐ'
                            : ($c->discount_percent . '%');
                        return ($i + 1) . ". Mã: " . $c->code . " (Giảm: " . $discount . ")";
                    })->implode("\n");
                    $reply = "Các mã giảm giá hiện có:\n\n" . $list;
                }
                return response()->json(['reply' => $reply, 'source' => 'DB_QUERY_COUPONS']);
            } catch (\Exception $e) {
                Log::error("Lỗi SQL coupon: " . $e->getMessage());
                return response()->json(['reply' => 'Lỗi dữ liệu khi tìm coupon.', 'source' => 'DB_ERROR']);
            }
        }

        // 6. Giá cao nhất
        $highPriceKeywords = ['gia cao nhat', 'dat nhat', 'sp cao nhat'];
        if (Str::contains($message, $highPriceKeywords)) {
            try {
                $highProducts = Product::select('product_id', 'product_name', 'price')
                    ->orderBy('price', 'desc')
                    ->limit(5)
                    ->get();

                $products = $highProducts->map(function ($p) {
                    return [
                        'product_id' => $p->product_id,
                        'product_name' => $p->product_name,
                        'price' => $p->price
                    ];
                })->toArray();
                $list = $highProducts->map(function ($p, $i) {
                    return ($i + 1) . ". " . $p->product_name . " (" . number_format($p->price, 0, ',', '.') . " VNĐ)";
                })->implode("\n");
                $reply = "Top 5 sản phẩm giá cao:\n\n" . $list;
                return response()->json(['reply' => $reply, 'products' => $products, 'source' => 'DB_QUERY_HIGH_PRICE']);
            } catch (\Exception $e) {
                Log::error("Lỗi SQL giá cao: " . $e->getMessage());
                return response()->json(['reply' => 'Lỗi dữ liệu khi tìm sản phẩm giá cao.', 'source' => 'DB_ERROR']);
            }
        }

        // 7. Sản phẩm trong khoảng giá X-Y
        if (preg_match_all('/(\d+)[^\d]+(\d+)/', $userMessage, $m)) {
            try {
                $min = (int)$m[1][0] * 1000;
                $max = (int)$m[2][0] * 1000;
                $rangeProducts = Product::select('product_id', 'product_name', 'price')
                    ->whereBetween('price', [$min, $max])
                    ->get();

                if ($rangeProducts->isEmpty()) {
                    $reply = "Không tìm thấy sản phẩm trong tầm giá " . number_format($min, 0, ',', '.') . " - " . number_format($max, 0, ',', '.') . " VNĐ.";
                    return response()->json(['reply' => $reply, 'source' => 'DB_QUERY_RANGE_PRICE']);
                } else {
                    $products = $rangeProducts->map(function ($p) {
                        return [
                            'product_id' => $p->product_id,
                            'product_name' => $p->product_name,
                            'price' => $p->price
                        ];
                    })->toArray();
                    $list = $rangeProducts->map(function ($p) {
                        return $p->product_name . " (" . number_format($p->price, 0, ',', '.') . " VNĐ)";
                    })->implode("\n");
                    $reply = "Các sản phẩm trong tầm giá " . number_format($min, 0, ',', '.') . " - " . number_format($max, 0, ',', '.') . ":\n\n" . $list;
                    return response()->json(['reply' => $reply, 'products' => $products, 'source' => 'DB_QUERY_RANGE_PRICE']);
                }
            } catch (\Exception $e) {
                Log::error("Lỗi SQL khoảng giá: " . $e->getMessage());
                return response()->json(['reply' => 'Lỗi dữ liệu khi tìm sản phẩm trong khoảng giá.', 'source' => 'DB_ERROR']);
            }
        }

        // 8. Liên hệ shop
        $contactKeywords = ['lien he', 'ho tro', 'support'];
        if (Str::contains($message, $contactKeywords)) {
            $reply = "Bạn có thể liên hệ với shop qua:\n📞 SĐT: 0961208936\n📧 Email: shop@example.com\n🏠 Địa chỉ: Hà Nội";
            return response()->json(['reply' => $reply, 'source' => 'STATIC_CONTACT']);
        }

        // 9. Danh sách thương hiệu (brands)
        $brandKeywords = ['thuong hieu', 'brand', 'cac thuong hieu', 'cac hang'];
        if (Str::contains($message, $brandKeywords)) {
            try {
                $brands = Brand::select('brand_name')->get();

                if ($brands->isEmpty()) {
                    $reply = "Hiện chưa có thương hiệu nào.";
                } else {
                    $list = $brands->map(function ($b, $i) {
                        return ($i + 1) . ". " . $b->brand_name;
                    })->implode("\n");
                    $reply = "Các thương hiệu chính:\n\n" . $list;
                }
                return response()->json(['reply' => $reply, 'source' => 'DB_QUERY_BRANDS']);
            } catch (\Exception $e) {
                Log::error("Lỗi SQL thương hiệu: " . $e->getMessage());
                return response()->json(['reply' => 'Lỗi dữ liệu khi tìm thương hiệu.', 'source' => 'DB_ERROR']);
            }
        }

        // 10. Sản phẩm theo thương hiệu (nếu chứa tên thương hiệu)
        try {
            $brands = Brand::all();
            foreach ($brands as $brand) {
                if (Str::contains($message, strtolower(Str::ascii($brand->brand_name)))) {
                    $products = Product::select('product_id', 'product_name', 'price')
                        ->where('brand_id', $brand->brand_id)
                        ->get();
                    if ($products->isEmpty()) {
                        $reply = "Không tìm thấy sản phẩm cho thương hiệu " . $brand->brand_name . ".";
                        return response()->json(['reply' => $reply, 'source' => 'DB_QUERY_PRODUCT_BY_BRAND']);
                    } else {
                        $productList = $products->map(function ($p) {
                            return [
                                'product_id' => $p->product_id,
                                'product_name' => $p->product_name,
                                'price' => $p->price
                            ];
                        })->toArray();
                        $list = $products->map(function ($p, $i) {
                            return ($i + 1) . ". " . $p->product_name . " (Giá: " . number_format($p->price, 0, ',', '.') . " VNĐ)";
                        })->implode("\n");
                        $reply = "Sản phẩm của thương hiệu " . $brand->brand_name . ":\n\n" . $list;
                        return response()->json(['reply' => $reply, 'products' => $productList, 'source' => 'DB_QUERY_PRODUCT_BY_BRAND']);
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error("Lỗi SQL sản phẩm theo thương hiệu: " . $e->getMessage());
        }

        // 11. Sản phẩm theo danh mục (nếu chứa tên danh mục)
        try {
            $categories = Category::all();
            foreach ($categories as $category) {
                if (Str::contains($message, strtolower(Str::ascii($category->category_name)))) {
                    $products = Product::select('product_id', 'product_name', 'price')
                        ->where('category_id', $category->category_id)
                        ->get();
                    if ($products->isEmpty()) {
                        $reply = "Không tìm thấy sản phẩm trong danh mục " . $category->category_name . ".";
                        return response()->json(['reply' => $reply, 'source' => 'DB_QUERY_PRODUCT_BY_CATEGORY']);
                    } else {
                        $productList = $products->map(function ($p) {
                            return [
                                'product_id' => $p->product_id,
                                'product_name' => $p->product_name,
                                'price' => $p->price
                            ];
                        })->toArray();
                        $list = $products->map(function ($p, $i) {
                            return ($i + 1) . ". " . $p->product_name . " (Giá: " . number_format($p->price, 0, ',', '.') . " VNĐ)";
                        })->implode("\n");
                        $reply = "Sản phẩm trong danh mục " . $category->category_name . ":\n\n" . $list;
                        return response()->json(['reply' => $reply, 'products' => $productList, 'source' => 'DB_QUERY_PRODUCT_BY_CATEGORY']);
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error("Lỗi SQL sản phẩm theo danh mục: " . $e->getMessage());
        }

        // 12. Sản phẩm đánh giá cao nhất (comments)
        $topRatedKeywords = ['san pham danh gia cao', 'top rating', 'danh gia tot', 'san pham tot nhat'];
        if (Str::contains($message, $topRatedKeywords)) {
            try {
                $topRatedProducts = DB::table('products')
                    ->join('comments', 'products.product_id', '=', 'comments.product_id')
                    ->select('products.product_id', 'products.product_name', 'products.price', DB::raw('AVG(comments.rating) as avg_rating'))
                    ->groupBy('products.product_id', 'products.product_name', 'products.price')
                    ->orderByDesc('avg_rating')
                    ->limit(5)
                    ->get();

                if ($topRatedProducts->isEmpty()) {
                    $reply = "Chưa có đánh giá sản phẩm nào.";
                    return response()->json(['reply' => $reply, 'source' => 'DB_QUERY_TOP_RATED']);
                } else {
                    $products = $topRatedProducts->map(function ($p) {
                        return [
                            'product_id' => $p->product_id,
                            'product_name' => $p->product_name,
                            'price' => $p->price
                        ];
                    })->toArray();
                    $list = $topRatedProducts->map(function ($p, $i) {
                        return ($i + 1) . ". " . $p->product_name . " (Đánh giá trung bình: " . number_format($p->avg_rating, 1) . "/5)";
                    })->implode("\n");
                    $reply = "Top 5 sản phẩm đánh giá cao:\n\n" . $list;
                    return response()->json(['reply' => $reply, 'products' => $products, 'source' => 'DB_QUERY_TOP_RATED']);
                }
            } catch (\Exception $e) {
                Log::error("Lỗi SQL đánh giá cao: " . $e->getMessage());
                return response()->json(['reply' => 'Lỗi dữ liệu khi tìm sản phẩm đánh giá cao.', 'source' => 'DB_ERROR']);
            }
        }

        // 13. Sản phẩm mới nhất (dựa trên created_at)
        $newProductKeywords = ['san pham moi', 'new product', 'san pham moi nhat', 'hang moi ve'];
        if (Str::contains($message, $newProductKeywords)) {
            try {
                $newProducts = Product::select('product_id', 'product_name', 'price')
                    ->orderBy('created_at', 'desc')
                    ->limit(5)
                    ->get();

                if ($newProducts->isEmpty()) {
                    $reply = "Không có sản phẩm mới nào.";
                    return response()->json(['reply' => $reply, 'source' => 'DB_QUERY_NEW_PRODUCTS']);
                } else {
                    $products = $newProducts->map(function ($p) {
                        return [
                            'product_id' => $p->product_id,
                            'product_name' => $p->product_name,
                            'price' => $p->price
                        ];
                    })->toArray();
                    $list = $newProducts->map(function ($p, $i) {
                        return ($i + 1) . ". " . $p->product_name . " (Giá: " . number_format($p->price, 0, ',', '.') . " VNĐ)";
                    })->implode("\n");
                    $reply = "Top 5 sản phẩm mới nhất:\n\n" . $list;
                    return response()->json(['reply' => $reply, 'products' => $products, 'source' => 'DB_QUERY_NEW_PRODUCTS']);
                }
            } catch (\Exception $e) {
                Log::error("Lỗi SQL sản phẩm mới: " . $e->getMessage());
                return response()->json(['reply' => 'Lỗi dữ liệu khi tìm sản phẩm mới.', 'source' => 'DB_ERROR']);
            }
        }

        // 14. Sản phẩm nổi bật (is_featured)
        $featuredKeywords = ['san pham noi bat', 'featured', 'san pham dac biet', 'hang noi bat'];
        if (Str::contains($message, $featuredKeywords)) {
            try {
                $featuredProducts = Product::select('product_id', 'product_name', 'price')
                    ->where('is_featured', 1)
                    ->limit(5)
                    ->get();

                if ($featuredProducts->isEmpty()) {
                    $reply = "Hiện chưa có sản phẩm nổi bật nào.";
                    return response()->json(['reply' => $reply, 'source' => 'DB_QUERY_FEATURED']);
                } else {
                    $products = $featuredProducts->map(function ($p) {
                        return [
                            'product_id' => $p->product_id,
                            'product_name' => $p->product_name,
                            'price' => $p->price
                        ];
                    })->toArray();
                    $list = $featuredProducts->map(function ($p, $i) {
                        return ($i + 1) . ". " . $p->product_name . " (Giá: " . number_format($p->price, 0, ',', '.') . " VNĐ)";
                    })->implode("\n");
                    $reply = "Các sản phẩm nổi bật:\n\n" . $list;
                    return response()->json(['reply' => $reply, 'products' => $products, 'source' => 'DB_QUERY_FEATURED']);
                }
            } catch (\Exception $e) {
                Log::error("Lỗi SQL sản phẩm nổi bật: " . $e->getMessage());
                return response()->json(['reply' => 'Lỗi dữ liệu khi tìm sản phẩm nổi bật.', 'source' => 'DB_ERROR']);
            }
        }

        // 15. Tìm kiếm sản phẩm bằng từ khóa (nếu chứa tên sản phẩm)
        try {
            $products = Product::select('product_id', 'product_name', 'price', 'description', 'stock_quantity', 'sold')
                ->where('product_name', 'like', "%$userMessage%")
                ->get();
            if ($products->isNotEmpty()) {
                $productList = $products->map(function ($p) {
                    return [
                        'product_id' => $p->product_id,
                        'product_name' => $p->product_name,
                        'price' => $p->price
                    ];
                })->toArray();
                $reply = $products->map(function ($p) {
                    return "Tên: " . $p->product_name . "\nGiá: " . number_format($p->price, 0, ',', '.') . " VNĐ\nMô tả: " . ($p->description ?? 'Không có') . "\nTồn kho: " . $p->stock_quantity . "\nBán: " . $p->sold;
                })->implode("\n\n");
                return response()->json(['reply' => "Kết quả tìm kiếm sản phẩm:\n\n" . $reply, 'products' => $productList, 'source' => 'DB_QUERY_PRODUCT_SEARCH']);
            }
        } catch (\Exception $e) {
            Log::error("Lỗi SQL tìm kiếm sản phẩm: " . $e->getMessage());
        }

        // 16. Nhà cung cấp (suppliers)
        $supplierKeywords = ['nha cung cap', 'supplier', 'cac nha cung cap'];
        if (Str::contains($message, $supplierKeywords)) {
            try {
                $suppliers = Supplier::all();

                if ($suppliers->isEmpty()) {
                    $reply = "Chưa có thông tin nhà cung cấp.";
                } else {
                    $list = $suppliers->map(function ($s, $i) {
                        return ($i + 1) . ". " . $s->supplier_name . " (Liên hệ: " . ($s->contact_info ?? 'Không có') . ")";
                    })->implode("\n");
                    $reply = "Các nhà cung cấp:\n\n" . $list;
                }
                return response()->json(['reply' => $reply, 'source' => 'DB_QUERY_SUPPLIERS']);
            } catch (\Exception $e) {
                Log::error("Lỗi SQL nhà cung cấp: " . $e->getMessage());
                return response()->json(['reply' => 'Lỗi dữ liệu khi tìm nhà cung cấp.', 'source' => 'DB_ERROR']);
            }
        }

        // 17. Sản phẩm tồn kho thấp
        $lowStockKeywords = ['san pham sap het', 'ton kho thap', 'hang sap het'];
        if (Str::contains($message, $lowStockKeywords)) {
            try {
                $lowStockProducts = Product::select('product_id', 'product_name', 'price', 'stock_quantity')
                    ->where('stock_quantity', '<', 100)
                    ->orderBy('stock_quantity', 'asc')
                    ->limit(5)
                    ->get();

                if ($lowStockProducts->isEmpty()) {
                    $reply = "Tất cả sản phẩm đều còn hàng đầy đủ.";
                    return response()->json(['reply' => $reply, 'source' => 'DB_QUERY_LOW_STOCK']);
                } else {
                    $products = $lowStockProducts->map(function ($p) {
                        return [
                            'product_id' => $p->product_id,
                            'product_name' => $p->product_name,
                            'price' => $p->price
                        ];
                    })->toArray();
                    $list = $lowStockProducts->map(function ($p, $i) {
                        return ($i + 1) . ". " . $p->product_name . " (Tồn kho: " . $p->stock_quantity . ")";
                    })->implode("\n");
                    $reply = "Top 5 sản phẩm tồn kho thấp:\n\n" . $list;
                    return response()->json(['reply' => $reply, 'products' => $products, 'source' => 'DB_QUERY_LOW_STOCK']);
                }
            } catch (\Exception $e) {
                Log::error("Lỗi SQL tồn kho thấp: " . $e->getMessage());
                return response()->json(['reply' => 'Lỗi dữ liệu khi tìm sản phẩm tồn kho thấp.', 'source' => 'DB_ERROR']);
            }
        }

        // Fallback sang Gemini AI
        return response()->json([
            'reply' => 'Xin lỗi, tôi chưa có thông tin này. Bạn có thể hỏi chi tiết hơn!',
            'source' => 'FALLBACK'
        ]);
    }
}