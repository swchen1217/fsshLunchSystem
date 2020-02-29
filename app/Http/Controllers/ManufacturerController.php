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
        $this->middleware('auth:api',['except' => ['get']]);
        $this->manufacturerService = $manufacturerService;
    }

    public function get()
    {
        $mResult = $this->manufacturerService->get();
        return response()->json($mResult[0], $mResult[1]);
    }

    public function create(Request $request)
    {
        $mResult = $this->manufacturerService->create($request);
        return response()->json($mResult[0], $mResult[1]);
    }

    public function edit(Request $request, $manufacturer_id)
    {
        $mResult = $this->manufacturerService->edit($request,$manufacturer_id);
        return response()->json($mResult[0], $mResult[1]);
    }

    public function remove(Request $request, $manufacturer_id)
    {
        $mResult = $this->manufacturerService->remove($request,$manufacturer_id);
        return response()->json($mResult[0], $mResult[1]);
    }
}
