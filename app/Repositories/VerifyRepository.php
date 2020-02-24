<?php

namespace App\Repositories;

use App\Entity\Verify;

class VerifyRepository
{
    /**
     * @var Verify
     */
    private $verify;

    public function __construct(Verify $verify)
    {
        $this->verify = $verify;
    }

    public function findByUserId($user_id)
    {
        return $this->verify->where('user_id',$user_id)->get();
    }

    public function caeate($data)
    {
        return $this->verify->create($data);
    }

    public function deleteByUserId($user_id)
    {
        return $this->verify->where('user_id',$user_id)->delete();
    }

}
