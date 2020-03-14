<?php

namespace App\Service;

use App\Constant\URLConstant;
use App\Mail\ForgetPswd;
use App\Repositories\ForgetPswdRepository;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class PswdService
{
    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var ForgetPswdRepository
     */
    private $forgetPswdRepository;

    public function __construct(UserRepository $userRepository, ForgetPswdRepository $forgetPswdRepository)
    {
        $this->userRepository = $userRepository;
        $this->forgetPswdRepository = $forgetPswdRepository;
    }

    public function account(Request $request)
    {
        $user = $this->userRepository->findByAccount($request->input('account'));
        if ($user != null) {
            if (Hash::check($request->input('old_pswd'), $user->password)) {
                $this->userRepository->update($user->id, ['password' => bcrypt($request->input('new_pswd'))]);
                //todo log
                return [[], Response::HTTP_NO_CONTENT];
            } else
                return [['error' => 'Old password error'], Response::HTTP_FORBIDDEN];
        } else
            return [['error' => 'The User Not Found'], Response::HTTP_NOT_FOUND];
    }

    public function forget(Request $request)
    {
        $user = $this->userRepository->findByEmail($request->input('email'));
        if ($user != null) {
            $token = md5(rand());
            if ($request->input('redirect') == 'Frontend') {
                $this->forgetPswdRepository->caeate(['user_id' => $user->id, 'token' => $token]);
                if (env('APP_ENV') == 'local')
                    $url = URLConstant::URL_DEV_FRONTEND_FORGET_PW;
                else
                    $url = URLConstant::URL_PDC_FRONTEND_FORGET_PW;
                Mail::to($user)->queue(new ForgetPswd(['url' => $url . '?token=' . $user->account . '.' . $token]));
            } elseif ($request->input('redirect') == 'AdminFrontend') {
                $this->forgetPswdRepository->caeate(['user_id' => $user->id, 'token' => $token]);
                if (env('APP_ENV') == 'local')
                    $url = URLConstant::URL_DEV_ADMIN_FRONTEND_FORGET_PW;
                else
                    $url = URLConstant::URL_PDC_ADMIN_FRONTEND_FORGET_PW;
                $url2 = explode('#', $url);
                Mail::to($user)->queue(new ForgetPswd(['url' => $url2[0] . '?token=' . $user->account . '.' . $token . '#' . $url2[1]]));
            } else
                return [['error' => 'The Redirect Not Found'], Response::HTTP_NOT_FOUND];
            return [[], Response::HTTP_NO_CONTENT];
        } else
            return [['error' => 'The User Not Found'], Response::HTTP_NOT_FOUND];
    }

    public function token(Request $request)
    {

    }
}
