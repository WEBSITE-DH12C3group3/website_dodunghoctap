<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Báo cáo doanh thu</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12pt;
            margin: 20mm;
        }
        h1 {
            text-align: center;
            font-size: 16pt;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .footer {
            position: fixed;
            bottom: 20mm;
            width: 100%;
            text-align: center;
            font-size: 10pt;
            color: #555;
        }
    </style>
</head>
<body>
    <h1>Báo cáo doanh thu {{ $periodText }}</h1>
    <table>
        <tr>
            <th>Khoảng thời gian</th>
            <td>{{ $periodText }}</td>
        </tr>
        <tr>
            <th>Người xuất báo cáo</th>
            <td>{{ $userName }}</td>
        </tr>
        <tr>
            <th>Số đơn hàng</th>
            <td>{{ $ordersCount }}</td>
        </tr>
        <tr>
            <th>Số mặt hàng bán ra</th>
            <td>{{ $sales }}</td>
        </tr>
        <tr>
            <th>Doanh thu</th>
            <td>{{ number_format($revenue) }} VNĐ</td>
        </tr>
        <tr>
            <th>Lợi nhuận</th>
            <td>{{ number_format($profit) }} VNĐ</td>
        </tr>
    </table>
    <div class="footer">
        Báo cáo được tạo vào {{ $date }} bởi {{ $userName }}
    </div>
</body>
</html>