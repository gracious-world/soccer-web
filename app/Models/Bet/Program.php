<?php
# 注单方案
namespace App\Models\Bet;

use App\Models\BaseModel;
use App\Models\Game\Game;
use App\Models\Game\Method;
use App\Models\Game\WayOdd;
use App\Models\Bet\ProgramGame;
use App\Models\Func\SysConfig;

use App\Models\BaseTask;

use Carbon;
use Config;
use DbTool;
use DB;

class Program extends BaseModel {
    static $cacheLevel = self::CACHE_LEVEL_NONE;
    protected static $cacheMinutes = 0;

    protected $table = 'programs';
    public static $resourceName = 'Program';

    public static $titleColumn = 'sn';
    // public static $amountAccuracy = 2;


    const PRICE = 2.00; // 每注金额

    const STATUS_WAITING           = 0; // 等待出票
    const STATUS_TICKET_FAILED     = 1; // 出票失败
    const STATUS_TICKET_PRINTED    = 2; // 出票成功
    const STATUS_CANCELED          = 3; // 已撤销
    const STATUS_SYSTEM_CANCELED   = 4; // 系统撤销
    const STATUS_LOSS              = 5; // 未中奖
    const STATUS_WIN               = 6; // 已中奖
    const STATUS_AWARD             = 7; // 已派奖
    const STATUS_PARTLY_CALCULATED = 8; // 部分计奖

    // const STATUS_COUNT_WAITING = 0; //等待计奖
    // const STATUS_COUNT_SENDING = 1; //计奖中
    // const STATUS_COUNT_PART    = 2; //部分计奖
    // const STATUS_COUNT_FINISH  = 3; //计奖完成

    // const STATUS_PRIZE_WAITING = 0; //等待派奖
    // const STATUS_PRIZE_SENDING = 1; //派奖中
    // const STATUS_PRIZE_PART    = 2; //部分派奖
    // const STATUS_PRIZE_FINISH  = 3; //派奖完成

    const ERRNO_LOCK_FAILED        = -211;
    // const ERRNO_PRIZE_SENDING      = -981;
    // const ERRNO_COMMISSION_SENDING = -982;

    public static $status = [
        self::STATUS_WAITING           => 'waiting',
        self::STATUS_TICKET_PRINTED    => 'ticket-printed',
        self::STATUS_TICKET_FAILED     => 'ticket-failed',
        self::STATUS_CANCELED          => 'canceled',
        self::STATUS_LOSS              => 'loss',
        self::STATUS_WIN               => 'win',
        self::STATUS_AWARD             => 'award',
        self::STATUS_SYSTEM_CANCELED   => 'system-canceled',
        self::STATUS_PARTLY_CALCULATED => 'partly-calculated',
    ];
    # 允许的状态流
    public static $statusAllowedFlow = [
        self::STATUS_TICKET_PRINTED    => [self::STATUS_WAITING],
        self::STATUS_TICKET_FAILED     => [self::STATUS_WAITING],
        self::STATUS_CANCELED          => [self::STATUS_WAITING, self::STATUS_TICKET_PRINTED],
        self::STATUS_LOSS              => [self::STATUS_TICKET_PRINTED, self::STATUS_PARTLY_CALCULATED],
        self::STATUS_WIN               => [self::STATUS_TICKET_PRINTED, self::STATUS_PARTLY_CALCULATED],
        self::STATUS_AWARD             => [self::STATUS_WIN],
        self::STATUS_SYSTEM_CANCELED   => [self::STATUS_WAITING, self::STATUS_TICKET_PRINTED],
        self::STATUS_PARTLY_CALCULATED => [self::STATUS_TICKET_PRINTED],
    ];

    public $orderColumns = ['bought_at' => 'desc'];
    public static $columnForList = [
        'bp_name',
        'sn',
        'game_type',
        'username',
        'is_tester',
        // 'first_played_at',
        // 'first_game_bn',
        'gate_type',
        'multiple',
        'bet_num',
        'bet_num_calculated',
        'game_num',
        'finished_game_num',
        'amount',
        // 'gate',
        // 'gate_type',
        // 'bet_content',
        // 'bet_odds_display',
        // 'coefficient',
        'prize',
        'status',
        // 'status_count',
        // 'locked_count',
        'bought_at',
        // 'canceled_by',
        // 'canceled_at',
        'counted_at',
        // 'prize_sent_at'
    ];
    public static $mainParamColumn = 'wo_identity';

