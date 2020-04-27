<?php

namespace App\Service;

use App\Repositories\BalanceRepository;
use App\Repositories\Money_logRepository;
use App\Repositories\UserRepository;
use App\Supports\PermissionSupport;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;

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
            if ($user->id != Auth::user()->id && PermissionSupport::check('balance.read.money', null, true)) {
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
            } else {
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
            }
        } else
            return [['error' => 'The User Not Found'], Response::HTTP_NOT_FOUND];
    }

    public function getLogByAccount(Request $request, $account)
    {
        $user = $this->userRepository->findByAccount($account);
        if ($user != null) {
            if ($user->id != Auth::user()->id && PermissionSupport::check('balance.read.log', null, true)) {
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
            } else {
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
            }
        } else
            return [['error' => 'The User Not Found'], Response::HTTP_NOT_FOUND];
    }

    public function getTotal($date1, $date2)
    {
        $data = array();
        $topUp_total = 0;
        $deduct_total = 0;
        $log = $this->money_logRepository->findByCreateDateInterval($date1, $date2);
        foreach ($log as $item) {
            $date = substr($item['create_at'], 0, 10);
            if (!isset($data[$date])) {
                $data[$date]['topUp'] = 0;
                $data[$date]['deduct'] = 0;
                $data[$date]['total'] = 0;
            }

            if ($item['event'] == 'top-up') {
                $topUp_total += $item['money'];
                $data[$date]['topUp'] += $item['money'];
                $data[$date]['total'] += $item['money'];
            }
            if ($item['event'] == 'deduct') {
                $deduct_total += $item['money'];
                $data[$date]['deduct'] += $item['money'];
                $data[$date]['total'] -= $item['money'];
            }
        }
        ksort($data);
        return [['data' => $data, 'topUp' => $topUp_total, 'deduct' => $deduct_total, 'total' => $topUp_total - $deduct_total], Response::HTTP_OK];
    }

    public function getToday()
    {
        $topUp = 0;
        $deduct = 0;
        $balance = $this->money_logRepository->findByCreateAt(Carbon::today()->toDateString());
        foreach ($balance as $item) {
            if ($item->event == 'top-up')
                $topUp += $item->money;
            if ($item->event == 'deduct')
                $deduct += $item->money;
        }
        return [['top-up' => $topUp, 'deduct' => $deduct, 'Total revenue' => $topUp - $deduct], Response::HTTP_OK];
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
            Log::channel('money')->info('Top up Success', ['ip' => $ip, 'trigger_id' => Auth::user()->id, 'user_id' => $user->id, 'Balance before top up' => $balance, 'Total top up' => $money, 'Balance after top up' => $mm]);
            return [['before' => $balance, 'total' => $money, 'after' => $mm], Response::HTTP_OK];
        } else
            return [['error' => 'The User Not Found'], Response::HTTP_NOT_FOUND];
    }

    public function deduct(Request $request)
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
            $mm = $balance - $money;
            if ($mm < 0)
                return [['error' => 'Balance after deduct must unsigned'], Response::HTTP_BAD_REQUEST];
            $this->balanceRepository->updateByUserId($user->id, ['money' => $mm]);
            $this->money_logRepository->caeate(['user_id' => $user->id, 'event' => 'deduct', 'money' => $money, 'trigger_id' => Auth::user()->id, 'note' => $balance . '-' . $money . '=' . $mm]);
            Log::channel('money')->info('Deduct Success', ['ip' => $ip, 'trigger_id' => Auth::user()->id, 'user_id' => $user->id, 'Balance before deduct' => $balance, 'Total deduct' => $money, 'Balance after deduct' => $mm]);
            return [['before' => $balance, 'total' => $money, 'after' => $mm], Response::HTTP_OK];
        } else
            return [['error' => 'The User Not Found'], Response::HTTP_NOT_FOUND];
    }
}
