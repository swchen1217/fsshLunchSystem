<?php

namespace App\Http\Controllers;

use App\Entity\DishContent;
use App\Repositories\DishInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use App\Service\DishService;
use Illuminate\Support\Facades\DB;
use MongoDB\Driver\Exception\Exception;

class DishController extends Controller
{

    /**
     * @var DishService
     */
    private $dishService;

    public function __construct(DishService $dishService)
    {
        $this->middleware('auth:api');
        $this->dishService = $dishService;
    }

    public function getDish()
    {
        $mResult = $this->dishService->getDish();
        return response()->json($mResult[0], $mResult[1]);
    }

    public function getDishById(Request $request, $id)
    {
        $mResult = $this->dishService->getDish($id);
        return response()->json($mResult[0], $mResult[1]);

    }

    public function newDish(Request $request)
    {
        $mResult = $this->dishService->newDish($request);
        return response()->json($mResult[0], $mResult[1]);
    }

    public function image(Request $request)
    {
        $mResult = $this->dishService->image($request);
        return response()->json($mResult[0], $mResult[1]);
    }

    public function editDish(Request $request)
    {
        $mResult = $this->dishService->image($request);
        return response()->json($mResult[0], $mResult[1]);
    }

    public function removeDish()
    {

    }
}
