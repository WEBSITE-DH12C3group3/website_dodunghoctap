<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Báo cáo phiếu nhập kho</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h1>Báo cáo phiếu nhập kho</h1>
    <table>
        <thead>
            <tr>
                <th>Mã phiếu</th>
                <th>Tổng tiền</th>
                <th>Ngày nhập</th>
                <th>Người nhập</th>
                <th>Nhà cung cấp</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($purchaseOrders as $purchaseOrder)
                <tr>
                    <td>{{ $purchaseOrder->code }}</td>
                    <td>{{ number_format($purchaseOrder->total_amount, 0, ',', '.') }} VNĐ</td>
                    <td>{{ date('d/m/Y', strtotime($purchaseOrder->order_date)) }}</td>
                    <td>{{ $purchaseOrder->user?->full_name ?? 'Không xác định' }}</td>
                    <td>{{ $purchaseOrder->supplier?->supplier_name ?? 'Không xác định' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>