    public static $rules = [
//        'bp_id'               => 'required|integer',
//        'bp_name'             => 'string|max:50',
        'sn'                  => 'required|string|max:32',
        'gt_id'               => 'required|integer',
        'game_type'           => 'string|max:50',
        'user_id'             => 'required|integer',
        'username'            => 'required|alpha_dash|between:6,16',
        'is_tester'           => 'boolean',
        // 'forefather_user_ids' => 'string',
        'account_id'          => 'integer',
        'first_played_at'     => 'date',
        'first_game_bn'       => 'String',
        'multiple'            => 'required|integer|max:500000',
        'bet_num'             => 'required|integer',
        'bet_num_calculated'  => 'integer',
        'game_num'            => 'integer',
        'finished_game_num'   => 'integer',
        'amount'              => 'required|numeric',
        'gate'                => 'required|string|max:30',
        'gate_type'           => 'required|integer',
        'method_id'           => 'required|string|max:100',
        'bet_content'         => 'required|string',
        'bet_odds_display'    => 'String',
        'coefficient'         => 'required|numeric',
        'prize'               => 'numeric',
        'status'              => 'digits_between:0,7',
        // 'status_count'        => 'digits_between:0,3',
        // 'status_prize'        => 'digits_between:0,3',
        // 'locked_count'        => 'integer',
        // 'locked_prize'        => 'integer',
        'canceled_by'         => 'alpha_num||between:0,32',
        'bought_at'           => 'date',
        'canceled_at'         => 'date',
        'counted_at'          => 'date',
        'prize_sent_at'       => 'date',
    ];
    protected $fillable = [
        'bp_id',
        'bp_name',
        'sn',
        'gt_id',
        'game_type',
        'user_id',
        'username',
        'is_tester',
        'is_rank',
        // 'forefather_user_ids',
        'account_id',
        'first_played_at',
        'first_game_bn',
        'multiple',
        'bet_num',
        'bet_num_calculated',
        'game_num',
        'finished_game_num',
        'amount',
        'gate',
        'gate_type',
        'method_id',
        'bet_content',
        'bet_odds_display',
        'coefficient',
        'prize',
        'status',
        // 'status_count',
        // 'status_prize',
        // 'locked_count',
        // 'locked_prize',
        'canceled_by',
        'bought_at',
        'canceled_at',
        'counted_at',
        'prize_sent_at',
        'created_at',
        'updated_at'
    ];

    public static $htmlNumberColumns = [
        'amount' => 2,
        'prize'  => 2,
    ];

    public static $htmlSelectColumns = [
        'gt_id'     => 'aGameTypes',
        'status'    => 'aStatus',
        'gate_type' => 'aGateTypes'
    ];

    public static $listColumnMaps = [
        'gate_type' => 'gate_type_display',
        'status' => 'status_display',
    ];

    public static $totalColumns = [
        'amount',
        'prize',
    ];

    public function games() {
        return $this->belongsToMany('App\Models\Game\Game', 'program_game')->withPivot(ProgramGame::$columnsForPivot)->withTimestamps();
    }

    public function bills() {
        return $this->hasMany('App\Models\Bet\Bill');
    }

    public function getAmountDisplayAttribute() {
        return number_format($this->amount, 2, '.', ',');
    }
    public function getPrizeDisplayAttribute() {
        return number_format($this->prize, 2, '.', ',');
    }
    public function getStatusDisplayAttribute() {
        return __('_program.' . static::$status[$this->status]);
    }
    public function getGateTypeDisplayAttribute() {
        return __('_method.' . Method::$types[$this->gate_type]);
    }
    public function getMethodIdsAttribute() {
        $aBetContent = json_decode($this->bet_content, true);
        $aGateIdentities = $aBetContent['gate'];
        $aMethodIds = Method::whereIn('identity', $aGateIdentities)->lists('id')->toArray();
        return $aMethodIds;
    }
    public function getIsTesterDisplayAttribute() {
        return is_null($this->is_tester) ? '' : __('_basic.' . strtolower(Config::get('custom-sysconfig.boolean')[$this->is_tester]));
    }
    // public function getBetDetailDisplayAttribute() {
    //     $aBetContent = json_decode($this->bet_content, true);
    //     foreach ($aBetContent['games'] as $key => $aOdds) {
    //         foreach ($aOdds as $key => $sOdd) {
    //             list($sOddIdentity, $iOdd) = explode('_', $sOdd);
    //             $s
    //         }
    //     }
    // }

    protected function beforeValidate() {
        $this->counted_at != '0000-00-00 00:00:00' or $this->counted_at = null;
        $this->canceled_at != '0000-00-00 00:00:00' or $this->canceled_at = null;
        // $this->prize_sent_at != '0000-00-00 00:00:00' or $this->prize_sent_at = null;
        return parent::beforeValidate();
    }

    /**
     * 验证是否超出最大串关次数，以及单关，自由过关，混合过关的同时可选性
     * @param $iMaxGate
     * @param $aOriginalGates
     * @param $aGates
     * @return bool
     */
    private function verifyGateLegal($iMaxGate, $aOriginalGates, $aGates) {
        if ($iMaxGate < max($aGates)) return false; // 如果拆分后的最大过关场次大于根据所选赔率单元计算的最大过关场次，返回false；
        $sGates = implode(',', $aOriginalGates) . ',';
        $iGatesNum = count($aOriginalGates);
        $bSimpleGateType = strpos($sGates, 'x1,') > -1; // 是否自由过关/单关
        if (!$bSimpleGateType && $iGatesNum > 1) return false;// 混合过关只能单选
        if ($bSimpleGateType) { // 自由过关不允许包含混合过关方式
            foreach ($aOriginalGates as $key => $value) {
                if (substr($value, -2) != 'x1') {
                    $bSimpleGateType = false;
                    break;
                }
            }
            return $bSimpleGateType;
        }
        return true;
    }

