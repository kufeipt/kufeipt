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

class FreePoolFeedRecordRepository extends BaseRepository
{
    public function allTopList()
    {
        $user = $this->getUser(Auth::id());

        $freePoolFeedRecordList = FreePoolFeedRecord::query()->groupBy('uid')
            ->orderByDesc(DB::raw('sum(bonus)'))
            ->limit(100)
            ->get(['uid', DB::raw('sum(bonus) as bonus'), DB::raw('count(uid) as static_num')]);
        $topStr = "";
        $i = 1;
        foreach ($freePoolFeedRecordList as $value) {
            $topStr .= '<tr>';
            $topStr .= '<td class="colfollow">' . $i . '</td>';
            $topStr .= '<td class="colfollow">';
            $topStr .= '<span class="nowrap" style="display:inline-flex;align-items:center;">';
            $topStr .= '<a target="_blank" href="./userdetails.php?id=' . $value['uid'] . '" class="SysOp_Name">';
            $topStr .= get_username($value['uid']);
            $topStr .= '</a>';
            $topStr .= '</span>';
            $topStr .= '</td>';
            $topStr .= '<td class="colfollow">' . $value['static_num'] . '</td>';
            $topStr .= '<td class="colfollow">' . $value['bonus'] . '</td>';
            $topStr .= '</tr>';

            $i++;
        }

        return $topStr;
    }

    public function latestList()
    {
        $user = $this->getUser(Auth::id());

        $freePoolFeedRecordList = FreePoolFeedRecord::query()
            ->orderByDesc('created_at')
            ->limit(100)
            ->get(['uid', 'bonus', 'created_at']);
        $latestStr = "";
        $i = 1;
        foreach ($freePoolFeedRecordList as $value) {
            $latestStr .= '<tr>';
            $latestStr .= '<td class="colfollow">' . $i . '</td>';
            $latestStr .= '<td class="colfollow" style="text-align: center">';
            $latestStr .= '<span class="nowrap" style="display:inline-flex;align-items:center;">';
            $latestStr .= '<a target="_blank" href="./userdetails.php?id=' . $value['uid'] . '" class="User_Name">';
            $latestStr .= '<b>';
            $latestStr .= get_username($value['uid']);
            $latestStr .= '</b>';
            $latestStr .= '</a>';
            $latestStr .= '</span>';
            $latestStr .= '</td>';
            $latestStr .= '<td class="colfollow">' . $value['bonus'] . '</td>';
            $latestStr .= '<td class="colfollow">' . $value['created_at'] . '</td>';
            $latestStr .= '</tr>';

            $i++;
        }

        return $latestStr;
    }

    public function periodsTop()
    {
        $user = $this->getUser(Auth::id());
        $freePoolInfo = FreePoolInfo::query()->where('is_current', 1)->first();

        $freePoolFeedRecordList = FreePoolFeedRecord::query()->where('periods', $freePoolInfo['periods'])
            ->groupBy('uid')
            ->orderByDesc(DB::raw('sum(bonus)'))
            ->limit(100)
            ->get(['uid', DB::raw('sum(bonus) as bonus'), DB::raw('count(uid) as static_num')]);
        $periodsTopStr = "";
        $i = 1;
        foreach ($freePoolFeedRecordList as $value) {
            $periodsTopStr .= '<tr>';
            $periodsTopStr .= '<td class="colfollow">' . $i . '</td>';
            $periodsTopStr .= '<td class="colfollow">';
            $periodsTopStr .= '<span class="nowrap" style="display:inline-flex;align-items:center;">';
            $periodsTopStr .= '<a target="_blank" href="./userdetails.php?id=' . $value['uid'] . '" class="SysOp_Name">';
            $periodsTopStr .= get_username($value['uid']);
            $periodsTopStr .= '</a>';
            $periodsTopStr .= '</span>';
            $periodsTopStr .= '</td>';
            $periodsTopStr .= '<td class="colfollow">' . $value['static_num'] . '</td>';
            $periodsTopStr .= '<td class="colfollow">' . $value['bonus'] . '</td>';
            $periodsTopStr .= '</tr>';

            $i++;
        }

        return $periodsTopStr;
    }
}
