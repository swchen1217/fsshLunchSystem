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

    public function findAll()
    {
        return $this->balance->all();
    }

    public function findById($id)
    {
        return $this->balance->find($id);
    }

    public function findByUserId($user_id)
    {
        return $this->balance->where('user_id', $user_id)->first();
    }

    public function create($data)
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
