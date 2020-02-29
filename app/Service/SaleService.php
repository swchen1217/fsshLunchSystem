<?php

namespace App\Service;

use App\Repositories\SaleRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

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

    public function getSaleData($type='all',$data=null){

    }

    public function create(Request $request)
    {

    }

    public function edit(Request $request, $manufacturer_id)
    {

    }

    public function remove(Request $request, $manufacturer_id)
    {
        
    }
}
