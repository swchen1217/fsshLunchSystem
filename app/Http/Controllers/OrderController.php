<?php

namespace App\Http\Controllers;

use App\Service\OrderService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

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
        $mResult = $this->orderService->get();
        return response()->json($mResult[0], $mResult[1]);
    }

    public function getById(Request $request, $order_id)
    {
        $mResult = $this->orderService->get('order_id', $order_id);
        return response()->json($mResult[0], $mResult[1]);
    }

    public function getByUser(Request $request, $user_id)
    {
        $mResult = $this->orderService->get('user_id', $user_id);
        return response()->json($mResult[0], $mResult[1]);
    }

    public function getByDish(Request $request, $dish_id)
    {
        $mResult = $this->orderService->get('dish_id', $dish_id);
        return response()->json($mResult[0], $mResult[1]);
    }

    public function getByDate(Request $request, $saleDate)
    {
        $validator = Validator::make([$saleDate], ['required|date_format:Y-m-d']);
        if ($validator->fails())
            return response()->json(['error' => 'Date format error'], Response::HTTP_BAD_REQUEST);
        $mResult = $this->orderService->get('saleDate', $saleDate);
        return response()->json($mResult[0], $mResult[1]);
    }

    public function getByManufacturer(Request $request, $manufacturer_id)
    {
        $mResult = $this->orderService->get('manufacturer_id', $manufacturer_id);
        return response()->json($mResult[0], $mResult[1]);
    }

    public function getByClass(Request $request, $class)
    {
        $mResult = $this->orderService->get('class', $class);
        return response()->json($mResult[0], $mResult[1]);
    }

    public function getTodayByClass(Request $request, $class)
    {
        $mResult = $this->orderService->get('classToday', $class);
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
