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

class Game extends BaseModel {
    static $cacheLevel = self::CACHE_LEVEL_NONE;
    protected static $cacheMinutes = 0;

    protected $table = 'games';
    public static $resourceName = 'Game';
    public static $titleColumn = 'bn';



    const STATUS_WAITING     = 0; // 未开售
    const STATUS_SALE_ON     = 1; // 销售中
    const STATUS_SALE_OFF    = 2; // 销售截止
    const STATUS_PROGRESSING = 3; // 比赛进行中
    const STATUS_DELAY       = 4; // 比赛延期
    const STATUS_FINISHED    = 5; // 比赛结束
    const STATUS_CANCELED    = 6; // 比赛取消

    public static $status = [
        self::STATUS_WAITING     => 'waiting',
        self::STATUS_SALE_ON     => 'on-sale',
        self::STATUS_SALE_OFF    => 'sale-stopped',
        self::STATUS_PROGRESSING => 'on-the-match',
        self::STATUS_DELAY       => 'delay',
        self::STATUS_FINISHED    => 'finished',
        self::STATUS_CANCELED    => 'canceled',
    ];

    public static $extraDisplayResult = [
        'had', 'hhad', 'hafu'
    ];
    public $orderColumns = ['played_at' => 'desc'];
    public static $columnForList = [
        // 'ds_id',
        // 'ds_g_id',
        'bn',
        'ds_g_bn',
        'gt_id',
        'is_rank',
        // 'l_id',
        // 'l_cn',
        // 'h_id',
        // 'h_cn',
        // 'a_id',
        // 'a_cn',
        'l_cn_abbr',
        'h_cn_abbr',
        'a_cn_abbr',
        // 'weather',
        // 'weather_city',
        // 'temperature',
        // 'single',
        // 'show',
        'status_display',
        // 'half_h_score',
        // 'half_a_score',
        // 'h_score',
        // 'a_score',
        // 'won',
        'fixed_display',
        'result_display',
        // 'sale_start_at',
        'sale_stop_at',
        'played_at',
    ];
    public static $mainParamColumn = 'bn';

    public static $rules = [
        'ds_id'         => 'required|integer',
        'ds_g_id'       => 'required|integer',
        'bn'            => 'required|alpha_num|max:16|unique:games',
        'ds_g_bn'       => 'required|max:20',
        'gt_id'         => 'required|integer',
        'l_id'          => 'integer',
        'is_rank'       => 'boolean',
        'l_cn'          => 'max:50',
        'h_id'          => 'integer',
        'h_cn'          => 'max:50',
        'a_id'          => 'integer',
        'a_cn'          => 'max:50',
        'l_cn_abbr'     => 'max:50',
        'h_cn_abbr'     => 'max:50',
        'a_cn_abbr'     => 'max:50',
        'weather'       => 'max:50',
        'weather_city'  => 'max:50',
        'temperature'   => 'max:50',
        'single'        => 'boolean',
        'show'          => 'boolean',
        'valid_ways'    => 'max:100',
        'status'        => 'in:0,1,2,3,4,5,6',
        'hot'           => 'boolean',
        'half_h_score'  => 'integer',
        'half_a_score'  => 'integer',
        'h_score'       => 'integer',
        'a_score'       => 'integer',
        'score_json'    => 'string',
        'won'           => 'boolean',
        'fixed'         => 'integer',
        'fixed_json'    => 'string',
        'l_background_color' => 'max:10',
        'sale_start_at' => 'date',
        'sale_stop_at'  => 'date',
        'played_at'     => 'date',
    ];
    protected $fillable = [
        'ds_id',
        'ds_g_id',
        'bn',
        'ds_g_bn',
        'gt_id',
        'is_rank',
        'l_id',
        'l_cn',
        'h_id',
        'h_cn',
        'a_id',
        'a_cn',
        'l_cn_abbr',
        'h_cn_abbr',
        'a_cn_abbr',
        'weather',
        'weather_city',
        'temperature',
        'single',
        'show',
        'valid_ways',
        'status',
        'hot',
        'half_h_score',
        'half_a_score',
        'h_score',
        'a_score',
        'score_json',
        'won',
        'fixed',
        'fixed_json',
        'l_background_color',
        'sale_start_at',
        'sale_stop_at',
        'played_at',
    ];