    private function verifyMaxGate($iGameTypeId, $aBetContent) {
        if (!isset($aBetContent['max_gate'])) return false;
        // if (intavl($aBetContent['force_danguan'])) return 1;
        // $aOddMaxGates = WayOdd::all()->pluck('max_connect_games', 'identity')->toArray();
        $aOddMaxGates = WayOdd::getWayOddNameOrConnectNumByGameType($iGameTypeId, 0);
        $aOddIdentities = [];
        $iMaxGate = 8;
        foreach ($aBetContent['games'] as $key => $aOdds) {
            foreach ($aOdds as $item) {
                $sOddIdentity = explode('_', $item)[0];
                $iMaxGate = min($iMaxGate, $aOddMaxGates[$sOddIdentity]);
            }
        }
        // 根据所选赔率单元，判断实际允许的最大串关场次，如果和传入的参数不符，返回false；
        if (!$iMaxGate || $iMaxGate != $aBetContent['max_gate']) return false;
        return $iMaxGate;
    }
    /**
     * [getGates 获取实际串关类型数组]
     * @param  [Array] $aOriginalGates [原始串关类型数组]
     * @return [Array]                 [实际串关类型数组]
     */
    public function getGates($aOriginalGates) {
        if (!$aOriginalGates) return false;
        $oMethods = Method::doWhere(['gt_id' => ['=', $this->gt_id], 'identity' => ['in', $aOriginalGates]])->get();
        foreach ($oMethods as $oMethod) {
            $aTypes[] = $oMethod->type;
        }
        $aTypes = array_unique($aTypes);
        // 组合过关只能单选
        if (count($aTypes) > 1 && in_array(Method::TYPE_COMBIN, $aTypes)) return false;
        $aGates = $oMethods->count() == 1 ? explode(',', $oMethods[0]->combination) : $oMethods->pluck('combination')->toArray();
        return $aGates;
    }

    /**
     * [verifyBetData 验证注单数据]
     * @param  [Array] $aBillData [注单数据]
     * @return [Boolean]          [验证注单数，金额是否正确]
     */
    /**
     * 输入的注单数据格式
     * {
     *     "gate": ["3x4"],
     *     "games": {"20160204YX006": ["h_1.40", "d_2.15"], "20160204YX007": ["hh_1.40", "0100_6.15"], "20160204YX008": ["ch_1.40", "s0_4.15"]},
     *     "dan": ["20160204YX006"],
     *     "max_gate": 4, // 最大允许串关场次
     *     "bet_num": 16,
     *     "amount": 32,
     *     "multiple": 1
     * }
     */
    public function verifyBetData($iGameTypeId, & $aBetContent) {
        $aLegalParamKeys = ['gate', 'games', 'dan', 'max_gate', 'bet_num', 'amount', 'multiple'];
        $iLegalParamKeysCount = count($aLegalParamKeys);
        $aIntersectKeys = array_intersect($aLegalParamKeys, array_keys($aBetContent));
        if (count($aIntersectKeys) != $iLegalParamKeysCount) return false; // 验证传入的参数是否缺少必须的值
        if (!$iMaxGate = $this->verifyMaxGate($iGameTypeId, $aBetContent)) return false; // 验证最大允许串关数
        // if (!array_key_exists('gate', $aBetContent)) return false; // 验证是否有串关参数
        if (!$aGates = $this->getGates($aBetContent['gate'])) return false; // 验证是否能获取到实际的串关数组
        if (!$this->verifyGateLegal($iMaxGate, $aBetContent['gate'], $aGates)) return false; // 验证串关合法性
        $aBetGameBns = array_keys($aBetContent['games']);
        if (count($aBetGameBns) != count(array_unique($aBetGameBns))) return false;
        if (count($aBetContent['dan']) != count(array_unique($aBetContent['dan']))) return false;
        $iBetsNum = $this->caculateOrderCount($aBetContent['games'], $aGates, $aBetContent['dan']);
        if ($iBetsNum != $aBetContent['bet_num']) return false;
        if ($iBetsNum * $aBetContent['multiple'] * self::PRICE != $aBetContent['amount']) return false;
        return true;
    }

    public function compileOddBetArray($aBillBetContent, $bDisplay = false) {
        $aOdds = [];
        foreach ($aBillBetContent as $key => $data) {
            foreach ($data as $key2 => $value) {
                $aOdds[$key][$key2] = implode('|', [$key, $key2, $value]);
            }
        }
        $aSplitedOdds = descartes($aOdds);
        // pr($aSplitedOdds);
        $aSplittedBetContent = [];
        foreach ($aSplitedOdds as $key => $aSplitedOdd) {
            $aOdd = [];
            foreach ($aSplitedOdd as $key2 => $value) {
                list($sGameBn, $sOddIdentity, $sOdd) = explode('|', $value);
                $aOdd[$sGameBn] = $bDisplay ? [$sOddIdentity => $sOdd] : [$sOdd];
            }
            $aSplittedBetContent[] = $aOdd;
        }
        return $aSplittedBetContent;
    }

