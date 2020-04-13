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
        $col = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K'];
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
                                'borderStyle' => 'trick',
                                'color' => ['rgb' => '000000'],
                            ],
                        ]
                    ]
                ]);
                //合併單元格
                $event->sheet->getDelegate()->mergeCells('A1:H1');
                $event->sheet->getDelegate()->mergeCells('I1:K1');
                $event->sheet->getDelegate()->mergeCells('B2:C2');
                $event->sheet->getDelegate()->mergeCells('D2:E2');
                $event->sheet->getDelegate()->mergeCells('F2:G2');
                $event->sheet->getDelegate()->mergeCells('H2:I2');

                $event->sheet->setCellValue('A1', '國立鳳山高中線上訂餐系統');
                $event->sheet->setCellValue('I1', '2020-04-13 (一)');
                $event->sheet->setCellValue('B2', '正園');
                $event->sheet->setCellValue('D2', '御饌坊');
                $event->sheet->setCellValue('F2', '彩鶴');
                $event->sheet->setCellValue('H2', '素食');
                $event->sheet->setCellValue('B3', 'A');
                $event->sheet->setCellValue('C3', 'B');
                $event->sheet->setCellValue('D3', 'A');
                $event->sheet->setCellValue('E3', 'B');
                $event->sheet->setCellValue('F3', 'A');
                $event->sheet->setCellValue('G3', 'B');
                $event->sheet->setCellValue('H3', 'A');
                $event->sheet->setCellValue('I3', 'B');
                $event->sheet->setCellValue('J3', '總數量');
                $event->sheet->setCellValue('K3', '總金額');
                for ($g = 1; $g >= 3; $g++) {
                    $r = ($g - 1) * 18;
                    for ($c = 1; $c >= 18; $c++) {
                        var_dump('A' . ($r + $c + 3));
                        $event->sheet->setCellValue('A' . ($r + $c + 3), 'class'/*$g.str_pad($c,2,"0",STR_PAD_LEFT)*/);
                    }
                }
                $event->sheet->setCellValue('A58', '總數量');
                $event->sheet->setCellValue('A59', '總金額');
            }
        ];
    }
}
