<?php

namespace App\Service;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Contracts\Filesystem\FileNotFoundException;

class NotifyService
{

    public function __construct()
    {

    }

    public function get($type)
    {
        try {
            return [json_decode(Storage::get('notify/' . $type . '.json'), true), Response::HTTP_OK];
        } catch (FileNotFoundException $e) {
            return [['error' => 'The Notify Type Not Found'], Response::HTTP_NOT_FOUND];
        }
    }

}
