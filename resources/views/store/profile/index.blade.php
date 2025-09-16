@extends('layouts.store')

@section('title', 'Thông tin tài khoản')

@section('content')
<div class="max-w-[1320px] mx-auto px-4 py-6">
    <div class="grid grid-cols-1 md:grid-cols-[340px,1fr] gap-6">

        {{-- SIDEBAR --}}
        <aside class="bg-white rounded-[10px] shadow border border-gray-200">
            <div class="py-6 flex flex-col items-center">
                @php
                $name = $user->full_name ?: ($user->email ?: 'user');
                $initials = collect(preg_split('/\s+/', trim($name)))->filter()->map(fn($w)=>mb_substr($w,0,1))->take(2)->implode('');
                $initials = $initials ?: 'nn';
                @endphp
                <div class="h-24 w-24 rounded-full bg-[#F59E0B] flex items-center justify-center text-white text-[42px] font-extrabold leading-none">
                    {{ mb_strtolower($initials) }}
                </div>
                <div class="mt-3 text-gray-600 italic text-[18px]">
                    Xin chào, <span class="not-italic font-medium text-gray-800">{{ $user->full_name ?? 'người dùng' }}</span>
                </div>
            </div>

            <div class="px-4 pb-5">
                <nav class="rounded-lg overflow-hidden bg-[#1E4DE8]">
                    <a href="{{ route('profile.index') }}"
                        class="flex items-center gap-3 px-4 py-3 bg-[#F59E0B] text-white font-medium">
                        <svg class="w-5 h-5 fill-white shrink-0" viewBox="0 0 20 20">
                            <path d="M10 10a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm-7 7c0-3.314 3.582-6 8-6s8 2.686 8 6v1H3v-1Z" />
                        </svg>
                        <span>Thông tin tài khoản</span>
                    </a>

                    <a href="{{ url('/orders') }}"
                        class="flex items-center gap-3 px-4 py-3 text-white/95 hover:bg-white/10">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-box2-heart-fill" viewBox="0 0 16 16">
                            <path d="M3.75 0a1 1 0 0 0-.8.4L.1 4.2a.5.5 0 0 0-.1.3V15a1 1 0 0 0 1 1h14a1 1 0 0 0 1-1V4.5a.5.5 0 0 0-.1-.3L13.05.4a1 1 0 0 0-.8-.4h-8.5ZM8.5 4h6l.5.667V5H1v-.333L1.5 4h6V1h1v3ZM8 7.993c1.664-1.711 5.825 1.283 0 5.132-5.825-3.85-1.664-6.843 0-5.132Z"></path>
                        </svg>
                        <span class="font-medium">Danh sách đơn hàng</span>
                    </a>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="w-full text-left flex items-center gap-3 px-4 py-3 text-white/95 hover:bg-white/10">
                            <svg class="w-5 h-5 fill-white shrink-0" viewBox="0 0 20 20">
                                <path d="M7 3h6v2H7v10h6v2H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2Zm8.707 9.707-2-2a1 1 0 0 1 0-1.414l2-2L17.121 8.3 15.414 10l1.707 1.7-1.414 1.414Z" />
                            </svg>
                            <span class="font-medium">Đăng xuất</span>
                        </button>
                    </form>
                </nav>
            </div>
        </aside>

        {{-- MAIN --}}
        <section class="bg-white rounded-[10px] shadow border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-[16px] md:text-[20px] font-semibold tracking-wide text-gray-800">
                    THÔNG TIN TÀI KHOẢN
                </h2>
            </div>

            <form method="POST" action="{{ route('profile.update') }}" class="p-6 space-y-5">
                @csrf
                @method('PATCH')

                @if (session('status'))
                <div class="rounded-lg bg-green-50 text-green-700 px-4 py-3">
                    {{ session('status') }}
                </div>
                @endif

                {{-- Họ và tên (gộp 1 ô) --}}
                <div>
                    <label class="block text-[16px] font-medium text-gray-700 mb-1">Họ và tên:</label>
                    <input type="text" name="full_name" value="{{ old('full_name', $user->full_name) }}"
                        placeholder="Ví dụ: Nguyễn Văn A"
                        class="w-full h-12 rounded-md border border-gray-300 bg-[#F3F4F6] px-3
                        focus:outline-none focus:ring-2 focus:ring-[#1E4DE8]/40 focus:border-[#1E4DE8]" />
                    @error('full_name') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Email (đỏ, chỉ xem) --}}
                <div>
                    <label class="block text-[16px] font-medium text-gray-700 mb-1">Email: <span class="text-red-600 font-bold">{{ $user->email }}</span></label>
                    <!-- <div class="h-12 flex items-center px-3 rounded-md bg-[#F3F4F6] border border-gray-300"> -->

                    <!-- </div> -->
                </div>

                {{-- Số điện thoại --}}
                <div>
                    <label class="block text-[16px] font-medium text-gray-700 mb-1">Số điện thoại:</label>
                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}"
                        class="w-full h-12 rounded-md border border-gray-300 bg-[#F3F4F6] px-3
                        focus:outline-none focus:ring-2 focus:ring-[#1E4DE8]/40 focus:border-[#1E4DE8]" />
                    @error('phone') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Địa chỉ --}}
                <div>
                    <label class="block text-[16px] font-medium text-gray-700 mb-1">Địa chỉ:</label>
                    <input type="text" name="address" value="{{ old('address', $user->address) }}"
                        placeholder="Địa chỉ"
                        class="w-full h-12 rounded-md border border-gray-300 bg-[#F3F4F6] px-3
                        focus:outline-none focus:ring-2 focus:ring-[#1E4DE8]/40 focus:border-[#1E4DE8]" />
                    @error('address') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="pt-1">
                    <button type="submit"
                        class="inline-flex items-center justify-center h-11 px-5 rounded-full
                         bg-[#1E4DE8] text-white font-semibold hover:bg-[#143BC5]
                         transition-colors">
                        Cập nhật thông tin
                    </button>
                </div>
            </form>
        </section>

    </div>
</div>
@endsection