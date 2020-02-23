<?php

namespace App\Http\Controllers;

use App\Entity\User;
use App\Repository\UserRepository;
use Firebase\JWT\JWT;
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
            $access_token=$mResultContent['access_token']??null;
            $refresh_token=$mResultContent['refresh_token']??null;
            if(!empty($access_token)){
                Log::info('pass');
                //是否需要驗證
                //SendEmail 刪access_token存refresh_token 驗證後refresh (200)
                //OK 發token (200)

                $path = storage_path('oauth-public.key');
                $file=fopen($path, "r");
                $publicKey = fread($file, filesize($path));
                fclose($file);
                $access_token_payload=(array)JWT::decode($access_token,$publicKey,array('RS256'));
                //$token=Passport::token()->where('id', $access_token_payload['jti'])->where('user_id', $this->UserRepository->findByAccount()->getkey())->first();

                //return response()->json($this->UserRepository->findByAccount($req['account']));
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
