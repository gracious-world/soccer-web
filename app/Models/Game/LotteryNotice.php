<?php

namespace App\Models\Game;

use App\Models\BaseModel;

use Carbon;

class LotteryNotice extends BaseModel {
    static $cacheLevel = self::CACHE_LEVEL_NONE;
    protected static $cacheMinutes = 0;

    protected $table = 'lottery_notices';
    public static $resourceName = 'LotteryNotice';

    public static $columnForList = [
        'id',
        'identity',
        'title',
        'text',
    ];

    public static $rules = [
        'identity' => 'required|max:32',
        'title'    => 'required',
        'text'     => 'required',
    ];
    protected $fillable = [
        'id',
        'identity',
        'title',
        'text',
    ];

}
