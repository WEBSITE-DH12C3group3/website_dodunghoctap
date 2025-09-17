@extends('layouts.app')

@section('content')
<div class="bg-white/80 dark:bg-slate-900/50 backdrop-blur p-6 rounded-2xl shadow ring-1 ring-slate-900/5 dark:ring-white/10">
    <h2 class="text-xl font-semibold text-slate-800 dark:text-slate-100 mb-6">Cập nhật đơn hàng #{{ $order->order_id }}</h2>

    <form action="{{ route('admin.orders.update', $order->order_id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="grid gap-6 md:grid-cols-2">
            <div>
                <label for="status" class="block text-sm font-medium text-slate-700 dark:text-slate-200">Trạng thái đơn hàng</label>
                <select name="status" id="status" class="mt-1 block w-full rounded-xl border-slate-300 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 focus:ring-brand-600 focus:border-brand-600" required>
                    <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="confirmed" {{ $order->status == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                    <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    <option value="delivered" {{ $order->status == 'delivered' ? 'selected' : '' }}>Delivered</option>
                </select>
                @error('status')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="shipping_type" class="block text-sm font-medium text-slate-700 dark:text-slate-200">Loại hình vận chuyển</label>
                <select name="shipping_type" id="shipping_type" class="mt-1 block w-full rounded-xl border-slate-300 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 focus:ring-brand-600 focus:border-brand-600">
                    <option value="standard" {{ old('shipping_type', $order->delivery->shipping_type ?? 'standard') == 'standard' ? 'selected' : '' }}>Standard</option>
                    <option value="express" {{ old('shipping_type', $order->delivery->shipping_type ?? 'standard') == 'express' ? 'selected' : '' }}>Express</option>
                </select>
                @error('shipping_type')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="shipping_provider" class="block text-sm font-medium text-slate-700 dark:text-slate-200">Đơn vị vận chuyển</label>
                <select name="shipping_provider" id="shipping_provider" class="mt-1 block w-full rounded-xl border-slate-300 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 focus:ring-brand-600 focus:border-brand-600">
                    <option value="GHTK" {{ old('shipping_provider', $order->delivery->shipping_provider ?? 'GHTK') == 'GHTK' ? 'selected' : '' }}>GHTK</option>
                    <option value="GHN" {{ old('shipping_provider', $order->delivery->shipping_provider ?? 'GHTK') == 'GHN' ? 'selected' : '' }}>GHN</option>
                    <option value="Viettel Post" {{ old('shipping_provider', $order->delivery->shipping_provider ?? 'GHTK') == 'Viettel Post' ? 'selected' : '' }}>Viettel Post</option>
                    <option value="Other" {{ old('shipping_provider', $order->delivery->shipping_provider ?? 'GHTK') == 'Other' ? 'selected' : '' }}>Other</option>
                </select>
                @error('shipping_provider')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="delivery_status" class="block text-sm font-medium text-slate-700 dark:text-slate-200">Trạng thái giao hàng</label>
                <input type="text" name="delivery_status" id="delivery_status" value="{{ old('delivery_status', $order->delivery->delivery_status ?? 'pending') }}" class="mt-1 block w-full rounded-xl border-slate-300 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 focus:ring-brand-600 focus:border-brand-600">
                @error('delivery_status')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="expected_delivery_date" class="block text-sm font-medium text-slate-700 dark:text-slate-200">Ngày giao dự kiến</label>
                <input type="date" name="expected_delivery_date" id="expected_delivery_date" value="{{ old('expected_delivery_date', $order->delivery->expected_delivery_date ?? now()->addDays(3)->format('Y-m-d')) }}" class="mt-1 block w-full rounded-xl border-slate-300 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 focus:ring-brand-600 focus:border-brand-600">
                @error('expected_delivery_date')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="mt-6 flex gap-4">
            <button type="submit" class="rounded-xl px-4 py-2 text-sm font-medium text-white bg-brand-600 hover:bg-brand-700 shadow-sm">Cập nhật</button>
            <a href="{{ route('admin.orders') }}" class="rounded-xl px-4 py-2 text-sm font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-200/60 dark:hover:bg-slate-800/60">Hủy</a>
        </div>
    </form>
</div>
@endsection