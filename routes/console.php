<?php

use App\Mail\Verify;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->describe('Display an inspiring quote');

Artisan::command('log:clear', function () {
    $path=storage_path('logs\laravel.log');
    if(is_file($path)){
        unlink($path);
        fclose(fopen($path, "w"));
    }
    $this->info('ok');
});

Artisan::command('my:test', function () {
    /*Mail::queue('emails.user_verify', ['verify_code'=>'12345678'], function ($message) {
        $message->to('swchen1217@gmail.com', 'SWC')->subject('帳號活動異常-使用者驗證');
    });*/
    $user=App\Entity\User::find(1);
    Mail::to($user)->queue(new Verify(['verify_code'=>'12345678']));
    $this->info('ok');
});
