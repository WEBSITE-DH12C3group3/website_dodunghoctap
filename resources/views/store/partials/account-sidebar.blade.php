@php use Illuminate\Support\Str; @endphp

<aside class="md:col-span-4 lg:col-span-3">
    <div class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-100 p-6">
        {{-- Avatar + tên --}}
        <div class="flex flex-col items-center text-center gap-3">
            <div class="w-24 h-24 rounded-full bg-amber-500 grid place-items-center text-white text-3xl font-bold">
                {{
          Str::of(auth()->user()->full_name ?? auth()->user()->name ?? 'U')
            ->trim()
            ->afterLast(' ')
            ->substr(0,1)
            ->upper()
        }}
            </div>
            <div>
                <div class="font-semibold text-gray-900">
                    {{ auth()->user()->full_name ?? auth()->user()->name ?? 'Người dùng' }}
                </div>
                <div class="text-sm text-gray-500">
                    {{ auth()->user()->email ?? '' }}
                </div>
            </div>
        </div>

        {{-- Menu tài khoản --}}
        <nav class="mt-6 grid gap-1 text-sm">
            {{-- Thay route() bằng tên route thực tế của bạn --}}
            <a href="{{ route('account.dashboard') }}" class="px-3 py-2 rounded-lg hover:bg-gray-50 flex items-center justify-between">
                <span>Bảng điều khiển</span>
                <span class="text-gray-400">›</span>
            </a>
            <a href="{{ route('account.orders') }}" class="px-3 py-2 rounded-lg hover:bg-gray-50 flex items-center justify-between">
                <span>Đơn hàng của tôi</span>
                <span class="text-gray-400">›</span>
            </a>
            <a href="{{ route('account.addresses') }}" class="px-3 py-2 rounded-lg hover:bg-gray-50 flex items-center justify-between">
                <span>Sổ địa chỉ</span>
                <span class="text-gray-400">›</span>
            </a>
            <a href="{{ route('account.wishlist') }}" class="px-3 py-2 rounded-lg hover:bg-gray-50 flex items-center justify-between">
                <span>Yêu thích</span>
                <span class="text-gray-400">›</span>
            </a>
            <a href="{{ route('account.profile') }}" class="px-3 py-2 rounded-lg hover:bg-gray-50 flex items-center justify-between">
                <span>Hồ sơ</span>
                <span class="text-gray-400">›</span>
            </a>
            <a href="{{ route('account.password') }}" class="px-3 py-2 rounded-lg hover:bg-gray-50 flex items-center justify-between">
                <span>Đổi mật khẩu</span>
                <span class="text-gray-400">›</span>
            </a>

            <form action="{{ route('logout') }}" method="POST" class="mt-1">
                @csrf
                <button class="w-full text-left px-3 py-2 rounded-lg hover:bg-red-50 text-red-600">
                    Đăng xuất
                </button>
            </form>
        </nav>
    </div>
</aside>