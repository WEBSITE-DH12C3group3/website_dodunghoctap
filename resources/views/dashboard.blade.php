@extends('layouts.app')

@section('content')
<div class="space-y-6">

    <h1 class="text-3xl font-bold tracking-tight text-slate-900 dark:text-slate-100 mb-6">Trang Quản Trị (Admin Dashboard)</h1>

    <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">

        {{-- User Info Card --}}
        <div class="lg:col-span-1 rounded-2xl bg-white/80 dark:bg-slate-900/50 backdrop-blur p-6 shadow ring-1 ring-slate-900/5 dark:ring-white/10">
            <h2 class="text-xl font-semibold mb-2 text-slate-800 dark:text-slate-100">Xin chào, {{ auth()->user()->full_name }} 👋</h2>
            <p class="text-sm text-slate-600 dark:text-slate-300 mb-1">Email: **{{ auth()->user()->email }}**</p>
            <p class="text-sm text-slate-600 dark:text-slate-300">Vai trò:
                <span class="ml-2 rounded-lg px-2 py-0.5 text-xs font-semibold bg-brand-600 text-white dark:bg-brand-700/80">
                    {{ optional(auth()->user()->role)->role_name }}
                </span>
            </p>
            <p class="mt-4 text-xs text-slate-500 dark:text-slate-400">Bạn đang ở khu vực quản trị. Vui lòng sử dụng các chức năng bên dưới hoặc thanh điều hướng để bắt đầu.</p>
        </div>
        
        {{-- Total Users/Customers Card (Placeholder - Requires data from controller) --}}
        {{-- Uncomment and replace 'X' with actual data passed from the backend if available --}}
        {{-- <div class="rounded-2xl bg-white/80 dark:bg-slate-900/50 backdrop-blur p-6 shadow ring-1 ring-slate-900/5 dark:ring-white/10 flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-slate-600 dark:text-slate-300">Tổng số Khách hàng</p>
                <p class="text-3xl font-bold text-slate-800 dark:text-slate-100 mt-1">12,345</p>
            </div>
            <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-indigo-500 to-indigo-600 text-white grid place-items-center shadow">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor"><path d="M7 11a4 4 0 110-8 4 4 0 010 8zm10 0a4 4 0 110-8 4 4 0 010 8zM2 20a5 5 0 015-5h2a5 5 0 015 5v1H2v-1zm13 0a6.97 6.97 0 00-2.05-4.95A4.98 4.98 0 0120 20v1h-5v-1z" /></svg>
            </div>
        </div> --}}

        {{-- Other Dashboard Summary Cards (Placeholders) --}}
        <div class="rounded-2xl bg-white/80 dark:bg-slate-900/50 backdrop-blur p-6 shadow ring-1 ring-slate-900/5 dark:ring-white/10 flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-slate-600 dark:text-slate-300">Đơn hàng trong tháng</p>
                <p class="text-3xl font-bold text-slate-800 dark:text-slate-100 mt-1">256</p>
            </div>
            <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-pink-500 to-pink-600 text-white grid place-items-center shadow">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor"><path d="M3 6h18v2H3V6Zm2 4h14l-1 8H6l-1-8Zm5 2v4h2v-4H10Zm4 0v4h2v-4h-2Z" /></svg>
            </div>
        </div>

        <div class="rounded-2xl bg-white/80 dark:bg-slate-900/50 backdrop-blur p-6 shadow ring-1 ring-slate-900/5 dark:ring-white/10 flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-slate-600 dark:text-slate-300">Tổng sản phẩm</p>
                <p class="text-3xl font-bold text-slate-800 dark:text-slate-100 mt-1">1,500</p>
            </div>
            <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-teal-500 to-teal-600 text-white grid place-items-center shadow">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor"><path d="M4 4h16v2H4V4zm2 4h12v12H6V8zm2 2v8h8v-8H8z" /></svg>
            </div>
        </div>

    </div>

    {{-- Quick Actions Section --}}
    <h2 class="text-2xl font-semibold tracking-tight text-slate-900 dark:text-slate-100 mt-8 mb-4">Các Hành Động Nhanh</h2>
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">

        {{-- Product Management --}}
        @if(auth()->check() && auth()->user()->hasPermission('manage_products'))
        <a href="{{ route('admin.products') }}" class="rounded-2xl bg-white/80 dark:bg-slate-900/50 backdrop-blur p-6 shadow ring-1 ring-slate-900/5 dark:ring-white/10 hover:shadow-md hover:ring-brand-600/50 dark:hover:ring-brand-500 transition duration-200">
            <div class="flex items-center gap-4">
                <div class="h-12 w-12 rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-600 text-white grid place-items-center shadow-lg shadow-emerald-500/30">
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M3 4h18v2H3V4Zm2 4h14l-1 12H6L5 8Zm4 2v8h2v-8H9Zm4 0v8h2v-8h-2Z" />
                    </svg>
                </div>
                <div>
                    <h3 class="font-semibold text-slate-800 dark:text-slate-100">Quản lý Sản phẩm</h3>
                    <p class="text-sm text-slate-600 dark:text-slate-400">Tạo, sửa, xoá sản phẩm & thuộc tính</p>
                </div>
            </div>
        </a>
        @endif

        {{-- Order Management --}}
        @if(auth()->check() && auth()->user()->hasPermission('manage_orders'))
        <a href="{{ route('admin.orders') }}" class="rounded-2xl bg-white/80 dark:bg-slate-900/50 backdrop-blur p-6 shadow ring-1 ring-slate-900/5 dark:ring-white/10 hover:shadow-md hover:ring-brand-600/50 dark:hover:ring-brand-500 transition duration-200">
            <div class="flex items-center gap-4">
                <div class="h-12 w-12 rounded-xl bg-gradient-to-br from-fuchsia-500 to-fuchsia-600 text-white grid place-items-center shadow-lg shadow-fuchsia-500/30">
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M3 3h18v2H3V3zm2 4h14v14H5V7zm2 2v10h10V9H7z" />
                    </svg>
                </div>
                <div>
                    <h3 class="font-semibold text-slate-800 dark:text-slate-100">Quản lý Đơn hàng</h3>
                    <p class="text-sm text-slate-600 dark:text-slate-400">Xử lý, theo dõi và cập nhật trạng thái đơn</p>
                </div>
            </div>
        </a>
        @endif

        {{-- Statistics --}}
        @if(auth()->check() && auth()->user()->hasPermission('view_statistics'))
        <a href="{{ route('admin.stats') }}" class="rounded-2xl bg-white/80 dark:bg-slate-900/50 backdrop-blur p-6 shadow ring-1 ring-slate-900/5 dark:ring-white/10 hover:shadow-md hover:ring-brand-600/50 dark:hover:ring-brand-500 transition duration-200">
            <div class="flex items-center gap-4">
                <div class="h-12 w-12 rounded-xl bg-gradient-to-br from-amber-500 to-amber-600 text-white grid place-items-center shadow-lg shadow-amber-500/30">
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M3 3h2v18H3V3Zm4 10h2v8H7v-8Zm4-6h2v14h-2V7Zm4 4h2v10h-2V11Zm4-6h2v16h-2V5Z" />
                    </svg>
                </div>
                <div>
                    <h3 class="font-semibold text-slate-800 dark:text-slate-100">Xem Thống kê</h3>
                    <p class="text-sm text-slate-600 dark:text-slate-400">Phân tích doanh thu & hành vi người dùng</p>
                </div>
            </div>
        </a>
        @endif

        {{-- User Management --}}
        @if(auth()->check() && auth()->user()->hasPermission('manage_users'))
        <a href="{{ route('admin.users') }}" class="rounded-2xl bg-white/80 dark:bg-slate-900/50 backdrop-blur p-6 shadow ring-1 ring-slate-900/5 dark:ring-white/10 hover:shadow-md hover:ring-brand-600/50 dark:hover:ring-brand-500 transition duration-200">
            <div class="flex items-center gap-4">
                <div class="h-12 w-12 rounded-xl bg-gradient-to-br from-sky-500 to-sky-600 text-white grid place-items-center shadow-lg shadow-sky-500/30">
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 12a5 5 0 1 0-5-5 5 5 0 0 0 5 5Zm-7 7a7 7 0 0 1 14 0H5z" />
                    </svg>
                </div>
                <div>
                    <h3 class="font-semibold text-slate-800 dark:text-slate-100">Quản lý Người dùng</h3>
                    <p class="text-sm text-slate-600 dark:text-slate-400">Kiểm soát tài khoản, vai trò và khách hàng</p>
                </div>
            </div>
        </a>
        @endif

        {{-- Category Management --}}
        @if(auth()->check() && auth()->user()->hasPermission('manage_categories'))
        <a href="{{ route('admin.categories') }}" class="rounded-2xl bg-white/80 dark:bg-slate-900/50 backdrop-blur p-6 shadow ring-1 ring-slate-900/5 dark:ring-white/10 hover:shadow-md hover:ring-brand-600/50 dark:hover:ring-brand-500 transition duration-200">
            <div class="flex items-center gap-4">
                <div class="h-12 w-12 rounded-xl bg-gradient-to-br from-yellow-500 to-yellow-600 text-white grid place-items-center shadow-lg shadow-yellow-500/30">
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M4 4h6v6H4V4zm0 10h6v6H4v-6zm10-10h6v6h-6V4zm0 10h6v6h-6v-6z" />
                    </svg>
                </div>
                <div>
                    <h3 class="font-semibold text-slate-800 dark:text-slate-100">Quản lý Danh mục</h3>
                    <p class="text-sm text-slate-600 dark:text-slate-400">Tổ chức và phân loại sản phẩm</p>
                </div>
            </div>
        </a>
        @endif
        
        {{-- Roles Management (Part of User Management, but separate link in sidebar) --}}
        @if(auth()->check() && auth()->user()->hasPermission('manage_users'))
        <a href="{{ route('admin.roles') }}" class="rounded-2xl bg-white/80 dark:bg-slate-900/50 backdrop-blur p-6 shadow ring-1 ring-slate-900/5 dark:ring-white/10 hover:shadow-md hover:ring-brand-600/50 dark:hover:ring-brand-500 transition duration-200">
            <div class="flex items-center gap-4">
                <div class="h-12 w-12 rounded-xl bg-gradient-to-br from-purple-500 to-purple-600 text-white grid place-items-center shadow-lg shadow-purple-500/30">
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 2l7 4v6c0 5-3.5 9-7 10-3.5-1-7-5-7-10V6l7-4z" />
                    </svg>
                </div>
                <div>
                    <h3 class="font-semibold text-slate-800 dark:text-slate-100">Nhóm quyền (Roles)</h3>
                    <p class="text-sm text-slate-600 dark:text-slate-400">Quản lý các nhóm và cấp độ truy cập</p>
                </div>
            </div>
        </a>
        @endif

        {{-- Purchase Management --}}
        @if(auth()->check() && auth()->user()->hasPermission('manage_purchases'))
        <a href="{{ route('admin.purchase_orders') }}" class="rounded-2xl bg-white/80 dark:bg-slate-900/50 backdrop-blur p-6 shadow ring-1 ring-slate-900/5 dark:ring-white/10 hover:shadow-md hover:ring-brand-600/50 dark:hover:ring-brand-500 transition duration-200">
            <div class="flex items-center gap-4">
                <div class="h-12 w-12 rounded-xl bg-gradient-to-br from-red-500 to-red-600 text-white grid place-items-center shadow-lg shadow-red-500/30">
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M4 4h16v2H4V4zm2 4h12v12H6V8zm6 2l-4 4h3v4h2v-4h3l-4-4z" />
                    </svg>
                </div>
                <div>
                    <h3 class="font-semibold text-slate-800 dark:text-slate-100">Quản lý Nhập kho</h3>
                    <p class="text-sm text-slate-600 dark:text-slate-400">Tạo và theo dõi đơn nhập hàng</p>
                </div>
            </div>
        </a>
        @endif
        
        {{-- Discount Management --}}
        @if(auth()->check() && auth()->user()->hasPermission('manage_discounts'))
        <a href="{{ route('admin.discounts') }}" class="rounded-2xl bg-white/80 dark:bg-slate-900/50 backdrop-blur p-6 shadow ring-1 ring-slate-900/5 dark:ring-white/10 hover:shadow-md hover:ring-brand-600/50 dark:hover:ring-brand-500 transition duration-200">
            <div class="flex items-center gap-4">
                <div class="h-12 w-12 rounded-xl bg-gradient-to-br from-lime-500 to-lime-600 text-white grid place-items-center shadow-lg shadow-lime-500/30">
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M21 6H7a2 2 0 00-2 2v10a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2zM9 10h2v2H9v-2zm6 4h2v2h-2v-2zM7 4h10v2H7V4z" />
                    </svg>
                </div>
                <div>
                    <h3 class="font-semibold text-slate-800 dark:text-slate-100">Mã Giảm giá</h3>
                    <p class="text-sm text-slate-600 dark:text-slate-400">Tạo, quản lý và theo dõi ưu đãi</p>
                </div>
            </div>
        </a>
        @endif
        
        {{-- Suppliers Management --}}
        @if(auth()->check() && auth()->user()->hasPermission('manage_purchases'))
        <a href="{{ route('admin.suppliers') }}" class="rounded-2xl bg-white/80 dark:bg-slate-900/50 backdrop-blur p-6 shadow ring-1 ring-slate-900/5 dark:ring-white/10 hover:shadow-md hover:ring-brand-600/50 dark:hover:ring-brand-500 transition duration-200">
            <div class="flex items-center gap-4">
                <div class="h-12 w-12 rounded-xl bg-gradient-to-br from-orange-500 to-orange-600 text-white grid place-items-center shadow-lg shadow-orange-500/30">
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M3 7h18v2H3V7zm2 4h14l-1 10H6l-1-10zm8 2h2v6h-2v-6z" />
                    </svg>
                </div>
                <div>
                    <h3 class="font-semibold text-slate-800 dark:text-slate-100">Quản lý Nhà cung cấp</h3>
                    <p class="text-sm text-slate-600 dark:text-slate-400">Thông tin và giao dịch với đối tác</p>
                </div>
            </div>
        </a>
        @endif

        {{-- Brands Management --}}
        @if(auth()->check() && auth()->user()->hasPermission('manage_brands'))
        <a href="{{ route('admin.brands') }}" class="rounded-2xl bg-white/80 dark:bg-slate-900/50 backdrop-blur p-6 shadow ring-1 ring-slate-900/5 dark:ring-white/10 hover:shadow-md hover:ring-brand-600/50 dark:hover:ring-brand-500 transition duration-200">
            <div class="flex items-center gap-4">
                <div class="h-12 w-12 rounded-xl bg-gradient-to-br from-cyan-500 to-cyan-600 text-white grid place-items-center shadow-lg shadow-cyan-500/30">
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M4 4h16v2H4V4zm2 4h12v12H6V8zm2 2h8v2H8v-2zm0 4h8v2H8v-2z" />
                    </svg>
                </div>
                <div>
                    <h3 class="font-semibold text-slate-800 dark:text-slate-100">Quản lý Thương hiệu</h3>
                    <p class="text-sm text-slate-600 dark:text-slate-400">Thêm, sửa các nhãn hiệu sản phẩm</p>
                </div>
            </div>
        </a>
        @endif

        {{-- Customers Management --}}
        @if(auth()->check() && auth()->user()->hasPermission('manage_users'))
        <a href="{{ route('admin.customers') }}" class="rounded-2xl bg-white/80 dark:bg-slate-900/50 backdrop-blur p-6 shadow ring-1 ring-slate-900/5 dark:ring-white/10 hover:shadow-md hover:ring-brand-600/50 dark:hover:ring-brand-500 transition duration-200">
            <div class="flex items-center gap-4">
                <div class="h-12 w-12 rounded-xl bg-gradient-to-br from-indigo-500 to-indigo-600 text-white grid place-items-center shadow-lg shadow-indigo-500/30">
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M7 11a4 4 0 110-8 4 4 0 010 8zm10 0a4 4 0 110-8 4 4 0 010 8zM2 20a5 5 0 015-5h2a5 5 0 015 5v1H2v-1zm13 0a6.97 6.97 0 00-2.05-4.95A4.98 4.98 0 0120 20v1h-5v-1z" />
                    </svg>
                </div>
                <div>
                    <h3 class="font-semibold text-slate-800 dark:text-slate-100">Quản lý Khách hàng</h3>
                    <p class="text-sm text-slate-600 dark:text-slate-400">Thông tin chi tiết và lịch sử mua hàng</p>
                </div>
            </div>
        </a>
        @endif

        {{-- Live Chat Management --}}
        @if(auth()->check() && auth()->user()->hasPermission('manage_livechat'))
        <a href="{{ route('admin.chat.index') }}" class="rounded-2xl bg-white/80 dark:bg-slate-900/50 backdrop-blur p-6 shadow ring-1 ring-slate-900/5 dark:ring-white/10 hover:shadow-md hover:ring-brand-600/50 dark:hover:ring-brand-500 transition duration-200">
            <div class="flex items-center gap-4">
                <div class="h-12 w-12 rounded-xl bg-gradient-to-br from-pink-500 to-pink-600 text-white grid place-items-center shadow-lg shadow-pink-500/30">
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 2C6.477 2 2 6.477 2 12c0 3.313 1.61 6.273 4.093 8.163-.05.158-.152.483-.497 1.05a.5.5 0 00-.012.44.5.5 0 00.548.27l3.655-1.127A9.946 9.946 0 0012 22c5.523 0 10-4.477 10-10S17.523 2 12 2zm1 14h-2v-2h2v2zm0-4h-2V6h2v6z" />
                    </svg>
                </div>
                <div>
                    <h3 class="font-semibold text-slate-800 dark:text-slate-100">Quản lý Live Chat</h3>
                    <p class="text-sm text-slate-600 dark:text-slate-400">Hỗ trợ khách hàng trực tuyến</p>
                </div>
            </div>
        </a>
        @endif

    </div>

</div>
@endsection