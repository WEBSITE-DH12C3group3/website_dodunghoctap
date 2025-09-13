<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;

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
            $categories = DB::table('categories')
                ->select('category_id', 'category_name')
                ->orderBy('category_name')
                ->get();

            // Nếu bạn có bảng cart_items theo user:
            $cartCount = auth()->check()
                ? DB::table('cart_items')->where('user_id', auth()->id())->sum('quantity')
                : collect(session('cart') ?? [])->sum('qty'); // fallback session

            $view->with(compact('categories', 'cartCount'));
        });
    }
}
