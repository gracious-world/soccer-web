<?php
/**
 * 赛事赔率表模型
 * User: damon
 * Date: 2/8/16
 * Time: 3:51 PM
 */
namespace App\Models\Game;

use App\Models\BaseModel;

class Odd extends BaseModel {
    static $cacheLevel = self::CACHE_LEVEL_NONE;
    protected static $cacheMinutes = 0;

    protected $table = 'odds';
    public static $resourceName = 'Odd';

    public static $titleColumn = 'odd';
    // public static $amountAccuracy = 2;


    public static $columnForList = [
        // 'ds_id',
        'gt_id',
        // 'g_id',
        'g_bn',
        'w_id',
        // 'wo_id',
        'wo_identity',
        'odd',
        'last_odd',
        // 'euro_odd',
        'single',
    ];
    public static $mainParamColumn = 'wo_identity';

    public static $rules = [
        'ds_id'       => 'required|integer',
        'gt_id'       => 'required|integer',
        'g_id'        => 'required|integer',
        'g_bn'        => 'required|alpha_num|max:16',
        'w_id'        => 'required|integer',
        'wo_id'       => 'required|integer',
        'wo_identity' => 'alpha_dash|max:15',
        'odd'         => 'numeric',
        'last_odd'    => 'numeric',
        'euro_odd'    => 'numeric',
        'probability' => 'String',
        'single'      => 'boolean',
    ];
    protected $fillable = [
        'ds_id',
        'gt_id',
        'g_id',
        'g_bn',
        'w_id',
        'wo_id',
        'wo_identity',
        'odd',
        'last_odd',
        'euro_odd',
        'probability',
        'single',
    ];

    public static $htmlNumberColumns = [
        'odd' => 2,
        'last_odd' => 2,
    ];

    public static $htmlSelectColumns = [
        'gt_id' => 'aGameTypes',
        'w_id'  => 'aWays',
    ];

    public function game() {
        return $this->belongsTo('App\Models\Game\Game', 'g_id');
    }

    public function getOddFormattedAttribute() {
        return number_format($this->odd, 2);
    }
    public function getLastOddFormattedAttribute() {
        return number_format($this->last_odd, 2);
    }
    /**
     * 根据赛事ID获取赔率
     * @param $aGameIds
     * @return mixed
     */
    public static function getOddsByGameIds($aGameIds, $aWayIds = [], $aColumns = ['*']) {
        $oQuery = static::whereIn('g_id', $aGameIds);
        if (count($aWayIds)) {
            $oQuery = $oQuery->whereIn('w_id', $aWayIds);
        }
        return $oQuery->get($aColumns);
    }

}