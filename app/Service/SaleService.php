<?php

namespace App\Service;

use App\Repositories\SaleRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
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

    public function __construct(SaleRepository $saleRepository, DishService $dishService)
    {
        $this->saleRepository = $saleRepository;
        $this->dishService = $dishService;
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
    }

    public function edit(Request $request, $manufacturer_id)
    {
        Cache::tags('sale')->flush();
    }

    public function remove(Request $request, $manufacturer_id)
    {
        Cache::tags('sale')->flush();
    }
}
