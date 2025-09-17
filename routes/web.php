<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Store\HomeController;
use App\Http\Controllers\Store\ProfileController;
use App\Http\Controllers\GoogleLoginController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\PurchaseOrderController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\StatsController;

use App\Http\Controllers\Admin\OrderController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Store\ProductShowController;
use App\Http\Controllers\Store\CartController;

require __DIR__ . '/auth.php';


Route::get('/', [HomeController::class, 'index'])->name('store.index');

Route::get('/categories', fn() => 'all categories')->name('store.categories.index');
Route::get('/category/{id}', fn($id) => "category $id")->name('store.category');

Route::get('/product/{id}', fn($id) => "product $id")->name('store.product.show');
Route::get('/cart/add/{id}', fn($id) => "add $id")->name('cart.add');


Route::get('/product/{id}', [ProductShowController::class, 'show'])->name('store.product.show');
Route::get('/cart', fn() => 'cart')->name('cart.index');
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::post('/buy-now', [CartController::class, 'buyNow'])->name('cart.buy_now');
Route::get('/products/new', fn() => 'new products')->name('store.products.new');
Route::get('/products/best', fn() => 'best sellers')->name('store.products.best');
Route::get('/products/featured', fn() => 'featured products')->name('store.products.featured');
Route::get('/search', fn() => 'search')->name('store.search');


Route::get('/auth/google', [GoogleLoginController::class, 'redirectToGoogle'])->name('google.redirect');
Route::get('/auth/google/callback', [GoogleLoginController::class, 'handleGoogleCallback'])->name('google.callback');

// Route::middleware('guest')->group(function () {
//     Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
//     Route::post('/login', [AuthController::class, 'login'])->name('login.post');
//     Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
//     Route::post('/register', [AuthController::class, 'register'])->name('register.post');
//     Route::get('/profile',   [ProfileController::class, 'index'])->name('profile.index');
//     Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
// });
Route::middleware('auth')->group(function () {
    Route::get('/profile',  [ProfileController::class, 'index'])->name('profile.index');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
});
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->middleware('role:admin|employee')->name('dashboard');

    Route::prefix('admin')->middleware('role:admin|employee')->group(function () {
        Route::get('/products', [ProductController::class, 'index'])
            ->middleware('permission:manage_products')->name('admin.products');
        Route::get('/products/create', [ProductController::class, 'create'])
            ->middleware('permission:manage_products')->name('admin.products.create');
        Route::post('/products', [ProductController::class, 'store'])
            ->middleware('permission:manage_products')->name('admin.products.store');
        Route::get('/products/edit/{id}', [ProductController::class, 'edit'])
            ->middleware('permission:manage_products')->name('admin.products.edit');
        Route::put('/products/{id}', [ProductController::class, 'update'])
            ->middleware('permission:manage_products')->name('admin.products.update');
        Route::delete('/products/{id}', [ProductController::class, 'destroy'])
            ->middleware('permission:manage_products')->name('admin.products.destroy');

        Route::get('/categories', [CategoryController::class, 'index'])
            ->middleware('permission:manage_categories')->name('admin.categories');
        Route::get('/categories/create', [CategoryController::class, 'create'])
            ->middleware('permission:manage_categories')->name('admin.categories.create');
        Route::post('/categories', [CategoryController::class, 'store'])
            ->middleware('permission:manage_categories')->name('admin.categories.store');
        Route::get('/categories/edit/{id}', [CategoryController::class, 'edit'])
            ->middleware('permission:manage_categories')->name('admin.categories.edit');
        Route::put('/categories/{id}', [CategoryController::class, 'update'])
            ->middleware('permission:manage_categories')->name('admin.categories.update');
        Route::delete('/categories/{id}', [CategoryController::class, 'destroy'])
            ->middleware('permission:manage_categories')->name('admin.categories.destroy');

        Route::get('/orders', [OrderController::class, 'index'])
            ->middleware('permission:manage_orders')->name('admin.orders');
        Route::get('/orders/show/{id}', [OrderController::class, 'show'])
            ->middleware('permission:manage_orders')->name('admin.orders.show');
        Route::get('/orders/edit/{id}', [OrderController::class, 'edit'])
            ->middleware('permission:manage_orders')->name('admin.orders.edit');
        Route::put('/orders/{id}', [OrderController::class, 'update'])
            ->middleware('permission:manage_orders')->name('admin.orders.update');
        Route::delete('/orders/{id}', [OrderController::class, 'destroy'])
            ->middleware('permission:manage_orders')->name('admin.orders.destroy');

        Route::get('/purchase_orders', [PurchaseOrderController::class, 'index'])
            ->middleware('permission:manage_purchases')->name('admin.purchase_orders');
        Route::get('/purchase_orders/create', [PurchaseOrderController::class, 'create'])
            ->middleware('permission:manage_purchases')->name('admin.purchase_orders.create');
        Route::post('/purchase_orders', [PurchaseOrderController::class, 'store'])
            ->middleware('permission:manage_purchases')->name('admin.purchase_orders.store');
        Route::get('/purchase_orders/show/{id}', [PurchaseOrderController::class, 'show'])
            ->middleware('permission:manage_purchases')->name('admin.purchase_orders.show');
        Route::get('/purchase_orders/edit/{id}', [PurchaseOrderController::class, 'edit'])
            ->middleware('permission:manage_purchases')->name('admin.purchase_orders.edit');
        Route::put('/purchase_orders/{id}', [PurchaseOrderController::class, 'update'])
            ->middleware('permission:manage_purchases')->name('admin.purchase_orders.update');
        Route::delete('/purchase_orders/{id}', [PurchaseOrderController::class, 'destroy'])
            ->middleware('permission:manage_purchases')->name('admin.purchase_orders.destroy');

        Route::get('/users', [UserController::class, 'index'])
            ->middleware('permission:manage_users')->name('admin.users');
        Route::get('/stats', [StatsController::class, 'index'])
            ->middleware('permission:view_statistics')->name('admin.stats');
    });
});

