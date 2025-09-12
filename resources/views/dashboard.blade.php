@extends('layouts.app')

@section('content')
<div class="grid gap-4 md:grid-cols-2">
    <div class="bg-white rounded-xl p-4 shadow">
        <h2 class="font-semibold mb-2">Chào, {{ auth()->user()->full_name }}</h2>
        <p>Email: {{ auth()->user()->email }}</p>
        <p>Vai trò: <span class="px-2 py-1 bg-gray-100 rounded">{{ optional(auth()->user()->role)->role_name }}</span></p>
    </div>

    <div class="bg-white rounded-xl p-4 shadow">
        <h2 class="font-semi@extends('layouts.app')
@section('content')
<div class=" grid gap-6 md:grid-cols-3">
            <div class="md:col-span-1 rounded-2xl bg-white/80 dark:bg-slate-900/50 backdrop-blur p-6 shadow ring-1 ring-slate-900/5 dark:ring-white/10">
                <h2 class="font-semibold mb-2">Xin chào, {{ auth()->user()->full_name }}</h2>
                <p class="text-sm text-slate-600 dark:text-slate-300">Email: {{ auth()->user()->email }}</p>
                <p class="text-sm text-slate-600 dark:text-slate-300">Vai trò:
                    <span class="ml-2 rounded-lg px-2 py-0.5 bg-slate-100 dark:bg-slate-800">
                        {{ optional(auth()->user()->role)->role_name }}
                    </span>
                </p>
            </div>

            <!-- Quick actions -->
            @if(auth()->check() && auth()->user()->hasPermission('manage_products'))
            <a href="{{ route('admin.products') }}" class="rounded-2xl bg-white/80 dark:bg-slate-900/50 backdrop-blur p-6 shadow ring-1 ring-slate-900/5 dark:ring-white/10 hover:shadow-md transition">
                <div class="flex items-center gap-3">
                    <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-600 text-white grid place-items-center shadow">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M3 4h18v2H3V4Zm2 4h14l-1 12H6L5 8Zm4 2v8h2v-8H9Zm4 0v8h2v-8h-2Z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-semibold">Quản lý sản phẩm</h3>
                        <p class="text-sm text-slate-600 dark:text-slate-300">Tạo/sửa/xoá sản phẩm & thuộc tính</p>
                    </div>
                </div>
            </a>
            @endif

            @if(auth()->check() && auth()->user()->hasPermission('manage_users'))
            <a href="{{ route('admin.users') }}" class="rounded-2xl bg-white/80 dark:bg-slate-900/50 backdrop-blur p-6 shadow ring-1 ring-slate-900/5 dark:ring-white/10 hover:shadow-md transition">
                <div class="flex items-center gap-3">
                    <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-sky-500 to-sky-600 text-white grid place-items-center shadow">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 12a5 5 0 1 0-5-5 5 5 0 0 0 5 5Zm-9 9a9 9 0 0 1 18 0Z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-semibold">Quản lý người dùng</h3>
                        <p class="text-sm text-slate-600 dark:text-slate-300">Phân quyền & kiểm soát truy cập</p>
                    </div>
                </div>
            </a>
            @endif

            @if(auth()->check() && auth()->user()->hasPermission('view_statistics'))
            <a href="{{ route('admin.stats') }}" class="rounded-2xl bg-white/80 dark:bg-slate-900/50 backdrop-blur p-6 shadow ring-1 ring-slate-900/5 dark:ring-white/10 hover:shadow-md transition">
                <div class="flex items-center gap-3">
                    <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-amber-500 to-amber-600 text-white grid place-items-center shadow">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M3 3h2v18H3V3Zm4 10h2v8H7v-8Zm4-6h2v14h-2V7Zm4 4h2v10h-2V11Zm4-6h2v16h-2V5Z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-semibold">Thống kê</h3>
                        <p class="text-sm text-slate-600 dark:text-slate-300">Doanh thu & hành vi người dùng</p>
                    </div>
                </div>
            </a>
            @endif

            @if(auth()->check() && auth()->user()->hasPermission('manage_orders'))
            <a href="{{ route('admin.orders') }}" class="rounded-2xl bg-white/80 dark:bg-slate-900/50 backdrop-blur p-6 shadow ring-1 ring-slate-900/5 dark:ring-white/10 hover:shadow-md transition">
                <div class="flex items-center gap-3">
                    <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-fuchsia-500 to-fuchsia-600 text-white grid place-items-center shadow">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M3 6h18v2H3V6Zm2 4h14l-1 8H6l-1-8Zm5 2v4h2v-4H10Zm4 0v4h2v-4h-2Z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-semibold">Đơn hàng</h3>
                        <p class="text-sm text-slate-600 dark:text-slate-300">Xử lý & theo dõi trạng thái</p>
                    </div>
                </div>
            </a>
            @endif
    </div>
    @endsection
    bold mb-2">Khu vực quản trị</h2>
    <ul class="list-disc ml-5">
        @if (auth()->user()->hasPermission('manage_products'))
        <li><a class="text-blue-600" href="{{ route('admin.products') }}">Quản lý sản phẩm</a></li>
        @endif
        @if (auth()->user()->hasPermission('manage_users'))
        <li><a class="text-blue-600" href="{{ route('admin.users') }}">Quản lý người dùng</a></li>
        @endif
        @if (auth()->user()->hasPermission('view_statistics'))
        <li><a class="text-blue-600" href="{{ route('admin.stats') }}">Xem thống kê</a></li>
        @endif
        @if (auth()->user()->hasPermission('manage_orders'))
        <li><a class="text-blue-600" href="{{ route('admin.orders') }}">Quản lý đơn hàng</a></li>
        @endif
    </ul>
</div>
</div>
@endsection