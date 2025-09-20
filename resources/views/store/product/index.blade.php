@extends('layouts.store')

@section('content')
<!-- <div class="max-w-7xl mx-auto px-3 lg:px-5 py-6"> -->
<div class="max-w-screen-2xl 2xl:max-w-[1680px] mx-auto px-2 sm:px-4 lg:px-6 2xl:px-8 py-6">

    <!-- <div class="rounded-3xl overflow-hidden mb-5 shadow-sm ring-1 ring-gray-200/60">
        <img src="{{ asset('images/banners/1.webp') }}" class="w-full h-auto" alt="">
    </div> -->

    <div class="grid grid-cols-12 gap-5">
      @php
  $kept = collect(request()->query())->except(['category','page']); // đổi key cho phù hợp 'brand','price' ở form tương ứng
@endphp

@foreach($kept as $k => $v)
  @if(is_array($v))
    @foreach($v as $vv)
      <input type="hidden" name="{{ $k }}[]" value="{{ e($vv) }}">
    @endforeach
  @else
    <input type="hidden" name="{{ $k }}" value="{{ e($v) }}">
  @endif
@endforeach


<aside class="col-span-12 md:col-span-3 space-y-5 md:sticky md:top-4 self-start">
  {{-- Danh mục --}}
  <div class="bg-white rounded-2xl p-4 ring-1 ring-gray-200/70">
    <div class="text-sm font-semibold text-gray-900 mb-3 uppercase">Loại sản phẩm</div>
    <div class="space-y-2" data-filter-group="category">
      @foreach($categories as $c)
        <label class="flex items-center gap-2">
          <input type="checkbox" value="{{ $c->category_id }}" class="h-4 w-4 accent-blue-600 filter-input"
                 {{ (string)$category===(string)$c->category_id ? 'checked' : '' }}>
          <span class="text-sm text-gray-700">{{ $c->category_name }}</span>
        </label>
      @endforeach
    </div>
  </div>

  {{-- Thương hiệu --}}
  <div class="bg-white rounded-2xl p-4 ring-1 ring-gray-200/70">
    <div class="text-sm font-semibold text-gray-900 mb-3 uppercase">Thương hiệu</div>
    <div class="space-y-2" data-filter-group="brand">
      @foreach($brands as $b)
        <label class="flex items-center gap-2">
          <input type="checkbox" value="{{ $b->brand_id }}" class="h-4 w-4 accent-blue-600 filter-input"
                 {{ (string)$brand===(string)$b->brand_id ? 'checked' : '' }}>
          <span class="text-sm text-gray-700">{{ $b->brand_name }}</span>
        </label>
      @endforeach
    </div>
  </div>

  {{-- Mức giá --}}
  <div class="bg-white rounded-2xl p-4 ring-1 ring-gray-200/70">
    <div class="text-sm font-semibold text-gray-900 mb-3 uppercase">Mức giá</div>
    <div class="space-y-2" data-filter-group="price">
      @foreach($priceRanges as $pr)
        <label class="flex items-center gap-2">
          <input type="checkbox" value="{{ $pr['value'] }}" class="h-4 w-4 accent-blue-600 filter-input"
                 {{ (string)$price===(string)$pr['value'] ? 'checked' : '' }}>
          <span class="text-sm text-gray-700">{{ $pr['label'] }}</span>
        </label>
      @endforeach
    </div>
  </div>
</aside>



        {{-- Content --}}
<section class="col-span-12 md:col-span-9">
  {{-- header + sort giữ nguyên, chỉ thêm id cho select nếu dùng --}}
  <div id="list-container">
    @include('store.product._grid') {{-- lần load đầu render sẵn --}}
  </div>
</section>        
<script>
(function(){
  const container = document.getElementById('list-container');

  // ép đơn chọn cho mỗi nhóm checkbox
  document.querySelectorAll('[data-filter-group]').forEach(group => {
    group.addEventListener('change', e => {
      if (!e.target.classList.contains('filter-input')) return;
      // bỏ chọn các ô khác trong nhóm
      group.querySelectorAll('.filter-input').forEach(inp => {
        if (inp !== e.target) inp.checked = false;
      });
      applyFilters();
    });
  });

  // nếu có select sort
  const sortSelect = document.querySelector('select[name="sort"]');
  if (sortSelect) sortSelect.addEventListener('change', applyFilters);

  // bắt link phân trang để AJAX
  container.addEventListener('click', function(e){
    const a = e.target.closest('a');
    if (!a) return;
    if (a.closest('#products-pagination')) {
      e.preventDefault();
      fetchAndSwap(a.href);
    }
  });

  function applyFilters(){
    const url = new URL(window.location.href);
    const params = url.searchParams;
// nếu đang ở /category/<id> và có params.category -> đổi pathname
const m = location.pathname.match(/^\/category\/(\d+)/);
if (m) {
  const pickedCat = params.get('category');
  if (pickedCat && pickedCat !== m[1]) {
    url.pathname = '/category/' + pickedCat;
    params.delete('category'); // vì id đã nằm trên path
  }
}

    // reset các param đơn chọn
    ['category','brand','price'].forEach(k => params.delete(k));

    // đọc checked mỗi nhóm -> đặt 1 value
    document.querySelectorAll('[data-filter-group]').forEach(group => {
      const key = group.getAttribute('data-filter-group');
      const picked = group.querySelector('.filter-input:checked');
      if (picked) params.set(key, picked.value);
    });

    if (sortSelect && sortSelect.value) params.set('sort', sortSelect.value);
    else params.delete('sort');

    // luôn bỏ trang cũ
    params.delete('page');

    const newUrl = url.pathname + '?' + params.toString();
    history.pushState({}, '', newUrl);
    fetchAndSwap(newUrl);
  }

async function fetchAndSwap(url){
  try{
    const res = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' }});
    let data;
    try {
      data = await res.json();              // ưu tiên JSON
    } catch {
      const html = await res.text();        // fallback nếu server trả HTML
      data = { html };
    }
    if (!data || typeof data.html !== 'string') {
      console.error('AJAX format error:', data);
      return;
    }
    document.getElementById('list-container').innerHTML = data.html;
    document.getElementById('list-container').scrollIntoView({ behavior: 'smooth', block: 'start' });
  }catch(err){
    console.error(err);
  }
}

  // khi back/forward: tải lại fragment đúng với URL
  window.addEventListener('popstate', () => fetchAndSwap(location.href));
})();
</script>
    </div>
</div>
@endsection