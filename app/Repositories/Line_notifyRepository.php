<?php

namespace App\Repositories;

use App\Entity\Line_notify;

class Line_notifyRepository
{

    /**
     * @var Line_notify
     */
    private $line_notify;

    public function __construct(Line_notify $line_notify)
    {
        $this->line_notify = $line_notify;
    }

    public function all()
    {
        return $this->line_notify->all();
    }

    public function findById($id)
    {
        return $this->line_notify->find($id);
    }
}
