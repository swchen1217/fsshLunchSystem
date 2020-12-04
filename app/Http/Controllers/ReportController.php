<?php

namespace App\Http\Controllers;

use App\Service\ReportService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ReportController extends Controller
{

    /**
     * @var ReportService
     */
    private $reportService;

    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    public function dish(Request $request)
    {
        $mResult = $this->reportService->dish($request);
        return response()->json($mResult[0], $mResult[1]);
    }
}
