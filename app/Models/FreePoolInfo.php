<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;

class FreePoolInfo extends NexusModel
{
    public $timestamps = true;

    protected $table = 'free_pool_info';

    protected $fillable = ['periods', 'need_bonus', 'current_bonus', 'remark', 'sustain_time', 'rest_time', 'is_current'];

    public static function feed($bonus): void
    {
        FreePoolInfo::query()->where('is_current', 1)->update([
            'current_bonus' => DB::raw("current_bonus + $bonus")
        ]);
        do_log("sql: " . last_query(), 'critical');
    }

    public static function store($data): void
    {
        $result = DB::table('free_pool_info')->insert($data);
        do_log("sql: " . last_query() . ", result: $result");
    }
}
