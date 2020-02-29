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
}