    public function compileBillOddDataSingle($aBillBetContent, $bDisplay = false) {
        $aOdds = [];
        foreach ($aBillBetContent as $key => $data) {
            foreach ($data as $key2 => $value) {
                $aOdds[] = [$key => $bDisplay ? [$key2 => $value] : [$value]];
            }
        }
        return $aOdds;
    }

    public function createBills($iGameTypeId, $aOriginalGates, $aGames, $aGates, $aDan, & $aGameBnMaps) {
        $bSucc = true;
        // pr($aGameBnMaps);
        $aSplittedGames     = $this->splitGames($aGames, $aGates, $aDan);
        $aBetOddsForDisplay = json_decode($this->bet_odds_display, true);
        $aFreeGates         = Method::getFreeGates($this->gt_id);
        $aFreeGateTypes     = Method::getFreeGateTypes($this->gt_id);
        $aRankMethods       = Method::getRankMethods($this->gt_id);

        $iRankMethodId = null;
        if ($aRankMethods && in_array($aOriginalGates[0], $aRankMethods)) {
            $aRankMethods = array_flip($aRankMethods);
            $iRankMethodId = $aRankMethods[$aOriginalGates[0]];
        }

        // $oCustomer = BusinessPartner::find($this->bp_id);
        // $aBillBetGames = [];
        // pr($aSplittedGames);exit;
        foreach(array_chunk($aSplittedGames, 10) as $aChunkedGames) {
            // $aBillOddContents = [];
            foreach ($aChunkedGames as $aSplittedGame) {
                // pr($aSplittedGame);
                $aGameDatas     = array_intersect($aGameBnMaps, $aSplittedGame);
                // pr($aGameDatas);
                $iGameNum               = count($aSplittedGame);
                $aSplittedGame          = array_flip($aSplittedGame);
                $aBillBetContent        = array_intersect_key($aGames, $aSplittedGame);
                $aBillBetOdds           = $iGameNum == 1 ? $this->compileBillOddDataSingle($aBillBetContent) : $this->compileOddBetArray($aBillBetContent);
                $aBillBetContentDisplay = array_intersect_key($aBetOddsForDisplay, $aSplittedGame);
                $aBillBetOddsDisplay    = $iGameNum == 1 ? $this->compileBillOddDataSingle($aBillBetContentDisplay, true) : $this->compileOddBetArray($aBillBetContentDisplay, true);
                // pr($this->sn);
                // pr($aBillBetOdds);
                // pr($aBillBetContentDisplay);
                // exit;
                // $oGames = Game::whereIn('bn', $aSplittedGame)->get();
                // pr($oGames->toArray());exit;
                $iIndex = $this->gt_id == 2 ? $iGameNum + 42 : $iGameNum;
                $iMethodId = $iRankMethodId ? $iRankMethodId : $aFreeGates[$iIndex];

                foreach ($aBillBetOdds as $key => $aBillBetOdd) {
                    $iBetsNum = 1;
                    $fAmount  = $this->multiple * static::PRICE;
                    $sBillSn  = generateSerialNumber('B');
                    $sBillContent        = json_encode($aBillBetOdd);
                    $sBillContentDisplay = json_encode($aBillBetOddsDisplay[$key]);
                    $sContentSign = md5(implode(',', [$this->gt_id, $this->bp_id, $this->user_id, $this->program_sn, $sBillContent]));
                    $iGateType = $aFreeGateTypes[$iIndex];
                    $aBillData = [
                        'program_id'       => $this->id,
                        'program_sn'       => $this->sn,
//                        'bp_id'            => $this->bp_id,
//                        'bp_name'          => $this->bp_name,
                        'sn'               => $sBillSn,
                        'gt_id'            => $this->gt_id,
                        'game_type'        => $this->game_type,
                        'user_id'          => $this->user_id,
                        'username'         => $this->username,
                        'is_tester'        => $this->is_tester,
                        'is_rank'          => $this->is_rank,
                        'account_id'       => $this->account_id,
                        'multiple'         => $this->multiple,
                        'gate_type'        => $iGateType,
                        'method_id'        => $iMethodId,
                        'bet_num'          => $iBetsNum,
                        'game_num'         => $iGameNum,
                        'amount'           => $fAmount,
                        'bet_content'      => $sBillContent,
                        'bet_odds_display' => $sBillContentDisplay,
                        'coefficient'      => $this->coefficient,
                        'bought_at'        => $this->bought_at,
                        'content_sign'     => $sContentSign,
                        'created_at'       => Carbon::now()->toDateTimeString(),
                        'updated_at'       => Carbon::now()->toDateTimeString(),
                    ];

                    $oBill = Bill::create($aBillData);
                    // pr($oBill->getValidationErrorString());exit;
                    // 注单赛事关联数据
                    $aBillGameExtraDatas = [];
                    foreach ($aGameDatas as $key => $sGameBn) {
                        $aBillGameExtraDatas[$key] = $oBill->compileBillGamesData($sGameBn);
                    }
                    // foreach ($oGames as $oGame) {
                    //     $aBillGameExtraDatas[$oGame->id] = $oBill->compileBillGamesData($oGame->bn);
                    // }
                    // pr($aBillGameExtraDatas);exit;
                    $oBill->games()->sync($aBillGameExtraDatas);
                    // $aBillOddContents[] = $aBillData;
                    // $aBillBetGames[$sBillSn] = array_keys($aBillBetOdd);
                }
            }
            // $bSucc = DB::table('bills')->insert($aBillOddContents);
            // unset($aBillOddContents);
        }
        // $oBills = Bill::where('program_id', $this->id)->get();
        // if ($bSucc && $oBills->count()) {
        //     return $oBills;
        // }
        return $bSucc;
    }

