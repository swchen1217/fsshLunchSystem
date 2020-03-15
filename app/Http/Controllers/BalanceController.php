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
