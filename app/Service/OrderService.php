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
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use PhpParser\Node\Expr\Cast\Object_;
use Spatie\Permission\Exceptions\UnauthorizedException;

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
	ManufacturerRepository $manufacturerRepository,
	BalanceRepository $balanceRepository,
	Money_logRepository $money_logRepository)
    {
        $this->orderRepository = $orderRepository;
        $this->userRepository = $userRepository;
        $this->saleRepository = $saleRepository;
        $this->dishRepository = $dishRepository;
	$this->manufacturerRepository = $manufacturerRepository;
	$this->balanceRepository = $balanceRepository;
	$this->money_logRepository = $money_logRepository;
    }

    public function get($type = 'all', $data = null, Request $request = null)
    {
        if ($type != 'order_id') {
            if ($type == 'all' && PermissionSupport::check('order.read', null, true)) //order.read
                $order = $this->orderRepository->all();
            elseif ($type == 'user_id') { //order.read.self
                if ($this->getByUserIdCheck($data))
                    $order = $this->orderRepository->findByUserId($data);
            } elseif ($type == 'sale_id' && PermissionSupport::check('order.read', null, true)) //order.read
                $order = $this->orderRepository->findBySaleId($data);
            elseif ($type == 'saleDate' && PermissionSupport::check('order.read', null, true)) { //order.read
                $sale = $this->saleRepository->findBySaleDate($data);
                $order = collect();
                foreach ($sale as $item) {
                    $ss = $this->orderRepository->findBySaleId($item->id);
                    foreach ($ss as $s)
                        $order->push($s);
                }
            } elseif ($type == 'manufacturer_id' && PermissionSupport::check('order.read', null, true)) { //order.read
                $dish = $this->dishRepository->findByManufacturer_id($data);
                $order = collect();
                foreach ($dish as $dd) {
                    $ss = $this->saleRepository->findByDishId($dd->id);
                    if ($ss != null) {
                        foreach ($ss as $s) {
                            $oo = $this->orderRepository->findBySaleId($s->id);
                            foreach ($oo as $o)
                                $order->push($o);
                        }
                    }
                }
            } elseif ($type == 'class' && PermissionSupport::check('order.read.class', null, true)) { //order.read.class
                $user = $this->userRepository->findByClass($data);
                $order = collect();
                foreach ($user as $uu) {
                    $oo = $this->orderRepository->findByUserId($uu->id);
                    foreach ($oo as $o)
                        $order->push($o);
                }
            } elseif ($type == 'classToday' && PermissionSupport::check('order.read.class.today', null, true)) { //order.read.class.today
                $user = $this->userRepository->findByClass($data);
                $order = collect();
                foreach ($user as $uu) {
                    $oo = $this->orderRepository->findByUserId($uu->id);
                    foreach ($oo as $o) {
                        $s = $this->saleRepository->findById($o->sale_id);
                        if (Carbon::today()->eq(Carbon::parse($s->sale_at)))
                            $order->push($o);
                    }
                }
            }
            $result = array();
            foreach ($order as $item) {
                $user = $this->userRepository->findById($item->user_id)->only(['id', 'account', 'class', 'number']);
                $sale = $this->saleRepository->findById($item->sale_id)->only(['id', 'sale_at', 'dish_id']);
                $dish_id = $sale['dish_id'];
                array_splice($sale, 2, 1);
                $dish = $this->dishRepository->findById($dish_id)->only(['id', 'name', 'manufacturer_id', 'price']);
                $manufacturer = $this->manufacturerRepository->findById($dish['manufacturer_id']);
                $result[] = array_merge(['order_id' => $item->id, 'user' => $user, 'sale' => array_merge($sale, ['dish' => array_merge($dish, ['manufacturer_name' => $manufacturer->name])])]);
            }

            return [$result, Response::HTTP_OK];
        } else {
            $order = $this->orderRepository->findById($data);
            if ($order != null && $this->getByUserIdCheck($order->user_id)) {
                $user = $this->userRepository->findById($order->user_id)->only(['id', 'account', 'class', 'number']);
                $sale = $this->saleRepository->findById($order->sale_id)->only(['id', 'sale_at', 'dish_id']);
                $dish_id = $sale['dish_id'];
                array_splice($sale, 2, 1);
                $dish = $this->dishRepository->findById($dish_id)->only(['id', 'name', 'manufacturer_id', 'price']);
                $manufacturer = $this->manufacturerRepository->findById($dish['manufacturer_id']);
                $result = array_merge(['order_id' => $order->id, 'user' => $user, 'sale' => array_merge($sale, ['dish' => array_merge($dish, ['manufacturer_name' => $manufacturer->name])])]);
                return [$result, Response::HTTP_OK];
            } else
                return [['error' => 'The Order Not Found'], Response::HTTP_NOT_FOUND];
        }
    }

    public function create(Request $request)
    {
        //self
        //all

        //user?
        //sale_id Array

        //has sale_id??
        //get price
        //has balance data ? create : get
        //balance-price>=0 ? ok : fail
        //log

        /*
         * {
         *      "user_id":"1",
         *      "sale_id":["16","25","31",...]
         * }
         */
	    
	DB::beginTransaction();
        try {
	    DB::commit();
	    if($request->input('user_id')!=null && PermissionSupport::check('order.modify.create',null,true)){
            $uu=$this->userRepository->findByUserId($request->input('user_id'));
            if($uu!=null)
                $user_id=$request->input('user_id');
            else
                a(); // todo throw user 404
            }else
            	$user_id=Auth::user()->id;
            $price_sum=0;
            $sale=$request->input('sale_id');
	        foreach($sale as $ss){
            	    $s=$this->saleRepository->findById($ss);
            	    if($s==null)
                	a();//throw sale not found 404
                    $price_sum+=$s->price;
      	   	}
            $balance=$this->balanceRepository->findById($user_id);
            if($balance==null){
            	$this->balanceRepository->create(['user_id'=>$user_id,'money'=>0]);
            	$money=0;
            	//throw insufficient balance 403 ?
            }else
            	$money=$this->balanceRepository->findByUserId($user_id)->money;
            $mm=$money-$price_sum;
            if($mm<0)
        	a();//throw insufficient balance 403 ?
            $this->balanceRepository->updateByUserId($user_id,['money'=>$mm]);
            $this->money_logRepository->create(['user_id'=>$user_id,'event'=>'deduction','money'=>$price_sum,'trigger_id'=>Auth::user()->id,'note'=>'FIOS_Sys_Auto']);
            foreach($sale as $ss)
            	$this->orderRepository->create(['user_id'=>$user_id,'sale_id'=$ss]);
	    return [[], Response::HTTP_CREATED];
        } catch (MyException $e) {
            return [unserialize($e->getMessage()), $e->getCode()];
        } catch (\Exception $e) {
            DB::rollback();
            return [['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR];
        }
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

    private function getByUserIdCheck($user_id)
    {
        if (PermissionSupport::check('order.read'))
            return true;
        elseif ($user_id == Auth::user()->id && PermissionSupport::check('order.read.self', null, true))
            return true;
        elseif (PermissionSupport::check('order.read.class', null, true)) {
            $class_user = $this->userRepository->findByClass(Auth::user()->class);
            foreach ($class_user as $cu) {
                if ($user_id == $cu->id) {
                    return true;
                }
            }
            throw UnauthorizedException::forPermissions(['order.read.class']);
        }
        return false;
    }
}
