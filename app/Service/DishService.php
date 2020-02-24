<?php

namespace App\Service;

use App\Repositories\DishRepository;

class DishService
{
    private $dishRepository;

    public function __construct(DishRepository $dishRepository)
    {
        $this->dishRepository = $dishRepository;
    }

}
