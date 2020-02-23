<?php

namespace App\Service;

use App\Repository\UserRepository;

class AuthService{

    public function __construct(UserRepository $user)
    {
        $this->user = $user ;
    }

    /*public function read($id)
    {
        return $this->post->find($id);
    }*/

}
