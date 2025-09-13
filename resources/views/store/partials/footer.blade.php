@php
// Tuỳ biến nhanh bằng biến truyền vào khi include (có mặc định)
$brandLogo = $brandLogo ?? asset('images/logo.svg');
$brandName = $brandName ?? 'Tập đoàn Itaewon';
$siteName = $siteName ?? 'peakvl.com';
$hotline = $hotline ?? '1900 866 819';
$supportTime = $supportTime ?? 'Thứ 2 - Thứ 6 (8h - 17h)';
$supportMail = $supportMail ?? 'n4mc0de@gmail.com';

// Địa chỉ mẫu — thay bằng địa chỉ của bạn
$headOffice = $headOffice ?? 'Tầng 15, Tòa Nhà Vincom, 72 Lê Thánh Tôn, Q.1, TP. Hồ Chí Minh';
$northOffice = $northOffice ?? 'Số 12, Phố Huế, P. Phố Huế, Q. Hai Bà Trưng, Hà Nội';


$year = date('Y');
@endphp

<footer class="mt-10 bg-[#144591] text-white rounded-t-[36px]">
    <div class="mx-auto max-w-screen-2xl px-4 py-8 md:py-10">
        <div class="grid gap-8 md:grid-cols-4">

            {{-- Cột 1: Logo + mô tả + newsletter --}}
            <div>
                <div class="flex items-center gap-3 mb-4">
                    <img src="{{ $brandLogo }}" alt="Logo" class="h-9 w-auto">
                    <span class="sr-only">{{ $brandName }}</span>
                </div>

                <p class="font-semibold text-[#F6D34B]">
                    {{ ucfirst($siteName) }} - Website thương mại điện tử thuộc {{ $brandName }}
                </p>
                <p class="mt-2 opacity-90">
                    Công ty Cổ Phần Tập Đoàn Itaewon<br>
                    GPĐKKD số 0301464830 do Sở KHĐT TP. Hà Nội cấp ngày 14/09/2025.
                </p>

                {{-- Newsletter --}}
                <form action="{{ route('store.newsletter.subscribe') }}" method="POST" class="mt-4 flex rounded-full overflow-hidden bg-white">
                    @csrf
                    <input name="email" type="email" required placeholder="Nhập địa chỉ email"
                        class="flex-1 px-4 py-3 text-gray-900 outline-none" />
                    <button class="px-4 py-3 bg-[#0E3271] hover:bg-[#0b2a5d] whitespace-nowrap">Đăng ký</button>
                </form>

                {{-- Logo đối tác (tuỳ chọn) --}}
                <div class="mt-3 flex items-center gap-3">
                    <img src="https://theme.hstatic.net/1000230347/1001380508/14/footer_bct.png?v=729" alt="Đã thông báo Bộ Công Thương" class="h-10">
                </div>
            </div>

            {{-- Cột 2: Địa chỉ công ty --}}
            <div>
                <h3 class="font-bold text-[#F6D34B] mb-3">ĐỊA CHỈ CÔNG TY</h3>
                <p class="mb-3">
                    <span class="font-semibold text-[#F6D34B]">Head Office:</span> {{ $headOffice }}
                </p>
                <p>
                    <span class="font-semibold text-[#F6D34B]">Miền Bắc:</span> {{ $northOffice }}
                </p>
            </div>

            {{-- Cột 3: Hỗ trợ khách hàng --}}
            <div>
                <h3 class="font-bold text-[#F6D34B] mb-3">HỖ TRỢ KHÁCH HÀNG</h3>
                <p><span class="font-semibold text-[#F6D34B]">Hotline:</span> {{ $hotline }}</p>
                <p>{{ $supportTime }}</p>
                <p class="mb-3"><a href="mailto:{{ $supportMail }}" class="underline">{{ $supportMail }}</a></p>
                <ul class="space-y-2">
                    <li><a class="hover:underline" href="{{ route('store.page.buying_guide') }}">– Hướng dẫn mua hàng</a></li>
                    <li><a class="hover:underline" href="{{ route('store.page.payment_guide') }}">– Hướng dẫn thanh toán</a></li>
                    <li><a class="hover:underline" href="{{ route('store.page.shipping_policy') }}">– Chính sách giao hàng</a></li>
                    <li><a class="hover:underline" href="{{ route('store.page.return_policy') }}">– Chính sách đổi trả & hoàn tiền</a></li>
                    <li><a class="hover:underline" href="{{ route('store.page.loyalty') }}">– Khách hàng thân thiết</a></li>
                    <li><a class="hover:underline" href="{{ route('store.page.priority') }}">– Khách hàng ưu tiên</a></li>
                </ul>
            </div>

            {{-- Cột 4: Về website + social --}}
            <div class="flex flex-col justify-between">
                <div>
                    <h3 class="font-bold text-[#F6D34B] mb-3">VỀ {{ strtoupper($siteName) }}</h3>
                    <ul class="space-y-2">
                        <li><a class="hover:underline" href="{{ route('store.page.about') }}">– Giới thiệu</a></li>
                        <li><a class="hover:underline" href="{{ route('store.page.ads_service') }}">– Dịch vụ in ấn quảng cáo</a></li>
                        <li><a class="hover:underline" href="{{ route('store.page.privacy') }}">– Chính sách bảo mật chung</a></li>
                        <li><a class="hover:underline" href="{{ route('store.page.privacy_personal') }}">– Bảo mật thông tin cá nhân</a></li>
                        <li><a class="hover:underline" href="{{ route('store.page.contact') }}">– Thông tin liên hệ</a></li>
                        <li><a class="hover:underline" href="{{ route('store.page.affiliate') }}">– Chương trình Affiliate</a></li>
                    </ul>
                </div>

                {{-- Social icons --}}
                <div class="mt-6 flex items-center gap-4">
                    <a href="https://facebook.com/nam.hsgc3" target="_blank" aria-label="Facebook"
                        class="h-10 w-10 grid place-content-center rounded-full bg-white/10 hover:bg-white/20">
                        {{-- FB icon --}}
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M13.5 22v-8.5H16l.5-3h-3V8.25c0-.87.29-1.5 1.79-1.5H16.5V4.06C16.24 4.03 15.31 4 14.25 4 12.02 4 10.5 5.24 10.5 7.77V10.5H8v3h2.5V22h3z" />
                        </svg>
                    </a>
                    <a href="https://www.youtube.com/watch?v=dQw4w9WgXcQ" target="_blank" aria-label="YouTube"
                        class="h-10 w-10 grid place-content-center rounded-full bg-white/10 hover:bg-white/20">
                        {{-- YT icon --}}
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M23 7.5a4.51 4.51 0 0 0-3.17-3.18C17.98 4 12 4 12 4s-5.98 0-7.83.32A4.51 4.51 0 0 0 1 7.5 47.63 47.63 0 0 0 .68 12c0 1.79.32 4.5.32 4.5a4.51 4.51 0 0 0 3.17 3.18C6.02 20 12 20 12 20s5.98 0 7.83-.32A4.51 4.51 0 0 0 23 16.5s.32-2.71.32-4.5S23 7.5 23 7.5ZM9.75 15.02V8.98L15.5 12l-5.75 3.02Z" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Bar dưới cùng --}}
    <div class="bg-[#123C86]">
        <div class="mx-auto max-w-screen-2xl px-4 py-3 text-center text-sm opacity-90">
            {{ $year }} © {{ ucfirst($siteName) }} – Bản quyền thuộc {{ $brandName }}.
        </div>
    </div>
</footer>