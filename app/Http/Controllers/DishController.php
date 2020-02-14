<?php

namespace App\Http\Controllers;

use App\Dish;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

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
        return response()->json(Dish::getDishById($id),200);
    }
}
