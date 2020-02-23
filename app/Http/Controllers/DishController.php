<?php

namespace App\Http\Controllers;

use App\Dish;
use App\Repository\DishRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Exception;

class DishController extends Controller
{
    /**
     * @var DishRepository
     */
    private $dishRepository;

    public function __construct(DishRepository $dishRepository)
    {
        $this->middleware('jwt.auth', ['except' => ['getDishById','getDish']]);
        $this->dishRepository=$dishRepository;
    }

    public function getDish()
    {
        return response()->json(['success'=>true,'data'=>Dish::getDish()],200);
    }

    public function getDishById(Request $request, $id)
    {
        try{
            return response()->json(['success'=>true,'data'=>Dish::getDishById($id)],200);
        }catch (ModelNotFoundException $e){
            return response()->json(['success'=>false,'error'=>'The Dish Not Found'],404);
        }
    }
}
