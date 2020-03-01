<?php

namespace App\Service;

use App\Repositories\OrderRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class OrderService
{
    /**
     * @var OrderRepository
     */
    private $orderRepository;

    public function __construct(OrderRepository $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    public function get(Request $request)
    {
        //all
        //id-self
        //id-all
        //user-self
        //...
    }

    public function create(Request $request)
    {
        //self
        //all
    }

    public function edit(Request $request)
    {
        //self
        //all
    }

    public function remove(Request $request)
    {
        //self
        //class
        //all
    }
}
