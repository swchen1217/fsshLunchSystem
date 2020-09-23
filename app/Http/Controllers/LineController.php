<?php

namespace App\Http\Controllers;

use App\Service\LineNotifyService;
use Illuminate\Http\Request;

class LineController extends Controller
{

    /**
     * @var LineNotifyService
     */
    private $lineNotifyService;

    public function __construct(LineNotifyService $lineNotifyService)
    {
        $this->lineNotifyService = $lineNotifyService;
    }

    public function getService()
    {
        $mResult = $this->lineNotifyService->getService();
        return response()->json($mResult[0], $mResult[1]);
    }

    public function newSubscribe(Request $request, $notify_id)
    {
        $mResult = $this->lineNotifyService->newSubscribe($request, $notify_id);
        return response()->json($mResult[0], $mResult[1]);
    }

    public function callback(Request $request)
    {
        return response()->json($request->all(),200);
        $mResult = $this->lineNotifyService->callback($request);
        return response()->json($mResult[0], $mResult[1]);
    }
}
