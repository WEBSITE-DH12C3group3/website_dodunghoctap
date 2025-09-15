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
# 2.1 Clone dự án
git clone https://github.com/<your-org-or-user>/peakvl.com.git
cd peakvl.com

# 2.2 CÀI PHP DEPENDENCIES (tạo thư mục vendor/)
composer install

# 2.3 TẠO FILE .env
# Cách 1: copy từ .env.example (khuyến nghị)
cp .env.example .env        # macOS/Linux/PowerShell
# Windows CMD:
# copy .env.example .env

# 2.4 Tạo APP_KEY
php artisan key:generate

# 2.5 CÀI JS DEPENDENCIES (TẠO node_modules/)
# Ưu tiên npm ci nếu có package-lock.json
npm ci
# hoặc (nếu không có lockfile)
# npm install

# 2.6 Cấu hình DB trong .env, tạo database 'peakvl' rồi migrate/seed
php artisan migrate --seed   # nếu có seeder
# hoặc chỉ migrate
# php artisan migrate

# 2.7 Link storage (nếu dự án dùng upload)
php artisan storage:link

# 2.8 Chạy server Laravel + Vite (2 tab)
php artisan serve
npm run dev
```

> **Ghi chú** (XAMPP mặc định): MySQL user `root`, mật khẩu rỗng. Tạo database `peakvl` trong phpMyAdmin trước khi migrate.

---

## 3) Các lệnh “bỏ túi” (Cheat‑sheet)

**TẠO `node_modules`**

```bash
npm ci        # chuẩn/nhanh (khi có package-lock.json)
# hoặc
npm install   # nếu không có lockfile
```

**TẠO `.env`**

```bash
cp .env.example .env  # macOS/Linux/PowerShell
# Windows CMD:
# copy .env.example .env
php artisan key:generate
```

**Chạy dev**

```bash
php artisan serve
npm run dev
```

**Build production**

```bash
npm run build
php artisan optimize        # gom cache view/config/route
```

**Dọn cache khi lỗi lạ**

```bash
php artisan optimize:clear
composer dump-autoload
```

**Migrate/Seed**

```bash
php artisan migrate
php artisan migrate --seed
```

**Storage symlink (media/upload)**

```bash
php artisan storage:link
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