    public function rebuildBetData(& $aBetData, & $aBetContent, & $aProgramGameExtraData = [], & $aBetOddsForDisplay = [], & $aFailedDesc = 0) {
        // if (!$aBetData['bet_content']) return false;
        // $aBetContent = json_decode($aBetData['bet_content'], true);
        $bSucc = true;
        $sErrorCode = null;
        $aGameBns = array_keys($aBetContent['games']);
        $aDan = $aBetContent['dan'];
        $aGates = $this->getGates($aBetContent['gate']);
        $bSingle = in_array(1, $aGates);
        $aQueryParam = [
            'bn' => ['in', $aGameBns],
            'status' => ['=', Game::STATUS_SALE_ON]
        ];
        $oGames = Game::doWhere($aQueryParam)->get();
        if ($oGames->count() != count($aGameBns)) {
            $sErrorCode = -105;
            $aFailedDesc[] = __(Config::get('custom-code.' . $sErrorCode));
            return false; // 如果数据库中记录数和传入的场次数不一致，返回错误(可能部分赛事已截止销售)
        }
        // TODO 如果任意一场赛事的停售时间超过了当前时间，则返回错误，不确定销售截止时间遇到延期的情况是否能及时更新
        // foreach ($oGames as $oGame) {
        //     // $sNow = Carbon::now()->toDateTimeString();
        //     if (Carbon::now()->gt(Carbon::parse($oGame->sale_stop_at))) {
        //         $bSucc = false;
        //         break;
        //     }
        // }
        // if (!$bSucc) return $bSucc;
        // pr($oGames->count());exit;
        $aOddsFromDB = [];
        // $aBetOddsForDisplay = [];
        // $aOddNames = WayOdd::where('gt_id', $this->gt_id)->get()->pluck('name', 'identity')->toArray();
        $aOddNames = WayOdd::getWayOddNameOrConnectNumByGameType($this->gt_id, 1);
        // pr($aOddNames);
        foreach ($oGames as $oGame) {
            $oOdds = $oGame->odds;
            $aOddItemsFromDB = $oOdds->pluck('odd', 'wo_identity')->toArray();
            $aOddItemsSingle = $oOdds->pluck('single', 'wo_identity')->toArray();
            $aOddItems = array_unique($aBetContent['games'][$oGame->bn]);
            $aOddsFromDB[$oGame->bn] = [];
            $aBetOddsForDisplay[$oGame->bn] = [];
            $bEssential = intval(in_array($oGame->bn, $aDan));
            foreach ($aOddItems as $sOdd) {
                list($sOddIdentity, $iOdd) = explode('_', $sOdd);
                if (!array_key_exists($sOddIdentity, $aOddItemsFromDB) || ($bSingle && !intval($aOddItemsSingle[$sOddIdentity]))) { // 没有赔率数据，或者选择了单关但是注单数据有非单关的赔率单元
                    $bSucc = false;
                    $sErrorCode = -106;
                    $aFailedDesc[] = __(Config::get('custom-code.' . $sErrorCode));
                    break 2;
                }
                $iRealOdd = $aOddItemsFromDB[$sOddIdentity];
                $aOddsFromDB[$oGame->bn][] = $sOddIdentity . '_' . $iRealOdd;
                $aBetOddsForDisplay[$oGame->bn][$sOddIdentity] = $aOddNames[$sOddIdentity] . '[' . number_format($iRealOdd, 2) . ']';
                // TIP 调整注单和比赛的关联方式
                $aProgramGameExtraData[$oGame->id] = $this->compileProgramGameData($oGame->bn);
                // $aOddDatas[] = $this->compileBillOddData($oBusinessPartner, $aBetData, $bEssential, $oGame, $sOddIdentity, $iRealOdd);
            }
        }
        // pr($aProgramGameExtraData);
        // pr($aBetOddsForDisplay);
        // exit;
        if (!$bSucc) return false;
        $aBetContent['games']         = $aOddsFromDB;
        $aBetData['bet_content']      = json_encode($aBetContent);
        $aBetData['bet_odds_display'] = json_encode($aBetOddsForDisplay);
        return true;
    }
    /**
     * [updateWinPrize 计奖]
     * @return [Boolean] [成功/失败]
     */
    // public function updateWinPrize(& $aReturnData = []) {
    //     $bSucc = true;
    //     $iPrize = 0;
    //     $oFinishedGames = $this->games()->whereIn('games.status', [Game::STATUS_FINISHED, Game::STATUS_CANCELED])->get();
    //     // pr($oFinishedGames->toArray());exit;
    //     if ($this->status != self::STATUS_TICKET_PRINTED) {
    //         $bSucc = false;
    //         $sResponseCode = -305;
    //     }
    //     if ($oFinishedGames->count() != $this->game_num) {
    //         $bSucc = false;
    //         $sResponseCode = -200;
    //     }
    //     // if ($bSucc && (!$oBillGames = $this->games())) {
    //     //     $bSucc = false;
    //     //     $sResponseCode = -201;
    //     // }
    //     if ($bSucc) {
    //         $iPrize = $this->calculatePrize($oFinishedGames);
    //         if (!is_numeric($iPrize)) {
    //             $bSucc = false;
    //             $sResponseCode = -202;
    //         }
    //     }
    //     // pr($iPrize);exit;
    //     if ($bSucc) {
    //         $data = [
    //             'finished_game_num' => $this->game_num,
    //             'status'            => ([self::STATUS_LOSS, self::STATUS_WIN][intval($iPrize > 0)]),
    //             'prize'             => $iPrize,
    //             'status_count'      => self::STATUS_COUNT_FINISH,
    //             'counted_at'        => Carbon::now()->toDateTimeString()
    //         ];
    //         // pr($data);exit;
    //         $this->fill($data);
    //         // pr($this->toArray());
    //         if (!$bSucc = $this->save()) {
    //             // pr($this->getValidationErrorString());exit;
    //             $sResponseCode = -203;
    //         } else {
    //             $sResponseCode = 198;
    //         }
    //     }
    //     $statData = [
    //         'type'       => 'turnover',
    //         'user_id'    => $this->user_id,
    //         'bp_id'      => $this->bp_id,
    //         'amount'     => $this->amount,
    //         'date'       => $this->counted_at,
    //         // 'lottery_id' => $this->gt_id,
    //     ];
    //     BaseTask::addTask('StatUpdateProfit', $statData, 'stat');
    //     if ($iPrize > 0) {
    //         BaseTask::addTask('SendPrize', ['bills' => [$this->id]], 'prize');
    //     }
    //     $aReturnData = ['conding' => $sResponseCode, 'msg' => __(Config::get('custom-code.bill.' . $sResponseCode)), 'data' => $this->toArray()];
    //     return $bSucc;
    // }
    // 计算奖金
    // public function calculatePrize($oFinishedGames) {
    //     $aFinishedGameWonOdds = []; // 已结束和取消的赛事
    //     $aCanceledGames = []; // 取消的赛事
    //     // 获取该注单所选赛事的所有赛果
    //     foreach ($oFinishedGames as $oGame) {
    //         $aFinishedGameWonOdds[$oGame->bn] = $oGame->calculateWonResults();
    //         if ($oGame->status == Game::STATUS_CANCELED) $aCanceledGames[] = $oGame->bn;
    //     }
    //     // pr($aFinishedGameWonOdds);
    //     $aBetContent = json_decode($this->bet_content, true);
    //     $aGates = $this->getGates($aBetContent['gate']);
    //     // pr($aGates);
    //     // 根据过关方式拆分赛事组合
    //     $aSplittedGames = $this->splitGames($aBetContent['games'], $aGates, $aBetContent['dan']);
    //     // pr($aSplittedGames);exit;
    //     $aOddNames = [];
    //     $aOddValues = [];
    //     $aWonGameOddNames = []; // 该注单中和赛果匹配上的中奖的赔率单元编码
    //     $aWonGameOddValues = []; // 中奖的赔率数据
    //     foreach ($aBetContent['games'] as $key => $aOdds) {
    //         $aOddNames[$key] = [];
    //         foreach ($aOdds as $sOdd) {
    //             $aNameAndOdd = explode('_', $sOdd);
    //             $aOddNames[$key][] = $aNameAndOdd[0];
    //             $aOddValues[$key.'_'.$aNameAndOdd[0]] = $aNameAndOdd[1];
    //         }
    //         // 如果赛事取消，则所有投注的赔率单元均算中奖，单式赔率为1.00
    //         if ($aCanceledGames && in_array($key, $aCanceledGames)) {
    //             $aWonOddNames = $aOddNames[$key];
    //         } else {
    //             $aWonOddNames = array_intersect($aFinishedGameWonOdds[$key], $aOddNames[$key]);
    //         }
    //         if ($aWonOddNames) {
    //             $aWonGameOddNames[$key] = $aWonOddNames;
    //             foreach ($aWonOddNames as $sWonOddName) {
    //                 $aWonGameOddValues[$key][] = in_array($key, $aCanceledGames) ? 1.00 : $aOddValues[$key.'_'.$sWonOddName];
    //             }
    //         }
    //     }
    //     // pr($aWonGameOddValues);
    //     // pr($aWonGameOddNames);
    //     // exit;
    //     $aFiltedWonGameOdds = []; // 拆分后的已中奖的赔率组合
    //     foreach ($aSplittedGames as $key => $aGameBns) {
    //         $bWon = true;
    //         foreach ($aGameBns as $sGameBn) {
    //             if (!array_key_exists($sGameBn, $aWonGameOddNames)) {
    //                 $bWon = false;
    //                 continue 2;
    //             }
    //         }
    //         if ($bWon) {
    //             if (!isset($aWonGameOddValues[$key])) $aWonGameOddValues[$key] = [];
    //             foreach ($aGameBns as $sGameBn) {
    //                 $aFiltedWonGameOdds[$key][] = $aWonGameOddValues[$sGameBn];
    //             }
    //         }
    //     }
    //     $iPrize = 0; // 奖金
    //     $iFloorPrize = 0; // 舍掉的小数点2位之后的金额
    //     // pr($aFiltedWonGameOdds);
    //     foreach ($aFiltedWonGameOdds as $aWonGameOddValue) {
    //         if (count($aWonGameOddValue) > 1) $aSplittedGameOdds = descartes($aWonGameOddValue);
    //         else if (count($aWonGameOddValue) == 1) {
    //             $aSplittedGameOdds = [];
    //             foreach ($aWonGameOddValue[0] as $key => $value) {
    //                 $aSplittedGameOdds[] = [$value];
    //             }
    //         }
    //         // pr($aSplittedGameOdds);
    //         foreach ($aSplittedGameOdds as $aWonOddValue) {
    //             $iGateNum = count($aWonOddValue);
    //             if ($iGateNum < 1) continue;
    //             $iMaxGatePrize = $iGateNum < 7 ? SysConfig::get('max_prize_gate_' . $iGateNum) : SysConfig::get('bet_max_prize');
    //             $iMultpile = $this->multiple < 99 ? 1 : $this->multiple % 99 + 1;
    //             $iRealPrize = min($iMaxGatePrize * $iMultpile, array_product($aWonOddValue) * $this->multiple * static::PRICE);
    //             $iSFloorPrize = floor($iRealPrize * 100) / 100;
    //             $iFloorPrize += $iRealPrize - $iSFloorPrize;
    //             $iPrize += $iSFloorPrize;
    //         }
    //     }
    //     // pr($iFloorPrize);
    //     return $iPrize;
    // }

