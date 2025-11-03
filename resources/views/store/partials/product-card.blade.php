@php
/** @var \App\Models\Product $product */
$price = number_format((float)($product->price ?? 0), 0, ',', '.').'đ';
$img = $product->image_url ?? $product->image ?? '';
$name = $product->product_name ?? $product->name ?? 'Sản phẩm';
$pid = $product->product_id ?? $product->id;
// Link xem chi tiết (tuỳ route của bạn)
$detailUrl = isset($product->slug)
? url('/product/'.$product->slug)
: url('/product/'.$pid);
@endphp

<a href="{{ $detailUrl }}" class="block group">
    <div class="bg-white border border-gray-100 rounded-xl overflow-hidden hover:shadow-md transition">
        <div class="aspect-[4/3] bg-gray-50 overflow-hidden">
            @if($img)
            <img src="{{ $p->image_url ? asset('storage/' . $p->image_url) : asset('images/placeholder.webp') }}" alt="{{ $name }}" class="w-full h-full object-contain group-hover:scale-105 transition" loading="lazy">
            @else
            <div class="w-full h-full flex items-center justify-center text-gray-400">No image</div>
            @endif
        </div>
        <div class="p-3">
            <h3 class="text-sm font-medium line-clamp-2 group-hover:text-blue-600">{{ $name }}</h3>
            <div class="mt-1 text-base font-semibold text-blue-600">{{ $price }}</div>
            @if(isset($product->sold))
            <div class="mt-1 text-xs text-gray-500">Đã bán: {{ (int)$product->sold }}</div>
            @endif
        </div>
    </div>
</a>