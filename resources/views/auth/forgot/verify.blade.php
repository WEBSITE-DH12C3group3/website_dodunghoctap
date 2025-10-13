@extends('layouts.store')
@section('title','Xác minh OTP')
@section('content')
<div class="max-w-md mx-auto mt-10 bg-white rounded-2xl shadow p-6">
    <h1 class="text-2xl font-bold mb-4">Nhập mã OTP</h1>
    <p class="text-sm text-gray-600 mb-3">Mã đã gửi tới: <b>{{ $email }}</b></p>

    @if(session('ok')) <div class="text-green-600 mb-3">{{ session('ok') }}</div> @endif
    @if(session('error')) <div class="text-red-600 mb-3">{{ session('error') }}</div> @endif

    <form method="POST" action="{{ route('profile.password.verify.submit') }}" class="space-y-4">
        @csrf
        <label class="block">
            <span class="text-sm font-medium">Mã OTP</span>
            <input type="text" name="otp" maxlength="6" inputmode="numeric" pattern="\d{6}" required class="w-full border rounded-lg p-2">
            @error('otp') <div class="text-red-600 text-sm mt-1">{{ $message }}</div> @enderror
        </label>
        <button class="w-full bg-indigo-600 text-white rounded-lg py-2">Xác minh</button>
    </form>

    <form method="POST" action="{{ route('profile.password.otp.send') }}" class="mt-4 text-center">
        @csrf
        <input type="hidden" name="email" value="{{ $email }}">
        <button class="text-indigo-600 underline">Gửi lại mã</button>
    </form>
</div>
@endsection