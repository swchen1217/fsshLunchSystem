<?php

namespace App\Repositories;

use App\Entity\Manufacturer;

class ManufacturerRepository
{
    /**
     * @var Manufacturer
     */
    private $manufacturer;

    public function __construct(Manufacturer $manufacturer)
    {
        $this->manufacturer = $manufacturer;
    }

    public function all()
    {
        return $this->manufacturer->all();
    }

    public function findById($id)
    {
        return $this->manufacturer->find($id);
    }

    public function caeate($data)
    {
        return $this->manufacturer->create($data);
    }

    public function update($id, $data)
    {
        return $this->manufacturer->where('id', $id)->update($data);
    }

    public function delete($id)
    {
        return $this->manufacturer->find($id)->delete();
    }
}
