<?php

namespace App\Http\Controllers;

use App\Service\OrderService;
use Illuminate\Http\Request;

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
}
