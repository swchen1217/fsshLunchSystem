<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GeneralController extends Controller
{
    public function isOk()
    {
        return response()->json(['success' => true]);
    }

    public function welcome()
    {
        return view('welcome');
    }
}
