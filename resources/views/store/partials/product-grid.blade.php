@props([
'title' => 'Sản phẩm',
'items' => collect(),
'moreUrl' => null,
])

@php
// Đảm bảo chỉ hiển thị tối đa 8 sản phẩm => 2 hàng * 4 cột
$items = $items instanceof \Illuminate\Support\Collection ? $items->take(8) : collect($items)->take(8);
@endphp

<section class="mb-10">
    <div class="flex items-baseline justify-between mb-4">
        <h2 class="text-xl font-semibold text-gray-900">{{ $title }}</h2>
        @if($moreUrl)
        <a href="{{ $moreUrl }}" class="text-sm text-blue-600 hover:underline">Xem tất cả</a>
        @endif
    </div>

    @if($items->isEmpty())
    <div class="h-28 grid place-items-center rounded-lg border border-dashed border-gray-300 text-gray-500">
        Không có sản phẩm nào
    </div>
    @else
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 md:gap-6">
        @foreach ($items as $p)
        {{-- Card sản phẩm (hover nổi + shadow xanh) --}}
        <a href="{{ route('store.product.show', $p->product_id) }}"
            class="group block bg-white ring-1 ring-gray-200/70 rounded-2xl p-3
          transition-all duration-300
          hover:-translate-y-1 hover:ring-blue-300
          hover:shadow-[0_14px_32px_-8px_rgba(37,99,235,0.45)]">
            {{-- Ảnh --}}
            <div class="aspect-[4/3] rounded-xl overflow-hidden bg-gray-50">
                <img
                    src="{{ $p->image_url ? (Str::startsWith($p->image_url, ['http://', 'https://']) ? $p->image_url : asset('storage/' . ($p->image_url ? ltrim($p->image_url, '/') : ''))) : asset('images/placeholder.jpg') }}"
                    alt="{{ $p->product_name }}"
                    class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-[1.02]">
            </div>

            {{-- Nội dung --}}
            <div class="mt-3 space-y-2">
                {{-- Đã bán (giữ trái) --}}
                <div class="flex items-center gap-2 text-[13px]">
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full
                 bg-blue-50 text-blue-700 ring-1 ring-blue-200">
                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                            <path d="M12 3l2.2 4.46 4.92.72-3.56 3.47.84 4.9L12 14.77 7.6 16.55l.84-4.9L4.88 8.18l4.92-.72L12 3z" />
                        </svg>
                        Đã bán {{ number_format($p->sold ?? 0, 0, ',', '.') }}
                    </span>
                </div>

                {{-- Tên (căn giữa) --}}
                <h3 class="text-sm font-medium text-gray-900 leading-5 line-clamp-2 text-center">
                    {{ $p->product_name }}
                </h3>

                {{-- Rating (căn giữa) --}}
                @php
                $avg = round($p->avg_rating ?? 0, 1);
                $cnt = $p->reviews_count ?? 0;
                @endphp
                <div class="flex items-center justify-center gap-2 text-amber-500">
                    <div class="flex">
                        @for ($i = 1; $i <= 5; $i++)
                            <svg class="w-4 h-4 {{ $i <= floor($avg) ? 'fill-current' : 'fill-gray-200 text-gray-200' }}"
                            viewBox="0 0 20 20" aria-hidden="true">
                            <path d="M10 1.5l2.59 5.25 5.8.84-4.2 4.1.99 5.76L10 14.9 4.82 17.45l.99-5.76-4.2-4.1 5.8-.84L10 1.5z" />
                            </svg>
                            @endfor
                    </div>
                    <span class="text-xs text-gray-500">({{ $cnt }})</span>
                </div>

                {{-- Giá (căn giữa, bỏ giảm) --}}
                <div class="flex items-baseline justify-center gap-2">
                    <div class="text-[17px] font-semibold text-blue-700">
                        {{ number_format($p->price, 0, ',', '.') }}₫
                    </div>
                </div>
            </div>

            {{-- Nút xem nhanh --}}
            <div class="mt-3">
                <span
                    class="inline-flex items-center justify-center w-full h-9 rounded-full
             text-blue-700 ring-1 ring-blue-300
             hover:bg-blue-50 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye-fill" viewBox="0 0 16 16">
                        <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0" />
                        <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8m8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7" />
                    </svg>
                    <span style="margin-left: 8px;">Xem chi tiết</span>
                </span>
            </div>
        </a>
        @endforeach
    </div>
    @endif
</section>