    public static $htmlSelectColumns = [
        'gt_id'  => 'aGameTypes',
        'status' => 'aStatus',
    ];


    public function odds() {
        return $this->hasMany('App\Models\Game\Odd', 'g_id');
    }

    public function changed_odds() {
        return $this->hasMany('App\Models\Game\ChangedOdd', 'g_id');
    }

    public function programs() {
        return $this->belongsToMany('App\Models\Bet\Program', 'program_game')->withPivot(ProgramGame::$columnsForPivot)->withTimestamps();
    }

    public function bills() {
        return $this->belongsToMany('App\Models\Bet\Bill', 'bill_game')->withPivot(BillGame::$columnsForPivot)->withTimestamps();
    }

    public function getFixedDisplayAttribute() {
        return $this->fixed > 0 ? '+' . $this->fixed : $this->fixed;
    }

    public function getFixedJsonDisplayAttribute() {
        return $this->fixed_json ? json_decode($this->fixed_json, true) : '';
    }

    public function getScoreDisplayAttribute() {
        return $this->status == self::STATUS_FINISHED ? $this->h_score . ':' . $this->a_score : '';
    }

    public function getStatusDisplayAttribute() {
        return __('_game.' . static::$status[$this->status]);
    }

    public function getGameResultAttribute() {
        return $this->calculateWonResults();
    }

    public function getGameResultDisplayAttribute() {
        return array_where($this->calculateWonResults(false), function($key, $value) {
            return in_array($key, static::$extraDisplayResult);
        });
    }

    public function getResultDisplayAttribute() {
        if ($this->is_rank) {
            return $this->status == self::STATUS_SALE_ON ? __('_game.waiting-result') : __('_game.' . ['lost', 'won'][$this->won]);
        }
        return $this->status == self::STATUS_SALE_ON ? __('_game.waiting-result') : (__('_game.half-game') . ': ' . ($this->gt_id == 2 ? $this->half_a_score : $this->half_h_score) . ':' . ($this->gt_id == 2 ? $this->half_h_score : $this->half_a_score) . ', ' . __('_game.final-game') . ': ' . ($this->gt_id == 2 ? $this->a_score : $this->h_score) . ':' . ($this->gt_id == 2 ? $this->h_score : $this->a_score));
    }

    public function getRealSaleStopAtAttribute() {
        $oSaleStopAt     = Carbon::parse($this->sale_stop_at);
        $sRealSaleStopAt = $this->sale_stop_at;
        $oToday = Carbon::today();
        $oSaleEnd = $oToday->isWeekend() ? $oToday->addDay()->addHour() : $oToday->addDay();
        if ($oSaleStopAt->gt($oSaleEnd)) {
            $sRealSaleStopAt = $oSaleEnd->toDateTimeString();
        }
        return $sRealSaleStopAt;
    }

    public function getRealSaleStartAtAttribute() {
        return Carbon::parse($this->sale_start_at)->addHours(9)->toDateTimeString();
    }

    protected function afterSave($oSavedModel) {
        $this->deleteCache($this->id);
        $this->deleteCache('gt-' . $this->gt_id);
        return true;
    }

    protected function afterDelete($oDeletedModel) {
        $this->deleteCache($this->id);
        $this->deleteCache('gt-' . $this->gt_id);
        return true;
    }

    protected function afterUpdate() {
        $this->deleteCache($this->id);
        $this->deleteCache('gt-' . $this->gt_id);
        return true;
    }

    public static function deleteCache($sKeyData) {
        if (static::$cacheLevel == self::CACHE_LEVEL_NONE) {
            return true;
        }
        $key = static::generateCacheKey($sKeyData);
        Cache::setDefaultDriver(static::$cacheDrivers[static::$cacheLevel]);
        !Cache::has($key) or Cache::forget($key);
    }

    public function getGameStatus($bSelling) {
        if (!in_array($this->status, [self::STATUS_PROGRESSING, self::STATUS_FINISHED, self::STATUS_CANCELED]) && $bSelling && $this->played_at < Carbon::now()->toDateTimeString()) {
            return self::STATUS_DELAY;
        }
    }

