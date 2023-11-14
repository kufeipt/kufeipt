<?php

namespace App\Console\Commands;

use App\Models\FreePoolInfo;
use App\Models\Setting;
use App\Models\UserBankAccount;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
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

class StartNextSiteFreePool extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'start_next_site_free_pool';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '开启下一期站免池';

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
        do_log("console start next site free poll start", 'critical');
        $siteFreePoolNoFeeding = Cache::get(Setting::SITE_FREE_POOL_NEXT_PERIODS_TIME);
        $current = FreePoolInfo::query()->where('is_current', 1)->first();
        if (empty($siteFreePoolNoFeeding) && $current['current_bonus'] >= $current['need_bonus']) {
            DB::beginTransaction();//开启事务
            try {
                FreePoolInfo::query()->where('periods', $current['periods'] + 1)->update([
                    'is_current' => 1
                ]);
                FreePoolInfo::query()->where('id', $current['id'])->update([
                    'is_current' => 0
                ]);
                DB::commit();
            } catch (\Exception $e) {
                DB::rollback();
                do_log("console start next site free poll error: " . $e, 'critical');
            }
        }
        do_log("console start next site free poll end", 'critical');


        return CommandAlias::SUCCESS;
    }

}
