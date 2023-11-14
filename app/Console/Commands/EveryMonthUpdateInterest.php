<?php

namespace App\Console\Commands;

use App\Models\UserBankAccount;
use App\Models\UserBankAccountLog;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use NexusPlugin\Menu\Filament\MenuItemResource\Pages\ManageMenuItems;
use NexusPlugin\Menu\MenuRepository;
use NexusPlugin\Menu\Models\MenuItem;
use NexusPlugin\Permission\Models\Permission;
use NexusPlugin\Permission\Models\Role;
use NexusPlugin\PostLike\PostLikeRepository;
use NexusPlugin\StickyPromotion\Models\StickyPromotion;
use NexusPlugin\StickyPromotion\Models\StickyPromotionParticipator;
use NexusPlugin\Work\Models\RoleWork;
use NexusPlugin\Work\WorkRepository;
use Symfony\Component\Console\Command\Command as CommandAlias;

class EveryMonthUpdateInterest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'interest:month';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '每月结算上月利息至本金';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        do_log("开始更新用户上月利息结算到本金","critical");

        DB::transaction(function () {
            //增加交易流水记录
            //查出所有银行账户循环赋值
            $accountList = DB::table('user_bank_account')->select()->get();
            $data = array();
            foreach ($accountList as $key => $value) {
                $value = (array) $value;
                $data[$key]['uid'] = $value['uid'];
                $data[$key]['bouns'] = $value['uninterest'];
                $data[$key]['type'] = 3;
            }
            UserBankAccountLog::store($data);
            //每月初更新利息到本金
            UserBankAccount::everyMonthUpdateInterest();
        });

        do_log("更新用户上月利息结算到本金结束","critical");

        return CommandAlias::SUCCESS;
    }

}