    public function compileProgramGameData($sGameBn) {
        $data = [
            'bp_id'      => $this->bp_id,
            'bp_name'    => $this->bp_name,
            'gt_id'      => $this->gt_id,
            'game_bn'    => $sGameBn,
            'program_sn' => $this->sn,
            'user_id'    => $this->user_id,
            'username'   => $this->username,
            'is_tester'  => $this->is_tester,
        ];
        return $data;
    }
    /**
     * Discard
     * [compileBillOddData 拼装注单所选赔率单元的数据]
     * @param  [type] $oBusinessPartner [description]
     * @param  [type] $aBillData        [description]
     * @param  [type] $bEssential       [description]
     * @param  [type] $oGame            [description]
     * @param  [type] $sOddIdentity     [description]
     * @param  [type] $iOdd             [description]
     * @return [type]                   [description]
     */
    private function compileBillOddData($oBusinessPartner, $aBillData, $bEssential, $oGame, $sOddIdentity, $iOdd) {
        $data = [
            'bp_id'       => $oBusinessPartner->id,
            'bp_name'     => $oBusinessPartner->name,
            'gt_id'       => $aBillData['gt_id'],
            'g_id'        => $oGame->id,
            'g_bn'        => $oGame->bn,
            'bill_id'     => $this->id,
            'bill_sn'     => $this->sn,
            'user_id'     => $aBillData['user_id'],
            'username'    => $aBillData['username'],
            'is_tester'   => $aBillData['is_tester'],
            'played_at'   => $oGame->played_at,
            'essential'   => $bEssential,
            'wo_identity' => $sOddIdentity,
            'odd'         => $iOdd,
            'status'      => Game::STATUS_SALE_ON,
            'bought_at'   => Carbon::now()->toDateTimeString(),
        ];
        return $data;
    }

