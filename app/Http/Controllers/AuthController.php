<?php

namespace App\Http\Controllers;

use App\Entity\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
//use Illuminate\Support\Facades\Request;
use Laravel\Passport\Passport;
use Laravel\Passport\RefreshTokenRepository;
use Laravel\Passport\TokenRepository;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['createToken']]);
    }

    public function createToken(Request $request)
    {
        $req=$request->all();

        //查看ip id 是否有fail紀錄
        //  <5 pass >=5 看最後一次是否超過10分鐘
        //                  刪除->pass
        //                  return (403)
        //  pass

        $mRequest = app('request')->create('/oauth/token', 'POST', $req);
        $mResponse = app('router')->prepareResponse($mRequest, app()->handle($mRequest));
        $mResultContent=json_decode($mResponse->getContent(),true);
        $mResultStatusCode=$mResponse->getStatusCode();

        if($req['grant_type']=="password"){
            if(!empty($mResult['access_token'])){
                Log::info('pass');
                //是否需要驗證
                //SendEmail 存token 驗證後發回 (200)
                //OK 發token (200)
            }else{
                Log::info('not-pass');
                //client是否錯誤 "error": "invalid_client"
                //  (401)
                //  username是否存在
                //      (401)->紀錄fail
                //      (401)
            }
        }

        return response()->json($mResultContent,$mResultStatusCode);
    }

    public function verify(Request $request)
    {

    }

    public function user(Request $request)
    {
        return response()->json(auth()->user());
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
