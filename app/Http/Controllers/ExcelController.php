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

    public function exportOrder(Request $request, $date)
    {
        Excel::store(new OrderExport($date), 'user.xlsx');
    }
}