    /**
     * [caculateOrderCount 计算注单数]
     * @param  [Array] $aGames [场次数据数组]
     * @param  [Array] $aGates [串关方式数组]
     * @param  [Array] $aDan   [胆码场次序号数组]
     * @return [Integer]       [注单数]
     */
    private function caculateOrderCount($aGames, $aGates, $aDan) {
        $aSplittedGames = $this->splitGames($aGames, $aGates, $aDan);
        $iBetsNum = $this->calculateBetsNum($aSplittedGames, $aGames);
        return $iBetsNum;
    }

    public function splitGames($aGames, $aGates, $aDan) {
        $aFiltedGames          = [];
        $aGameBns              = array_keys($aGames); // 场次序号
        $iGamesCount           = count($aGameBns); // 场次数
        $iGatesCount           = count($aGates); // 串关方式数量
        $iDanCount             = count($aDan); // 胆码数
        $aGamesWithoutDan      = array_diff($aGameBns, $aDan); // 除胆码外的其他比赛场次的序号
        $iGamesWithoutDanCount = count($aGamesWithoutDan);
        $aGamesCombinated = [];
        foreach ($aGates as $iGate) {
            $n = $iGate - $iDanCount; // 剔除胆码场次后，从剩余场次中选择 (串关场次数 - 胆码场次数) 的场次n
            if ($n == $iGamesWithoutDanCount) {
                $aFiltedGames[] = $aGamesWithoutDan;
            } else {
                $aGamesCombinated = [];
                combination($aGamesWithoutDan, $n, $aGamesCombinated);
                $aFiltedGames = empty($aFiltedGames) ? $aGamesCombinated : array_merge($aFiltedGames, $aGamesCombinated);
            }
        }
        // 生成完整的n串1的场次序号二维数组
        $aSplittedGames = array_map(function($item) use ($aDan) {
            return array_merge($item, $aDan);
        }, $aFiltedGames);
        return $aSplittedGames;
    }
    private function calculateBetsNum($aSplittedGames, $aGames) {
        $iBetsNum = 0; // 注单数
        foreach ($aSplittedGames as $aGame) {
            $iCount = 1;
            foreach ($aGame as $sGameBn) {
                $iCount *= count($aGames[$sGameBn]);
            }
            $iBetsNum += $iCount;
        }
        return $iBetsNum;
    }

