<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        View::composer(['layouts.store', 'store.*', '*'], function ($view) {
            // Danh mục cho header/footer
            $categories = DB::table('categories')
                ->select('category_id', 'category_name')
                ->orderBy('category_name')
                ->get();

            // ĐẾM GIỎ: ưu tiên bảng cart_items nếu có, không thì lấy từ session 'cart'
            $cartCount = 0;

            if (Schema::hasTable('cart_items') && auth()->check()) {
                // Chỉ chạy nhánh này nếu bạn thực sự tạo bảng cart_items về sau
                $cartCount = DB::table('cart_items')
                    ->where('user_id', auth()->id())
                    ->sum('quantity');
            } else {
                // Lấy từ session 'cart' với nhiều khả năng cấu trúc khác nhau
                $cartSession = session('cart', []);
                if (is_array($cartSession)) {
                    // Ví dụ: [['product_id'=>1,'qty'=>2], ...] hoặc ['1'=>['quantity'=>2], ...]
                    $cartCount = collect($cartSession)->sum(function ($item) {
                        if (is_array($item)) {
                            return (int)($item['qty'] ?? $item['quantity'] ?? 1);
                        }
                        // Trường hợp session lưu trực tiếp số lượng
                        return (int)$item;
                    });
                }
            }

            $view->with(compact('categories', 'cartCount'));
        });
    }
}
