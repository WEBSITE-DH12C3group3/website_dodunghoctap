@extends('layouts.store')

@section('content')
@php
$u = $user ?? auth()->user();
$displayName = $u->full_name
?? trim(($u->first_name ?? '').' '.($u->last_name ?? ''))
?? ($u->name ?? $u->email);
$parts = preg_split('/\s+/', trim($displayName));
$ini = strtoupper(mb_substr($parts[0] ?? 'U',0,1) . mb_substr($parts[count($parts)-1] ?? 'N',0,1));
@endphp

<main class="mx-auto max-w-screen-2xl px-4 py-6 md:py-8">
    {{-- Flash --}}
    @if (session('success'))
    <div class="mb-4 rounded-lg bg-green-50 border border-green-200 text-green-700 px-4 py-3">
        {{ session('success') }}
    </div>
    @endif
    @if ($errors->any())
    <div class="mb-4 rounded-lg bg-red-50 border border-red-200 text-red-700 px-4 py-3">
        <ul class="list-disc pl-5 space-y-1">
            @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
        </ul>
    </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-[320px,1fr] gap-6">
        {{-- Sidebar trái --}}
        <aside class="rounded-2xl bg-gray-50 p-6 shadow-sm ring-1 ring-gray-100">
            <div class="mx-auto grid place-items-center gap-2">
                <div class="grid place-items-center h-24 w-24 rounded-full bg-[#F6A622] text-white text-4xl font-bold">{{ $ini }}</div>
                <div class="italic text-gray-600">Xin chào, <span class="font-medium not-italic">{{ $displayName }}</span></div>
            </div>

            <nav class="mt-6 space-y-2">
                <a href="#" class="flex items-center gap-2 rounded-lg bg-[#2252F3] text-white px-4 py-3">
                    <span class="inline-block w-5 text-center">▦</span>
                    <span>Tổng quan tài khoản</span>
                </a>
                <a href="{{ route('user.index') }}" class="flex items-center gap-2 rounded-lg bg-[#F6A622] text-white px-4 py-3">
                    <span class="inline-block w-5 text-center">🛈</span>
                    <span>Thông tin tài khoản</span>
                </a>
                <a href="#" class="flex items-center gap-2 rounded-lg bg-[#2252F3] text-white/95 hover:text-white px-4 py-3">
                    <span class="inline-block w-5 text-center">💳</span>
                    <span>Chương trình thành viên</span>
                </a>
                <a href="#" class="flex items-center gap-2 rounded-lg bg-[#2252F3] text-white/95 hover:text-white px-4 py-3">
                    <span class="inline-block w-5 text-center">📦</span>
                    <span>Danh sách đơn hàng</span>
                </a>
                <a href="#" class="flex items-center gap-2 rounded-lg bg-[#2252F3] text-white/95 hover:text-white px-4 py-3">
                    <span class="inline-block w-5 text-center">🏠</span>
                    <span>Sổ địa chỉ</span>
                </a>
                <form method="POST" action="{{ route('logout') }}" class="mt-2">
                    @csrf
                    <button class="w-full text-left flex items-center gap-2 rounded-lg bg-[#2252F3] text-white/95 hover:text-white px-4 py-3">
                        <span class="inline-block w-5 text-center">⎋</span>
                        <span>Đăng xuất</span>
                    </button>
                </form>
            </nav>
        </aside>

        {{-- Nội dung phải --}}
        <section class="rounded-2xl bg-gray-50 shadow-sm ring-1 ring-gray-100 overflow-hidden">
            <div class="bg-gray-100/70 px-6 py-4 md:px-8 md:py-5">
                <h2 class="text-[#F6A622] text-xl md:text-2xl font-bold">THÔNG TIN TÀI KHOẢN</h2>
            </div>

            <div class="p-6 md:p-8">
                <form method="POST" action="{{ route('user.update') }}" class="space-y-5">
                    @csrf
                    @method('PUT')

                    {{-- Họ & Tên: 2 cột trên md+ --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block font-medium text-gray-700">Họ:</label>
                            <input type="text" name="first_name" value="{{ old('first_name', $u->first_name ?? '') }}"
                                class="mt-1 w-full rounded-md border-gray-300 focus:border-blue-400 focus:ring-blue-300" />
                        </div>
                        <div>
                            <label class="block font-medium text-gray-700">Tên:</label>
                            <input type="text" name="last_name" value="{{ old('last_name', $u->last_name ?? '') }}"
                                class="mt-1 w-full rounded-md border-gray-300 focus:border-blue-400 focus:ring-blue-300" />
                        </div>
                    </div>

                    {{-- Email hiển thị (không sửa) --}}
                    <div>
                        <span class="block font-medium text-gray-700">Email:</span>
                        <div class="mt-1 text-red-600 font-semibold">{{ $u->email }}</div>
                    </div>

                    {{-- SĐT --}}
                    <div>
                        <label class="block font-medium text-gray-700">Số điện thoại:</label>
                        <input type="text" name="phone" value="{{ old('phone', $u->phone ?? '') }}"
                            class="mt-1 w-full rounded-md border-gray-300 focus:border-blue-400 focus:ring-blue-300"
                            placeholder="0123456789" />
                    </div>



                    {{-- Nút cập nhật --}}
                    <div class="pt-2">
                        <button class="ml-auto block rounded-full bg-[#2252F3] px-6 py-2.5 text-white font-semibold hover:brightness-110">
                            Cập nhật thông tin
                        </button>
                    </div>
                </form>
            </div>
        </section>
    </div>
</main>
@endsection