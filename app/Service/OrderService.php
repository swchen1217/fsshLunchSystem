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
use PhpParser\Node\Expr\Cast\Object_;

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

        if($type!='order_id'){
            if($type=='all')
                $order = $this->orderRepository->all();
            elseif ($type=='user_id')
                $order = $this->orderRepository->findByUserId($data);
            elseif ($type=='sale_id')
                $order = $this->orderRepository->findBySaleId($data);
            elseif ($type=='saleDate'){
                $sale = $this->saleRepository->findBySaleDate($data);
                $order=collect();
                foreach ($sale as $item){
                    $order->push($this->orderRepository->findBySaleId($item->id));
                }
            }



            $result = array();
            foreach ($order as $item) {
                $user = $this->userRepository->findById($item->user_id)->only(['id', 'account', 'class', 'number']);
                $sale = $this->saleRepository->findById($item->sale_id)->only(['id', 'sale_at', 'dish_id']);
                $dish_id=$sale['dish_id'];
                array_splice($sale, 2, 1);
                $dish = $this->dishRepository->findById($dish_id)->only(['id', 'name', 'manufacturer_id', 'price']);
                $manufacturer = $this->manufacturerRepository->findById($dish['manufacturer_id']);
                $result[] = array_merge(['order_id' => $item->id, 'user' => $user, 'sale'=>array_merge($sale,['dish'=>array_merge($dish,['manufacturer_name'=>$manufacturer->name])])]);
            }

            return [$result,Response::HTTP_OK];
        }else{
            $order = $this->orderRepository->findById($data);
            if($order!=null){
                $user = $this->userRepository->findById($order->user_id)->only(['id', 'account', 'class', 'number']);
                $sale = $this->saleRepository->findById($order->sale_id)->only(['id', 'sale_at', 'dish_id']);
                $dish_id=$sale['dish_id'];
                array_splice($sale, 2, 1);
                $dish = $this->dishRepository->findById($dish_id)->only(['id', 'name', 'manufacturer_id', 'price']);
                $manufacturer = $this->manufacturerRepository->findById($dish['manufacturer_id']);
                $result = array_merge(['order_id' => $order->id, 'user' => $user, 'sale'=>array_merge($sale,['dish'=>array_merge($dish,['manufacturer_name'=>$manufacturer->name])])]);
                return [$result,Response::HTTP_OK];
            }else
                return [['error' => 'The Order Not Found'], Response::HTTP_NOT_FOUND];
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
