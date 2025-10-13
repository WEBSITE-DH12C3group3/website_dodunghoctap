@extends('layouts.store')
@section('title', 'Quên mật khẩu')

@section('content')
<div class="max-w-md mx-auto mt-10 bg-white rounded-2xl shadow p-6">
    <h1 class="text-2xl font-bold mb-4">Khôi phục mật khẩu</h1>

    @if(session('ok')) <div class="mb-3 text-green-600">{{ session('ok') }}</div> @endif
    @if(session('error'))<div class="mb-3 text-red-600">{{ session('error') }}</div> @endif

    <form method="POST" action="{{ route('profile.password.otp.send') }}" class="space-y-4">
        @csrf
        <label class="block">
            <span class="text-sm font-medium">Email đăng ký</span>
            <input type="email" name="email" value="{{ old('email') }}" required
                class="mt-1 w-full border rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-indigo-500" />
            @error('email') <div class="text-red-600 text-sm mt-1">{{ $message }}</div> @enderror
        </label>

        <button type="submit"
            class="w-full bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg py-2 font-medium">
            Gửi mã OTP
        </button>
    </form>

    <div class="mt-4 text-center">
        <a href="{{ url('/login') }}" class="text-indigo-600 hover:underline">Quay lại đăng nhập</a>
    </div>
</div>
@endsection