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
        Log::debug('passwd');

        $request = app('request')->create('/oauth/token', 'POST', $request->all());
        $response = app('router')->prepareResponse($request, app()->handle($request));
        $result=json_decode($response->content(),true);

        return response()->json($result);
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
