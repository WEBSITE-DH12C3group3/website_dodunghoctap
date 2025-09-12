@extends('layouts.store')

@section('content')
<div class="grid gap-6 md:grid-cols-2">
    <div class="rounded-2xl overflow-hidden bg-white dark:bg-slate-800 border dark:border-slate-700">
        @php $img = $p->display_image; @endphp
        <div class="aspect-[4/3] bg-slate-100 dark:bg-slate-700">
            @if($img)
            <img src="{{ $img }}" alt="{{ $p->display_name }}" class="w-full h-full object-cover">
            @else
            <div class="w-full h-full grid place-items-center text-slate-400">No Image</div>
            @endif
        </div>
    </div>
    <div>
        <h1 class="text-2xl font-semibold mb-2">{{ $p->display_name }}</h1>
        <div class="flex items-baseline gap-3 mb-4">
            @if($p->display_sale_price)
            <span class="text-2xl font-bold text-rose-600">{{ number_format($p->display_sale_price,0,',','.') }}₫</span>
            @if($p->display_price)
            <span class="text-slate-500 line-through">{{ number_format($p->display_price,0,',','.') }}₫</span>
            @endif
            @elseif($p->display_price)
            <span class="text-2xl font-bold">{{ number_format($p->display_price,0,',','.') }}₫</span>
            @endif
        </div>
        <button class="rounded-xl bg-slate-900 dark:bg-brand-600 text-white px-4 py-2">Thêm vào giỏ</button>
        <div class="mt-6 prose dark:prose-invert max-w-none">
            {!! nl2br(e($p->description ?? $p->short_description ?? '')) !!}
        </div>
    </div>
</div>
@endsection