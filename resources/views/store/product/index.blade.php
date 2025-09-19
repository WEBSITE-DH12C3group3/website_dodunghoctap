@extends('layouts.store')

@section('content')
<!-- <div class="max-w-7xl mx-auto px-3 lg:px-5 py-6"> -->
<div class="max-w-screen-2xl 2xl:max-w-[1680px] mx-auto px-2 sm:px-4 lg:px-6 2xl:px-8 py-6">

    <!-- <div class="rounded-3xl overflow-hidden mb-5 shadow-sm ring-1 ring-gray-200/60">
        <img src="{{ asset('images/banners/1.webp') }}" class="w-full h-auto" alt="">
    </div> -->

    <div class="grid grid-cols-12 gap-5">
        @php
        // helper giữ lại query khi đổi filter
        function keep_query_except($except) {
        $html = '';
        foreach(request()->except($except) as $k=>$v){
        if(is_array($v)){ foreach($v as $vv){ $html.="<input type='hidden' name='{$k}[]' value='".e($vv)."'>"; }
        }else{ $html.="<input type='hidden' name='{$k}' value='".e($v)."'>"; }
        } return $html;
        }
        @endphp

        <aside class="col-span-12 md:col-span-3 space-y-5 md:sticky md:top-4 self-start">
            {{-- Danh mục (đơn chọn) --}}
            <div class="bg-white rounded-2xl p-4 ring-1 ring-gray-200/70">
                <div class="text-sm font-semibold text-gray-900 mb-3 uppercase">Loại sản phẩm</div>

                <form id="form-category" class="space-y-2" onchange="gotoCategory(event)">
                    {{-- giữ lại query TRỪ category & page --}}
                    @foreach(collect(request()->query())->except(['category','page']) as $k => $v)
                    @if(is_array($v))
                    @foreach($v as $vv) <input type="hidden" name="{{ $k }}[]" value="{{ e($vv) }}"> @endforeach
                    @else
                    <input type="hidden" name="{{ $k }}" value="{{ e($v) }}">
                    @endif
                    @endforeach

                    @foreach($categories as $c)
                    <label class="flex items-center gap-2">
                        <input type="radio" name="category" value="{{ $c->category_id }}"
                            class="h-4 w-4 accent-blue-600"
                            {{ (string)$category === (string)$c->category_id ? 'checked' : '' }}>
                        <span class="text-sm text-gray-700">{{ $c->category_name }}</span>
                    </label>
                    @endforeach
                </form>
            </div>

            <script>
                function gotoCategory(e) {
                    const form = document.getElementById('form-category');
                    const picked = form.querySelector('input[name="category"]:checked');
                    if (!picked) return;
                    const params = new URLSearchParams(new FormData(form));
                    // /category/{id}?brand=..&price=..&sort=..
                    const url = "{{ url('/category') }}/" + encodeURIComponent(picked.value) + "?" + params.toString();
                    window.location.href = url;
                }
            </script>


            {{-- Thương hiệu (đơn chọn) --}}
            <div class="bg-white rounded-2xl p-4 ring-1 ring-gray-200/70">
                <div class="text-sm font-semibold text-gray-900 mb-3 uppercase">Thương hiệu</div>
                <form method="GET" class="space-y-2">
                    {!! keep_query_except('brand') !!}
                    @foreach($brands as $b)
                    <label class="flex items-center gap-2">
                        <input type="radio" name="brand" value="{{ $b->brand_id }}"
                            class="h-4 w-4 accent-blue-600"
                            {{ (string)$brand===(string)$b->brand_id ? 'checked' : '' }}
                            onchange="this.form.submit()">
                        <span class="text-sm text-gray-700">{{ $b->brand_name }}</span>
                    </label>
                    @endforeach
                </form>
            </div>

            {{-- Mức giá (đơn chọn) --}}
            <div class="bg-white rounded-2xl p-4 ring-1 ring-gray-200/70">
                <div class="text-sm font-semibold text-gray-900 mb-3 uppercase">Mức giá</div>
                <form method="GET" class="space-y-2">
                    {!! keep_query_except('price') !!}
                    @foreach($priceRanges as $pr)
                    <label class="flex items-center gap-2">
                        <input type="radio" name="price" value="{{ $pr['value'] }}"
                            class="h-4 w-4 accent-blue-600"
                            {{ (string)$price===(string)$pr['value'] ? 'checked' : '' }}
                            onchange="this.form.submit()">
                        <span class="text-sm text-gray-700">{{ $pr['label'] }}</span>
                    </label>
                    @endforeach
                </form>
            </div>
        </aside>


        {{-- Content --}}
        <section class="col-span-12 md:col-span-9">
            <div class="flex flex-wrap items-center justify-between gap-3 mb-3">
                <h1 class="text-2xl font-bold tracking-wide text-gray-900 uppercase">Văn phòng phẩm</h1>

                {{-- Sắp xếp: link ngang giống ảnh --}}
                @php $sort = request('sort'); @endphp
                <div class="flex flex-wrap items-center gap-4 text-sm">
                    <span class="text-gray-500">Sắp xếp:</span>
                    @php
                    $sortLinks = [
                    '' => 'Mặc định',
                    'name_asc' => 'Tên A → Z',
                    'name_desc' => 'Tên Z → A',
                    'price_asc' => 'Giá tăng dần',
                    'price_desc' => 'Giá giảm dần',
                    'newest' => 'Hàng mới',
                    ];
                    $q = request()->query();
                    @endphp
                    @foreach($sortLinks as $k => $label)
                    @php $url = url()->current().'?'.http_build_query(array_replace($q, ['sort'=>$k ?: null])); @endphp
                    <a href="{{ $url }}"
                        class="inline-flex items-center h-8 px-2.5 rounded-full
                  {{ $sort===$k || ($k==='' && !$sort) ? 'bg-blue-50 text-blue-700 ring-1 ring-blue-200' : 'text-gray-600 hover:text-blue-700' }}">
                        {{ $label }}
                    </a>
                    @endforeach
                </div>

                {{-- Grid --}}
                @if($products->isEmpty())
                <div class="h-40 grid place-items-center rounded-xl border border-dashed text-gray-500">
                    Không có sản phẩm nào
                </div>
                @else
                <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-4 md:gap-6">
                    @foreach($products as $p)
                    {{-- card tái dùng như bạn đang dùng cho home, giữ hover nổi + bóng xanh --}}
                    <a href="{{ route('store.product.show', $p->product_id) }}"
                        class="group block bg-white ring-1 ring-gray-200/70 rounded-2xl p-3
                     transition-all duration-300 hover:-translate-y-1 hover:ring-blue-300
                     hover:shadow-[0_14px_32px_-8px_rgba(37,99,235,0.45)]">
                        <div class="aspect-[4/3] rounded-xl overflow-hidden bg-gray-50">
                            <img src="{{ Str::startsWith($p->image_url, ['http://','https://','/']) ? $p->image_url : asset('storage/'.$p->image_url) }}"
                                alt="{{ $p->product_name }}"
                                class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-[1.02]">
                        </div>
                        <div class="mt-3 space-y-2 text-center">
                            <div class="flex justify-start">
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-blue-50 text-blue-700 ring-1 ring-blue-200 text-[13px]">
                                    Đã bán {{ number_format($p->sold ?? 0, 0, ',', '.') }}
                                </span>
                            </div>
                            <h3 class="text-sm font-medium text-gray-900 leading-5 line-clamp-2">{{ $p->product_name }}</h3>
                            <div class="flex items-center justify-center gap-2 text-amber-500">
                                <div class="flex">
                                    @for($i=1;$i<=5;$i++)
                                        <svg class="w-4 h-4 {{ $i <= floor($p->avg_rating ?? 0) ? 'fill-current' : 'fill-gray-200 text-gray-200' }}" viewBox="0 0 20 20">
                                        <path d="M10 1.5l2.59 5.25 5.8.84-4.2 4.1.99 5.76L10 14.9 4.82 17.45l.99-5.76-4.2-4.1 5.8-.84L10 1.5z" /></svg>
                                        @endfor
                                </div>
                                <span class="text-xs text-gray-500">({{ $p->reviews_count ?? 0 }})</span>
                            </div>
                            <div class="text-[17px] font-semibold text-blue-700">
                                {{ number_format($p->price,0,',','.') }}₫
                            </div>
                        </div>
                        <div class="mt-3">
                            <span class="inline-flex items-center justify-center w-full h-9 rounded-full text-blue-700 ring-1 ring-blue-300 hover:bg-blue-50 transition-colors">Xem nhanh</span>
                        </div>
                    </a>
                    @endforeach
                </div>
                @endif

                {{-- Pagination --}}
                <div class="mt-6">
                    {{ $products->onEachSide(1)->links() }}
                </div>
        </section>
    </div>
</div>
@endsection