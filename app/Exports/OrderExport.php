<?php

namespace App\Exports;

use App\Entity\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class OrderExport implements WithEvents
{
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                //設定列寬
                $event->sheet->getDelegate()->getColumnDimension('A')->setWidth(8.11);
                $event->sheet->getDelegate()->getColumnDimension('B')->setWidth(8.11);
                $event->sheet->getDelegate()->getColumnDimension('C')->setWidth(8.11);
                $event->sheet->getDelegate()->getColumnDimension('D')->setWidth(8.11);
                $event->sheet->getDelegate()->getColumnDimension('E')->setWidth(8.11);
                $event->sheet->getDelegate()->getColumnDimension('F')->setWidth(8.11);
                $event->sheet->getDelegate()->getColumnDimension('G')->setWidth(8.11);
                $event->sheet->getDelegate()->getColumnDimension('H')->setWidth(8.11);
                $event->sheet->getDelegate()->getColumnDimension('I')->setWidth(8.11);
                $event->sheet->getDelegate()->getColumnDimension('J')->setWidth(8.11);
                $event->sheet->getDelegate()->getColumnDimension('K')->setWidth(8.11);
                //設定行高，$i為資料行數
                for ($i = 0; $i <= 59; $i++) {
                    $event->sheet->getDelegate()->getRowDimension($i)->setRowHeight(13.8);
                }
                //設定區域單元格垂直居中
                $event->sheet->getDelegate()->getStyle('A1:K59')->getAlignment()->setVertical('center');
                $event->sheet->getDelegate()->getStyle('A1:K59')->getAlignment()->setHorizontal('center');
                //設定區域單元格字型、顏色、背景等，其他設定請檢視 applyFromArray 方法，提供了註釋
                $event->sheet->getDelegate()->getStyle('A1:K59')->applyFromArray([
                    'font' => [
                        'name' => 'Noto Sans CJK TC Regular',
                        'color' => [
                            'rgb' => '000000'
                        ],
                        'borders' => [
                            'outline' => [
                                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                                'color' => ['argb' => '000000'],
                            ],
                        ]
                    ]
                ]);
                //合併單元格
                $event->sheet->getDelegate()->mergeCells('A1:G1');
                $event->sheet->getDelegate()->mergeCells('H1:K1');
                $event->sheet->getDelegate()->mergeCells('B2:C2');
                $event->sheet->getDelegate()->mergeCells('D2:E2');
                $event->sheet->getDelegate()->mergeCells('F2:G2');
                $event->sheet->getDelegate()->mergeCells('H2:I2');
            }
        ];
    }
}
