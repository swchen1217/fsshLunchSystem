<?php

namespace App\Repositories;

use App\Entity\Nutrition;

class NutritionRepository
{
    /**
     * @var Nutrition
     */
    private $nutrition;

    public function __construct(Nutrition $nutrition)
    {
        $this->nutrition = $nutrition;
    }

    public function findById($id)
    {
        return $this->nutrition->find($id);
    }

    public function create($data)
    {
        return $this->nutrition->create($data);
    }

    public function update($id, $data)
    {
        return $this->nutrition->where('id', $id)->update($data);
    }

    public function delete($id)
    {
        return $this->nutrition->find($id)->delete();
    }
}
