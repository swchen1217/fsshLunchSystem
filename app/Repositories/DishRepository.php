<?php

namespace App\Repositories;

use App\Entity\Dish;

class DishRepository
{
    /**
     * @var Dish
     */
    private $dish;

    public function __construct(Dish $dish)
    {
        $this->dish = $dish;
    }

    public function all()
    {
        return $this->dish->all();
    }

    public function findById($id)
    {
        return $this->dish->find($id);
    }

    public function findByManufacturer_id($manufacturer_id)
    {
        return $this->dish->where('manufacturer_id', $manufacturer_id)->get();
    }

    public function caeate($data)
    {
        return $this->dish->create($data);
    }

    public function update($id, $data)
    {
        return $this->dish->where('id', $id)->update($data);
    }

    public function delete($id)
    {
        $dish=$this->dish->find($id);
        if($dish!=null)
            return $dish->delete();
        else
            return -1;
    }
}
