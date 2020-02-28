<?php

namespace App\Http\Controllers;

use App\Service\ManufacturerService;
use Illuminate\Http\Request;

class ManufacturerController extends Controller
{
    /**
     * @var ManufacturerService
     */
    private $manufacturerService;

    public function __construct(ManufacturerService $manufacturerService)
    {
        $this->middleware('auth:api');
        $this->manufacturerService = $manufacturerService;
    }

    public function getDish()
    {
        $mResult = $this->dishService->getDish();
        return response()->json($mResult[0], $mResult[1]);
    }
}
