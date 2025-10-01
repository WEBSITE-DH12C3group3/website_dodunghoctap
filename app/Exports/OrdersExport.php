<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class OrdersExport implements FromCollection, WithHeadings
{
    protected $orders;

    public function __construct($orders)
    {
        $this->orders = $orders;
    }

    public function collection()
    {
        $data = [];
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

        foreach ($this->orders as $order) {
            $items = $order->items()->with('product')->get();
            if ($items->isEmpty()) {
                $data[] = [
                    'ID' => $order->order_id,
                    'Khách hàng' => $order->user?->full_name ?? 'Khách vãng lai',
                    'Ngày đặt' => date('d/m/Y H:i', strtotime($order->order_date)),
                    'Tổng tiền' => number_format($order->total_amount, 0, ',', '.') . ' VNĐ',
                    'Thanh toán' => $paymentMethodMap[$order->payment_method] ?? ucfirst($order->payment_method),
                    'Trạng thái' => $statusMap[$order->status] ?? ucfirst($order->status),
                    'Địa chỉ giao hàng' => $order->delivery?->address ?? 'Không xác định',
                    'Đơn vị vận chuyển' => $order->delivery?->shipping_provider ?? 'Không xác định',
                    'Trạng thái giao hàng' => $order->delivery?->delivery_status ?? 'Không xác định',
                    'Sản phẩm' => '',
                    'Số lượng' => '',
                    'Giá' => '',
                ];
            } else {
                foreach ($items as $index => $item) {
                    $data[] = [
                        'ID' => $index === 0 ? $order->order_id : '',
                        'Khách hàng' => $index === 0 ? ($order->user?->full_name ?? 'Khách vãng lai') : '',
                        'Ngày đặt' => $index === 0 ? date('d/m/Y H:i', strtotime($order->order_date)) : '',
                        'Tổng tiền' => $index === 0 ? number_format($order->total_amount, 0, ',', '.') . ' VNĐ' : '',
                        'Thanh toán' => $index === 0 ? ($paymentMethodMap[$order->payment_method] ?? ucfirst($order->payment_method)) : '',
                        'Trạng thái' => $index === 0 ? ($statusMap[$order->status] ?? ucfirst($order->status)) : '',
                        'Địa chỉ giao hàng' => $index === 0 ? ($order->delivery?->address ?? 'Không xác định') : '',
                        'Đơn vị vận chuyển' => $index === 0 ? ($order->delivery?->shipping_provider ?? 'Không xác định') : '',
                        'Trạng thái giao hàng' => $index === 0 ? ($order->delivery?->delivery_status ?? 'Không xác định') : '',
                        'Sản phẩm' => $item->product?->product_name ?? 'Không xác định',
                        'Số lượng' => $item->quantity,
                        'Giá' => number_format($item->price, 0, ',', '.') . ' VNĐ',
                    ];
                }
            }
        }
        return collect($data);
    }

    public function headings(): array
    {
        return [
            'ID', 
            'Khách hàng', 
            'Ngày đặt', 
            'Tổng tiền', 
            'Thanh toán', 
            'Trạng thái', 
            'Địa chỉ giao hàng', 
            'Đơn vị vận chuyển', 
            'Trạng thái giao hàng', 
            'Sản phẩm', 
            'Số lượng', 
            'Giá'
        ];
    }
}