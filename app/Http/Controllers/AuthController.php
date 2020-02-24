<?php

namespace App\Http\Controllers;

use App\Service\AuthService;
use Firebase\JWT\JWT;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Laravel\Passport\Passport;

class AuthController extends Controller
{

    /**
     * @var AuthService
     */
    private $authService;

    public function __construct(AuthService $authService)
    {
        $this->middleware('auth:api', ['except' => ['createToken']]);
        $this->authService=$authService;
    }

    public function createToken(Request $request)
    {
        try {
            //DB::beginTransaction();
            $mRequest=$request->all();
            $mResult=$this->authService->createToken($mRequest);
            return response()->json($mResult);
            //DB::commit();
        } catch (Exception $exception) {
            //DB::rollBack();
            throw $exception;
        }
    }

    public function verify(Request $request)
    {

    }

    public function user()
    {
        try {
            $mResult=$this->authService->getUser();
            return response()->json($mResult);
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    public function revokeToken(Request $request, $tokenId)
    {
        $token=Passport::token()->where('id', $tokenId)->where('user_id', $request->user()->getKey())->first();

        if (is_null($token)) {
            return response()->json([],404);
        }

        $token->revoke();

        Passport::refreshToken()->where('access_token_id', $tokenId)->update(['revoked' => true]);

        return response()->json(['success' => true]);
    }
}
