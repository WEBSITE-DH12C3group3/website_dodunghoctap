@extends('layouts.store')
@section('title','Quên mật khẩu')
@section('content')
<div class="max-w-md mx-auto mt-10 bg-white p-6 rounded-xl ring-1 ring-gray-100">
    <h1 class="text-xl font-semibold mb-4">Nhập email để nhận liên kết đặt lại mật khẩu</h1>

    @if (session('status'))
    <div class="mb-3 text-green-600">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
        @csrf
        <div>
            <label class="block text-sm mb-1">Email</label>
            <input type="email" name="email" class="w-full border rounded-lg p-2" required autofocus>
            @error('email') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
        </div>
        <button class="w-full py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700">Gửi liên kết</button>
    </form>
</div>
@endsection