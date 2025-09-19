@extends('layouts.app')

@section('content')
<div class="bg-white/80 dark:bg-slate-900/50 backdrop-blur p-6 rounded-2xl shadow ring-1 ring-slate-900/5 dark:ring-white/10">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-xl font-semibold text-slate-800 dark:text-slate-100">Danh sách danh mục</h2>
            <p class="text-sm text-slate-600 dark:text-slate-300">Tổng số danh mục: {{ $categories instanceof \Illuminate\Pagination\LengthAwarePaginator ? $categories->total() : count($categories) }}</p>
        </div>
        <a href="{{ route('admin.categories.create') }}" class="rounded-xl px-4 py-2 text-sm font-medium text-white bg-brand-600 hover:bg-brand-700 shadow-sm">
            Thêm danh mục mới
        </a>
    </div>

    <div class="mb-4">
        <form action="{{ route('admin.categories') }}" method="GET" class="flex items-center space-x-2">
            <input type="text" name="search" placeholder="Tìm kiếm danh mục..." 
                   class="flex-1 rounded-xl px-4 py-2 text-sm text-slate-700 dark:text-slate-200 
                          bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 
                          focus:outline-none focus:ring-2 focus:ring-brand-500"
                   value="{{ request('search') }}">
            <button type="submit" class="rounded-xl px-4 py-2 text-sm font-medium text-white bg-brand-600 hover:bg-brand-700 shadow-sm">
                Tìm kiếm
            </button>
            @if(request('search'))
                <a href="{{ route('admin.categories') }}" class="text-slate-500 hover:text-red-500">Xóa tìm kiếm</a>
            @endif
        </form>
    </div>

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

    @if ($categories->isEmpty() && request('search'))
        <p class="text-slate-600 dark:text-slate-300">Không tìm thấy danh mục nào phù hợp với "{{ request('search') }}".</p>
    @elseif ($categories->isEmpty())
        <p class="text-slate-600 dark:text-slate-300">Chưa có danh mục nào.</p>
    @else
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-700 dark:text-slate-200">
                <thead class="bg-slate-100 dark:bg-slate-800">
                    <tr>
                        <th class="px-4 py-3 font-semibold">ID</th>
                        <th class="px-4 py-3 font-semibold">Tên danh mục</th>
                        <th class="px-4 py-3 font-semibold">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($categories as $category)
                    <tr class="border-b border-slate-200 dark:border-slate-700">
                        <td class="px-4 py-3">{{ $category->category_id }}</td>
                        <td class="px-4 py-3">{{ $category->category_name }}</td>
                        <td class="px-4 py-3 flex gap-2">
                            <a href="{{ route('admin.categories.edit', $category->category_id) }}" class="text-blue-600 hover:underline">Sửa</a>
                            <button type="button" onclick="showDeleteModal({{ $category->category_id }})" class="text-red-600 hover:underline">Xóa</button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if ($categories instanceof \Illuminate\Pagination\LengthAwarePaginator && $categories->hasPages())
            <div class="mt-4">
                {{ $categories->links() }}
            </div>
        @endif
    @endif

    <!-- Modal xác nhận xóa -->
    <div id="deleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center hidden">
        <div class="bg-white dark:bg-slate-800 rounded-xl p-6 max-w-sm w-full">
            <h3 class="text-lg font-semibold text-slate-800 dark:text-slate-100">Xác nhận xóa</h3>
            <p class="text-sm text-slate-600 dark:text-slate-300 mt-2">Bạn có chắc muốn xóa danh mục này?</p>
            <div class="mt-4 flex justify-end gap-2">
                <button onclick="closeDeleteModal()" class="rounded-xl px-4 py-2 text-sm font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-200/60 dark:hover:bg-slate-700/60">Hủy</button>
                <form id="deleteForm" method="POST" action="">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="rounded-xl px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700">Xóa</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function showDeleteModal(categoryId) {
            const form = document.getElementById('deleteForm');
            form.action = '{{ route("admin.categories.destroy", ":id") }}'.replace(':id', categoryId);
            document.getElementById('deleteModal').classList.remove('hidden');
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.add('hidden');
        }
    </script>
</div>
@endsection