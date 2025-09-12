<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Store\HomeController;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/product/{id}-{slug?}', [HomeController::class, 'show'])->name('product.show');

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
