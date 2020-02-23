<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Log;

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
