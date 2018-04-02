<?php
/**
 * 赛事赔率表模型
 * User: damon
 * Date: 2/8/16
 * Time: 3:51 PM
 */
namespace App\Models\Game;

use App\Models\BaseModel;

class ChangedOdd extends BaseModel {
    static $cacheLevel = self::CACHE_LEVEL_NONE;
    protected static $cacheMinutes = 0;

    protected $table = 'changed_odds';
    public static $resourceName = 'ChangedOdd';

    public static $titleColumn = 'odd';
    public static $amountAccuracy = 2;


    public static $columnForList = [
        'gt_id',
        // 'g_id',
        'g_bn',
        'w_id',
        // 'wo_id',
        'wo_identity',
        'odd',
        'single',
    ];
    public static $mainParamColumn = 'wo_identity';

    public static $rules = [
        'gt_id'       => 'required|integer',
        'g_id'        => 'required|integer',
        'g_bn'        => 'required|alpha_num|max:16',
        'w_id'        => 'required|integer',
        'wo_id'       => 'required|integer',
        'wo_identity' => 'alpha_dash|max:15',
        'odd'         => 'numeric',
        'single'      => 'boolean',
        'probability' => 'String',
    ];
    protected $fillable = [
        'gt_id',
        'g_id',
        'g_bn',
        'w_id',
        'wo_id',
        'wo_identity',
        'odd',
        'single',
        'probability',
    ];

    public static $htmlNumberColumns = [
        'odd' => 2,
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