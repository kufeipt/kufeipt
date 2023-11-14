<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;

class UserBankAccountLog extends NexusModel
{
    public $timestamps = true;

    protected $table = 'user_bank_account_log';

    protected $fillable = ['uid', 'bouns', 'type'];

    /**
     * 新增或者批量一条交易流水
     * @param data [['a' => 1], ['b'=>2]]
     * @return void
     */
    public static function store($data): void
    {
        $result = DB::table('user_bank_account_log')->insert($data);
        do_log("sql: " . last_query() . ", result: $result");
    }
}
