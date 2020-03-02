<?php

namespace App\Repositories;

use App\Entity\Order;

class OrderRepository
{
    /**
     * @var Order
     */
    private $order;

    public function __construct(Order $order)
{
    $this->order = $order;
}

    public function all()
    {
        return $this->order->all();
    }

    public function findById($id)
    {
        return $this->order->find($id);
    }

    public function findByUserId($user_id)
    {
        return $this->order->where('user_id',$user_id)->get();
    }

    public function findBySaleId($sale_id)
    {
        return $this->order->where('sale_id',$sale_id)->get();
    }

    public function caeate($data)
    {
        return $this->order->create($data);
    }

    public function update($id, $data)
    {
        return $this->order->where('id', $id)->update($data);
    }

    public function delete($id)
    {
        return $this->order->where('id', $id)->delete();
    }
}
