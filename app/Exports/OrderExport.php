<?php

namespace App\Exports;

use App\Entity\Dish;
use App\Entity\Sale;
use App\Entity\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;

class OrderExport implements FromCollection, WithTitle, WithEvents, WithCustomStartCell
{
    public $date;

    public function __construct($date)
    {
        $this->date = $date;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $col = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K'];

                $event->sheet->getDelegate()->getPageMargins()->setTop(1);
                $event->sheet->getDelegate()->getPageMargins()->setBottom(1);
                $event->sheet->getDelegate()->getPageMargins()->setRight(0.5);
                $event->sheet->getDelegate()->getPageMargins()->setLeft(0.5);

                //設定列寬
                for ($i = 0; $i < 11; $i++) {
                    $event->sheet->getDelegate()->getColumnDimension($col[$i])->setWidth(8.11);
                }
                //設定行高，$i為資料行數
                for ($i = 0; $i <= 59; $i++) {
                    $event->sheet->getDelegate()->getRowDimension($i)->setRowHeight(13.8);
                }
                //設定區域單元格垂直居中
                $event->sheet->getDelegate()->getStyle('A1:K59')->getAlignment()->setVertical('center');
                $event->sheet->getDelegate()->getStyle('A1:K59')->getAlignment()->setHorizontal('center');
                //設定區域單元格字型、顏色、背景等，其他設定請檢視 applyFromArray 方法，提供了註釋
                $event->sheet->getStyle('A1:K59')->applyFromArray([
                    'font' => [
                        'name' => 'Noto Sans CJK TC Regular',
                        'color' => [
                            'rgb' => '000000'
                        ],
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
                for ($g = 1; $g <= 3; $g++) {
                    $r = ($g - 1) * 18;
                    for ($c = 1; $c <= 18; $c++) {
                        $event->sheet->setCellValue('A' . ($r + $c + 3), $g . str_pad($c, 2, "0", STR_PAD_LEFT));
                    }
                }
                $event->sheet->setCellValue('A58', '總數量');
                $event->sheet->setCellValue('A59', '總金額');

                $event->sheet->getStyle('A1:K59')->applyFromArray([
                    'borders' => [
                        'outline' => [
                            'borderStyle' => 'medium',
                            'color' => ['argb' => '000000'],
                        ],
                        'allBorders' => [
                            'borderStyle' => 'thin',
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                ]);
                $event->sheet->getStyle('A1:K1')->applyFromArray([
                    'borders' => [
                        'outline' => [
                            'borderStyle' => 'medium',
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                ]);
                $event->sheet->getStyle('B2:C59')->applyFromArray([
                    'borders' => [
                        'outline' => [
                            'borderStyle' => 'medium',
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                ]);
                $event->sheet->getStyle('D2:E59')->applyFromArray([
                    'borders' => [
                        'outline' => [
                            'borderStyle' => 'medium',
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                ]);
                $event->sheet->getStyle('F2:G59')->applyFromArray([
                    'borders' => [
                        'outline' => [
                            'borderStyle' => 'medium',
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                ]);
                $event->sheet->getStyle('H2:I59')->applyFromArray([
                    'borders' => [
                        'outline' => [
                            'borderStyle' => 'medium',
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                ]);
                $event->sheet->getStyle('A4:K8')->applyFromArray([
                    'borders' => [
                        'outline' => [
                            'borderStyle' => 'medium',
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                ]);
                $event->sheet->getStyle('A9:K13')->applyFromArray([
                    'borders' => [
                        'outline' => [
                            'borderStyle' => 'medium',
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                ]);
                $event->sheet->getStyle('A14:K18')->applyFromArray([
                    'borders' => [
                        'outline' => [
                            'borderStyle' => 'medium',
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                ]);
                $event->sheet->getStyle('A19:K21')->applyFromArray([
                    'borders' => [
                        'outline' => [
                            'borderStyle' => 'medium',
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                ]);
                $event->sheet->getStyle('A22:K26')->applyFromArray([
                    'borders' => [
                        'outline' => [
                            'borderStyle' => 'medium',
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                ]);
                $event->sheet->getStyle('A27:K31')->applyFromArray([
                    'borders' => [
                        'outline' => [
                            'borderStyle' => 'medium',
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                ]);
                $event->sheet->getStyle('A32:K36')->applyFromArray([
                    'borders' => [
                        'outline' => [
                            'borderStyle' => 'medium',
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                ]);
                $event->sheet->getStyle('A37:K39')->applyFromArray([
                    'borders' => [
                        'outline' => [
                            'borderStyle' => 'medium',
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                ]);
                $event->sheet->getStyle('A40:K44')->applyFromArray([
                    'borders' => [
                        'outline' => [
                            'borderStyle' => 'medium',
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                ]);
                $event->sheet->getStyle('A45:K49')->applyFromArray([
                    'borders' => [
                        'outline' => [
                            'borderStyle' => 'medium',
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                ]);
                $event->sheet->getStyle('A50:K54')->applyFromArray([
                    'borders' => [
                        'outline' => [
                            'borderStyle' => 'medium',
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                ]);
                $event->sheet->getStyle('A55:K57')->applyFromArray([
                    'borders' => [
                        'outline' => [
                            'borderStyle' => 'medium',
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                ]);
            }
        ];
    }

    public function collection()
    {
        $sale_data = array();

        $sale = Sale::where('sale_at', $this->date)->get();
        foreach ($sale as $item) {
            $dish = Dish::find($item['dish_id']);
            $sale_data[] = [
                'sale_id' => $item['id'],
                'dish_id' => $item['dish_id'],
                'dish_alias' => substr($dish['name'], 0, 1),
                'dish_manufacturer_id' => $dish['manufacturer_id'],
                'dish_price' => $dish['price']
            ];
        }


        return collect($sale_data);
    }

    public function startCell(): string
    {
        return 'B4';
    }

    public function title(): string
    {
        // 設定工作䈬的名稱
        return $this->date . '訂單';
    }
}
