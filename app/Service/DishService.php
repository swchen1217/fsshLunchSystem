<?php

namespace App\Service;

use App\Repository\DishRepository;

class DishService
{

    private $dishRepository;

    public function __construct(DishRepository $dishRepository)
    {
        $this->dishRepository = $dishRepository;
    }

    /*public function read($id)
    {
        return $this->post->find($id);
    }*/

}
