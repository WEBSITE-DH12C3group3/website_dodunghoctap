@extends('layouts.app')

@section('content')
<div class="bg-white/80 dark:bg-slate-900/50 backdrop-blur p-6 rounded-2xl shadow ring-1 ring-slate-900/5 dark:ring-white/10">
    <h2 class="text-xl font-semibold text-slate-800 dark:text-slate-100 mb-6">Danh sách người dùng</h2>

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

    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-slate-700 dark:text-slate-200">
            <thead class="bg-slate-100 dark:bg-slate-800">
                <tr>
                    <th class="px-4 py-3 font-semibold">ID</th>
                    <th class="px-4 py-3 font-semibold">Tên</th>
                    <th class="px-4 py-3 font-semibold">Email</th>
                    <th class="px-4 py-3 font-semibold">Vai trò</th>
                    <th class="px-4 py-3 font-semibold">Trạng thái</th>
                    <th class="px-4 py-3 font-semibold">Online</th>
                    <th class="px-4 py-3 font-semibold">Lần online cuối</th>
                    <th class="px-4 py-3 font-semibold">Hành động</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($users as $user)
                    <tr class="border-b border-slate-200 dark:border-slate-700">
                        <td class="px-4 py-3">{{ $user->user_id }}</td>
                        <td class="px-4 py-3">{{ $user->full_name }}</td>
                        <td class="px-4 py-3">{{ $user->email }}</td>
                        <td class="px-4 py-3">{{ optional($user->role)->role_name ?? 'N/A' }}</td>
                        <td class="px-4 py-3">
                            @if ($user->is_active)
                                <span class="px-2 py-1 text-xs font-medium text-green-800 bg-green-100 rounded-full">Hoạt động</span>
                            @else
                                <span class="px-2 py-1 text-xs font-medium text-red-800 bg-red-100 rounded-full">Khóa</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            @if ($user->isOnline())
                                <span class="px-2 py-1 text-xs font-medium text-green-800 bg-green-100 rounded-full">Online</span>
                            @else
                                <span class="px-2 py-1 text-xs font-medium text-gray-800 bg-gray-100 rounded-full">Offline</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">{{ $user->last_activity ? $user->last_activity->format('d/m/Y H:i') : 'Chưa có' }}</td>
                        <td class="px-4 py-3 flex gap-2">
                            <a href="{{ route('admin.users.show', $user->user_id) }}" class="text-blue-600 hover:underline">Xem</a>
                            <a href="{{ route('admin.users.edit', $user->user_id) }}" class="text-green-600 hover:underline">Sửa</a>
                            <form action="{{ route('admin.users.destroy', $user->user_id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa người dùng này?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline">Xóa</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-4 py-4 text-center text-slate-500 dark:text-slate-400">Không có người dùng nào.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection