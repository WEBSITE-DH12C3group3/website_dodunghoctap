<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ChatbotRule;
use App\Models\Product;
use App\Models\Category;
use App\Models\Coupon;
use Illuminate\Support\Facades\Http;
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
                    ->select('products.product_name', DB::raw('SUM(order_items.quantity) as total_sold'))
                    ->groupBy('products.product_id', 'products.product_name')
                    ->orderByDesc('total_sold')
                    ->limit(5)
                    ->get();

                if ($topProducts->isEmpty()) {
                    $reply = "Hiện tại chưa có dữ liệu sản phẩm bán chạy.";
                } else {
                    $list = $topProducts->map(function ($p, $i) {
                        return ($i + 1) . ". " . $p->product_name . " (Đã bán: " . $p->total_sold . ")";
                    })->implode("\n");
                    $reply = "Top 5 sản phẩm bán chạy:\n\n" . $list;
                }
                return response()->json(['reply' => $reply, 'source' => 'DB_QUERY_BEST_SELLER']);
            } catch (\Exception $e) {
                Log::error("Lỗi SQL bán chạy: " . $e->getMessage());
                return response()->json(['reply' => 'Lỗi dữ liệu khi tìm sản phẩm bán chạy.', 'source' => 'DB_ERROR']);
            }
        }

        // 3. Sản phẩm giá thấp nhất
        $lowPriceKeywords = ['gia thap nhat', 're nhat', 'mat hang re', 'gia thap'];
        if (Str::contains($message, $lowPriceKeywords)) {
            try {
                $lowPriceProducts = Product::orderBy('price', 'asc')->limit(5)->get();

                if ($lowPriceProducts->isEmpty()) {
                    $reply = "Không tìm thấy sản phẩm nào.";
                } else {
                    $list = $lowPriceProducts->map(function ($p, $i) {
                        return ($i + 1) . ". " . $p->product_name . " (Giá: " . number_format($p->price, 0, ',', '.') . " VNĐ)";
                    })->implode("\n");
                    $reply = "Top 5 sản phẩm giá rẻ:\n\n" . $list;
                }
                return response()->json(['reply' => $reply, 'source' => 'DB_QUERY_LOW_PRICE']);
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
    $highProducts = Product::orderBy('price', 'desc')->limit(5)->get();
    $reply = $highProducts->map(fn($p, $i) =>
        ($i+1).". ".$p->product_name." (".number_format($p->price,0,',','.')." VNĐ)"
    )->implode("\n");
    return response()->json(['reply' => "Top 5 sản phẩm giá cao:\n\n".$reply, 'source'=>'DB_QUERY_HIGH_PRICE']);
}

// 7. Sản phẩm trong khoảng giá X-Y
if (preg_match_all('/(\d+)[^\d]+(\d+)/', $userMessage, $m)) {
    $min = (int)$m[1][0]*1000;
    $max = (int)$m[2][0]*1000;
    $rangeProducts = Product::whereBetween('price', [$min,$max])->get();
    if ($rangeProducts->isNotEmpty()) {
        $reply = $rangeProducts->map(fn($p)=>$p->product_name." (".number_format($p->price,0,',','.')." VNĐ)")->implode("\n");
        return response()->json(['reply' => "Các sản phẩm trong tầm giá ".number_format($min,0,',','.')." - ".number_format($max,0,',','.').":\n\n".$reply, 'source'=>'DB_QUERY_RANGE_PRICE']);
    }
}

// 8. Liên hệ shop
$contactKeywords = ['lien he', 'ho tro', 'support'];
if (Str::contains($message, $contactKeywords)) {
    $reply = "Bạn có thể liên hệ với shop qua:\n📞 SĐT: 0961208936\n📧 Email: shop@example.com\n🏠 Địa chỉ: Hà Nội";
    return response()->json(['reply'=>$reply,'source'=>'STATIC_CONTACT']);
}


        // 6. Fallback sang Gemini AI
        return response()->json([
            'reply' => 'Xin lỗi, tôi chưa có thông tin này. Bạn có thể hỏi chi tiết hơn!',
            'source' => 'FALLBACK'
        ]);
    }
}
