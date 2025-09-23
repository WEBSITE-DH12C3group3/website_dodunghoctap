<div class="flex flex-wrap items-center justify-between gap-3 mb-3">
  <h1 class="text-2xl font-bold tracking-wide text-gray-900 uppercase">Văn phòng phẩm</h1> {{-- Sắp xếp: link ngang giống ảnh --}}
  @php $sort = request('sort'); @endphp
  <div class="flex flex-wrap items-center gap-4 text-sm">
    <span class="text-gray-500">Sắp xếp:</span>
    @php $sortLinks = [ '' => 'Mặc định', 'name_asc' => 'Tên A → Z', 'name_desc' => 'Tên Z → A', 'price_asc' => 'Giá tăng dần', 'price_desc' => 'Giá giảm dần', 'newest' => 'Hàng mới', ]; $q = request()->query(); @endphp @foreach($sortLinks as $k => $label) @php $url = url()->current().'?'.http_build_query(array_replace($q, ['sort'=>$k ?: null])); @endphp
    <a href="{{ $url }}" class="inline-flex items-center h-8 px-2.5 rounded-full {{ $sort===$k || ($k==='' && !$sort) ? 'bg-blue-50 text-blue-700 ring-1 ring-blue-200' : 'text-gray-600 hover:text-blue-700' }}"> {{ $label }} </a> @endforeach
  </div>
</div> {{-- Khối này chứa tiêu đề và liên kết sắp xếp --}}

@if($products->isEmpty())
<div class="h-80 grid place-items-center rounded-xl border border-dashed text-gray-500">
  <img src="https://cdn.hstatic.net/themes/200001055169/1001394513/14/cart_banner_image.jpg?v=468" alt="empty" class="mx-auto h-60 object-contain">
  Không có sản phẩm nào
</div>
@else
{{-- Thêm class "w-full" để chiếm toàn bộ chiều rộng --}}
<div id="products-grid" class="w-full grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-4 md:gap-5 xl:gap-6">
  @foreach($products as $p)
  <a href="{{ route('store.product.show', $p->product_id) }}"
    class="group bg-white ring-1 ring-gray-200/70 rounded-2xl p-3 transition-all duration-300
                hover:-translate-y-1 hover:ring-blue-300 hover:shadow-[0_14px_32px_-8px_rgba(37,99,235,0.45)] flex flex-col">
    <div class="aspect-[4/3] rounded-xl overflow-hidden bg-gray-50">
      <img src="{{ Str::startsWith($p->image_url,['http://','https://','/']) ? $p->image_url : asset('storage/'.$p->image_url) }}"
        alt="{{ $p->product_name }}"
        class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-[1.02]">
    </div>
    <div class="mt-3 space-y-2 text-center flex-1">
      <div class="flex justify-start">
        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-blue-50 text-blue-700 ring-1 ring-blue-200 text-[13px]">
          Đã bán {{ number_format($p->sold ?? 0, 0, ',', '.') }}
        </span>
      </div>
      <h3 class="text-sm font-medium text-gray-900 leading-5 line-clamp-2">{{ $p->product_name }}</h3>
      <div class="flex items-center justify-center gap-2 text-amber-500">
        <div class="flex">
          @for($i=1;$i<=5;$i++)
            <svg class="w-4 h-4 {{ $i <= floor($p->avg_rating ?? 0) ? 'fill-current' : 'fill-gray-200 text-gray-200' }}" viewBox="0 0 20 20" aria-hidden="true">
            <path d="M10 1.5l2.59 5.25 5.8.84-4.2 4.1.99 5.76L10 14.9 4.82 17.45l.99-5.76-4.2-4.1 5.8-.84L10 1.5z" />
            </svg>
            @endfor
        </div>
        <span class="text-xs text-gray-500">({{ $p->reviews_count ?? 0 }})</span>
      </div>
      <div class="text-[17px] font-semibold text-blue-700">
        {{ number_format($p->price, 0, ',', '.') }}₫
      </div>
    </div>
    <div class="mt-3">
      <span class="inline-flex items-center justify-center w-full h-9 rounded-full text-blue-700 ring-1 ring-blue-300 hover:bg-blue-50 transition-colors">
        Xem nhanh
      </span>
    </div>
  </a>
  @endforeach
</div>
@endif

{{-- phân trang --}}
<div id="products-pagination" class="mt-6">
  {{ $products->onEachSide(1)->links() }}
</div>