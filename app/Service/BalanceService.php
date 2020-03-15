<?php

namespace App\Service;

use App\Repositories\BalanceRepository;
use App\Repositories\Money_logRepository;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class BalanceService
{
    /**
     * @var BalanceRepository
     */
    private $balanceRepository;
    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var Money_logRepository
     */
    private $money_logRepository;

    public function __construct(BalanceRepository $balanceRepository, UserRepository $userRepository, Money_logRepository $money_logRepository)
    {
        $this->balanceRepository = $balanceRepository;
        $this->userRepository = $userRepository;
        $this->money_logRepository = $money_logRepository;
    }

    public function getByAccount(Request $request, $account, $detail = false)
    {
        $user = $this->userRepository->findByAccount($account);
        if ($user != null) {
            $balance = $this->balanceRepository->findByUserId($user->id);
            if ($balance != null)
                $money = $balance->money;
            else {
                $this->balanceRepository->caeate(['user_id' => $user->id, 'money' => 0]);
                $money = 0;
            }
            if ($detail)
                return [['user_id' => $user->id, 'name' => $user->name, 'balance' => $money], Response::HTTP_OK];
            return [['balance' => $money], Response::HTTP_OK];
        } else
            return [['error' => 'The User Not Found'], Response::HTTP_NOT_FOUND];
    }

    public function getLogByAccount(Request $request, $account)
    {
        $user = $this->userRepository->findByAccount($account);
        if ($user != null) {
            $balance = $this->balanceRepository->findByUserId($user->id);
            if ($balance != null)
                $money = $balance->money;
            else {
                $this->balanceRepository->caeate(['user_id' => $user->id, 'money' => 0]);
                $money = 0;
            }
            $log = $this->money_logRepository->findByUserIdAndOrderByCreated_atDesc($user->id, 20);
            $tid = 0;
            $tname = "";
            foreach ($log as $key => $value) {
                if ($tid != $value['trigger_id']) {
                    $tid = $value['trigger_id'];
                    $tt = $this->userRepository->findById($tid);
                    $tname = $tt->name;
                }
                $log[$key] = array_merge($value->toArray(), ['user_name' => $user->name, 'trigger_name' => $tname]);
            }
            return [['user_id' => $user->id, 'name' => $user->name, 'balance' => $money, 'log' => $log->toArray()], Response::HTTP_OK];
        } else
            return [['error' => 'The User Not Found'], Response::HTTP_NOT_FOUND];
    }

    public function topUp(Request $request)
    {
        $ip = $request->ip();
        $user = $this->userRepository->findById($request->input('user_id'));
        if ($user != null) {
            $money = $request->input('money');
            if ($money < 0)
                return [['error' => '`money` must unsigned'], Response::HTTP_BAD_REQUEST];
            $balanceObj = $this->balanceRepository->findByUserId($user->id);
            if ($balanceObj != null)
                $balance = $balanceObj->money;
            else {
                $this->balanceRepository->caeate(['user_id' => $user->id, 'money' => 0]);
                $balance = 0;
            }
            $mm = $balance + $money;
            $this->balanceRepository->updateByUserId($user->id, ['money' => $mm]);
            $this->money_logRepository->caeate(['user_id' => $user->id, 'event' => 'top-up', 'money' => $money, 'trigger_id' => Auth::user()->id, 'note' => $balance . '+' . $money . '=' . $mm]);
            Log::channel('balance')->info('Top up Success', ['ip' => $ip, 'trigger_id' => Auth::user()->id, 'user_id' => $user->id, 'Balance before top up' => $balance, 'Total top up' => $money, 'Balance after top up' => $mm]);
            return [['Balance before top up' => $balance, 'Total top up' => $money, 'Balance after top up' => $mm], Response::HTTP_OK];
        } else
            return [['error' => 'The User Not Found'], Response::HTTP_NOT_FOUND];
    }

    public function deduct(Request $request)
    {

    }
}
