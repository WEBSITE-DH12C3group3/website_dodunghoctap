<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Illuminate\Support\Collection;

class SalesReportExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return new Collection([
            [
                $this->data['periodText'],
                $this->data['userName'],
                $this->data['ordersCount'],
                $this->data['sales'],
                number_format($this->data['revenue']) . ' VNĐ',
                number_format($this->data['profit']) . ' VNĐ',
                $this->data['date'],
            ],
        ]);
    }

    public function headings(): array
    {
        return [
            'Khoảng thời gian',
            'Người xuất báo cáo',
            'Số đơn hàng',
            'Số mặt hàng bán ra',
            'Doanh thu',
            'Lợi nhuận',
            'Ngày xuất báo cáo',
        ];
    }
}