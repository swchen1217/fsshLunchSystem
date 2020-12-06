<?php

namespace App\Http\Controllers;

use App\Service\PswdService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class PswdController extends Controller
{
    /**
     * @var PswdService
     */
    private $pswdService;

    public function __construct(PswdService $pswdService)
    {
        $this->pswdService = $pswdService;
    }

    public function account(Request $request)
    {
        $mResult = $this->pswdService->account($request);
        return response()->json($mResult[0], $mResult[1]);
    }

    public function forget(Request $request)
    {
        $validator = Validator::make([$request->input('email')], ['required|email']);
        if ($validator->fails())
            return response()->json(['error' => 'Email format error'], Response::HTTP_BAD_REQUEST);
        $mResult = $this->pswdService->forget($request);
        return response()->json($mResult[0], $mResult[1]);
    }

    public function token(Request $request)
    {
        $mResult = $this->pswdService->token($request);
        return response()->json($mResult[0], $mResult[1]);
    }
}
