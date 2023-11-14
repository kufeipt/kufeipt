<?php

namespace App\Console\Commands;

use App\Models\UserBankAccount;
use Illuminate\Console\Command;
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

class EveryDayUpdateInterest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'interest:day';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '每天更新用户未结算的利息';

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
        do_log("start every day interrest","critical");

        UserBankAccount::everyDayUpdateInterest();

        do_log("end every day interrest","critical");

        return CommandAlias::SUCCESS;
    }

}
