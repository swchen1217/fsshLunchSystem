<?php

namespace App\Service;

use App\Repositories\BalanceRepository;
use App\Repositories\Money_logRepository;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

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
            return [['user_id' => $user->id, 'name' => $user->name, 'balance' => $money, 'log' => $log->toArray()], Response::HTTP_OK];
        } else
            return [['error' => 'The User Not Found'], Response::HTTP_NOT_FOUND];
    }

    public function topUp(Request $request)
    {
        /*$user = $this->userRepository->findById($request->input('user'));
        if ($user != null) {
            $balance = $this->balanceRepository->findByUserId($user->id);
            if ($balance != null)
                $money = $balance->money;
            else {
                $this->balanceRepository->caeate(['user_id' => $user->id, 'money' => 0]);
                $money = 0;
            }
            $log = $this->money_logRepository->findByUserIdAndOrderByCreated_atDesc($user->id, 20);
            return [['user_id' => $user->id, 'name' => $user->name, 'balance' => $money, 'log' => $log->toArray()], Response::HTTP_OK];
        } else
            return [['error' => 'The User Not Found'], Response::HTTP_NOT_FOUND];*/
    }

    public function deduct(Request $request)
    {

    }
}
