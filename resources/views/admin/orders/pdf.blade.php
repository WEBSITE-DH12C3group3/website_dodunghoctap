<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Báo cáo đơn hàng</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        h1 { text-align: center; }
        .header { margin-bottom: 20px; }
        .header p { margin: 5px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .no-items { font-style: italic; color: #666; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Báo cáo đơn hàng</h1>
        <p>Khoảng thời gian: {{ $periodText }}</p>
        <p>Người xuất báo cáo: {{ $userName }}</p>
        <p>Ngày xuất: {{ $date }}</p>
    </div>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Khách hàng</th>
                <th>Ngày đặt</th>
                <th>Tổng tiền</th>
                <th>Thanh toán</th>
                <th>Trạng thái</th>
                <th>Địa chỉ giao hàng</th>
                <th>Đơn vị vận chuyển</th>
                <th>Trạng thái giao hàng</th>
                <th>Sản phẩm</th>
                <th>Số lượng</th>
                <th>Giá</th>
            </tr>
        </thead>
        <tbody>
            @php
                $statusMap = [
                    'pending' => 'Chờ xử lý',
                    'confirmed' => 'Đã xác nhận',
                    'cancelled' => 'Đã hủy',
                    'delivered' => 'Đã giao',
                ];
                $paymentMethodMap = [
                    'cod' => 'Thanh toán khi nhận hàng',
                    'bank_transfer' => 'Chuyển khoản ngân hàng',
                    'momo' => 'Momo',
                ];
            @endphp
            @foreach ($orders as $order)
                @php
                    $items = $order->items()->with('product')->get();
                    $rowspan = $items->isEmpty() ? 1 : $items->count();
                    $first = true;
                @endphp
                @if ($items->isEmpty())
                    <tr>
                        <td>{{ $order->order_id }}</td>
                        <td>{{ $order->user?->full_name ?? 'Khách vãng lai' }}</td>
                        <td>{{ date('d/m/Y H:i', strtotime($order->order_date)) }}</td>
                        <td>{{ number_format($order->total_amount, 0, ',', '.') }} VNĐ</td>
                        <td>{{ $paymentMethodMap[$order->payment_method] ?? ucfirst($order->payment_method) }}</td>
                        <td>{{ $statusMap[$order->status] ?? ucfirst($order->status) }}</td>
                        <td>{{ $order->delivery?->address ?? 'Không xác định' }}</td>
                        <td>{{ $order->delivery?->shipping_provider ?? 'Không xác định' }}</td>
                        <td>{{ $order->delivery?->delivery_status ?? 'Không xác định' }}</td>
                        <td colspan="3" class="no-items">Không có mục hàng</td>
                    </tr>
                @else
                    @foreach ($items as $item)
                        <tr>
                            @if ($first)
                                <td rowspan="{{ $rowspan }}">{{ $order->order_id }}</td>
                                <td rowspan="{{ $rowspan }}">{{ $order->user?->full_name ?? 'Khách vãng lai' }}</td>
                                <td rowspan="{{ $rowspan }}">{{ date('d/m/Y H:i', strtotime($order->order_date)) }}</td>
                                <td rowspan="{{ $rowspan }}">{{ number_format($order->total_amount, 0, ',', '.') }} VNĐ</td>
                                <td rowspan="{{ $rowspan }}">{{ $paymentMethodMap[$order->payment_method] ?? ucfirst($order->payment_method) }}</td>
                                <td rowspan="{{ $rowspan }}">{{ $statusMap[$order->status] ?? ucfirst($order->status) }}</td>
                                <td rowspan="{{ $rowspan }}">{{ $order->delivery?->address ?? 'Không xác định' }}</td>
                                <td rowspan="{{ $rowspan }}">{{ $order->delivery?->shipping_provider ?? 'Không xác định' }}</td>
                                <td rowspan="{{ $rowspan }}">{{ $order->delivery?->delivery_status ?? 'Không xác định' }}</td>
                                @php $first = false; @endphp
                            @endif
                            <td>{{ $item->product?->product_name ?? 'Không xác định' }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>{{ number_format($item->price, 0, ',', '.') }} VNĐ</td>
                        </tr>
                    @endforeach
                @endif
            @endforeach
        </tbody>
    </table>
</body>
</html>