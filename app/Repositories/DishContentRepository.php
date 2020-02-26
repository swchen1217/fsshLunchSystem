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
}