    public static function getGameAfterDate($iDate){
        return static::where('sale_start_at', '>=', $iDate)->get();
    }

    // public static function getGamesByBns($aBns){
    //     return static::whereIn('bn', $aBns)->get();
    // }

    // TIP --------------- old ways to get available games-------------------
    // public static function getAvailableGames($iGameType, $bSingle = false) {
    //     $aParams = [
    //         'gt_id' => $iGameType,
    //         'status' => Game::STATUS_SALE_ON,
    //         'played_at' => ['>', Carbon::now()->toDateTimeString()]
    //     ];
    //     if ($bSingle) $aParams['single'] = 1;
    //     return self::getAvailableGamesFromDB($aParams);
    // }

    // public static function getAvailableGamesFromDB($aParams) {
    //     return static::complexWhere($aParams)->orderBy('bn', 'asc')->get();
    // }
    // TIP --------------- old ways to get available games-------------------

    public static function getAvailableGames($iGameType, $iSingle = false, $bRankGame = 0, $bIsFocus = 0) {
        $aParams = ['gt_id' => $iGameType, 'is_rank' => intval($bRankGame), 'status' => Game::STATUS_SALE_ON];
        if ($iSingle) $aParams['single'] = 1;
        if (static::$cacheLevel == self::CACHE_LEVEL_NONE) {
            return self::getAvailableGamesFromDB($aParams, $bIsFocus);
        }
        Cache::setDefaultDriver(static::$cacheDrivers[static::$cacheLevel]);
        $sKeyStr = md5(http_build_query($aParams));
        $key = static::generateCacheKey($sKeyStr);
        if ($aAttributes = Cache::get($key)) {
            $obj = new static;
            $obj = $obj->newFromBuilder($aAttributes);
        } else {
            $obj = self::actived()->orderBy('bn')->get();
            if (!is_object($obj)) {
                return false;
            }
            $data = $obj->pluck('name', 'id')->toArray();
            if (static::$cacheMinutes){
                Cache::put($key, $data, static::$cacheMinutes);
            } else {
                Cache::forever($key, $data);
            }
        }
        return $obj;
    }

    public static function getAvailableGamesFromDB($aParams, $bIsFocus = 0) {
        $oQuery = static::complexWhere($aParams);
        if (intval($aParams['is_rank'])) $oQuery = $oQuery->orderBy('id', 'asc');
        else $oQuery = $oQuery->orderBy('bn', 'asc');
        if ($bIsFocus) $oQuery = $oQuery->take(4);
        return $oQuery->get();
    }

    public static function getGamesByBns($gameBns){
        return static::whereIn('bn', $gameBns)->orderBy('played_at', 'asc')->get();
    }

    public static function getGamesByPlayedAt($iGameType, $sPlayedAt, $iSingle = false, $bRankGame = 0){
        $sBetDate = Carbon::parse($sPlayedAt)->toDateString();
        $sPlayedAtStart = $sBetDate . ' 00:00:00';
        $sPlayedAtEnd   = $sBetDate . ' 23:59:59';
        $param = [
            'gt_id'         => ['=', $iGameType],
            'is_rank'       => $bRankGame,
            'sale_start_at' => ['=', $sPlayedAtStart]
        ];
        if ($iSingle) $param['single'] = 1;
        $oQuery = parent::complexWhere($param)->orderBy('bn');
        return $oQuery->get();
    }

