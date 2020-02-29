<?php

namespace App\Service;

use App\Repositories\SaleRepository;

class SaleService
{
    /**
     * @var SaleRepository
     */
    private $saleRepository;

    public function __construct(SaleRepository $saleRepository)
    {
        $this->saleRepository = $saleRepository;
    }
}
