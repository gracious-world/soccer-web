<?php

namespace App\Models\Game;

use App\Models\BaseModel;

class Method extends BaseModel {
    static $cacheLevel = self::CACHE_LEVEL_NONE;
    protected static $cacheMinutes = 0;

    protected $table = 'methods';
    public static $resourceName = 'Method';

    const TYPE_SINGLE = 0; // 单关
    const TYPE_FREE   = 1; // 自由过关
    const TYPE_COMBIN = 2; // 混合过关
    const TYPE_CHP    = 3; // 冠军
    const TYPE_FNL    = 4; // 冠亚军
    public static $types = [
        self::TYPE_SINGLE => 'single',
        self::TYPE_FREE   => 'free',
        self::TYPE_COMBIN => 'combin',
        self::TYPE_CHP    => 'champion',
        self::TYPE_FNL    => 'championship',
    ];

    public static $aRankMethodIds = [3 => 41, 4 => 42];

    public static $columnForList = [
        'gt_id',
        'name',
        'identity',
        'type',
        'combination',
    ];
    public static $mainParamColumn = 'cn_name';

    public static $rules = [
        'gt_id'    => 'required|integer',
        'name'     => 'max:15',
        'identity' => 'alpha_num|max:15',
        'type'     => 'integer',
        'is_rank'  => 'boolean',
        'combination' => 'max:15',
    ];
    protected $fillable = [
        'gt_id',
        'name',
        'identity',
        'type',
        'is_rank',
        'combination',
    ];

    public static $htmlSelectColumns = [
        'gt_id' => 'aGameTypes',
        'type'  => 'aTypes',
    ];

    public static function getGateIdentityAndCombinationMap($iGameTypeId) {
        return self::where('gt_id', $iGameTypeId)->get()->pluck('combination', 'identity')->toArray();
    }
    public static function getFreeGates($iGameTypeId, $aTypes = [self::TYPE_SINGLE, self::TYPE_FREE]) {
        return self::where('gt_id', $iGameTypeId)->whereIn('type', $aTypes)->get()->pluck('combination', 'id')->toArray();
    }
    public static function getFreeGateTypes($iGameTypeId, $aTypes = [self::TYPE_SINGLE, self::TYPE_FREE]) {
        return self::where('gt_id', $iGameTypeId)->whereIn('type', $aTypes)->get()->pluck('type', 'id')->toArray();
    }
    public static function getGateNameAndIdMap($iGameTypeId) {
        return self::where('gt_id', $iGameTypeId)->get()->pluck('identity', 'id')->toArray();
    }

    public static function getRankMethods($iGameTypeId) {
        return self::where('gt_id', $iGameTypeId)->where('is_rank', 1)->get()->pluck('identity', 'id')->toArray();
    }
}
