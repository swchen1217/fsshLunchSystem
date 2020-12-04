<?php

namespace App\Service;

use App\Constant\URLConstant;
use App\Mail\ForgetPswd;
use App\Repositories\ForgetPswdRepository;
use App\Repositories\UserRepository;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use KS\Line\LineNotify;

class ReportService
{

    /**
     * @var LineNotify
     */
    private $lineNotify;
    /**
     * @var Client
     */
    private $guzzleHttpClient;

    public function __construct(Client $client)
    {
        $this->lineNotify = new LineNotify("");
        $this->guzzleHttpClient = $client;
    }

    private function commit($token, $text, $imagePath = null, $sticker = null)
    {
        $this->lineNotify->setToken($token);
        $this->lineNotify->send($text, $imagePath, $sticker);
    }

    public function dish(Request $request)
    {
        $ip = $request->ip();
        $response = $this->guzzleHttpClient->request('POST', 'https://api.imgur.com/3/image', [
            'headers' => [
                'authorization' => 'Bearer ' . env('IMGUR_ACCESS_TOKEN'),
                //'authorization' => 'Client-ID 604fafdd7bce440',
                'content-type' => 'application/x-www-form-urlencoded',
            ],
            'form_params' => [
                'image' => base64_encode(file_get_contents($request->file('image')->path())),
                'title' => 'report_dish_' . time()
            ],
        ]);
        $photo_url = json_decode($response->getBody()->getContents(), true)['data']['link'];

        $data=[
            'timestamp'=>date("Y-m-d-G-i-s",time()),
            'class'=>$request->input('class'),
            'number'=>$request->input('number'),
            'name'=>$request->input('name'),
            'date'=>$request->input('date'),
            'manufacturer'=>$request->input('manufacturer'),
            'dishNum'=>$request->input('dishNum'),
            'image'=>$photo_url
            ];

        Storage::put('report/report-dish-tmp.json',json_encode($data));
        Storage::append('report/report-dish.json',json_encode($data).',');

        return [[],200];

        /*$user = $this->userRepository->findByAccount($request->input('account'));
        if ($user != null) {
            if (Hash::check($request->input('old_pswd'), $user->password)) {
                if (!$user->pw_changed)
                    $this->userRepository->update($user->id, ['password' => bcrypt($request->input('new_pswd')), 'pw_changed' => 1]);
                else
                    $this->userRepository->update($user->id, ['password' => bcrypt($request->input('new_pswd'))]);
                Log::channel('pswd')->info('Success (Account)', ['ip' => $ip, 'user_id' => $user->id]);
                Mail::to($user)->queue(new \App\Mail\PswdChanged());
                return [[], Response::HTTP_NO_CONTENT];
            } else
                return [['error' => 'Old password error'], Response::HTTP_FORBIDDEN];
        } else
            return [['error' => 'The User Not Found'], Response::HTTP_NOT_FOUND];*/
    }
}