    /**
     * [calculateWonResults 计算赛事的所有玩法的赛果]
     * @return [Array] [赛果]
     */
    public function calculateWonResults($bIdentity = true) {
        if (!in_array($this->status, [self::STATUS_FINISHED, self::STATUS_CANCELED])) return [];
        $aValidWays = explode(',', $this->valid_ways);
        $oWays      = Way::whereIn('identity', $aValidWays)->where('gt_id', $this->gt_id)->get();
        $aResult    = [];
        // pr($oWays->toArray());exit;
        foreach ($oWays as $key => $oWay) {
            $aValidOdds = $oWay->way_odds->pluck('name', 'identity')->toArray();
            $sFun = 'calculate' . ucfirst($oWay->identity) . 'Result';
            $aResult[$oWay->identity] = $this->$sFun($aValidOdds, $bIdentity);
        }
        // $aResult = [];
        // foreach ($aValidWays as $sWay) {
        //     $sFun = 'calculate' . ucfirst($sWay) . 'Result';
        //     $aResult[$sWay] = $this->$sFun();
        // }
        return $aResult;
    }
    # 篮球胜负赛果
    private function calculateMnlResult($aValidOdds, $bIdentity = true) {
        $iWon = intval($this->h_score > $this->a_score);
        $sResult = ['bka', 'bkh'][$iWon];
        return $bIdentity ? $sResult : $aValidOdds[$sResult];
    }
    # 篮球让球胜负赛果
    private function calculateHdcResult($aValidOdds, $bIdentity = true) {
        $jFixed = json_decode($this->fixed_json, true);
        $iFixed = $jFixed && array_key_exists('hdc', $jFixed) ? intval($jFixed['hdc']) : 0;
        $iWon = intval($this->h_score + $iFixed > $this->a_score);
        $sResult = ['bkha', 'bkhh'][$iWon];
        return $bIdentity ? $sResult : $aValidOdds[$sResult];
    }
    # 篮球大小分赛果
    private function calculateHiloResult($aValidOdds, $bIdentity = true) {
        $jFixed = json_decode($this->fixed_json, true);
        $iFixed = $jFixed && array_key_exists('hilo', $jFixed) ? intval($jFixed['hilo']) : 0;
        $iWon = intval((intval($this->h_score) + intval($this->a_score)) > $iFixed);
        $sResult = ['bkbsh', 'bkbsl'][$iWon];
        return $bIdentity ? $sResult : $aValidOdds[$sResult];
    }
    # 篮球胜分差赛果
    private function calculateWnmResult($aValidOdds, $bIdentity = true) {
        $iWon = intval($this->h_score > $this->a_score);
        $sPre = ['bkl', 'bkw'][$iWon];
        $iScoreDiff = abs(intval($this->h_score) - intval($this->a_score));
        $aScoreDiffs = [26,21,16,11,6];
        $iTotalLevels = 6;
        $iLevel = 1;
        foreach ($aScoreDiffs as $key => $iScoreLevel) {
            if ($iScoreDiff >= $iScoreLevel) $iLevel = $iTotalLevels - $key;
        }
        $sResult = $sPre . $iLevel;
        return $bIdentity ? $sResult : $aValidOdds[$sResult];
    }
    # 足球欧洲杯冠军赛果
    private function calculateChpResult($aValidOdds, $bIdentity = true) {
        return ['out', 'champion'][$this->won];
    }
    # 足球欧洲杯冠亚军赛果
    private function calculateFnlResult($aValidOdds, $bIdentity = true) {
        return ['out', 'championship'][$this->won];
    }
    # 足球胜平负赛果
    private function calculateHadResult($aValidOdds, $bIdentity = true) {
        $iDiff = intval($this->h_score) - intval($this->a_score);
        $sResult = $iDiff > 0 ? 'h' : ($iDiff < 0 ? 'a' : 'd');
        return $bIdentity ? $sResult : $aValidOdds[$sResult];
    }
    # 足球让球胜平负赛果
    private function calculateHhadResult($aValidOdds, $bIdentity = true) {
        $iDiff = intval($this->h_score) + intval($this->fixed) - intval($this->a_score);
        $sResult = $iDiff > 0 ? 'ch' : ($iDiff < 0 ? 'ca' : 'cd');
        return $bIdentity ? $sResult : $aValidOdds[$sResult];
    }
    # 足球半全场赛果
    private function calculateHafuResult($aValidOdds, $bIdentity = true) {
        $iDiff = intval($this->half_h_score) - intval($this->half_a_score);
        $sHalfHad = $iDiff > 0 ? 'h' : ($iDiff < 0 ? 'a' : 'd');
        $sHad     = $this->calculateHadResult($aValidOdds);
        $sResult = $sHalfHad . $sHad;
        return $bIdentity ? $sResult : $aValidOdds[$sResult];
    }
    # 足球总进球赛果
    private function calculateTtgResult($aValidOdds, $bIdentity = true) {
        $iTotalGoals = intval($this->h_score) + intval($this->a_score);
        $sResult = 's' . min($iTotalGoals, 7);
        return $bIdentity ? $sResult : $aValidOdds[$sResult];
    }
    # 足球比分赛果
    private function calculateCrsResult($aValidOdds, $bIdentity = true) {
        // $oWay       = Way::where('identity', 'crs')->where('gt_id', $this->gt_id)->first();
        // $aValidOdds = $oWay->way_odds->pluck('identity')->toArray();
        $sResult        = str_pad($this->h_score, 2, '0', STR_PAD_LEFT) . str_pad($this->a_score, 2, '0', STR_PAD_LEFT);
        if (!in_array($sResult, array_keys($aValidOdds))) {
            $iDiff  = intval($this->h_score) - intval($this->a_score);
            $sResult = $iDiff > 0 ? '-1-h' : ($iDiff < 0 ? '-1-a' : '-1-d');
        }
        return $bIdentity ? $sResult : $aValidOdds[$sResult];
    }


