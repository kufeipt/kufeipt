<?php

namespace App\Repositories;

use App\Exceptions\InsufficientPermissionException;
use App\Exceptions\NexusException;
use App\Http\Resources\ExamUserResource;
use App\Http\Resources\UserResource;
use App\Models\ExamUser;
use App\Models\FreePoolFeedRecord;
use App\Models\FreePoolInfo;
use App\Models\Invite;
use App\Models\LoginLog;
use App\Models\Message;
use App\Models\Setting;
use App\Models\User;
use App\Models\UserBankAccount;
use App\Models\UserBankAccountLog;
use App\Models\UserBanLog;
use App\Models\UserMeta;
use App\Models\UsernameChangeLog;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Nexus\Database\NexusDB;

class BankRepository extends BaseRepository
{
    public function operate(array $params)
    {
        $result = ['status' => 1, 'msg' => '成功', 'err_code' => 0, 'self_bonus' => 0.0];
        $user = $this->getUser(Auth::id());
        $account = UserBankAccount::query()->where('uid', $user['id'])->first();

        switch ($params['action']) {
            case 'kaihu':
                //判断是否已开户
                if (!$account) {
                    $accountInsert = [
                        'uid' => $user['id'],
                        'bouns' => 0.0,
                        'rate' => 0.0167,
                        'interest' => 0.0,
                        'uninterest' => 0.0
                    ];
                    UserBankAccount::store($accountInsert);
                }
                $result['self_bonus'] = $user['seedbonus'];
                break;
            case 'cunqian':
                if (!$account) {
                    $result = ['status' => -1,'msg' => '请先开户'];
                } else {
                    $result = $this->bonusJudge($params['bonus'], $params['action'], $user, $account);
                    if ($result['status'] == 1) {
                        DB::transaction(function () use ($user, $params) {
                            User::query()->where('id', $user['id'])->update([
                                'seedbonus' => DB::raw("seedbonus - " . $params['bonus']),
                            ]);
                            UserBankAccount::query()->where('uid', $user['id'])->update([
                                'bouns' => DB::raw("bouns + " . $params['bonus']),
                            ]);
                            $data = [];
                            $data['uid'] = $user['id'];
                            $data['bouns'] = $params['bonus'];
                            $data['type'] = 1;
                            UserBankAccountLog::store($data);
                        });
                        $result['self_bonus'] = $user['seedbonus'] - $params['bonus'];
                    }
                }
                break;
            case 'quqian';
                if (!$account) {
                    $result = ['status' => -1,'msg' => '请先开户'];
                } else {
                    $result = $this->bonusJudge($params['bonus'], $params['action'], $user, $account);
                    if ($result['status'] == 1) {
                        DB::transaction(function () use ($user, $params) {
                            User::query()->where('id', $user['id'])->update([
                                'seedbonus' => DB::raw("seedbonus + " . $params['bonus']),
                            ]);
                            UserBankAccount::query()->where('uid', $user['id'])->update([
                                'bouns' => DB::raw("bouns - " . $params['bonus']),
                            ]);
                            $data = [];
                            $data['uid'] = $user['id'];
                            $data['bouns'] = $params['bonus'];
                            $data['type'] = 2;
                            UserBankAccountLog::store($data);
                        });
                        $result['self_bonus'] = $user['seedbonus'] + $params['bonus'];
                    }
                }
                break;
            default:
                break;
        }

        return $result;
    }

    public function info(array $params) {
        $result = ['status' => 1, 'msg' => '成功', 'data' => null];
        $user = $this->getUser(Auth::id());
        $userBank = UserBankAccount::query()->where('uid', $user['id'])->first();

        $data = [
            'uid' => $user['id'],
            'username' => get_username($user['id']),
            'bank_account_id' => $userBank['id'],
            'bonus' => $userBank['bouns'],
            'uninterest' => $userBank['uninterest'],
            'interest' => $userBank['interest'],
            'created_at' => $userBank['created_at'],
            'updated_at' => $userBank['updated_at'],
            'remark' => '月息5%每月1日结息'
        ];
        $result['data'] = $data;

        return $result;
    }

    public function accountStatement(array $params) {
        $result = ['status' => 1, 'msg' => '成功', 'data' => null];
        $user = $this->getUser(Auth::id());
        if (!isset($params['type']) || !$params['type']) {
            $list = UserBankAccountLog::query()->where('uid', $user['id'])->orderBy('created_at')->get();
        } else {
            $list = UserBankAccountLog::query()->where('uid', $user['id'])->where('type', $params['type'])
                ->orderBy('created_at')->get();
        }

        foreach ($list as &$value) {
            $value['username'] = get_username($value['uid']);
        }
        $result['data'] = $list;

        return $result;
    }

    public function top(array $params) {
        $result = ['status' => 1, 'msg' => '成功', 'data' => null];
        $user = $this->getUser(Auth::id());
        $list = UserBankAccount::query()->orderBy('bouns')->limit(10)->get();
        foreach ($list as &$value) {
            $value['username'] = get_username($value['uid']);
        }
        $result['data'] = $list;

        return $result;
    }

    private function bonusJudge($bonus, $action, $user, $account) {
        $result = [
            'status' => 1,
            'msg' => '',
            'err_code' => 0
        ];

        if (!isset($bonus)) {
            $result = [
                'status' => -1,
                'err_code' => 1,
                'msg' => '魔力输入不能为空'
            ];
        } else if (!is_numeric($bonus)) {
            $result = [
                'status' => -1,
                'err_code' => 2,
                'msg' => '魔力输入只能为数字'
            ];
        } else if ($bonus < 0.1) {
            $result = [
                'status' => -1,
                'err_code' => 3,
                'msg' => '魔力输入必须不小于0.1'
            ];
        } else {
            if ($action == 'cunqian') {
                $is_enough = $this->judgeUserBonus($user, $account, "-", $bonus);
                if (!$is_enough) {
                    $result = [
                        'status' => -1,
                        'err_code' => 4,
                        'msg' => '你的站点魔力不足'
                    ];
                }
            } else if ($action == 'quqian') {
                $is_enough = $this->judgeUserBonus($user, $account, "+", $bonus);
                if (!$is_enough) {
                    $result = [
                        'status' => -1,
                        'err_code' => 5,
                        'msg' => '你的存款余额不足'
                    ];
                }
            }
        }

        return $result;
    }

    private function judgeUserBonus($user, $account, $type = "+", $point = "1.0") {
        if ($type == '-' && $user['seedbonus'] < $point) {
            return false;
        } else if ($type == '+' && $account['bouns'] < $point) {
            return false;
        }

        return true;
    }
}
