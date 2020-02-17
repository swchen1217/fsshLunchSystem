<?php

namespace App\Http\Controllers;

use App\Dish;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Exception;

class DishController extends Controller
{
    public function __construct()
    {
        $this->middleware('jwt.auth', ['except' => ['getDishById','getDish']]);
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
