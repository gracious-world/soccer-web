<?php

namespace App\Models\Bet;

use App\Models\BaseModel;
// use App\Models\Game\Game;
// use App\Models\Game\Method;
// use App\Models\Game\WayOdd;

// use Carbon;

class ProgramGame extends BaseModel {
    static $cacheLevel = self::CACHE_LEVEL_NONE;
    protected static $cacheMinutes = 0;

    protected $table = 'program_game';
    public static $resourceName = 'ProgramGame';

    public static $titleColumn = 'program_sn';

    protected $fillable = [
        'id',
        'program_id',
        'game_id',
        'program_sn',
        'game_bn',
        'bp_id',
        'bp_name',
        'gt_id',
        'user_id',
        'username',
        'is_tester',
        'status',
    ];
    public static $columnForList = [
        'program_sn',
        'game_bn',
        'created_at',
        'updated_at',
    ];
    public static $columnsForPivot = [
        'id',
        'bp_id',
        'bp_name',
        'gt_id',
        'user_id',
        'username',
        'is_tester',
        'status',
    ];
    /**
     * 下拉列表框字段配置
     * @var array
     */
    public static $htmlSelectColumns = [

    ];
    public $orderColumns = [
        'program_sn' => 'desc'
    ];
    public static $rules = [
        'program_id' => 'required|integer',
        'game_id' => 'required|integer',
    ];

}