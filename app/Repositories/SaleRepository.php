<?php

namespace App\Repositories;

use App\Entity\Sale;

class SaleRepository
{
    /**
     * @var Sale
     */
    private $sale;

    public function __construct(Sale $sale)
    {
        $this->sale = $sale;
    }

    public function all()
    {
        return $this->sale->all();
    }

    public function findById($id)
    {
        return $this->sale->find($id);
    }

    public function findBySaleDate($date)
    {
        return $this->sale->where('sale_at', $date)->get();
    }

    public function findBySaleDateInterval($date1, $date2)
    {
        return $this->sale->where('sale_at', '>=', $date1 . ' 00:00:00')->where('sale_at', '<=', $date2 . ' 23:59:59')->get();
    }

    public function findByDishId($dish_id)
    {
        return $this->sale->where('dish_id', $dish_id)->get();
    }

    public function create($data)
    {
        return $this->sale->create($data);
    }

    public function update($id, $data)
    {
        return $this->sale->where('id', $id)->update($data);
    }

    public function delete($id)
    {
        return $this->sale->where('id', $id)->delete();
    }
}
