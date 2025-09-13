@php
$id = $p->product_id ?? $p['product_id'] ?? null;
$name = $p->product_name ?? $p['product_name'] ?? '';
$img = $p->image_url ?? $p['image_url'] ?? null;
$price = $p->price ?? $p['price'] ?? null;

$fmt = fn($v) => $v !== null ? number_format((float)$v, 0, ',', '.') . '₫' : '';
$src = $img
? (filter_var($img, FILTER_VALIDATE_URL) ? $img : asset('storage/'.$img))
: null;
@endphp

<article class="group rounded-2xl bg-white ring-1 ring-gray-100 hover:ring-blue-200 transition shadow-sm hover:shadow">
    <a href="{{ $id ? route('store.product.show', $id) : '#' }}"
        class="block overflow-hidden rounded-t-2xl bg-gray-50 aspect-[4/3]">
        @if($src)
        <img class="h-full w-full object-contain p-3 transition duration-300 group-hover:scale-[1.02]" src="{{ $src }}" alt="{{ $name }}">
        @else
        <div class="h-full w-full flex items-center justify-center text-gray-400">No image</div>
        @endif
    </a>

    <div class="p-3 md:p-4">
        <a href="{{ $id ? route('store.product.show', $id) : '#' }}"
            class="block text-sm md:text-[15px] font-medium text-gray-800 hover:text-blue-700 truncate"
            title="{{ $name }}">{{ $name }}</a>

        <div class="mt-1">
            <span class="text-base md:text-lg font-semibold text-gray-900">{{ $fmt($price) }}</span>
        </div>

        <div class="mt-3">
            <a href="{{ $id ? route('cart.add', $id) : '#' }}"
                class="inline-flex items-center justify-center w-full rounded-full bg-blue-600 px-3 py-2 text-white text-sm hover:bg-blue-700">
                Thêm vào giỏ
            </a>
        </div>
    </div>
</article>