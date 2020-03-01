<?php

namespace App\Service;

use App\Repositories\DishRepository;
use App\Repositories\ManufacturerRepository;
use App\Repositories\OrderRepository;
use App\Repositories\SaleRepository;
use App\Repositories\UserRepository;
use App\Supports\PermissionSupport;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class OrderService
{
    /**
     * @var OrderRepository
     */
    private $orderRepository;
    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var SaleRepository
     */
    private $saleRepository;
    /**
     * @var DishRepository
     */
    private $dishRepository;
    /**
     * @var ManufacturerRepository
     */
    private $manufacturerRepository;

    public function __construct(
        OrderRepository $orderRepository,
        UserRepository $userRepository,
        SaleRepository $saleRepository,
        DishRepository $dishRepository,
        ManufacturerRepository $manufacturerRepository)
    {
        $this->orderRepository = $orderRepository;
        $this->userRepository = $userRepository;
        $this->saleRepository = $saleRepository;
        $this->dishRepository = $dishRepository;
        $this->manufacturerRepository = $manufacturerRepository;
    }

    public function get($type = 'all', $data = null, Request $request = null)
    {
        if (PermissionSupport::check('order.read', null, true)) {
            $order = $this->orderRepository->all();
            $result = array();
            foreach ($order as $item) {
                $user = $this->userRepository->findById($item->user_id)->only(['id', 'account', 'class', 'number']);
                $sale = $this->saleRepository->findById($item->sale_id)->only(['id', 'sale_at', 'dish_id']);
                $dish = $this->dishRepository->findById($sale->dish_id)->only(['id', 'name', 'manufacturer_id', 'price']);
                $manufacturer = $this->manufacturerRepository->findById($dish->manufacturer_id);
                $result[] = array_merge(['order_id' => $item->id, 'user' => $user->toArray(), 'dish' => array_merge($dish->toArray(),['manufacturer_name'=>$manufacturer->name])]);
            }
            return [$result,Response::HTTP_OK];
        }


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
