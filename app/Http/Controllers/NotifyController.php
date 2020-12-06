<?php

namespace App\Http\Controllers;

use App\Service\NotifyService;
use Illuminate\Http\Request;

class NotifyController extends Controller
{

    /**
     * @var NotifyService
     */
    private $notifyService;

    public function __construct(NotifyService $notifyService)
    {
        $this->notifyService = $notifyService;
    }

    public function get(Request $request, $type)
    {
        $mResult = $this->notifyService->get($type);
        return response()->json($mResult[0], $mResult[1]);
    }
}
