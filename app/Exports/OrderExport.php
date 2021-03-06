<?php

namespace App\Exports;

use App\Entity\Dish;
use App\Entity\Order;
use App\Entity\Sale;
use App\Entity\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;

class OrderExport implements FromCollection, WithTitle, WithEvents, WithCustomStartCell, WithStrictNullComparison
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

                $event->sheet->getPageMargins()->setTop(1);
                $event->sheet->getPageMargins()->setBottom(1);
                $event->sheet->getPageMargins()->setRight(0.5);
                $event->sheet->getPageMargins()->setLeft(0.5);
                $event->sheet->getPageMargins()->setHeader(0);
                $event->sheet->getPageMargins()->setFooter(0);

                //設定列寬
                for ($i = 0; $i < 11; $i++) {
                    $event->sheet->getDelegate()->getColumnDimension($col[$i])->setWidth(8.11);
                }
                //設定行高，$i為資料行數
                for ($i = 1; $i <= 60; $i++) {
                    $event->sheet->getDelegate()->getRowDimension($i)->setRowHeight(13.8);
                }
                //設定區域單元格垂直居中
                $event->sheet->getDelegate()->getStyle('A1:K60')->getAlignment()->setVertical('center');
                $event->sheet->getDelegate()->getStyle('A1:K60')->getAlignment()->setHorizontal('center');
                //設定區域單元格字型、顏色、背景等，其他設定請檢視 applyFromArray 方法，提供了註釋
                $event->sheet->getStyle('A1:K60')->applyFromArray([
                    'font' => [
                        'name' => 'Noto Sans CJK TC Regular',
                        'size' => 12,
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
                $event->sheet->setCellValue('I1', date("Y-m-d D", strtotime($this->date)));
                $event->sheet->setCellValue('B2', '旺春豐');
                $event->sheet->setCellValue('D2', '快樂餐飲');
                $event->sheet->setCellValue('F2', '御饌坊');
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
                $event->sheet->setCellValue('A58', '教師');
                $event->sheet->setCellValue('A59', '總數量');
                $event->sheet->setCellValue('A60', '總金額');

                $event->sheet->getStyle('A1:K60')->applyFromArray([
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
                $event->sheet->getStyle('B2:C60')->applyFromArray([
                    'borders' => [
                        'outline' => [
                            'borderStyle' => 'medium',
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                ]);
                $event->sheet->getStyle('D2:E60')->applyFromArray([
                    'borders' => [
                        'outline' => [
                            'borderStyle' => 'medium',
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                ]);
                $event->sheet->getStyle('F2:G60')->applyFromArray([
                    'borders' => [
                        'outline' => [
                            'borderStyle' => 'medium',
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                ]);
                $event->sheet->getStyle('H2:I60')->applyFromArray([
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
                $event->sheet->getStyle('A9:K60')->applyFromArray([
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
                $event->sheet->getStyle('A58:K58')->applyFromArray([
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
        $order_data = array();
        $display_data = array();

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

        usort($sale_data, function ($a, $b) {
            if ($a['dish_manufacturer_id'] == $b['dish_manufacturer_id']) {
                return strcmp($a['dish_alias'], $b['dish_alias']);
            }
            return $a['dish_manufacturer_id'] - $b['dish_manufacturer_id'];
		});

		//echo json_encode($sale_data);

        foreach ($sale_data as $ss) {
            $order = Order::where('sale_id', $ss['sale_id'])->get();
            $class = array();
            foreach ($order as $oo) {
                $user = User::find($oo->user_id);
                if (isset($class[$user['class']])) {
                    $class[$user['class']] += 1;
                } else {
                    $class[$user['class']] = 1;
                }
            }
            $order_data[$ss['sale_id']] = ['count' => count($order), 'class' => $class];
        }

        for ($g = 1; $g <= 3; $g++) {
            for ($c = 1; $c <= 18; $c++) {
                $tmp = array();
                $total_count_class = 0;
                $total_money_class = 0;
                $cc = $g . str_pad($c, 2, "0", STR_PAD_LEFT);
                foreach ($sale_data as $ss) {
                    $count = $order_data[$ss['sale_id']]['class'][$cc] ?? 0;
                    $total_count_class += $count;
                    $total_money_class += $count * $ss['dish_price'];
                    $tmp[] = $count;
                }
                while (count($tmp) < 8)
                    array_push($tmp, 0);
                $display_data[] = array_merge($tmp, [$total_count_class, $total_money_class]);
            }
        }

        $tmp = array();
        $total_count_class = 0;
        $total_money_class = 0;
        $cc = '555';
        foreach ($sale_data as $ss) {
            $count = $order_data[$ss['sale_id']]['class'][$cc] ?? 0;
            $total_count_class += $count;
            $total_money_class += $count * $ss['dish_price'];
            $tmp[] = $count;
        }
        while (count($tmp) < 8)
            array_push($tmp, 0);
        $display_data[] = array_merge($tmp, [$total_count_class, $total_money_class]);

        $tmp_count = array();
        $tmp_money = array();
        $total_count = 0;
        $total_money = 0;
        foreach ($sale_data as $ss) {
            $count = $order_data[$ss['sale_id']]['count'];
            $money = $ss['dish_price'];
            $tmp_count[] = $count;
            $tmp_money[] = ($money * $count);
            $total_count += $count;
            $total_money += ($money * $count);
        }
        while (count($tmp_count) < 8)
            array_push($tmp_count, 0);
        while (count($tmp_money) < 8)
            array_push($tmp_money, 0);
        array_push($tmp_count, $total_count, null);
        array_push($tmp_money, null, $total_money);
        array_push($display_data, $tmp_count, $tmp_money);

        foreach ($display_data as $rowKey => $rowValue) {
            foreach ($rowValue as $colKey => $colValue)
                $display_data[$rowKey][$colKey] = ($colValue != 0) ? $colValue : '-';
        }

        return collect($display_data);
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
