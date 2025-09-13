<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Store\HomeController;
use Illuminate\Http\Request;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/categories', fn() => 'all categories')->name('store.categories.index');
Route::get('/category/{id}', fn($id) => "category $id")->name('store.category');
Route::get('/product/{id}', fn($id) => "product $id")->name('store.product.show');
Route::get('/cart/add/{id}', fn($id) => "add $id")->name('cart.add');

Route::get('/products/new', fn() => 'new products')->name('store.products.new');
Route::get('/products/best', fn() => 'best sellers')->name('store.products.best');
Route::get('/products/featured', fn() => 'featured products')->name('store.products.featured');
Route::get('/search',      fn() => 'search')->name('store.search');
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');

    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Ví dụ khu admin: chỉ admin hoặc employee
    Route::prefix('admin')->middleware('role:admin|employee')->group(function () {

        // Ví dụ quản lý sản phẩm: cần permission manage_products
        Route::get('/products', function () {
            return 'Trang quản lý sản phẩm (đã có permission: manage_products)';
        })->middleware('permission:manage_products')->name('admin.products');

        // Ví dụ quản lý user
        Route::get('/users', function () {
            return 'Trang quản lý người dùng (permission: manage_users)';
        })->middleware('permission:manage_users')->name('admin.users');

        // Ví dụ thống kê
        Route::get('/stats', function () {
            return 'Trang thống kê (permission: view_statistics)';
        })->middleware('permission:view_statistics')->name('admin.stats');

        // Ví dụ đơn hàng
        Route::get('/orders', function () {
            return 'Trang đơn hàng (permission: manage_orders)';
        })->middleware('permission:manage_orders')->name('admin.orders');
    });
});

Route::post('/newsletter/subscribe', function (Request $request) {
    $data = $request->validate(['email' => 'required|email:rfc,dns']);
    // Nếu có bảng newsletter_subscribers thì lưu, không có thì chỉ flash thông báo.
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
    Route::view('/huong-dan-mua-hang',     'store.pages.stub', ['title' => 'Hướng dẫn mua hàng'])->name('buying_guide');
    Route::view('/huong-dan-thanh-toan',   'store.pages.stub', ['title' => 'Hướng dẫn thanh toán'])->name('payment_guide');
    Route::view('/chinh-sach-giao-hang',   'store.pages.stub', ['title' => 'Chính sách giao hàng'])->name('shipping_policy');
    Route::view('/chinh-sach-doi-tra',     'store.pages.stub', ['title' => 'Chính sách đổi trả & hoàn tiền'])->name('return_policy');
    Route::view('/khach-hang-than-thiet',  'store.pages.stub', ['title' => 'Khách hàng thân thiết'])->name('loyalty');
    Route::view('/khach-hang-uu-tien',     'store.pages.stub', ['title' => 'Khách hàng ưu tiên'])->name('priority');

    Route::view('/gioi-thieu',             'store.pages.stub', ['title' => 'Giới thiệu'])->name('about');
    Route::view('/dich-vu-in-an-quang-cao', 'store.pages.stub', ['title' => 'Dịch vụ in ấn quảng cáo'])->name('ads_service');
    Route::view('/bao-mat-chung',          'store.pages.stub', ['title' => 'Chính sách bảo mật chung'])->name('privacy');
    Route::view('/bao-mat-thong-tin-ca-nhan', 'store.pages.stub', ['title' => 'Bảo mật thông tin cá nhân'])->name('privacy_personal');
    Route::view('/lien-he',                'store.pages.stub', ['title' => 'Thông tin liên hệ'])->name('contact');
    Route::view('/affiliate',              'store.pages.stub', ['title' => 'Chương trình Affiliate'])->name('affiliate');
});
