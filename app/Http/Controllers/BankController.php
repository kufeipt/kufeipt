<?php

namespace App\Http\Controllers;

use App\Http\Resources\ExamResource;
use App\Http\Resources\InviteResource;
use App\Http\Resources\TorrentResource;
use App\Http\Resources\UserResource;
use App\Models\Peer;
use App\Models\Snatch;
use App\Models\User;
use App\Repositories\BankRepository;
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
class BankController extends Controller
{
    private $repository;

    public function __construct(BankRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * 操作(开户、存钱、取钱)
     * @return void
     */
    public function operate(Request $request) {
        $request->validate([
            'action' => 'required',
        ]);

        $operate = $this->repository->operate($request->all());
        $msg = $operate['msg'];
        $status = $operate['status'];
        unset($operate['status'], $operate['msg']);
        if ($status == -1) {
            return $this->fail($operate, $msg);
        }

        return $this->success($operate, $msg);
    }

    /**
     * 银行账户信息
     * @param Request $request
     * @return array
     */
    public function info(Request $request) {
        $info = $this->repository->info($request->all());
        if ($info['status'] == -1) {
            return $this->fail(null, $info['msg']);
        }
        $msg = $info['msg'];

        unset($info['status'], $info['msg']);

        return $this->success($info['data'], $msg);
    }

    /**
     * 银行流水
     * @param Request $request
     * @return array
     */
    public function accountStatement(Request $request) {
        $data = $this->repository->accountStatement($request->all());
        if ($data['status'] == -1) {
            return $this->fail(null, $data['msg']);
        }
        $msg = $data['msg'];

        unset($data['status'], $data['msg']);

        return $this->success($data['data'], $msg);
    }

    public function top(Request $request) {
        $data = $this->repository->top($request->all());
        if ($data['status'] == -1) {
            return $this->fail(null, $data['msg']);
        }
        $msg = $data['msg'];

        unset($data['status'], $data['msg']);

        return $this->success($data['data'], $msg);
    }

}
