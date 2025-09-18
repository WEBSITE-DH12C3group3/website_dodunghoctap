@extends('layouts.app')

@section('content')
<div class="bg-white/80 dark:bg-slate-900/50 backdrop-blur p-6 rounded-2xl shadow ring-1 ring-slate-900/5 dark:ring-white/10">
    <h2 class="text-xl font-semibold text-slate-800 dark:text-slate-100 mb-6">Chỉnh sửa người dùng</h2>

    <form action="{{ route('admin.users.update', $user->user_id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="grid gap-6 md:grid-cols-2">
            <div>
                <label for="role_id" class="block text-sm font-medium text-slate-700 dark:text-slate-200">Vai trò</label>
                <select name="role_id" id="role_id" class="mt-1 block w-full rounded-xl border-slate-300 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 focus:ring-brand-600 focus:border-brand-600" required>
                    @foreach ($roles as $role)
                        <option value="{{ $role->role_id }}" {{ old('role_id', $user->role_id) == $role->role_id ? 'selected' : '' }}>{{ $role->role_name }}</option>
                    @endforeach
                </select>
                @error('role_id')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="is_active" class="block text-sm font-medium text-slate-700 dark:text-slate-200">Trạng thái tài khoản</label>
                <select name="is_active" id="is_active" class="mt-1 block w-full rounded-xl border-slate-300 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 focus:ring-brand-600 focus:border-brand-600" required>
                    <option value="1" {{ old('is_active', $user->is_active) == 1 ? 'selected' : '' }}>Hoạt động</option>
                    <option value="0" {{ old('is_active', $user->is_active) == 0 ? 'selected' : '' }}>Khóa</option>
                </select>
                @error('is_active')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="mt-6 flex gap-4">
            <button type="submit" class="rounded-xl px-4 py-2 text-sm font-medium text-white bg-brand-600 hover:bg-brand-700 shadow-sm">Cập nhật</button>
            <a href="{{ route('admin.users') }}" class="rounded-xl px-4 py-2 text-sm font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-200/60 dark:hover:bg-slate-800/60">Hủy</a>
        </div>
    </form>
</div>
@endsection