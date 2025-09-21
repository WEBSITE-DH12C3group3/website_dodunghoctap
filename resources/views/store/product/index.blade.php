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
            {{-- Danh mục (đơn chọn) --}}
            <div class="bg-white rounded-2xl p-4 ring-1 ring-gray-200/70">
                <div class="text-sm font-semibold text-gray-900 mb-3 uppercase">Loại sản phẩm</div>
                <div class="space-y-2" data-filter-group="category">
                    @foreach($categories as $c)
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox"
                            value="{{ $c->category_id }}"
                            class="h-4 w-4 accent-blue-600 filter-input"
                            {{ (string)request('category') === (string)$c->category_id ? 'checked' : '' }}>
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
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox"
                            value="{{ $b->brand_id }}"
                            class="h-4 w-4 accent-blue-600 filter-input"
                            {{ (string)request('brand') === (string)$b->brand_id ? 'checked' : '' }}>
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
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox"
                            value="{{ $pr['value'] }}"
                            class="h-4 w-4 accent-blue-600 filter-input"
                            {{ (string)request('price') === (string)$pr['value'] ? 'checked' : '' }}>
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
            document.addEventListener('DOMContentLoaded', function() {
                const container = document.getElementById('list-container');
                const PRODUCTS_BASE = @json(route('store.product.index')); // "/products"

                function getPicked(groupKey) {
                    const g = document.querySelector(`[data-filter-group="${groupKey}"]`);
                    if (!g) return null;
                    const picked = g.querySelector('.filter-input:checked');
                    return picked ? picked.value : null;
                }

                // ép đơn chọn trong mỗi nhóm
                document.querySelectorAll('[data-filter-group]').forEach(group => {
                    group.addEventListener('change', e => {
                        if (!e.target.classList.contains('filter-input')) return;
                        group.querySelectorAll('.filter-input').forEach(inp => {
                            if (inp !== e.target) inp.checked = false;
                        });
                        applyFilters();
                    });
                });

                // sort (nếu dùng select)
                const sortSelect = document.querySelector('select[name="sort"]');
                if (sortSelect) sortSelect.addEventListener('change', applyFilters);

                // phân trang ajax
                container.addEventListener('click', function(e) {
                    const a = e.target.closest('a');
                    if (a && a.closest('#products-pagination')) {
                        e.preventDefault();
                        fetchAndSwap(a.href);
                    }
                });

                function applyFilters() {
                    const base = new URL(PRODUCTS_BASE, window.location.origin);
                    const params = base.searchParams;

                    const cat = getPicked('category');
                    const brand = getPicked('brand');
                    const price = getPicked('price');
                    const sort = sortSelect ? sortSelect.value : '';

                    params.delete('category');
                    params.delete('brand');
                    params.delete('price');
                    params.delete('sort');
                    params.delete('page');

                    if (cat) params.set('category', cat);
                    if (brand) params.set('brand', brand);
                    if (price) params.set('price', price);
                    if (sort) params.set('sort', sort);

                    // nếu từ trang scope (new/best/featured) muốn giữ logic đó:
                    @if(!empty($scope))
                    params.set('scope', @json($scope)); // 'new' | 'best' | 'feat'
                    @endif

                    const newUrl = base.pathname + (params.toString() ? ('?' + params.toString()) : '');
                    history.pushState({}, '', newUrl);
                    fetchAndSwap(newUrl);
                }

                async function fetchAndSwap(url) {
                    try {
                        const res = await fetch(url, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });
                        let data;
                        try {
                            data = await res.json();
                        } catch {
                            data = {
                                html: await res.text()
                            };
                        }
                        if (!data || typeof data.html !== 'string') {
                            console.error('AJAX format error', data);
                            return;
                        }
                        container.innerHTML = data.html;
                        container.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    } catch (err) {
                        console.error(err);
                    }
                }

                // back/forward
                window.addEventListener('popstate', () => fetchAndSwap(location.href));
            });
        </script>


    </div>
</div>
@endsection