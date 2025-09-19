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

    {{-- SẢN PHẨM MỚI --}}
    @include('store.partials.product-grid', [
    'title' => 'Sản phẩm mới',
    'items' => $newProducts,
    'moreUrl' => route('store.products.new'),
    ])
    {{-- BANNER NHỎ --}}
    <section class="mb-8 md:mb-10">
        <a href="#" class="block overflow-hidden rounded-2xl bg-gray-100 aspect-[16/4]">
            <img class="h-full w-full object-cover" src="{{ asset('images/banners/4.webp') }}" alt="Khuyến mãi">
        </a>
    </section>
    {{-- BÁN CHẠY --}}
    @include('store.partials.product-grid', [
    'title' => 'Bán chạy',
    'items' => $bestSellers,
    'moreUrl' => route('store.products.best'),
    ])
    {{-- NỔI BẬT --}}
    @include('store.partials.product-grid', [
    'title' => 'Nổi bật',
    'items' => $featured,
    'moreUrl' => route('store.products.featured'),
    ])
</main>
@endsection