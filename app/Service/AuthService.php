<?php

namespace App\Service;

use App\Entity\Login_fail;
use App\Repositories\Login_failRepository;
use App\Repositories\UserRepository;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Laravel\Passport\Passport;
use Illuminate\Http\Response;

class AuthService
{

    private $userRepository;

    public function __construct(UserRepository $userRepository,Login_failRepository $login_failRepository)
    {
        $this->userRepository = $userRepository;
        $this->login_failRepository = $login_failRepository;
    }

    public function createToken(Request $request)
    {
        //查看 ip id 是否有fail紀錄
        //  <5 pass >=5 看最後一次是否超過10分鐘
        //                  刪除->pass
        //                  return (403)
        //  pass

        $req=$request->all();
        if ($req['grant_type'] == "password"){
            $user_id=$this->userRepository->findByAccount($req['username'])->id;
            //$fail=$this->login_failRepository->findByUserIdAndIp($user_id,);
            //if()


        }

        $mRequest = app('request')->create('/oauth/token', 'POST', $req);
        $mResponse = app('router')->prepareResponse($mRequest, app()->handle($mRequest));
        $mResultContent = json_decode($mResponse->getContent(), true);
        $mResultStatusCode = $mResponse->getStatusCode();

        if ($req['grant_type'] == "password") {
            $access_token = $mResultContent['access_token'] ?? null;
            $refresh_token = $mResultContent['refresh_token'] ?? null;
            if (!empty($access_token)) {
                Log::info('pass');
                //是否需要驗證
                //SendEmail 刪access_token存refresh_token 驗證後refresh (200)
                //OK 發token (200)

                $path = storage_path('oauth-public.key');
                $file = fopen($path, "r");
                $publicKey = fread($file, filesize($path));
                fclose($file);
                $access_token_payload = (array)JWT::decode($access_token, $publicKey, array('RS256'));
                //$token=Passport::token()->where('id', $access_token_payload['jti'])->where('user_id', $this->UserRepository.php->findByAccount()->getkey())->first();

                //return response()->json($this->UserRepository.php->findByAccount($req['account']));
            } else {
                Log::info('not-pass');
                //client是否錯誤 "error": "invalid_client"
                //  (401)
                //  username是否存在
                //      (401)->紀錄fail
                //      (401)
            }
        }
        return [$mResultContent, $mResultStatusCode];
    }

    public function getUser(Request $request)
    {
        $user = $request->user();
        $mResult[0]=array_merge($user->toArray(), array('permissions' => $this->userRepository->getAllPermissiosNamesById(auth()->user()->id)));
        $mResult[1]=Response::HTTP_OK;
        return $mResult;
    }

    public function verifyCommit()
    {
        //todo
    }

    public function revokeToken(Request $request, $tokenId)
    {
        $token = Passport::token()->where('id', $tokenId)->where('user_id', $request->user()->getKey())->first();
        if (is_null($token)) {
            return [[],Response::HTTP_NOT_FOUND];
        }
        $token->revoke();
        Passport::refreshToken()->where('access_token_id', $tokenId)->update(['revoked' => true]);
        return [[],Response::HTTP_NO_CONTENT];
    }

}
