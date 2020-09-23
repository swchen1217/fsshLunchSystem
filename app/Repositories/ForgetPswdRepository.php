<?php

namespace App\Repositories;

use App\Entity\ForgetPswd;

class ForgetPswdRepository
{

    /**
     * @var ForgetPswd
     */
    private $forgetPswd;

    public function __construct(ForgetPswd $forgetPswd)
    {
        $this->forgetPswd = $forgetPswd;
    }

    public function findByUserId($user_id)
    {
        return $this->forgetPswd->where('user_id', $user_id)->get();
    }

    public function findByUserIdAndToken($user_id, $token)
    {
        return $this->forgetPswd->where('user_id', $user_id)->where('token', $token)->first();
    }

    public function create($data)
    {
        return $this->forgetPswd->create($data);
    }

    public function deleteByUserId($user_id)
    {
        return $this->forgetPswd->where('user_id', $user_id)->delete();
    }

}
