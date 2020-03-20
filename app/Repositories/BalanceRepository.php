<?php

namespace App\Repositories;

use App\Entity\Balance;

class BalanceRepository
{
    /**
     * @var Balance
     */
    private $balance;

    public function __construct(Balance $balance)
    {
        $this->balance = $balance;
    }

    public function findById($id)
    {
        return $this->balance->find($id);
    }

    public function findByUserId($user_id)
    {
        return $this->balance->where('user_id', $user_id)->first();
    }

    public function findByCreateAt($date)
    {
        return $this->balance->where('created_at', '>=', "'". $date . ' 00:00:00' . '"')->where('created_at', '<=', '"' . $date . ' 23:59:59' . '"')->first();
    }

    public function caeate($data)
    {
        return $this->balance->create($data);
    }

    public function update($id, $data)
    {
        return $this->balance->where('id', $id)->update($data);
    }

    public function updateByUserId($id, $data)
    {
        return $this->balance->where('user_id', $id)->update($data);
    }

    public function delete($id)
    {
        return $this->balance->where('id', $id)->delete();
    }

    public function deleteByUserId($id)
    {
        return $this->balance->where('id', $id)->delete();
    }
}
