<?php

namespace App\Service;

use App\Repositories\SaleRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

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
            return [$result, Response::HTTP_OK];
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

    }

    public function edit(Request $request, $manufacturer_id)
    {

    }

    public function remove(Request $request, $manufacturer_id)
    {

    }
}
