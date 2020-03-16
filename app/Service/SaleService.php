<?php

namespace App\Service;

use App\Repositories\DishRepository;
use App\Repositories\SaleRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class SaleService
{
    /**
     * @var SaleRepository
     */
    private $saleRepository;
    /**
     * @var DishService
     */
    private $dishService;
    /**
     * @var DishRepository
     */
    private $dishRepository;

    public function __construct(SaleRepository $saleRepository, DishService $dishService, DishRepository $dishRepository)
    {
        $this->saleRepository = $saleRepository;
        $this->dishService = $dishService;
        $this->dishRepository = $dishRepository;
    }

    public function getSaleData($type = 'all', $data = null)
    {
        if ($type != 'id') {
            if ($type == 'all') {
                $key = 'all';
            } elseif ($type == 'saleDate') {
                $key = 'date_' . $data;
            }
            $sale_date = Cache::tags('sale')->remember($key, Carbon::now()->addHours(3), function () use ($type, $data) {
                if ($type == 'all') {
                    $sale = $this->saleRepository->all();
                } elseif ($type == 'saleDate') {
                    $sale = $this->saleRepository->findBySaleDate($data);
                }
                $result = array();
                foreach ($sale as $item) {
                    $ss = $item->toArray();
                    $dish_id = $ss['dish_id'];
                    array_splice($ss, 2, 1);
                    $result[] = array_merge($ss, ['dish' => $this->dishService->getDish($dish_id)[0]]);
                }
                return serialize($result);
            });
            return [unserialize($sale_date), Response::HTTP_OK];
        } else {
            $sale = $this->saleRepository->findById($data);
            if ($sale == null)
                return [['error' => 'Not found the dish from sale_id'], Response::HTTP_NOT_FOUND];
            $ss = $sale->toArray();
            $dish_id = $ss['dish_id'];
            array_splice($ss, 2, 1);
            $result = array_merge($ss, ['dish' => $this->dishService->getDish($dish_id)[0]]);
            return [$result, Response::HTTP_OK];
        }
    }

    public function create(Request $request)
    {
        Cache::tags('sale')->flush();
        $sale_at = $request->input('sale_at');
        $dish_id = $request->input('dish_id');
        $status = $request->input('status');
        if (Carbon::today()->gt(Carbon::parse($sale_at)))
            return [['error' => 'Sales time has passed'], Response::HTTP_BAD_REQUEST];
        elseif (Carbon::today()->eq(Carbon::parse($sale_at))) {
            if (Carbon::now()->gt(Carbon::createFromTimeString(env('ORDER_DEADLINE', '08:00'))))
                return [['error' => 'Sales time has passed'], Response::HTTP_BAD_REQUEST];
        }
        if ($this->dishRepository->findById($dish_id) == null)
            return [['error' => 'The Dish Not Found'], Response::HTTP_NOT_FOUND];

        $sale = $this->saleRepository->caeate(['sale_at' => $sale_at, 'dish_id' => $dish_id, 'status' => $status]);
        return [$sale, Response::HTTP_CREATED];
    }

    public function edit(Request $request, $sale_id)
    {
        Cache::tags('sale')->flush();
        $edit = $this->saleRepository->update($sale_id, $request->all());
        if ($edit != 0)
            return [$this->saleRepository->findById($sale_id), Response::HTTP_OK];
        else
            return [['error' => 'The Sale Not Found'], Response::HTTP_NOT_FOUND];
    }

    public function remove(Request $request, $sale_id)
    {
        Cache::tags('sale')->flush();
        $remove = $this->saleRepository->delete($sale_id);
        if ($remove != 0)
            return [[], Response::HTTP_NO_CONTENT];
        else
            return [['error' => 'The Manufacturer Not Found'], Response::HTTP_NOT_FOUND];
    }
}
