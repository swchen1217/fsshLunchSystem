<?php

namespace App\Service;

use App\Constant\LineNotifyMessageHandle;
use App\Repositories\Line_notifyRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use KS\Line\LineNotify;

class LineNotifyService
{

    /**
     * @var LineNotify
     */
    private $lineNotify;
    /**
     * @var Line_notifyRepository
     */
    private $line_notifyRepository;

    public function __construct(Line_notifyRepository $line_notifyRepository)
    {
        $this->lineNotify = new LineNotify("");
        $this->line_notifyRepository = $line_notifyRepository;
    }

    public function send($line_notify_id)
    {
        // return [true|flase,msg]

        $line_notify = $this->line_notifyRepository->findById($line_notify_id);
        if ($line_notify == null)
            return [false, '`line_notify_id` not found'];

        if (is_callable(array(LineNotifyMessageHandle::class, $line_notify->method))) {
            return LineNotifyMessageHandle::{$line_notify->method}();
        } else {
            return [false, 'method not found'];
        }

        //return $this->{$line_notify->method}();
    }

    private function commit($token, $text, $imagePath = null, $sticker = null)
    {
        $this->lineNotify->setToken($token);
        $this->lineNotify->send($text, $imagePath, $sticker);
    }

}
