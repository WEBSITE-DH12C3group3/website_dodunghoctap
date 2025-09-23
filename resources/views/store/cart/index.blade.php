@extends('layouts.store')
@section('title', 'Giỏ hàng')
@section('content')
<div class="max-w-5xl mx-auto px-4 py-6">
    <h1 class="text-2xl font-bold mb-4">Giỏ hàng</h1>

    <div class="max-w-screen-2xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        {{-- Tiêu đề --}}
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl sm:text-3xl font-bold tracking-tight">Giỏ hàng</h1>
            <a href="{{ route('home') }}" class="text-sm text-blue-600 hover:underline">Tiếp tục mua sắm</a>
        </div>

        @if (empty($cart) || count($cart) === 0)
        {{-- State trống --}}
        <div class="bg-white rounded-2xl p-10 text-center ring-1 ring-gray-100 shadow-sm">
            <div class="text-2xl font-semibold mb-2">
                <img src="https://cdn.hstatic.net/themes/200001055169/1001394513/14/cart_banner_image.jpg?v=468" alt="empty" class="mx-auto h-60 object-contain">
            </div>
            <p class="text-gray-500 mb-6">Bạn chưa có sản phẩm nào. Hãy khám phá các ưu đãi hấp dẫn nhé!</p>
            <div class="flex justify-center">
                <a href="{{ route('home') }}" class="items-center justify-center inline-flex h-11 px-6 rounded-full bg-indigo-600 text-white font-medium hover:bg-indigo-700">
                    Mua sắm ngay
                </a>
            </div>
        </div>
        @else
        <div class="bg-white rounded-2xl shadow-sm divide-y">
            @foreach($cart as $item)
            <div class="p-4 flex items-center gap-4">
                <img src="{{ asset('storage/'.$item['image']) }}" class="w-16 h-16 object-contain bg-gray-50 rounded" />
                <div class="flex-1">
                    <div class="font-medium">{{ $item['name'] }}</div>
                    <div class="text-blue-700 font-semibold">{{ number_format($item['price'],0,',','.') }}đ</div>
                </div>
                <form action="{{ route('cart.update') }}" method="POST" class="flex items-center gap-2">
                    @csrf
                    <input type="hidden" name="id" value="{{ $item['id'] }}">
                    <input name="qty" value="{{ $item['qty'] }}" class="w-16 text-center border rounded" />
                    <button class="px-3 py-2 rounded bg-gray-800 text-white">Cập nhật</button>
                </form>
                <form action="{{ route('cart.remove', $item['id']) }}" method="POST" class="ml-2">
                    @csrf @method('DELETE')
                    <button class="px-3 py-2 rounded bg-rose-600 text-white">Xóa</button>
                </form>
            </div>
            @endforeach
        </div>

        <<<<<<< Updated upstream
            <div class="mt-4 flex items-center justify-between">
            <div class="text-lg">
                Tổng: <span class="font-bold text-blue-700">{{ number_format($total,0,',','.') }}đ</span>
            </div>

            <form action="{{ route('checkout.vnpay.start') }}" method="POST">
                @csrf
                <button class="h-12 px-6 rounded-full bg-indigo-700 text-white grid place-items-center">
                    Thanh toán VNPAY
                </button>
            </form>
            =======
            @endforeach

            {{-- Banner freeship --}}
            <div class="flex items-center gap-3 text-[#0D2E69] bg-blue-50 rounded-2xl px-4 py-3 ring-1 ring-blue-100">
                <img src="https://cdn-icons-png.flaticon.com/512/1670/1670915.png " alt="Freeship" class="w-6 h-6">
                <p class="text-sm">Miễn phí vận chuyển cho đơn hàng từ <span class="font-semibold">100,000đ</span></p>
            </div>
    </div>

    {{-- Cột phải: tóm tắt đơn hàng --}}
    <aside class="lg:sticky lg:top-24">
        <div class="bg-white rounded-2xl ring-1 ring-gray-100 shadow-sm p-4 sm:p-5">
            <div class="flex items-center justify-between">
                <h2 class="font-semibold">Tổng tiền</h2>
                <div id="cart-total" class="text-xl font-bold text-[#2659f3]">
                    {{ number_format($total,0,',','.') }}đ
                </div>
            </div>
            <script>
                (() => {
                    const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                    const routeUpdate = @json(route('cart.update'));
                    const format = n => n.toLocaleString('vi-VN');
                    const timers = new Map(); // debounce theo mỗi row

                    // Chuẩn hoá chuỗi thành số lượng >= 1
                    function normalizeQty(val) {
                        const n = parseInt(String(val).replace(/[^\d]/g, ''), 10);
                        return isNaN(n) || n < 1 ? 1 : n;
                    }

                    async function postUpdate(rowEl, qty) {
                        const id = rowEl.dataset.id;
                        const price = parseInt(rowEl.dataset.price || '0', 10);

                        const res = await fetch(routeUpdate, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrf,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                id,
                                qty
                            })
                        });

                        if (!res.ok) throw new Error('Update failed');
                        const data = await res.json();

                        // Cập nhật UI
                        const subtotalEl = rowEl.closest('.bg-white, .ring-1, [data-subtotal]') // tìm card chứa
                            .querySelector('[data-subtotal]');
                        if (subtotalEl) {
                            const sub = (data.itemSubtotal != null) ? data.itemSubtotal : (price * qty);
                            subtotalEl.textContent = format(sub) + 'đ';
                        }
                        if (data.total != null) {
                            const totalEl = document.getElementById('cart-total');
                            if (totalEl) totalEl.textContent = format(data.total) + 'đ';
                        }
                    }

                    // Click nút +/- vẫn hoạt động
                    document.addEventListener('click', async (e) => {
                        const btn = e.target.closest('.qty-btn');
                        if (!btn) return;

                        const row = btn.closest('[data-cart-row]');
                        const input = row.querySelector('.qty-input');
                        const curr = normalizeQty(input.value);
                        const qty = Math.max(1, curr + (parseInt(btn.dataset.delta || '0', 10)));

                        input.value = qty;

                        try {
                            await postUpdate(row, qty);
                        } catch {
                            location.reload();
                        }
                    });

                    // Gõ số trong input → debounce update
                    document.addEventListener('input', (e) => {
                        const inp = e.target.closest('.qty-input');
                        if (!inp) return;

                        // Giữ chỉ chữ số, không cho ký tự khác
                        const cleaned = String(inp.value).replace(/[^\d]/g, '');
                        inp.value = cleaned;

                        const row = inp.closest('[data-cart-row]');
                        // debounce 350ms
                        clearTimeout(timers.get(row));
                        const t = setTimeout(async () => {
                            const qty = normalizeQty(inp.value);
                            inp.value = qty;
                            try {
                                await postUpdate(row, qty);
                            } catch {
                                /* im lặng */
                            }
                        }, 350);
                        timers.set(row, t);
                    });

                    // Enter/blur → cập nhật ngay
                    document.addEventListener('keydown', async (e) => {
                        const inp = e.target.closest('.qty-input');
                        if (!inp) return;
                        if (e.key === 'Enter') {
                            e.preventDefault();
                            const row = inp.closest('[data-cart-row]');
                            const qty = normalizeQty(inp.value);
                            inp.value = qty;
                            try {
                                await postUpdate(row, qty);
                            } catch {
                                location.reload();
                            }
                        } else if (e.key === 'ArrowUp' || e.key === 'ArrowDown') {
                            // Hỗ trợ mũi tên ↑↓ tăng/giảm
                            e.preventDefault();
                            const row = inp.closest('[data-cart-row]');
                            const curr = normalizeQty(inp.value);
                            const qty = Math.max(1, curr + (e.key === 'ArrowUp' ? 1 : -1));
                            inp.value = qty;
                            try {
                                await postUpdate(row, qty);
                            } catch {
                                location.reload();
                            }
                        }
                    });

                    document.addEventListener('blur', async (e) => {
                        const inp = e.target.closest('.qty-input');
                        if (!inp) return;
                        const row = inp.closest('[data-cart-row]');
                        const qty = normalizeQty(inp.value);
                        inp.value = qty;
                        try {
                            await postUpdate(row, qty);
                        } catch {
                            /* bỏ qua */
                        }
                    }, true);
                })();
            </script>

            <!-- <div class="mt-3">
                    <label class="flex items-center gap-3 text-sm">
                        <input type="checkbox" class="h-4 w-4 rounded border-gray-300 text-indigo-600">
                        <span>Xuất hoá đơn công ty</span>
                    </label>
                    <p class="mt-2 text-xs text-rose-600 font-medium">
                        *Lưu ý: Nhập rõ ràng và đầy đủ thông tin hoá đơn (không viết tắt phường/xã, quận/huyện, tỉnh/thành phố, tên công ty).
                    </p>
                </div> -->
            <div class="mt-4">
                <label for="note" class="text-sm font-medium">Ghi chú đơn hàng</label>
                <textarea id="note" name="note" rows="3"
                    class="mt-2 w-full rounded-xl border border-gray-200
           px-3 py-2             {{-- đệm trong: cách chữ với khung một tẹo --}}
           leading-relaxed       {{-- dòng thoáng hơn chút --}}
           placeholder:text-gray-400
           focus:outline-none focus:ring-0 focus:border-gray-200
           resize-y"
                    placeholder="Ví dụ: Giao giờ hành chính, gọi trước khi giao..."></textarea>
            </div>

            <form action="#" method="POST" class="mt-5">
                @csrf
                <button type="submit"
                    class="w-full h-12 rounded-full bg-indigo-600 text-white font-semibold shadow-sm hover:bg-indigo-700">
                    Tiến hành đặt hàng
                </button>
            </form>

            <p class="mt-3 text-[13px] text-gray-500 text-center">
                Bằng việc tiếp tục, bạn đồng ý với <a href="#" class="text-blue-600 hover:underline">Điều khoản mua hàng</a>.
            </p>
        </div>
    </aside>
    >>>>>>> Stashed changes
</div>

@endif
</div>
@endsection