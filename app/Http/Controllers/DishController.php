<?php

namespace App\Http\Controllers;

use App\Repositories\DishInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use App\Service\DishService;

class DishController extends Controller
{

    /**
     * @var DishService
     */
    private $dishServiced;

    public function __construct(DishService $dishServiced)
    {
        //$this->middleware('auth:api', ['except' => ['getDishById','getDish']]);
        $this->dishServiced = $dishServiced;
    }

    public function getDish()
    {
        return response()->json(['isOk' => $this->dishServiced->test()], 200);
        //return response()->json(['success'=>true,'data'=>Dish::getDish()],200);
    }

    public function getDishById(Request $request, $id)
    {
        try {
            return response()->json(['success' => true, 'data' => Dish::getDishById($id)], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['success' => false, 'error' => 'The Dish Not Found'], 404);
        }
    }
}
