<?php

namespace App\Http\Controllers;

use App\Exports\OrderExport;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Validator;


class ExcelController extends Controller
{
    // Excel 檔案到處功能

    public function exportOrder(Request $request, $date)
    {
        $validator = Validator::make([$date], ['required|date_format:Y-m-d']);
        if ($validator->fails())
            return response()->json(['error' => 'Date format error'], Response::HTTP_BAD_REQUEST);
        Excel::store(new OrderExport($date), 'public/order/order-' . $date . '.xlsx');
        $protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        return response()->json(['url' => $protocol . $_SERVER['HTTP_HOST'] . '/storage/order/order-' . $date . '.xlsx'], 200);
    }
}
