<?php
/**
 * 赛事表模型
 * User: damon
 * Date: 2/8/16
 * Time: 3:51 PM
 */
namespace App\Models\Game;

use App\Models\BaseModel;
use App\Models\Game\Way;
use App\Models\Game\Odd;
use App\Models\Bet\Program;
use App\Models\Bet\BillGame;
use App\Models\Bet\ProgramGame;

use Carbon;
use Config;

class CustomSaleStopTime extends BaseModel {
    static $cacheLevel = self::CACHE_LEVEL_NONE;
    protected static $cacheMinutes = 0;

    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;

    protected $table = 'custom_sale_stop_times';
    protected $fillable = ['gt_id', 'game_type', 'date', 'sale_stop_hour', 'status', 'remark'];
    public static $resourceName = 'CustomSaleStopTime';
    public static $titleColumn = 'date';

    public static $columnForList = [
        'gt_id',
        'date',
        'sale_stop_hour',
        'status',
        'remark',
        'created_at',
    ];
    public static $rules = [
        'gt_id'          => 'integer',
        'game_type'      => 'string|max:50',
        'date'           => 'date',
        'sale_stop_hour' => 'integer',
        'status'         => 'boolean',
        'remark'         => 'string|max:200'
    ];

    public static $mainParamColumn = 'date';

    public static $htmlSelectColumns = [
        'gt_id' => 'aGameTypes',
        // 'status'=> 'aStatus',
    ];

    public function beforeValidate() {
        // $this->date = Carbon::parse($this->date)->toDateString();
        return parent::beforeValidate();
    }


    public static function getLatestSaleStopRules($iGtId, $sDate = null) {
        $sDate or $sDate = Carbon::today()->toDateString();
        $aSaleStopTimes = [];
        if ($oCustomSaleStopTimes = self::where('gt_id', $iGtId)->where('date', '>=', $sDate)->where('status', static::STATUS_ACTIVE)->get()) {
            $aSaleStopTimes = $oCustomSaleStopTimes->pluck('sale_stop_hour', 'date')->toArray();
        }
        return $aSaleStopTimes;
    }

}