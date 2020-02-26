<?php

namespace App\Repositories;

use App\Entity\DishContent;

class DishContentRepository
{
    /**
     * @var DishContent
     */
    private $dishContent;

    public function __construct(DishContent $dishContent)
    {
        $this->dishContent = $dishContent;
    }

    public function findById($id)
    {
        return $this->dishContent->find($id);
    }

    public function findByDishId($dish_id)
    {
        return $this->dishContent->where('dish_id',$dish_id);
    }

    public function caeate($data)
    {
        return $this->dishContent->create($data);
    }

    public function delete($id)
    {
        return $this->dishContent->find($id)->delete();
    }
}
