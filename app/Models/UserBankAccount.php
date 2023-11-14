<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;

class UserBankAccount extends NexusModel
{
    public $timestamps = true;

    protected $table = 'user_bank_account';

    protected $fillable = ['uid', 'bouns', 'rate', 'interest', 'uninterest'];

    /**
     * 每天更新利息
     * @return void
     */
    public static function everyDayUpdateInterest(): void
    {
        UserBankAccount::query()->update([
            'uninterest' => DB::raw('uninterest + bouns * rate')
        ]);
        do_log("sql: " . last_query() ,'errer');
    }

    /**
     * 每月初更新利息到本金
     * @return void
     */
    public static function everyMonthUpdateInterest(): void
    {
        UserBankAccount::query()->update([
            'bouns' => DB::raw("bouns + uninterest"),
            'interest' => DB::raw("interest + uninterest"),
            'uninterest' => 0.00
        ]);
        do_log("sql: " . last_query(),'error');
    }

    /**
     * 新增或者批量银行账户
     * @param data [['a' => 1], ['b'=>2]]
     * @return void
     */
    public static function store($data): void
    {
        $result = DB::table('user_bank_account')->insert($data);
        do_log("sql: " . last_query() . ", result: $result");
    }
}
