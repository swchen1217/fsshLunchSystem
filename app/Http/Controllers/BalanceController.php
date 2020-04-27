<?php

namespace App\Http\Controllers;

use App\Service\BalanceService;
use Illuminate\Http\Request;

class BalanceController extends Controller
{
    /**
     * @var BalanceService
     */
    private $balanceService;

    public function __construct(BalanceService $balanceService)
    {
        $this->middleware('auth:api');
        $this->balanceService = $balanceService;
    }

    public function getByAccount(Request $request, $account)
    {
        $mResult = $this->balanceService->getByAccount($request, $account, true);
        return response()->json($mResult[0], $mResult[1]);
    }

    public function getLogByAccount(Request $request, $account)
    {
        $mResult = $this->balanceService->getLogByAccount($request, $account);
        return response()->json($mResult[0], $mResult[1]);
    }

    public function getToday(Request $request)
    {
        $mResult = $this->balanceService->getToday();
        return response()->json($mResult[0], $mResult[1]);
    }

    public function getTotal(Request $request, $date1, $date2)
    {
        $validator = Validator::make([$date1, $date2], ['required|date_format:Y-m-d', 'required|date_format:Y-m-d']);
        if ($validator->fails())
            return response()->json(['error' => 'Date format error'], Response::HTTP_BAD_REQUEST);
        $mResult = $this->balanceService->getTotal($date1, $date2);
        return response()->json($mResult[0], $mResult[1]);
    }

    public function topUp(Request $request)
    {
        $mResult = $this->balanceService->topUp($request);
        return response()->json($mResult[0], $mResult[1]);
    }

    public function deduct(Request $request)
    {
        $mResult = $this->balanceService->deduct($request);
        return response()->json($mResult[0], $mResult[1]);
    }
}
