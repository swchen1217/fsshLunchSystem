<?php

namespace App\Service;

use App\Constant\LineNotifyMessageHandle;
use App\Repositories\DishRepository;
use App\Repositories\Line_notify_subscribeRepository;
use App\Repositories\Line_notify_tokenRepository;
use App\Repositories\Line_notifyRepository;
use App\Repositories\ManufacturerRepository;
use App\Repositories\OrderRepository;
use App\Repositories\SaleRepository;
use App\Repositories\UserRepository;
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

    private $notifyInfo;
    /**
     * @var Line_notify_tokenRepository
     */
    private $line_notify_tokenRepository;
    /**
     * @var OrderRepository
     */
    private $orderRepository;
    /**
     * @var SaleRepository
     */
    private $saleRepository;

    private $weekChinese = ['日', '一', '二', '三', '四', '五', '六'];
    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var DishRepository
     */
    private $dishRepository;
    /**
     * @var ManufacturerRepository
     */
    private $manufacturerRepository;
    /**
     * @var Line_notify_subscribeRepository
     */
    private $line_notify_subscribeRepository;

    public function __construct(
        Line_notifyRepository $line_notifyRepository,
        Line_notify_tokenRepository $line_notify_tokenRepository,
        OrderRepository $orderRepository,
        SaleRepository $saleRepository,
        UserRepository $userRepository,
        DishRepository $dishRepository,
        ManufacturerRepository $manufacturerRepository,
        Line_notify_subscribeRepository $line_notify_subscribeRepository
    )
    {
        $this->lineNotify = new LineNotify("");
        $this->line_notifyRepository = $line_notifyRepository;
        $this->line_notify_tokenRepository = $line_notify_tokenRepository;
        $this->orderRepository = $orderRepository;
        $this->saleRepository = $saleRepository;
        $this->userRepository = $userRepository;
        $this->dishRepository = $dishRepository;
        $this->manufacturerRepository = $manufacturerRepository;
        $this->line_notify_subscribeRepository = $line_notify_subscribeRepository;
    }

    public function getService()
    {
        $notify = $this->line_notifyRepository->all()->only(['id', 'name', 'description']);
        return [$notify, Response::HTTP_OK];
    }

    public function newSubscribe(Request $request, $notify_id)
    {
        $line_notify_server = "https://notify-bot.line.me/oauth/authorize";
        $redirect_uri = "https://api.fios.fssh.khc.edu.tw/api/line/callback";
        $user_id = Auth::user()->id;
        $tokenMd5 = md5(rand());
        $this->line_notify_subscribeRepository->create(['user_id' => $user_id, 'notify_id' => $notify_id, 'token' => $tokenMd5]);
        $tokenBase64 = base64_encode($user_id . '.' . $notify_id . '.' . $tokenMd5);
        $line_notify = $this->line_notifyRepository->findById($notify_id);
        $oAuthURL =
            $line_notify_server . "?response_type=code&scope=notify&response_mode=form_post" .
            "&client_id=" . $line_notify->client_id .
            "&redirect_uri=" . $redirect_uri .
            "&state=" . $tokenBase64;
        return [['redirect' => $oAuthURL], Response::HTTP_OK];
    }

    public function callback(Request $request)
    {

    }

    public function send($line_notify_id)
    {
        // return [true|flase,msg]

        $line_notify = $this->line_notifyRepository->findById($line_notify_id);
        if ($line_notify == null)
            return [false, '`line_notify_id` not found'];

        $this->notifyInfo = $line_notify;

        if (is_callable(array($this, $line_notify->method))) {
            return $this->{$line_notify->method}($line_notify->param);
        } else {
            return [false, 'method not found'];
        }
    }

    private function commit($token, $text, $imagePath = null, $sticker = null)
    {
        $this->lineNotify->setToken($token);
        $this->lineNotify->send($text, $imagePath, $sticker);
    }

    private function ln_1()
    {
        // test token UWjYTfSjp4qcDjNmA24TFgIMsyqYFkxRtQIzQXdw3B4
        //$this->commit("UWjYTfSjp4qcDjNmA24TFgIMsyqYFkxRtQIzQXdw3B4", "test");

        $today = Carbon::today();

        $sales = $this->saleRepository->findBySaleDate($today->toDateString());
        $orders = collect([]);
        foreach ($sales as $sale)
            $orders = $orders->merge($this->orderRepository->findBySaleId($sale->id));
        $tokens = $this->line_notify_tokenRepository->findByNotifyId($this->notifyInfo->id);

        foreach ($tokens as $token) {
            $order = $orders->where('user_id', $token->user_id);
            $user = $this->userRepository->findById($token->user_id);
            foreach ($order as $oo) {
                $sale = $this->saleRepository->findById($oo->sale_id);
                $dish = $this->dishRepository->findById($sale->dish_id);
                $manufacturer = $this->manufacturerRepository->findById($dish->manufacturer_id);
                $str = "Hi " . $user->name . "\n今日" . $today->month . "/" . $today->day . "（" . $this->weekChinese[$today->dayOfWeek] . "）\n你的餐點是:\n" . $manufacturer->name . "-" . $dish->name;
                $this->commit($token->token, $str);
            }
        }

        return [true, 'Success'];
    }

}
