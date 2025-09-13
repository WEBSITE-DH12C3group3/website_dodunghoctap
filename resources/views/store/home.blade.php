@extends('layouts.store')

@section('content')
<main class="mx-auto max-w-screen-2xl px-4 py-6 md:py-8">

    {{-- BANNERS --}}
    <section class="mb-6 md:mb-8">
        <div class="grid gap-3 md:gap-4 md:grid-cols-4">
            <a href="#" class="md:col-span-3 overflow-hidden rounded-2xl bg-gray-100 aspect-[16/7] md:aspect-[16/6]">
                <img class="h-full w-full object-cover" src="{{ asset('images/banners/1.webp') }}" alt="Banner">
            </a>
            <div class="grid gap-3 md:gap-4">
                <a href="#" class="overflow-hidden rounded-2xl bg-gray-100 aspect-[16/9]">
                    <img class="h-full w-full object-cover" src="{{ asset('images/banners/2.webp') }}" alt="">
                </a>
                <a href="#" class="overflow-hidden rounded-2xl bg-gray-100 aspect-[16/9]">
                    <img class="h-full w-full object-cover" src="{{ asset('images/banners/3.webp') }}" alt="">
                </a>
            </div>
        </div>
    </section>

    {{-- DANH MỤC (cuộn ngang) --}}
    <section class="mb-6 md:mb-8">
        <div class="flex items-center justify-between mb-3">
            <h2 class="text-lg md:text-xl font-semibold">Danh mục</h2>
            <a href="{{ route('store.categories.index') }}" class="text-blue-700 hover:underline text-sm">Xem tất cả</a>
        </div>
        <ul class="flex gap-3 md:gap-4 overflow-x-auto [-ms-overflow-style:none] [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
            @foreach(($categories ?? []) as $c)
            @php
            $cid = $c->category_id ?? $c['category_id'] ?? null;
            $cname = $c->category_name ?? $c['category_name'] ?? '';
            @endphp
            <li class="shrink-0">
                <a href="{{ $cid ? route('store.category', $cid) : '#' }}"
                    class="inline-flex items-center gap-2 rounded-full bg-white px-4 py-2 text-sm shadow hover:shadow-md">
                    <span class="text-lg">•</span>
                    <span class="whitespace-nowrap">{{ $cname }}</span>
                </a>
            </li>
            @endforeach
        </ul>
    </section>

    {{-- SẢN PHẨM MỚI --}}
    <section class="mb-8 md:mb-10">
        <div class="flex items-center justify-between mb-3 md:mb-4">
            <h2 class="text-lg md:text-xl font-semibold">Sản phẩm mới</h2>
            <a href="{{ route('store.products.new') }}" class="text-blue-700 hover:underline text-sm">Xem tất cả</a>
        </div>
        <div class="grid gap-3 md:gap-4 grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5">
            @forelse($newProducts as $p)
            @include('store.partials.product-card', ['p' => $p])
            @empty
            <p class="col-span-full text-gray-500">Chưa có sản phẩm.</p>
            @endforelse
        </div>
    </section>

    {{-- BANNER NHỎ --}}
    <section class="mb-8 md:mb-10">
        <a href="#" class="block overflow-hidden rounded-2xl bg-gray-100 aspect-[16/4]">
            <img class="h-full w-full object-cover" src="{{ asset('images/banners/banner-wide.jpg') }}" alt="Khuyến mãi">
        </a>
    </section>

    {{-- BÁN CHẠY --}}
    <section class="mb-8 md:mb-10">
        <div class="flex items-center justify-between mb-3 md:mb-4">
            <h2 class="text-lg md:text-xl font-semibold">Bán chạy</h2>
            <a href="{{ route('store.products.best') }}" class="text-blue-700 hover:underline text-sm">Xem tất cả</a>
        </div>
        <div class="grid gap-3 md:gap-4 grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5">
            @forelse($bestSellers as $p)
            @include('store.partials.product-card', ['p' => $p])
            @empty
            <p class="col-span-full text-gray-500">Chưa có dữ liệu.</p>
            @endforelse
        </div>
    </section>

    {{-- NỔI BẬT --}}
    <section class="mb-4 md:mb-12">
        <div class="flex items-center justify-between mb-3 md:mb-4">
            <h2 class="text-lg md:text-xl font-semibold">Nổi bật</h2>
            <a href="{{ route('store.products.featured') }}" class="text-blue-700 hover:underline text-sm">Xem tất cả</a>
        </div>
        <div class="grid gap-3 md:gap-4 grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5">
            @forelse($featured as $p)
            @include('store.partials.product-card', ['p' => $p])
            @empty
            <p class="col-span-full text-gray-500">Chưa có dữ liệu.</p>
            @endforelse
        </div>
    </section>

</main>
@endsection