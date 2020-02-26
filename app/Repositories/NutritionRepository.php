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
}
