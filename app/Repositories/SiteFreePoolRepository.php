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

class SiteFreePoolRepository extends BaseRepository
{

    public static $bonusRule = [
        'forehead' => [50000, 100000],
        'ear' => [10000, 50000],
        'nose' => [1000, 5000],
        'tail' => [5000, 10000],
        'body' => [500, 1000],
    ];

    public function feed(array $params)
    {
        $result = [];
        $user = $this->getUser(Auth::id());
        $userBonus = $user['seedbonus'];
        //当前期站免池信息
        $freePoolInfo = FreePoolInfo::query()->where('is_current', 1)->first();

        //如果当期魔力池已经喂饱了，就不再进行喂了
        if ($freePoolInfo['current_bonus'] >= $freePoolInfo['need_bonus']) {
            $result['status'] = -1;
            $result['msg'] = '站免池已喂饱，请等待下一期喂养~';
            return $result;
        }

        if (!isset($params['position']) || empty($params['position'])) {
            $params['position'] = 'body';
        }
        $bonusRuleRange = self::$bonusRule[$params['position']];
        //生成随机数
        //判断下最小值与用户的魔力大小
        if ($user['seedbonus'] >= $bonusRuleRange[1]) {
            $rand = rand($bonusRuleRange[0], $bonusRuleRange[1]);
        } else if ($user['seedbonus'] >= $bonusRuleRange[0] && $user['seedbonus'] <= $bonusRuleRange[1]) {
            $rand = rand($bonusRuleRange[0], $user['seedbonus']);
        } else {
            $result['status'] = -1;
            $result['msg'] = '您的魔力值不足，快去积攒魔力吧~';
            return $result;
        }
        //1.用户进行扣减魔力
        //2.魔力池增加魔力
        //3.增加投喂记录
        //4.如果魔力池满了，开启魔力池
        DB::beginTransaction();//开启事务
        try {
            $user->update(['seedbonus' => DB::raw("seedbonus - " . $rand)]);

            FreePoolInfo::feed($rand);

            $freePoolFeedRecordData = [
              'uid' => $user['id'],
              'bonus' => $rand,
              'periods' => $freePoolInfo['periods']
            ];
            FreePoolFeedRecord::store($freePoolFeedRecordData);

            //判断魔力池是否满了，满了就开启站免
            if ($freePoolInfo['current_bonus'] + $rand >= $freePoolInfo['need_bonus']) {
                $startTime = date("y-m-d H:i:s");
                $endTime = date("Y-m-d H:i:s", strtotime("+" . $freePoolInfo['sustain_time'] . " day" ));
                \Nexus\Database\NexusDB::table('torrents_state')->where('id', 1)->update([
                    'begin' => $startTime,
                    'deadline' => $endTime,
                    'global_sp_state' => 2,
                ]);
                NexusDB :: cache_del(Setting::TORRENT_GLOBAL_STATE_CACHE_KEY);
                //站免池下期开启开关，当前站免池喂满且该key过期，自动开启下一期
                Cache::set(Setting::SITE_FREE_POOL_NEXT_PERIODS_TIME, 1, $freePoolInfo['rest_time'] * 86400);

                //设置下一期信息
                $freePoolInfoInsert = [
                    'periods' => $freePoolInfo['periods'] + 1,
                    'need_bonus' => 2000000,
                    'current_bonus' => 0,
                    'sustain_time' => 1,
                    'rest_time' => 1,
                    'is_current' => 0,
                ];
                FreePoolInfo::store($freePoolInfoInsert);

                //当禁止喂养key过期时，自动开启下一期站免池，这个动作用定时脚本完成
            }

            DB::commit();

            $result['self_bonus'] = $userBonus - $rand;
            $result['feed_bonus'] = $rand;
            $result['feed_schedule'] = floor(($freePoolInfo['current_bonus'] + $rand) / $freePoolInfo['need_bonus'] * 10000) / 100;
            $result['status'] = 1;
            $result['msg'] = "感谢您的投喂，我肚子又增加了". $rand. "魔力~";
            return $result;
        } catch (\Exception $e) {
            DB::rollback();
            do_log("feed error: " . $e, 'error');
            $result['status'] = -1;
            $result['msg'] = '未知错误，请联系管理员~';
            return $result;
        }

    }

    public function info(array $params) {
        $info = ['status' => 1, 'msg' => '成功', 'data' => null];
        $data = FreePoolInfo::query()->where('is_current', 1)->first();
        $redis=new \Redis();
        $redis->connect('127.0.0.1', 6379, 30);
        $ttl = $redis->TTL(Setting::SITE_FREE_POOL_NEXT_PERIODS_TIME);
        $data['next_start_count_down'] = $ttl;
        $info['data'] = $data;

        return $info;
    }
}
