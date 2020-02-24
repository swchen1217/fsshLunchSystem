<?php

namespace App\Service;

use Laravel\Passport\Bridge\UserRepository;

class AuthService
{

    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

}
