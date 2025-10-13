@extends('layouts.store')
@section('title','Đặt mật khẩu mới')
@section('content')
<div class="max-w-md mx-auto mt-10 bg-white rounded-2xl shadow p-6">
    <h1 class="text-2xl font-bold mb-4">Đặt mật khẩu mới</h1>

    @if(session('ok')) <div class="text-green-600 mb-3">{{ session('ok') }}</div> @endif
    @if(session('error')) <div class="text-red-600 mb-3">{{ session('error') }}</div> @endif

    <form method="POST" action="{{ route('profile.password.reset.submit') }}" class="space-y-4">
        @csrf
        <label class="block">
            <span class="text-sm font-medium">Mật khẩu</span>
            <input type="password" name="password" required class="w-full border rounded-lg p-2">
            @error('password') <div class="text-red-600 text-sm mt-1">{{ $message }}</div> @enderror
        </label>
        <label class="block">
            <span class="text-sm font-medium">Nhập lại mật khẩu</span>
            <input type="password" name="password_confirmation" required class="w-full border rounded-lg p-2">
        </label>
        <button class="w-full bg-indigo-600 text-white rounded-lg py-2">Lưu mật khẩu</button>
    </form>
</div>
@endsection