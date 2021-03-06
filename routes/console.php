<?php

use App\Entity\User;
use App\Mail\Verify;
use App\Repositories\BalanceRepository;
use App\Repositories\Money_logRepository;
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
    $user = App\Entity\User::all();
    foreach ($user as $uu) {
        if ($uu->id == 1 || $uu->id == 2)
            continue;
        $npw = bcrypt($uu->password);
        App\Entity\User::where('id', $uu->id)->update(['password' => $npw]);
        $this->line($uu->account . ' : ' . $npw);
    }
    $this->info('ok');
});

Artisan::command('my:stu', function () {
    $user = App\Entity\User::all();
    foreach ($user as $uu) {
        if ($uu->id == 1 || $uu->id == 2)
            continue;
        $uu->syncRoles('Student');
        $this->line($uu->account . ' : ' . 'Student');
    }
    $this->info('ok');
});

Artisan::command('my:mailTest01 {user_id}', function (BalanceRepository $balanceRepository, Money_logRepository $money_logRepository) {
    $money = 1000;
    $uu = App\Entity\User::find($this->argument('user_id'));
    $uu->syncRoles('Student');
    $balanceObj = $balanceRepository->findByUserId($uu->id);
    if ($balanceObj != null)
        $balance = $balanceObj->money;
    else {
        $balanceRepository->create(['user_id' => $uu->id, 'money' => 0]);
        $balance = 0;
    }
    $mm = $balance + $money;
    $balanceRepository->updateByUserId($uu->id, ['money' => $mm]);
    $money_logRepository->create(['user_id' => $uu->id, 'event' => 'top-up', 'money' => $money, 'trigger_id' => 1, 'note' => $balance . '+' . $money . '=' . $mm . '(TEST01)']);
    Log::channel('money')->info('Top up Success', ['ip' => '127.0.0.1', 'trigger_id' => 1, 'user_id' => $uu->id, 'Balance before top up' => $balance, 'Total top up' => $money, 'Balance after top up' => $mm]);
    Mail::to($uu)->queue(new \App\Mail\TestInvite01());
    $this->line($uu->account . ' OK');
    $this->info('DONE');
});

Artisan::command('user:new', function () {
    $this->line('Create User ...');
    $account = $this->ask('Input Account: ');
    $password = $this->secret('Input Password: ');
    $password_c = $this->secret('Input Password: ');
    while ($password != $password_c) {
        $this->error('Password Not Match');
        $password = $this->secret('Input Password: ');
        $password_c = $this->secret('Confirm Password: ');
    }
    $class = $this->ask('Input Class: ');
    $number = $this->ask('Input Number: ');
    $name = $this->ask('Input Name: ');
    $email = $this->ask('Input Email: ');
    $data = ['account' => $account, 'password' => '********', 'class' => $class, 'number' => $number, 'name' => $name, 'email' => $email];
    $headers = ['account', 'password', 'class', 'number', 'name', 'email'];
    $this->table($headers, [$data]);
    if ($this->confirm('Is the user information correct? [y|N]')) {
        $data['password'] = bcrypt($password);
        $uu = User::create($data);
        $this->info('User set Success');
        $this->info('User ID: ' . $uu->id);
        $this->info('Account: ' . $account);
        if ($this->confirm('Do you want to set the user role? [y|N]')) {
            $role = $this->ask('Input Role Name (Only one): ');
            $uu->syncRoles($role);
            $this->info('Role set Success');
        }
        $this->info('DONE');
    }
    $this->error('Bye');
    return false;
});

Artisan::command('my:mailTest02', function () {
    $user = App\Entity\User::all();
    foreach ($user as $uu) {
        $uid = $uu->id;
        $skipId = ['3', '4', '6', '8', '11', '19', '23', '24', '25', '28', '31', '39', '40', '41', '43', '56', '57', '60', '70', '71', '73', '77', '78', '88', '89', '92', '94'];
        foreach ($skipId as $sid) {
            if ($uid == $sid)
                continue;
        }
        Mail::to($uu)->queue(new \App\Mail\TestInvite01());
        $this->line($uu->account . ' OK');
    }
    $this->info('DONE');
});

Artisan::command('user:importInit', function (BalanceRepository $balanceRepository, Money_logRepository $money_logRepository) {
    $money = 1000;
    $this->info('ok');
    $user = App\Entity\User::all();
    foreach ($user as $uu) {
        if ($uu->id == 1 || $uu->id == 2 || $uu->id == 3)
            continue;
        $npw = bcrypt($uu->password);
        App\Entity\User::where('id', $uu->id)->update(['password' => $npw]);
        $uu->syncRoles('Student');
        $balanceObj = $balanceRepository->findByUserId($uu->id);
        if ($balanceObj != null)
            $balance = $balanceObj->money;
        else {
            $balanceRepository->create(['user_id' => $uu->id, 'money' => 0]);
            $balance = 0;
        }
        $mm = $balance + $money;
        $balanceRepository->updateByUserId($uu->id, ['money' => $mm]);
        $money_logRepository->create(['user_id' => $uu->id, 'event' => 'top-up', 'money' => $money, 'trigger_id' => 1, 'note' => $balance . '+' . $money . '=' . $mm . '(TEST02)']);
        Log::channel('money')->info('Top up Success', ['ip' => '127.0.0.1', 'trigger_id' => 1, 'user_id' => $uu->id, 'Balance before top up' => $balance, 'Total top up' => $money, 'Balance after top up' => $mm]);
        $this->line('OK ' . $uu->account . ' : ' . $npw . ' : Student');
    }
    $this->info('DONE');
});

