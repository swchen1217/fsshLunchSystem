<?php

namespace App\Service;

use App\Repositories\UserRepository;

class AuthService
{

    /**
     * @var UserRepository
     */
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

}
