@extends('layouts.app')

@section('content')
<div class="bg-white/80 dark:bg-slate-900/50 backdrop-blur p-6 rounded-2xl shadow ring-1 ring-slate-900/5 dark:ring-white/10">
    <h2 class="text-xl font-semibold text-slate-800 dark:text-slate-100 mb-6">Chi tiết người dùng</h2>

    @if (session('ok'))
        <div class="mb-4 rounded-xl border border-green-200/60 dark:border-green-400/30 bg-green-50/80 dark:bg-green-900/30 px-4 py-3 text-green-700 dark:text-green-200 shadow-sm">
            {{ session('ok') }}
        </div>
    @endif
    @if (session('error'))
        <div class="mb-4 rounded-xl border border-red-200/60 dark:border-red-400/30 bg-red-50/80 dark:bg-red-900/30 px-4 py-3 text-red-700 dark:text-red-200 shadow-sm">
            {{ session('error') }}
        </div>
    @endif

    <div class="space-y-4">
        <div>
            <span class="font-semibold text-slate-700 dark:text-slate-200">ID:</span>
            <span>{{ $user->user_id }}</span>
        </div>
        <div>
            <span class="font-semibold text-slate-700 dark:text-slate-200">Tên:</span>
            <span>{{ $user->full_name }}</span>
        </div>
        <div>
            <span class="font-semibold text-slate-700 dark:text-slate-200">Email:</span>
            <span>{{ $user->email }}</span>
        </div>
        <div>
            <span class="font-semibold text-slate-700 dark:text-slate-200">Vai trò:</span>
            <span>{{ optional($user->role)->role_name ?? 'N/A' }}</span>
        </div>
        <div>
            <span class="font-semibold text-slate-700 dark:text-slate-200">Trạng thái:</span>
            @if ($user->is_active)
                <span class="px-2 py-1 text-xs font-medium text-green-800 bg-green-100 rounded-full">Hoạt động</span>
            @else
                <span class="px-2 py-1 text-xs font-medium text-red-800 bg-red-100 rounded-full">Khóa</span>
            @endif
        </div>
        <div>
            <span class="font-semibold text-slate-700 dark:text-slate-200">Online:</span>
            @if ($user->isOnline())
                <span class="px-2 py-1 text-xs font-medium text-green-800 bg-green-100 rounded-full">Online</span>
            @else
                <span class="px-2 py-1 text-xs font-medium text-gray-800 bg-gray-100 rounded-full">Offline</span>
            @endif
        </div>
        <div>
            <span class="font-semibold text-slate-700 dark:text-slate-200">Lần online cuối:</span>
            @if ($user->last_activity instanceof \Illuminate\Support\Carbon)
                <span>{{ $user->last_activity->format('d/m/Y H:i') }}</span>
            @else
                <span>{{ $user->last_activity ? \Illuminate\Support\Carbon::parse($user->last_activity)->format('d/m/Y H:i') : 'Chưa có' }}</span>
            @endif
        </div>
        <div class="flex gap-4">
            <a href="{{ route('admin.users.edit', $user->user_id) }}" class="text-green-600 hover:underline">Sửa</a>
            <form action="{{ route('admin.users.destroy', $user->user_id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa người dùng này?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="text-red-600 hover:underline">Xóa</button>
            </form>
            <a href="{{ route('admin.users') }}" class="text-blue-600 hover:underline">Quay lại</a>
        </div>
    </div>
</div>
@endsection