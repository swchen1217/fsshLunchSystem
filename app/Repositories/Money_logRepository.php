<?php

namespace App\Repositories;

use App\Entity\Money_log;

class Money_logRepository
{
    /**
     * @var Money_log
     */
    private $money_log;

    public function __construct(Money_log $money_log)
    {
        $this->money_log = $money_log;
    }

    public function findById($id)
    {
        return $this->money_log->find($id);
    }

    public function findByUserId($user_id)
    {
        return $this->money_log->where('user_id',$user_id)->get();
    }

    public function findByTriggerId($trigger_id)
    {
        return $this->money_log->where('trigger_id',$trigger_id)->get();
    }

    public function findByEvent($event)
    {
        return $this->money_log->where('event',$event)->get();
    }

    public function caeate($data)
    {
        return $this->money_log->create($data);
    }

    public function delete($id)
    {
        return $this->money_log->where('id', $id)->delete();
    }
}
