@extends('layouts.store')

@section('content')
<main class="mx-auto max-w-screen-2xl px-4 py-8">
    <div class="bg-white rounded-2xl p-6 md:p-8 shadow-sm ring-1 ring-gray-100">
        <h1 class="text-2xl md:text-3xl font-semibold mb-2">{{ $title ?? 'Trang thông tin' }}</h1>
        <p class="text-gray-600 mb-6">Trang đang được cập nhật nội dung. Đây là trang mẫu để test header & footer.</p>

        {{-- Nội dung demo để dễ nhìn layout --}}
        <div class="prose max-w-none">
            <h2>Mục lục demo</h2>
            <ul>
                <li>Mục 1</li>
                <li>Mục 2</li>
                <li>Mục 3</li>
            </ul>
            <p>Nếu muốn mỗi trang có nội dung riêng, bạn có thể tạo file blade khác và sửa route trỏ tới file đó.</p>
        </div>
    </div>
</main>
@endsection