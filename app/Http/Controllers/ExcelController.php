<?php

namespace App\Http\Controllers;

use App\Exports\OrderExport;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;


class ExcelController extends Controller
{
    // Excel 檔案到處功能

    public function export()
    {
        Excel::store(new OrderExport(), 'user.xlsx');

        /*$cellData = ['90', '88', '85', '263'];
        Excel::create('財務報表', function ($excel) use ($cellData) {

            $excel->sheet('財務報表', function ($sheet) use ($cellData) {
                $tot = count($cellData);
                //設定單元格寬度、字型大小
                $sheet->setWidth(array(
                    'A' => 12,
                    'B' => 12,
                    'C' => 12,
                    'D' => 12
                ))->rows($cellData)->setFontSize(12);

                // 選單 樣式
                $sheet->cells('A1:C1', function ($cells) {
                    $cells->setAlignment('center');
                    $cells->setFontWeight('bold');
                });

                // 總分 右對齊
                $sheet->cells('D', function ($cells) {
                    $cells->setAlignment('right');
                });
                // 總分內容樣式
                $sheet->cells('D', function ($cells) {
                    $cells->setAlignment('left');
                    $cells->setFontColor('#a09b9b');
                    $cells->setFontFamily('Calibri');
                    $cells->setFontWeight('normal');
                    $cells->setFontSize(12);
                });
                // 高亮顯示
                $sheet->cells('A3:D3', function ($cells) {
                    $cells->setBackground('#87eabd');
                    $cells->setFontWeight('bold');
                    $cells->setFontSize(14);
                });
                //合併行
                $sheet->mergeCells('A1:D1');
                //填充每個單元格的內容
                $sheet->cell('A1', '張三');
                $sheet->cell('A2', '語文');
                $sheet->cell('B2', '數學');
                $sheet->cell('C2', '外語');
                $sheet->cell('D2', '總分');
                $sheet->cell('A3', $cellData[0]);
                $sheet->cell('B3', $cellData[1]);
                $sheet->cell('C3', $cellData[2]);
                $sheet->cell('C3', $cellData[3]);
            });
        })->export('xls');*/
    }
}
