<?php

use App\Mail\Verify;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

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
    $path = storage_path('logs\laravel.log');
    if (is_file($path)) {
        unlink($path);
        fclose(fopen($path, "w"));
    }
    $this->info('ok');
});

Artisan::command('my:test', function () {
    throw new Exception('testB');
    $this->info('ok');
});

Artisan::command('my:getImage', function () {
    $url = Storage::disk('public')->url('image/dish/default.png');
    $this->line($url);
    $this->info('ok');
});

Artisan::command('my:test2', function () {
    $id = 3;
    Cache::tags('rating')->flush();
    $result = Cache::tags('rating')->remember($id, Carbon::now()->addHours(3), function () use ($id) {
        $rating = \App\Entity\Rating::where('dish_id', $id)->get();
        if ($rating == null || count($rating) < 10)
            return -1;
        return round($rating->avg('rating'), 1);
    });
    $this->line($result);
    $this->info('ok');
});

Artisan::command('my:pswd', function () {
    $user=App\Entity\User::all();
    foreach ($user as $uu){
        if($uu->id==1 || $uu->id==2)
            continue;
        $npw=bcrypt($uu->password);
        App\Entity\User::where('id',$uu->id)->update(['password'=>$npw]);
        $this->line($uu->account.' : '.$npw);
    }
    $this->info('ok');
});
