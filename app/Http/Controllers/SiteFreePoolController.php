<?php

namespace App\Http\Controllers;

use App\Http\Resources\ExamResource;
use App\Http\Resources\InviteResource;
use App\Http\Resources\TorrentResource;
use App\Http\Resources\UserResource;
use App\Models\Peer;
use App\Models\Snatch;
use App\Models\User;
use App\Repositories\ExamRepository;
use App\Repositories\FreePoolFeedRecordRepository;
use App\Repositories\SiteFreePoolRepository;
use App\Repositories\TorrentRepository;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

/**
 * 站免费池接口
 */
class SiteFreePoolController extends Controller
{
    private $repository;

    public function __construct(SiteFreePoolRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * 投喂
     * @return void
     */
    public function feed(Request $request) {
        $feed = $this->repository->feed($request->all());
        if ($feed['status'] == -1) {
            return $this->fail(null, $feed['msg']);
        }
        $msg = $feed['msg'];

        unset($feed['status'], $feed['msg']);
        return $this->success($feed, $msg);
    }

    /**
     * top榜
     */
    public function allTopList() {
        $freePoolFeedRecordRep = new FreePoolFeedRecordRepository();
        return $this->success($freePoolFeedRecordRep->allTopList());
    }

    /**
     * 最新投喂
     */
    public function latestList() {
        $freePoolFeedRecordRep = new FreePoolFeedRecordRepository();
        return $this->success($freePoolFeedRecordRep->latestList());
    }

    /**
     * 本期top榜
     */
    public function periodsTopList() {
        $freePoolFeedRecordRep = new FreePoolFeedRecordRepository();
        return $this->success($freePoolFeedRecordRep->periodsTop());
    }

    public function info(Request $request) {
        $info = $this->repository->info($request->all());
        if ($info['status'] == -1) {
            return $this->fail(null, $info['msg']);
        }
        $msg = $info['msg'];

        unset($info['status'], $info['msg']);
        return $this->success($info['data'], $msg);
    }

}
