@extends('layouts.store')

@section('content')
{{-- Bộ lọc nhanh --}}
<div class="mb-5 flex flex-col md:flex-row gap-3 items-stretch md:items-center">
    <form action="{{ route('home') }}" method="get" class="flex-1 md:hidden">
        <input type="text" name="q" value="{{ $q ?? '' }}" placeholder="Tìm kiếm…"
            class="w-full rounded-xl border border-slate-300/70 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2">
    </form>

    @if($categories->count())
    <form action="{{ route('home') }}" method="get" class="flex items-center gap-2">
        <input type="hidden" name="q" value="{{ $q ?? '' }}">
        <select name="cat" class="rounded-xl border border-slate-300/70 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2">
            <option value="">Tất cả danh mục</option>
            @foreach($categories as $c)
            <option value="{{ $c->category_id }}" {{ ($cat ?? '')==$c->category_id?'selected':'' }}>
                {{ $c->category_name }}
            </option>
            @endforeach
        </select>
        @if($brands->count())
        <select name="brand" class="rounded-xl border border-slate-300/70 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2">
            <option value="">Tất cả thương hiệu</option>
            @foreach($brands as $b)
            <option value="{{ $b->brand_id }}" {{ ($brand ?? '')==$b->brand_id?'selected':'' }}>
                {{ $b->brand_name }}
            </option>
            @endforeach
        </select>
        @endif
        <button class="rounded-xl bg-slate-900 dark:bg-brand-600 text-white px-3 py-2">Lọc</button>
    </form>
    @endif
</div>

{{-- Lưới sản phẩm --}}
@if($products->count())
<div class="grid gap-4 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
    @foreach($products as $p)
    <a href="{{ route('product.show', ['id'=>$p->getKey(), 'slug'=>Str::slug($p->display_name)]) }}"
        class="group rounded-2xl overflow-hidden bg-white dark:bg-slate-800 border border-slate-200/70 dark:border-slate-700 hover:shadow-md transition">
        <div class="aspect-[4/3] bg-slate-100 dark:bg-slate-700 overflow-hidden">
            @php $img = $p->display_image; @endphp
            @if($img)
            <img src="{{ $img }}" alt="{{ $p->display_name }}" class="w-full h-full object-cover group-hover:scale-[1.02] transition">
            @else
            <div class="w-full h-full grid place-items-center text-slate-400">No Image</div>
            @endif
        </div>
        <div class="p-3">
            <h3 class="font-medium line-clamp-2 min-h-[44px]">{{ $p->display_name }}</h3>
            <div class="mt-2 flex items-baseline gap-2">
                @if($p->display_sale_price)
                <span class="text-lg font-semibold text-rose-600">
                    {{ number_format($p->display_sale_price,0,',','.') }}₫
                </span>
                @if($p->display_price)
                <span class="text-sm text-slate-500 line-through">
                    {{ number_format($p->display_price,0,',','.') }}₫
                </span>
                @endif
                @elseif($p->display_price)
                <span class="text-lg font-semibold">
                    {{ number_format($p->display_price,0,',','.') }}₫
                </span>
                @else
                <span class="text-sm text-slate-500">Liên hệ</span>
                @endif
            </div>
            <button class="mt-3 w-full rounded-xl bg-slate-900 dark:bg-brand-600 text-white py-2 text-sm">Thêm vào giỏ</button>
        </div>
    </a>
    @endforeach
</div>

<div class="mt-6">
    {{ $products->onEachSide(1)->links() }}
</div>
@else
<div class="text-center text-slate-500">Chưa có sản phẩm.</div>
@endif
@endsection