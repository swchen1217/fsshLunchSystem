<?php

namespace App\Http\Controllers;

use App\Service\OrderService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class OrderController extends Controller
{
    /**
     * @var OrderService
     */
    private $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->middleware('auth:api');
        $this->orderService = $orderService;
    }

    public function getAll()
    {
        $mResult = $this->orderService->getSaleData();
        return response()->json($mResult[0], $mResult[1]);
    }

    public function getById()
    {
        $mResult = $this->orderService->getSaleData();
        return response()->json($mResult[0], $mResult[1]);
    }

    public function getByUser()
    {
        $mResult = $this->orderService->getSaleData();
        return response()->json($mResult[0], $mResult[1]);
    }

    public function getByDish()
    {
        $mResult = $this->orderService->getSaleData();
        return response()->json($mResult[0], $mResult[1]);
    }

    public function getByDate()
    {
        $mResult = $this->saleService->getSaleData();
        return response()->json($mResult[0], $mResult[1]);
    }

    public function getByManufacturer()
    {
        $mResult = $this->saleService->getSaleData();
        return response()->json($mResult[0], $mResult[1]);
    }

    public function getByClass()
    {
        $mResult = $this->orderService->getSaleData();
        return response()->json($mResult[0], $mResult[1]);
    }

    public function getTodayByClass()
    {
        $mResult = $this->orderService->getSaleData();
        return response()->json($mResult[0], $mResult[1]);
    }

    public function create(Request $request)
    {
        $mResult = $this->orderService->create($request);
        return response()->json($mResult[0], $mResult[1]);
    }

    public function edit(Request $request, $sale_id)
    {
        $mResult = $this->orderService->edit($request, $sale_id);
        return response()->json($mResult[0], $mResult[1]);
    }

    public function remove(Request $request, $sale_id)
    {
        $mResult = $this->orderService->remove($request, $sale_id);
        return response()->json($mResult[0], $mResult[1]);
    }
}
