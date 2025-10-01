<?php
namespace App\Exports;

use App\Models\PurchaseOrder;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PurchaseOrdersExport implements FromCollection, WithHeadings
{
    protected $purchaseOrders;

    public function __construct($purchaseOrders)
    {
        $this->purchaseOrders = $purchaseOrders;
    }

    public function collection()
    {
        return $this->purchaseOrders->map(function ($order) {
            return [
                'Mã phiếu' => $order->code,
                'Tổng tiền' => number_format($order->total_amount, 0, ',', '.') . ' VNĐ',
                'Ngày nhập' => date('d/m/Y', strtotime($order->order_date)),
                'Người nhập' => $order->user?->full_name ?? 'Không xác định',
                'Nhà cung cấp' => $order->supplier?->supplier_name ?? 'Không xác định',
            ];
        });
    }

    public function headings(): array
    {
        return ['Mã phiếu', 'Tổng tiền', 'Ngày nhập', 'Người nhập', 'Nhà cung cấp'];
    }
}