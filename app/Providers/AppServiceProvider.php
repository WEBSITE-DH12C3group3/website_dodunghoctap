<?php

  namespace App\Providers;

  use Illuminate\Support\Facades\View;
  use Illuminate\Support\Facades\DB;
  use Illuminate\Support\ServiceProvider;
  use Illuminate\Support\Facades\Schema;
  use Illuminate\Support\Facades\Log;

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
                  try {
                      $cartCount = DB::table('cart_items')
                          ->where('user_id', auth()->id())
                          ->sum('quantity');
                      Log::info('Cart count from cart_items for user ' . auth()->id() . ': ' . $cartCount);
                  } catch (\Exception $e) {
                      Log::error('Error fetching cart_items: ' . $e->getMessage());
                  }
              } else {
                  $cartSession = session('cart', []);
                  if (is_array($cartSession)) {
                      try {
                          $cartCount = collect($cartSession)->sum(function ($item) {
                              return (int)($item['qty'] ?? $item['quantity'] ?? 1);
                          });
                          Log::info('Cart count from session: ' . $cartCount);
                      } catch (\Exception $e) {
                          Log::error('Error processing cart session: ' . $e->getMessage());
                      }
                  }
              }

              $view->with(compact('categories', 'cartCount'));
          });
      }
  }