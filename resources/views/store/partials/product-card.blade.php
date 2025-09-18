@php($p = $product ?? $item ?? null)
@if($p)
<a href="{{ route('store.product.show', $p->product_id) }}"
    class="group border border-gray-100 rounded-xl p-3 hover:shadow transition bg-white">
    <div class="aspect-[4/3] bg-gray-50 rounded-lg overflow-hidden">
        <img src="{{ asset('storage/'.$p->image_url) }}"
            alt="{{ $p->product_name }}"
            class="w-full h-full object-contain group-hover:scale-105 transition">
    </div>
    <div class="mt-3">
        <div class="line-clamp-2 font-medium text-gray-900">{{ $p->product_name }}</div>
        <div class="mt-1 text-blue-700 font-semibold">
            {{ number_format($p->price,0,',','.') }}đ
        </div>
    </div>
</a>
@endif