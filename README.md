# PeakVL.com — Website Thương Mại Điện Tử

Dự án **PeakVL.com** là website TMĐT (Laravel + MySQL + Vite + TailwindCSS). Tài liệu này hướng dẫn cài đặt nhanh trên máy local, **bao gồm các lệnh tạo `node_modules` và `.env`**.

---

## 1) Yêu cầu hệ thống

-   **PHP** ≥ 8.1 (khuyến nghị 8.2+)
-   **Composer** ≥ 2.5
-   **Node.js** LTS 18/20 + **npm**
-   **MySQL/MariaDB**
-   **Git**

Kiểm tra nhanh:

```bash
php -v
composer -V
node -v
npm -v
```

---

## 2) Bắt đầu nhanh (Quick Start)

```bash
# Clone
git clone https://github.com/WEBSITE-DH12C3group3/website_dodunghoctap.git
cd website_dodunghoctap
```

```bash
#  Required instructions
# Front-end
npm ci
npm run build             # sinh public/build với file có hash (immutable)

# Laravel
cp .env.example .env      # nếu chưa có
php artisan key:generate  # nếu APP_KEY trống
# đặt .env:
APP_ENV=production
APP_DEBUG=false

composer install --no-dev --optimize-autoloader
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize:clear    # clear rác cũ, giữ cache mới

```

---

## 6) Công nghệ chính

-   **Backend**: Laravel (API + Blade/Inertia tùy dự án), Policy/Middleware, Validation
-   **Frontend**: Vite, TailwindCSS, (có thể kèm Inertia/Vue/React nếu dùng)
-   **Database**: MySQL/MariaDB
-   **Auth**: Laravel Auth/Passport/Sanctum (tùy cấu hình)
-   **Assets**: Vite build, `public/` + `storage/app/public`

---

## 9) Giấy phép & liên hệ

-   Bản quyền © PeakVL.
-   Vấn đề kỹ thuật/bugs: tạo **Issue** trên repo hoặc liên hệ đội phát triển.
