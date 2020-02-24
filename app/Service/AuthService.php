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
        $req=$request->all();
        if ($req['grant_type'] == "password"){
            $user=$this->userRepository->findByAccount($req['username']);
            if($user!=null){
                $user_id=$user->id;
            }else{
                return [['error'=>'Username or Password Error'],Response::HTTP_UNAUTHORIZED];
            }
            $ip=$request->ip();
            $fail=$this->login_failRepository->findByUserIdAndIp($user_id,$ip)->sortByDesc('created_at');
            if(count($fail->where('used',false))>=5){
                $tmp=$fail->first();
                $timeLast=strtotime($tmp->created_at);
                $timeNow=time();
                if($timeNow-$timeLast>600){
                    $this->login_failRepository->changeToUsedByUserIdAndIp($user_id,$ip);
                }else{
                    return [
                        ["message"=>"You try and fail many times".
                            ",Please try again later".
                            ",Or replace the IP address"]
                        , Response::HTTP_FORBIDDEN];
                }
            }
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
                $user_id=$this->userRepository->findByAccount($req['username'])->id;
                $ip=$request->ip();
                $fail=$this->login_failRepository->findByUserIdAndIp($user_id,$ip);
                if(count($fail)>5){
                    $path = storage_path('oauth-public.key');
                    $file = fopen($path, "r");
                    $publicKey = fread($file, filesize($path));
                    fclose($file);
                    $access_token_payload = (array)JWT::decode($access_token, $publicKey, array('RS256'));
                    Passport::token()->where('id', $access_token_payload['jti'])->where('user_id', $user_id)->first()->revoke();



                    //TODO SendEmail 刪access_token存refresh_token 驗證後refresh (200)
                }
                // todo 刪login_fail
            } else {
                Log::info('not-pass');
                $user_id=$this->userRepository->findByAccount($req['username'])->id;
                $ip=$request->ip();
                if($mResultContent['error']=="invalid_client"){
                    //todo Log or Notify
                    return [$mResultContent, Response::HTTP_UNAUTHORIZED];
                }elseif ($mResultContent['error']=="invalid_grant"){
                    if($user_id!=null){
                        $this->login_failRepository->caeate(['user_id'=>$user_id,'ip'=>$ip,'used'=>false]);
                    }
                    return [['error'=>'Username or Password Error'],Response::HTTP_UNAUTHORIZED];
                }else{
                    //todo Log or Notify
                }
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
