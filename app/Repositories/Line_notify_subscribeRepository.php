<?php

namespace App\Repositories;

use App\Entity\Line_notify_subscribe;

class Line_notify_subscribeRepository
{

    /**
     * @var Line_notify_subscribe
     */
    private $line_notify_subscribe;

    public function __construct(Line_notify_subscribe $line_notify_subscribe)
    {
        $this->line_notify_subscribe = $line_notify_subscribe;
    }

    public function findByUserIdAndLineNotifyIdAndToken($user_id, $line_notify_id, $token)
    {
        return $this->line_notify_subscribe->where('user_id', $user_id)->where('line_notify_id', $line_notify_id)->where('token', $token)->first();
    }

    public function create($data)
    {
        return $this->line_notify_subscribe->create($data);
    }

    public function deleteByUserIdAndLineNotifyId($user_id, $line_notify_id)
    {
        return $this->line_notify_subscribe->where('user_id', $user_id)->where('line_notify_id', $line_notify_id)->delete();
    }

}
