<?php

namespace App\Http\Controllers;

use App\Service\SaleService;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SaleController extends Controller
{
    /**
     * @var SaleService
     */
    private $saleService;

    public function __construct(SaleService $saleService)
    {
        $this->middleware('auth:api', ['except' => ['getAll', 'getById', 'getBySaleDate']]);
        $this->saleService = $saleService;
    }

    public function getAll()
    {
        $mResult = $this->saleService->getSaleData();
        return response()->json($mResult[0], $mResult[1]);
    }

    public function getById(Request $request, $sale_id)
    {
        $mResult = $this->saleService->getSaleData('id', $sale_id);
        return response()->json($mResult[0], $mResult[1]);
    }

    public function getBySaleDate(Request $request, $saleDate)
    {
        $validator = Validator::make([$saleDate], ['required|date_format:Y-m-d']);
        if ($validator->fails())
            return response()->json(['error' => 'Date format error'], Response::HTTP_BAD_REQUEST);
        $mResult = $this->saleService->getSaleData('saleDate', $saleDate);
        return response()->json($mResult[0], $mResult[1]);
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), ['sale_at'=>'required|date_format:Y-m-d']);
        if ($validator->fails())
            return response()->json(['error' => 'Date format error'], Response::HTTP_BAD_REQUEST);
        $mResult = $this->saleService->create($request);
        return response()->json($mResult[0], $mResult[1]);
    }

    public function edit(Request $request, $sale_id)
    {
        $validator = Validator::make($request->all(), ['sale_at'=>'date_format:Y-m-d']);
        if ($validator->fails())
            return response()->json(['error' => 'Date format error'], Response::HTTP_BAD_REQUEST);
        $mResult = $this->saleService->edit($request, $sale_id);
        return response()->json($mResult[0], $mResult[1]);
    }

    public function remove(Request $request, $sale_id)
    {
        $mResult = $this->saleService->remove($request, $sale_id);
        return response()->json($mResult[0], $mResult[1]);
    }
}
