<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class CustomExcelServiceProvider extends ServiceProvider
{
    public function register()
    {
        // Không cần đăng ký Excel class thủ công, vì maatwebsite/excel tự động xử lý
    }

    public function boot()
    {
        // Xuất bản cấu hình nếu cần
        $this->publishes([
            base_path('vendor/maatwebsite/excel/config/excel.php') => config_path('excel.php'),
        ], 'config');
    }
}