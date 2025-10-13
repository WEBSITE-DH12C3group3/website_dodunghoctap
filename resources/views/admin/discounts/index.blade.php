@extends('layouts.app')

@section('content')
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-semibold text-slate-800 dark:text-slate-100">Quản lý mã giảm giá</h1>
            <a href="{{ route('admin.discounts.create') }}"
               class="px-4 py-2 bg-brand-600 text-white rounded-xl hover:bg-brand-700 transition-colors">
                Thêm mã giảm giá
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b dark:border-slate-700">
                        <th class="p-3 text-slate-600 dark:text-slate-300">Mã</th>
                        <th class="p-3 text-slate-600 dark:text-slate-300">Mô tả</th>
                        <th class="p-3 text-slate-600 dark:text-slate-300">Giảm giá</th>
                        <th class="p-3 text-slate-600 dark:text-slate-300">Hiệu lực</th>
                        <th class="p-3 text-slate-600 dark:text-slate-300">Số lần sử dụng</th>
                        <th class="p-3 text-slate-600 dark:text-slate-300">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($coupons as $coupon)
                        <tr class="border-b dark:border-slate-700">
                            <td class="p-3">{{ $coupon->code }}</td>
                            <td class="p-3">{{ $coupon->description ?? 'Không có mô tả' }}</td>
                            <td class="p-3">
                                @if ($coupon->discount_amount)
                                    {{ number_format($coupon->discount_amount, 0, ',', '.') }} VNĐ
                                @elseif ($coupon->discount_percent)
                                    {{ $coupon->discount_percent }}%
                                @else
                                    Không có giảm giá
                                @endif
                            </td>
                            <td class="p-3">
                                @if ($coupon->valid_from && $coupon->valid_to)
                                    {{ $coupon->valid_from->format('d/m/Y') }} - {{ $coupon->valid_to->format('d/m/Y') }}
                                @else
                                    Không giới hạn
                                @endif
                            </td>
                            <td class="p-3">
                                {{ $coupon->used_count }} / {{ $coupon->usage_limit ?? 'Không giới hạn' }}
                            </td>
                            <td class="p-3">
                                <a href="{{ route('admin.discounts.edit', $coupon->coupon_id) }}"
                                   class="text-brand-600 hover:text-brand-700">Sửa</a>
                                <form action="{{ route('admin.discounts.destroy', $coupon->coupon_id) }}" method="POST"
                                      class="inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            onclick="return confirm('Bạn có chắc muốn xóa mã này?')"
                                            class="text-red-600 hover:text-red-700 ml-2">Xóa</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-3 text-center text-slate-600 dark:text-slate-300">
                                Chưa có mã giảm giá nào
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection