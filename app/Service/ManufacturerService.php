<?php

namespace App\Service;

use App\Repositories\ManufacturerRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ManufacturerService
{
    /**
     * @var ManufacturerRepository
     */
    private $manufacturerRepository;

    public function __construct(ManufacturerRepository $manufacturerRepository)
    {
        $this->manufacturerRepository = $manufacturerRepository;
    }

    public function get()
    {
        return [$this->manufacturerRepository->all(), Response::HTTP_OK];
    }

    public function create(Request $request)
    {
        return [$this->manufacturerRepository->caeate($request->only(['name'])), Response::HTTP_CREATED];
    }

    public function edit(Request $request, $manufacturer_id)
    {
        $edit = $this->manufacturerRepository->update($manufacturer_id, $request->only(['name']));
        if ($edit != 0)
            return [$this->manufacturerRepository->findById($manufacturer_id), Response::HTTP_OK];
        else
            return [['error' => 'The Manufacturer Not Found'], Response::HTTP_NOT_FOUND];
    }

    public function remove(Request $request, $manufacturer_id)
    {
        if ($this->manufacturerRepository->findById($manufacturer_id) != null)
            return [[], Response::HTTP_NO_CONTENT];

        else
            return [['error' => 'The Manufacturer Not Found'], Response::HTTP_NOT_FOUND];
    }
}
