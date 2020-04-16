<?php

namespace App\Service;

use App\Exceptions\MyException;
use App\Repositories\BalanceRepository;
use App\Repositories\DishRepository;
use App\Repositories\ManufacturerRepository;
use App\Repositories\Money_logRepository;
use App\Repositories\OrderRepository;
use App\Repositories\SaleRepository;
use App\Repositories\UserRepository;
use App\Supports\PermissionSupport;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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
    /**
     * @var BalanceRepository
     */
    private $balanceRepository;
    /**
     * @var Money_logRepository
     */
    private $money_logRepository;

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
                if ($this->userIdCheckWithAllAndSelfAndClass($data, 'order.read'))
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
            if ($order != null && $this->userIdCheckWithAllAndSelfAndClass($order->user_id, 'order.read')) {
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

    public function getInfo($date)
    {
        $sale = $this->saleRepository->findBySaleDate($date);
        $resultBySale = array();
        $class = array();
        $sort = array();
        foreach ($sale as $ss) {
            $dish = $this->dishRepository->findById($ss->dish_id);
            $price = $dish->price;
            $name = $dish->name;
            $manufacturer_name = $this->manufacturerRepository->findById($dish->manufacturer_id)->name;
            $order = $this->orderRepository->findBySaleId($ss->id);
            $count = count($order);
            $resultBySale[] = ['sale_id' => $ss->id, 'count' => $count, 'total' => $price * $count, 'name' => $name, 'manufacturer_name' => $manufacturer_name];
            if (isset($sort[$manufacturer_name])) {
                $sort[$manufacturer_name] = array_merge($sort[$manufacturer_name], [substr($name, 0, 1) => $ss->id]);
                ksort($sort[$manufacturer_name]);
            } else {
                $sort[$manufacturer_name] = array(substr($name, 0, 1) => $ss->id);
            }
            foreach ($order as $oo) {
                $user = $this->userRepository->findById($oo->user_id);
                if (isset($class[$user['class']])) {
                    if (isset($class[$user['class']][$ss->id]))
                        $class[$user['class']][$ss->id] += 1;
                    else
                        $class[$user['class']][$ss->id] = 1;
                } else {
                    $class[$user['class']] = array($ss->id => 1);
                }
            }
        }
        return [['sale' => $resultBySale, 'class' => $class, 'sort' => $sort], Response::HTTP_OK];
    }

    public function getTotal($date1, $date2)
    {
        $manufacturer_data = $this->manufacturerRepository->all()->sortBy('id')->toArray();
        $mm = array();
        $sale = $this->saleRepository->findBySaleDateInterval($date1, $date2);
        foreach ($sale as $ss) {
            $dish = $this->dishRepository->findById($ss['dish_id']);
            $order = $this->orderRepository->findBySaleId($ss['id']);
            $money = $dish['price'] * count($order);
            if (isset($mm[$dish['manufacturer_id']])) {
                $mm[$dish['manufacturer_id']] += $money;
            } else {
                $mm[$dish['manufacturer_id']] = $money;
            }
        }
        foreach ($manufacturer_data as $key => $value)
            array_merge($manufacturer_data[$key], ['total_money' => $mm[$value['manufacturer_id']] ?? 0]);
        return [$this->manufacturerRepository, Response::HTTP_OK];
    }

    public function create(Request $request)
    {
        $ip = $request->ip();
        DB::beginTransaction();
        try {
            if ($request->input('user_id') != null && PermissionSupport::check('order.modify.create', null, true)) {
                $uu = $this->userRepository->findById($request->input('user_id'));
                if ($uu != null)
                    $user_id = $request->input('user_id');
                else
                    throw new MyException(serialize(['error' => 'The User Not Found']), Response::HTTP_NOT_FOUND);
            } else
                $user_id = Auth::user()->id;
            $price_sum = 0;
            $sale = $request->input('sale_id');
            foreach ($sale as $ss) {
                $s = $this->saleRepository->findById($ss);
                if ($s == null)
                    throw new MyException(serialize(['error' => 'The Sale Not Found']), Response::HTTP_NOT_FOUND);
                if (Carbon::today()->gt(Carbon::parse($s->sale_at)))
                    throw new MyException(serialize(['error' => 'Sales time has passed']), Response::HTTP_BAD_REQUEST);
                elseif (Carbon::today()->eq(Carbon::parse($s->sale_at))) {
                    if (Carbon::now()->gt(Carbon::createFromTimeString(env('ORDER_DEADLINE', '08:00'))))
                        throw new MyException(serialize(['error' => 'Sales time has passed']), Response::HTTP_BAD_REQUEST);
                }
                $price_sum += $this->dishRepository->findById($s->dish_id)->price;
            }
            $balance = $this->balanceRepository->findByUserId($user_id);
            if ($balance == null) {
                $this->balanceRepository->caeate(['user_id' => $user_id, 'money' => 0]);
                $money = 0;
                Log::channel('order')->notice('Insufficient balance', ['ip' => $ip, 'trigger_id' => Auth::user()->id, 'user_id' => $user_id, 'sale_id' => $sale, 'Balance' => $money, 'Total pay' => $price_sum]);
                throw new MyException(serialize(['error' => 'Insufficient balance']), Response::HTTP_FORBIDDEN);
            } else
                $money = $balance->money;
            $mm = $money - $price_sum;
            if ($mm < 0) {
                Log::channel('order')->notice('Insufficient balance', ['ip' => $ip, 'trigger_id' => Auth::user()->id, 'user_id' => $user_id, 'sale_id' => $sale, 'Balance' => $money, 'Total pay' => $price_sum]);
                throw new MyException(serialize(['error' => 'Insufficient balance']), Response::HTTP_FORBIDDEN);
            }
            $this->balanceRepository->updateByUserId($user_id, ['money' => $mm]);
            $oId = array();
            $oIdString = "";
            foreach ($sale as $ss) {
                $order = $this->orderRepository->caeate(['user_id' => $user_id, 'sale_id' => $ss]);
                $oId[] = $order->id;
                $oIdString .= $order->id . " ";
            }
            $this->money_logRepository->caeate(['user_id' => $user_id, 'event' => 'pay', 'money' => $price_sum, 'trigger_id' => Auth::user()->id, 'note' => 'new Order ID:' . $oIdString]);
            DB::commit();
            Log::channel('money')->info('Pay Success', ['ip' => $ip, 'trigger_id' => Auth::user()->id, 'user_id' => $user_id, 'Balance before pay' => $money, 'Total pay' => $price_sum, 'Balance after pay' => $mm]);
            Log::channel('order')->info('Create Success', ['ip' => $ip, 'trigger_id' => Auth::user()->id, 'user_id' => $user_id, 'sale_id' => $sale, 'order_id' => $oId, 'Balance before pay' => $money, 'Total pay' => $price_sum, 'Balance after pay' => $mm]);
            return [['before' => $money, 'total' => $price_sum, 'after' => $mm, 'order_id' => $oId], Response::HTTP_CREATED];
        } catch (MyException $e) {
            DB::rollback();
            return [unserialize($e->getMessage()), $e->getCode()];
        } catch (UnauthorizedException $e) {
            DB::rollback();
            return [['error' => $e->getMessage()], Response::HTTP_FORBIDDEN];
        } catch (\Exception $e) {
            if ($request->input('user_id') != null)
                $uid = $request->input('user_id');
            else
                $uid = Auth::user()->id;
            Log::channel('order')->warning('Order failed', ['ip' => $ip, 'trigger_id' => Auth::user()->id, 'user_id' => $uid, 'sale_id' => $request->input('sale_id')]);
            DB::rollback();
            throw $e;
        }
    }

    public function edit(Request $request, $order_id)
    {
        return [['error' => 'The Route Not Enable'], Response::HTTP_FORBIDDEN];
    }

    public function remove(Request $request, $order_id)
    {
        $ip = $request->ip();
        DB::beginTransaction();
        try {
            $order = $this->orderRepository->findById($order_id);
            if ($order == null)
                throw new MyException(serialize(['error' => 'The Order Not Found']), Response::HTTP_NOT_FOUND);
            if ($this->userIdCheckWithAllAndSelfAndClass($order->user_id, 'order.modify.delete')) {
                $sale = $this->saleRepository->findById($order->sale_id);
                if (Carbon::today()->gt(Carbon::parse($sale->sale_at)))
                    throw new MyException(serialize(['error' => 'Sales time has passed']), Response::HTTP_BAD_REQUEST);
                elseif (Carbon::today()->eq(Carbon::parse($sale->sale_at))) {
                    if (Carbon::now()->gt(Carbon::createFromTimeString(env('ORDER_DEADLINE', '08:00'))))
                        throw new MyException(serialize(['error' => 'Sales time has passed']), Response::HTTP_BAD_REQUEST);
                }
                $this->orderRepository->delete($order_id);
                $price = $this->dishRepository->findById($sale->dish_id)->price;
                $balance = $this->balanceRepository->findByUserId($order->user_id);
                if ($balance == null) {
                    $this->balanceRepository->caeate(['user_id' => $order->user_id, 'money' => 0]);
                    $money = 0;
                } else
                    $money = $balance->money;
                $mm = $money + $price;
                $this->balanceRepository->updateByUserId($order->user_id, ['money' => $mm]);
                $this->money_logRepository->caeate(['user_id' => $order->user_id, 'event' => 'refund', 'money' => $price, 'trigger_id' => Auth::user()->id, 'note' => 'delete Order ID: ' . $order_id]);
                DB::commit();
                Log::channel('money')->info('Refund Success', ['ip' => $ip, 'trigger_id' => Auth::user()->id, 'user_id' => $order->user_id, 'Balance before refund' => $money, 'Total refund' => $price, 'Balance after refund' => $mm]);
                Log::channel('order')->info('Remove Success', ['ip' => $ip, 'trigger_id' => Auth::user()->id, 'user_id' => $order->user_id, 'order_id' => $order_id, 'Balance before refund' => $money, 'Total refund' => $price, 'Balance after refund' => $mm]);
                return [['before' => $money, 'total' => $price, 'after' => $mm], Response::HTTP_OK];
            }
        } catch (MyException $e) {
            DB::rollback();
            return [unserialize($e->getMessage()), $e->getCode()];
        } catch (UnauthorizedException $e) {
            DB::rollback();
            return [['error' => $e->getMessage()], Response::HTTP_FORBIDDEN];
        } catch (\Exception $e) {
            Log::channel('order')->warning('Remove order failed', ['ip' => $ip, 'trigger_id' => Auth::user()->id, 'order_id' => $order]);
            DB::rollback();
            throw $e;
        }
    }

    private function userIdCheckWithAllAndSelfAndClass($user_id, $permission)
    {
        if (PermissionSupport::check($permission))
            return true;
        elseif ($user_id == Auth::user()->id && PermissionSupport::check($permission . '.self', null, true))
            return true;
        elseif (PermissionSupport::check($permission . '.class', null, true)) {
            $class_user = $this->userRepository->findByClass(Auth::user()->class);
            foreach ($class_user as $cu) {
                if ($user_id == $cu->id) {
                    return true;
                }
            }
            throw UnauthorizedException::forPermissions([$permission . '.class']);
        }
        return false;
    }
}
