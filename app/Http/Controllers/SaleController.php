<?php

namespace App\Http\Controllers;

use App\Service\SaleService;
use Illuminate\Http\Request;

class SaleController extends Controller
{
    /**
     * @var SaleService
     */
    private $saleService;

    public function __construct(SaleService $saleService)
    {
        $this->middleware('auth:api');
        $this->saleService = $saleService;
    }
}
