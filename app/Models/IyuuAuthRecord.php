<?php

namespace App\Models;

class IyuuAuthRecord extends NexusModel
{
    protected $table = 'iyuu_auth_records'; // 表名
    protected $fillable = ['userid', 'iyuuid']; // 可以进行 Mass Assignment 的字段

    // 如果需要记录添加时间，可以启用 timestamps
    public $timestamps = true;


}
