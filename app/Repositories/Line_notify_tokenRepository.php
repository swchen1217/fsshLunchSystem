<?php

namespace App\Repositories;

use App\App\Entity\Line_notify_token;

class Line_notify_tokenRepository
{

    /**
     * @var Line_notify_token
     */
    private $line_notify_token;

    public function __construct(Line_notify_token $line_notify_token)
    {
        $this->line_notify_token = $line_notify_token;
    }

    public function all()
    {
        return $this->line_notify_token->all();
    }

    public function findById($id)
    {
        return $this->line_notify_token->find($id);
    }

    public function findByUserId($userId)
    {
        return $this->line_notify_token->where('user_id', $userId)->get();
    }

    public function findByNotifyId($notifyId)
    {
        return $this->line_notify_token->where('notify_id', $notifyId)->get();
    }

    public function create($data)
    {
        return $this->line_notify_token->create($data);
    }

    public function update($id, $data)
    {
        return $this->line_notify_token->where('id', $id)->update($data);
    }

    public function delete($id)
    {
        return $this->line_notify_token->where('id', $id)->delete();
    }
}