    // public static function getAllowBetTime() {
    //     $sSaleStart = ' 09:00:00';
    //     $sWorkdaySaleEnd = ' 23:59:59';
    //     $sWeekendSaleEnd = ' 01:00:00';

    //     $oSaleStopAt     = Carbon::parse($this->sale_stop_at);
    //     $oSaleStart      = Carbon::parse($this->sale_start_at . $sSaleStart);
    //     $oWorkdaySaleEnd = Carbon::parse($oSaleStopAt->toDateString() . $sWorkdaySaleEnd);
    //     $oWeekendSaleEnd = Carbon::parse($oSaleStopAt->toDateString() . $sWeekendSaleEnd);
    //     $oSaleEnd        = $oSaleStopAt->isWeekend() ? $oWeekendSaleEnd : $oWorkdaySaleEnd;

    //     $sRealSaleStopAt = $oSaleStopAt->toDateTimeString();
    //     if (!$oSaleStopAt->between($oSaleStart, $oSaleEnd)) {
    //         $sRealSaleStopAt = $oSaleEnd->toDateTimeString();
    //     }
    // }

    public function getRealSaleStopTimeAttribute() {
        // 周六,周日,周一凌晨1点以前都算周末
        $bIsWeekend = Carbon::now()->isWeekend() || (Carbon::now()->dayOfWeek == Carbon::MONDAY && Carbon::now() <= Carbon::now()->startOfDay()->addHour());
        $oSaleStopAt = $bIsWeekend ? Carbon::today()->hour(24+1) : Carbon::today()->hour(24)->subSecond();
        $sSaleStopAt = min($oSaleStopAt->toDateTimeString(), $this->sale_stop_at);
        return $sSaleStopAt;
    }

    public static function getSaleStopAt($sPlayedAt, $iAllowBetHour = 0) {
        $iSaleStopBeforeMinutes = Config::get('custom-sysconfig.sale-stop-minutes', 5);
        $oPlayedAt = Carbon::parse($sPlayedAt);
        if (!$iAllowBetHour) {
            // 如果比赛时间在周日,周一,则凌晨截止时间为1:00
            if (in_array($oPlayedAt->dayOfWeek, [Carbon::MONDAY, Carbon::SUNDAY])) $iAllowBetHour = 1;
            else $iAllowBetHour = 0;
        }
        $oRestStartOfPlayedAt = Carbon::parse($sPlayedAt)->startOfDay()->hour($iAllowBetHour);
        $oRestEndOfPlayedAt = Carbon::parse($sPlayedAt)->startOfDay()->hour(9);
        // $iAllowBetHour or $iAllowBetHour = $oPlayedAt->isWeekend() ? 1 : 0;
        // (凌晨截止小时,9]区间内不可投注,赛事时间取最近允许投注的时间
        if ($oPlayedAt->gt($oRestStartOfPlayedAt) && $oPlayedAt->lte($oRestEndOfPlayedAt)) $oPlayedAt = $oRestStartOfPlayedAt;

        $sSaleStopAt = $oPlayedAt->subMinutes($iSaleStopBeforeMinutes)->toDateTimeString();
        return $sSaleStopAt;
    }
}