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
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use KS\Line\LineNotify;

class LineNotifyService
{

    private $line_notify_server = "https://notify-bot.line.me/oauth";
    private $redirect_uri = "https://api.fios.fssh.khc.edu.tw/api/line/callback";
    private $weekChinese = ['日', '一', '二', '三', '四', '五', '六'];

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
    /**
     * @var Client
     */
    private $guzzleHttpClient;

    public function __construct(
        Line_notifyRepository $line_notifyRepository,
        Line_notify_tokenRepository $line_notify_tokenRepository,
        OrderRepository $orderRepository,
        SaleRepository $saleRepository,
        UserRepository $userRepository,
        DishRepository $dishRepository,
        ManufacturerRepository $manufacturerRepository,
        Line_notify_subscribeRepository $line_notify_subscribeRepository,
        Client $client
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
        $this->guzzleHttpClient = $client;
    }

    public function getService()
    {
        $notify = $this->line_notifyRepository->all()->only(['id', 'name', 'description']);
        return [$notify, Response::HTTP_OK];
    }

    public function newSubscribe(Request $request, $notify_id)
    {
        if (Auth::user() == null)
            return [['message' => 'Unauthenticated'], Response::HTTP_UNAUTHORIZED];
        $user_id = Auth::user()->id;
        $tokenMd5 = md5(rand());
        $this->line_notify_subscribeRepository->create(['user_id' => $user_id, 'notify_id' => $notify_id, 'token' => $tokenMd5]);
        $tokenBase64 = base64_encode($user_id . '.' . $notify_id . '.' . $tokenMd5);
        $line_notify = $this->line_notifyRepository->findById($notify_id);
        $oAuthURL =
            $this->line_notify_server . "/authorize?response_type=code&scope=notify&response_mode=form_post" .
            "&client_id=" . $line_notify->client_id .
            "&redirect_uri=" . $this->redirect_uri .
            "&state=" . $tokenBase64;
        return [['redirect' => $oAuthURL], Response::HTTP_OK];
    }

    public function callback(Request $request)
    {
        $code = $request->input('code');
        $state = $request->input('state');
        $state_data = explode('.', base64_decode($state));
        $subscribe = $this->line_notify_subscribeRepository->findByUserIdAndLineNotifyIdAndToken($state_data[0], $state_data[1], $state_data[2]);
        if ($subscribe != null) {
            $ct = strtotime($subscribe->created_at);
            $now = time();
            if ($now - $ct <= 1800) {
                $this->line_notify_subscribeRepository->deleteByUserIdAndLineNotifyId($state_data[0], $state_data[1]);
                $line_notify = $this->line_notifyRepository->findById($state_data[1]);
                $response = $this->guzzleHttpClient->request('POST', $this->line_notify_server . '/token', [
                    'headers' => [
                        'content-type' => 'application/x-www-form-urlencoded',
                    ],
                    'form_params' => [
                        'grant_type' => 'authorization_code',
                        'code' => $code,
                        'redirect_uri' => $this->redirect_uri,
                        'client_id' => $line_notify->client_id,
                        'client_secret' => $line_notify->client_secret,
                    ],
                ]);
                if ($response->getStatusCode() == 200) {
                    $access_token = json_decode($response->getBody()->getContents(), true)['access_token'];
                    $this->line_notify_tokenRepository->create(['notify_id' => $state_data[1], 'user_id' => $state_data[0], 'token' => $access_token]);
                    return [['success' => 'Success. You Can Close This Windows'], Response::HTTP_OK];
                } else
                    return [['error' => 'Line Notify Token Issue Error'], Response::HTTP_INTERNAL_SERVER_ERROR];
            } else
                return [['error' => 'The Token expired,Please Re-apply'], Response::HTTP_BAD_REQUEST];
        } else
            return [['error' => 'The Token Error ,Please Re-apply'], Response::HTTP_BAD_REQUEST];
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
                $str = "\nHi " . $user->name . "\n今日" . $today->month . "/" . $today->day . "（" . $this->weekChinese[$today->dayOfWeek] . "）\n你的餐點是:\n" . $manufacturer->name . "-" . $dish->name;
                $this->commit($token->token, $str);
            }
        }

        return [true, 'Success'];
    }

    private function ln_report_dish()
    {
        $data = json_decode(Storage::get('report/report-dish-tmp.json'), true);
        if ($data == null)
            return [false, 'Data Empty'];
        $tokens = $this->line_notify_tokenRepository->findByNotifyId($this->notifyInfo->id);
        foreach ($tokens as $token) {
            $str = "\n新通報！！\n通報者：{$data['class']}班{$data['number']}號{$data['name']}\n於{$data['date']} {$data['manufacturer']}-{$data['dishNum']}\n發現異物，情況如附圖\n通報時間：{$data['timestamp']}";
            $this->commit($token->token, $str, $data['image']);
        }
        Storage::put('report/report-dish-tmp.json', '');
        return [true, 'Success'];
    }

}
