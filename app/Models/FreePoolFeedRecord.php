<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;

class FreePoolFeedRecord extends NexusModel
{
    public $timestamps = true;

    protected $table = 'free_pool_feed_record';

    protected $fillable = ['uid', 'bonus', 'periods'];

    public static function store($data): void
    {
        $result = DB::table('free_pool_feed_record')->insert($data);
        do_log("sql: " . last_query() . ", result: $result");
    }

}
