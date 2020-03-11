<?php

namespace App\Service;

use App\Mail\Verify;
use App\Repositories\Login_failRepository;
use App\Repositories\UserRepository;
use App\Repositories\VerifyRepository;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Laravel\Passport\Passport;
use Illuminate\Http\Response;

class AuthService
{

    private $userRepository;
    private $login_failRepository;
    private $verifyRepository;

    public function __construct(UserRepository $userRepository, Login_failRepository $login_failRepository, VerifyRepository $verifyRepository)
    {
        $this->userRepository = $userRepository;
        $this->login_failRepository = $login_failRepository;
        $this->verifyRepository = $verifyRepository;
    }

    public function createToken(Request $request)
    {
        $req = $request->all();
        if ($req['grant_type'] == "password") {
            $user = $this->userRepository->findByAccount($req['username']);
            if ($user != null) {
                $user_id = $user->id;
            } else {
                return [['error' => 'Username or Password Error'], Response::HTTP_UNAUTHORIZED];
            }
            $ip = $request->ip();
            $fail = $this->login_failRepository->findByUserIdAndIp($user_id, $ip)->sortByDesc('created_at');
            if (count($fail->where('used', false)) >= 5) {
                $tmp = $fail->first();
                $timeLast = strtotime($tmp->created_at);
                $timeNow = time();
                if ($timeNow - $timeLast > 600) {
                    $this->login_failRepository->changeToUsedByUserIdAndIp($user_id, $ip);
                } else {
                    return [
                        ["message" => "You try and fail many times" .
                            ",Please try again later" .
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
                $user = $this->userRepository->findByAccount($req['username']);
                $user_id = $user->id;
                $ip = $request->ip();
                $fail = $this->login_failRepository->findByUserId($user_id);
                if (count($fail) > 5) {
                    $path = storage_path('oauth-public.key');
                    $file = fopen($path, "r");
                    $publicKey = fread($file, filesize($path));
                    fclose($file);
                    $access_token_payload = (array)JWT::decode($access_token, $publicKey, array('RS256'));
                    Passport::token()->where('id', $access_token_payload['jti'])->where('user_id', $user_id)->first()->revoke();
                    $code = strtoupper(substr(md5(time()), 0, 10));
                    $this->verifyRepository->caeate(['user_id' => $user_id, 'code' => $code, 'client_id' => $req['client_id'], 'client_secret' => $req['client_secret'], 'refresh_token' => $refresh_token]);
                    Mail::to($user)->queue(new Verify(['verify_code' => $code]));
                    return [['message' => 'You failed to log in too many times,' .
                        'Please verify your identity before you can log in.' .
                        'Receive email and get verification code to request `/ oauth / verify` API.']
                        , Response::HTTP_OK];
                } else {
                    if ($mResultStatusCode == 200) {
                        $this->login_failRepository->deleteByUserId($user_id);
                        $mResultContent['access_token'] = $this->payload($mResultContent['access_token']);
                        return [$mResultContent, Response::HTTP_OK];
                    } else {
                        //todo Log or Notify
                        return [['error' => 'Server Error ,Please contact the admin'], Response::HTTP_INTERNAL_SERVER_ERROR];
                    }
                }
            } else {
                Log::info('not-pass');
                $user_id = $this->userRepository->findByAccount($req['username'])->id;
                $ip = $request->ip();
                if ($mResultContent['error'] == "invalid_client") {
                    //todo Log or Notify
                    return [$mResultContent, Response::HTTP_UNAUTHORIZED];
                } elseif ($mResultContent['error'] == "invalid_grant") {
                    if ($user_id != null) {
                        $this->login_failRepository->caeate(['user_id' => $user_id, 'ip' => $ip, 'used' => false]);
                    }
                    return [['error' => 'Username or Password Error'], Response::HTTP_UNAUTHORIZED];
                } else {
                    //todo Log or Notify
                }
            }
        }
        return [$mResultContent, $mResultStatusCode];
    }

    public function getUser(Request $request)
    {
        //$user = $request->user();
        $user = Auth::user();
        $mResult[0] = array_merge($user->toArray(), array('permissions' => $this->userRepository->getAllPermissiosNamesById(auth()->user()->id)));
        $mResult[1] = Response::HTTP_OK;
        return $mResult;
    }

    public function verifyCommit(Request $request)
    {
        $req = $request->all();
        $user = $this->userRepository->findByAccount($req['username']);
        if ($user != null) {
            $user_id = $user->id;
            $verify = $this->verifyRepository->findByUserId($user_id)->sortByDesc('created_at')->first();
            if ($verify != null) {
                if ($req['verify_code'] == $verify->code) {
                    $timeLast = strtotime($verify->created_at);
                    $timeNow = time();
                    if ($timeNow - $timeLast <= 1800) {
                        $req2 = [
                            'grant_type' => 'refresh_token',
                            'refresh_token' => $verify->refresh_token,
                            'client_id' => $verify->client_id,
                            'client_secret' => $verify->client_secret];
                        $m2Request = app('request')->create('/oauth/token', 'POST', $req2);
                        $m2Response = app('router')->prepareResponse($m2Request, app()->handle($m2Request));
                        $m2ResultContent = json_decode($m2Response->getContent(), true);
                        $m2ResultStatusCode = $m2Response->getStatusCode();
                        if ($m2ResultStatusCode == 200) {
                            //todo delete V_C
                            $this->login_failRepository->deleteByUserId($user_id);
                            $this->verifyRepository->deleteByUserId($user_id);
                            $m2ResultContent['access_token'] = $this->payload($m2ResultContent['access_token']);
                            return [$m2ResultContent, Response::HTTP_OK];
                        } else {
                            //todo Log or Notify
                            return [['error' => 'Server Error ,Please contact the admin'], Response::HTTP_INTERNAL_SERVER_ERROR];
                        }
                    } else {
                        return [['error' => 'Verify code expired'], Response::HTTP_FORBIDDEN];
                    }
                } else {
                    return [['error' => 'Verify code error'], Response::HTTP_FORBIDDEN];
                }
            } else {
                return [['error' => 'You don`t need to verify'], Response::HTTP_FORBIDDEN];
            }
        } else {
            return [['error' => 'Username Not Fount'], Response::HTTP_NOT_FOUND];
        }
    }

    public function revokeToken(Request $request, $tokenId)
    {
        $token = Passport::token()->where('id', $tokenId)->where('user_id', $request->user()->getKey())->first();
        if (is_null($token)) {
            return [[], Response::HTTP_NOT_FOUND];
        }
        $token->revoke();
        Passport::refreshToken()->where('access_token_id', $tokenId)->update(['revoked' => true]);
        return [[], Response::HTTP_NO_CONTENT];
    }

    private function payload($access_token)
    {
        $jwt = explode('.', $access_token);
        $payload = json_decode(base64_decode($jwt[1]), true);
        $user = $this->userRepository->findById($payload['sub']);
        $user_info = array_merge($user->toArray(), ['permissions' => $this->userRepository->getAllPermissiosNamesById($user->id)]);
        $payload = array_merge($payload, ['user' => $user_info]);
        $path = storage_path('oauth-private.key');
        $file = fopen($path, "r");
        $privateKey = fread($file, filesize($path));
        fclose($file);
        $token = JWT::encode($payload, $privateKey, 'RS256');
        return $token;
    }
}