Route::post('/newsletter/subscribe', function (Request $request) {
    $data = $request->validate(['email' => 'required|email:rfc,dns']);
    try {
        if (\Illuminate\Support\Facades\Schema::hasTable('newsletter_subscribers')) {
            \Illuminate\Support\Facades\DB::table('newsletter_subscribers')->updateOrInsert(
                ['email' => $data['email']],
                ['created_at' => now()]
            );
        }
        return back()->with('success', 'Đã đăng ký nhận tin!');
    } catch (\Throwable $e) {
        return back()->with('error', 'Không thể đăng ký lúc này.');
    }
})->name('store.newsletter.subscribe');

Route::name('store.page.')->group(function () {
    Route::view('/huong-dan-mua-hang', 'store.pages.stub', ['title' => 'Hướng dẫn mua hàng'])->name('buying_guide');
    Route::view('/huong-dan-thanh-toan', 'store.pages.stub', ['title' => 'Hướng dẫn thanh toán'])->name('payment_guide');
    Route::view('/chinh-sach-giao-hang', 'store.pages.stub', ['title' => 'Chính sách giao hàng'])->name('shipping_policy');
    Route::view('/chinh-sach-doi-tra', 'store.pages.stub', ['title' => 'Chính sách đổi trả & hoàn tiền'])->name('return_policy');
    Route::view('/khach-hang-than-thiet', 'store.pages.stub', ['title' => 'Khách hàng thân thiết'])->name('loyalty');
    Route::view('/khach-hang-uu-tien', 'store.pages.stub', ['title' => 'Khách hàng ưu tiên'])->name('priority');
    Route::view('/gioi-thieu', 'store.pages.stub', ['title' => 'Giới thiệu'])->name('about');
    Route::view('/dich-vu-in-an-quang-cao', 'store.pages.stub', ['title' => 'Dịch vụ in ấn quảng cáo'])->name('ads_service');
    Route::view('/bao-mat-chung', 'store.pages.stub', ['title' => 'Chính sách bảo mật chung'])->name('privacy');
    Route::view('/bao-mat-thong-tin-ca-nhan', 'store.pages.stub', ['title' => 'Bảo mật thông tin cá nhân'])->name('privacy_personal');
    Route::view('/lien-he', 'store.pages.stub', ['title' => 'Thông tin liên hệ'])->name('contact');
    Route::view('/affiliate', 'store.pages.stub', ['title' => 'Chương trình Affiliate'])->name('affiliate');
});