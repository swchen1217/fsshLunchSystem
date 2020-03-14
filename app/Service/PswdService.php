<?php

namespace App\Service;

use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PswdService
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function account(Request $request, $account)
    {

    }

    public function forget(Request $request, $email)
    {

    }

    public function token(Request $request, $token)
    {

    }
}
