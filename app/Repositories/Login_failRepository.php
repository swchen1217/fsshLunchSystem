<?php

namespace App\Repositories;

use App\Entity\Login_fail;

class Login_failRepository
{
    /**
     * @var Login_fail
     */
    private $login_fail;

    public function __construct(Login_fail $login_fail)
    {
        $this->login_fail = $login_fail;
    }

    public function findByUserId($user_id)
    {
        return $this->login_fail->where('user_id',$user_id)->get();
    }

    public function findByUserIdAndIp($user_id,$ip)
    {
        return $this->login_fail->where('user_id',$user_id)->where('ip',$ip)->get();
    }

    public function caeate($data)
    {
        return $this->login_fail->create($data);
    }

    public function changeToUsedByUserIdAndIp($user_id,$ip)
    {
        return $this->login_fail->where('user_id',$user_id)->where('ip',$ip)->where('used',false)->update(['used'=>true]);
    }

    public function deleteByUserId($user_id)
    {
        return $this->login_fail->where('user_id',$user_id)->delete();
    }

    public function deleteByUserIdAndIp($user_id,$ip)
    {
        return $this->login_fail->where('user_id',$user_id)->where('ip',$ip)->delete();
    }

}