    /**
     * 加奖金发送锁
     * @return bool
     */
//     public function lockForSendPrize() {
//         $aConditions = [
//             'id' => ['=', $this->id],
//             'status_prize' => ['=', self::STATUS_PRIZE_WAITING]
//         ];
//         $data = [
//             'locked_prize' => $iThreadId = DbTool::getDbThreadId(),
//             'status_prize' => self::STATUS_PRIZE_SENDING
//         ];
//         if ($bSucc = $this->strictUpdate($aConditions, $data)) {
//             $this->status_prize = self::STATUS_PRIZE_SENDING;
//             $this->locked_prize = $iThreadId;
// //            $this->deleteCache($this->id);
//         }
//         return $bSucc;
//     }

    /**
     * 解奖金发送锁
     * @return bool
     */
    // public function unlockForSendPrize() {
    //     $aConditions = [
    //         'id' => ['=', $this->id],
    //         'status_prize' => ['=', self::STATUS_PRIZE_SENDING],
    //         'locked_prize' => $this->locked_prize
    //     ];
    //     $data = [
    //         'locked_prize' => 0,
    //         'status_prize' => self::STATUS_PRIZE_WAITING
    //     ];
    //     $bSucc = $this->strictUpdate($aConditions, $data);
    //     return $bSucc;
    // }

    /**
     * 设置派奖状态为已完成
     * @return bool
     */
    // public function setPrizeSentStatus() {
    //     $aConditions = [
    //         'id'           => ['=', $this->id],
    //         'status'       => ['=', self::STATUS_WIN],
    //         'status_prize' => ['in', [self::STATUS_PRIZE_SENDING]]
    //     ];
    //     $data = [
    //         'status_prize'  => self::STATUS_PRIZE_FINISH,
    //         'locked_prize'  => 0,
    //         'prize_sent_at' => Carbon::now()->toDateTimeString(),
    //     ];
    //     if (!$bSucc = $this->strictUpdate($aConditions, $data)) {
    //         $this->prize_sent_at = null;
    //         $this->status_prize = $this->original['status_prize'];
    //     }
    //     return $bSucc;
    // }

    # 判断状态流是否合法
    public function updateValidStatus($iToStatus, $aExtraData = []) {
        $bSucc = false;
        if (array_key_exists($iToStatus, static::$statusAllowedFlow) && in_array($this->status, static::$statusAllowedFlow[$iToStatus])) {
            $aExtraData['status'] = $iToStatus;

            // pr(self::where('id', $this->id)->lockForUpdate()->get()->toArray());exit;
            $bSucc = self::where('id', $this->id)->lockForUpdate()->update($aExtraData);
        }
        return $bSucc;
    }

    // public static function getUnSentPrizeBills($aIds) {
    //     $aConditions = [
    //         'id'           => ['in', $aIds],
    //         'status'       => ['=', self::STATUS_WIN],
    //         'status_prize' => ['in', [self::STATUS_PRIZE_WAITING, self::STATUS_PRIZE_PART]],
    //     ];
    //     return self::complexWhere($aConditions)->orderBy('id', 'asc')->get();
    // }
}
