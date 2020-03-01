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

    public function caeate($data)
    {
        return $this->sale->create($data);
    }

    public function update($id, $data)
    {
        return $this->sale->where('id', $id)->update($data);
    }

    public function delete($id)
    {
        return $this->sale->find($id)->delete();
    }
}
