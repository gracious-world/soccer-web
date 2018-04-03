<?php

namespace App\Models\Bet;

use App\Models\BaseModel;
use App\Models\Game\Game;
use App\Models\Game\Method;
use App\Models\Game\WayOdd;
use App\Models\Bet\Program;
use App\Models\Bet\BillGame;
use App\Models\Func\SysConfig;
use App\Models\Customer\PointJournal;
use App\Models\Fund\TransactionType;
use App\Models\Fund\Transaction;
use App\Models\Fund\Account;
use App\Models\Customer\Wallet;
use App\Models\Basic\BusinessPartner;
use App\Models\User\User;

use App\Models\BaseTask;

use Carbon;
use Config;
use DbTool;
use DB;

class Bill extends BaseModel {
    static $cacheLevel = self::CACHE_LEVEL_NONE;
    protected static $cacheMinutes = 0;

    protected $table = 'bills';
    public static $resourceName = 'Bill';

    public static $titleColumn = 'sn';
    // public static $amountAccuracy = 2;
    protected $appends = ['status_display', 'prize_display', 'amount_display', 'gate_type_display'];


    const PRICE = 2.00; // 每注金额

    const STATUS_WAITING         = 0; // 等待出票
    const STATUS_TICKET_FAILED   = 1; // 出票失败
    const STATUS_TICKET_PRINTED  = 2; // 出票成功
    const STATUS_CANCELED        = 3; // 已撤销
    const STATUS_SYSTEM_CANCELED = 4; // 系统撤销
    const STATUS_LOSS            = 5; // 未中奖
    const STATUS_WIN             = 6; // 已中奖
    const STATUS_AWARD           = 7; // 已派奖

    const STATUS_COUNT_WAITING = 0; //等待计奖
    const STATUS_COUNT_SENDING = 1; //计奖中
    const STATUS_COUNT_PART    = 2; //部分计奖
    const STATUS_COUNT_FINISH  = 3; //计奖完成

    const STATUS_PRIZE_WAITING = 0; //等待派奖
    const STATUS_PRIZE_SENDING = 1; //派奖中
    const STATUS_PRIZE_PART    = 2; //部分派奖
    const STATUS_PRIZE_FINISH  = 3; //派奖完成

    const ERRNO_LOCK_FAILED        = -211;
    // const ERRNO_PRIZE_SENDING      = -981;
    // const ERRNO_COMMISSION_SENDING = -982;
    const ERRNO_ERR_PRE_STATUS = -208;
    const ERRNO_ERR_ON_SAVING = -209;

    public static $status = [
        self::STATUS_WAITING         => 'waiting',
        self::STATUS_TICKET_PRINTED  => 'ticket-printed',
        self::STATUS_TICKET_FAILED   => 'ticket-failed',
        self::STATUS_CANCELED        => 'canceled',
        self::STATUS_LOSS            => 'loss',
        self::STATUS_WIN             => 'win',
        self::STATUS_AWARD           => 'award',
        self::STATUS_SYSTEM_CANCELED => 'system-canceled',
    ];
    # 允许的状态流
    public static $statusAllowedFlow = [
        self::STATUS_TICKET_PRINTED  => [self::STATUS_WAITING],
        self::STATUS_TICKET_FAILED   => [self::STATUS_WAITING],
        self::STATUS_CANCELED        => [self::STATUS_WAITING, self::STATUS_TICKET_PRINTED],
        self::STATUS_LOSS            => [self::STATUS_TICKET_PRINTED],
        self::STATUS_WIN             => [self::STATUS_TICKET_PRINTED],
        self::STATUS_AWARD           => [self::STATUS_WIN],
        self::STATUS_SYSTEM_CANCELED => [self::STATUS_WAITING, self::STATUS_TICKET_PRINTED, ] // TODO self::STATUS_LOSS, self::STATUS_WIN, self::STATUS_AWARD
    ];

    public $orderColumns = ['bought_at' => 'desc'];
    public static $columnForList = [
        'program_sn',
        'bp_name',
        // 'bp_bill_sn',
        'sn',
        'game_type',
        'username',
        // 'is_tester',
        // 'first_played_at',
        // 'first_game_bn',
        'multiple',
        // 'bet_num',
        // 'game_num',
        // 'finished_game_num',
        'amount',
        'gate_type',
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
        'prize_sent_at'
    ];
    public static $mainParamColumn = 'wo_identity';

    public static $rules = [
        'program_id'          => 'required|integer',
        'program_sn'          => 'required|string|max:32',
//        'bp_id'               => 'required|integer',
//        'bp_name'             => 'string|max:50',
        // 'bp_bill_sn'          => 'required|string|max:32',
        'sn'                  => 'required|string|max:32',
        'gt_id'               => 'required|integer',
        'game_type'           => 'string|max:50',
        'user_id'             => 'required|integer',
        'username'            => 'required|alpha_dash|between:6,16',
        'is_tester'           => 'boolean',
        // 'forefather_user_ids' => 'string',
        'account_id'          => 'integer',
        // 'first_played_at'     => 'date',
        // 'first_game_bn'       => 'required|string',
        'multiple'            => 'required|integer|max:500000',
        'bet_num'             => 'required|integer',
        'game_num'            => 'integer',
        'finished_game_num'   => 'integer',
        'amount'              => 'required|numeric',
        'gate_type'           => 'required|integer',
        'method_id'           => 'integer',
        'bet_content'         => 'required|string',
        'bet_odds_display'    => 'String',
        'coefficient'         => 'required|numeric',
        'content_sign'        => 'required|string|min:32',
        'prize'               => 'numeric',
        'status'              => 'digits_between:0,7',
        'status_count'        => 'digits_between:0,3',
        'status_prize'        => 'digits_between:0,3',
        'locked_count'        => 'integer',
        'locked_prize'        => 'integer',
        'canceled_by'         => 'alpha_dash|between:4,32',
        'bought_at'           => 'date',
        'canceled_at'         => 'date',
        'counted_at'          => 'date',
        'prize_sent_at'       => 'date',
    ];
    protected $fillable = [
        'program_id',
        'program_sn',
        'bp_id',
        'bp_name',
        // 'bp_bill_sn',
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
        'game_num',
        'finished_game_num',
        'amount',
        'gate_type',
        'method_id',
        'bet_content',
        'bet_odds_display',
        'coefficient',
        'content_sign',
        'prize',
        'status',
        'status_count',
        'status_prize',
        'locked_count',
        'locked_prize',
        'canceled_by',
        'bought_at',
        'canceled_at',
        'counted_at',
        'prize_sent_at',
    ];

    public static $htmlNumberColumns = [
        'amount' => 2,
        'prize'  => 2,
    ];

    public static $htmlSelectColumns = [
        // 'gt_id' => 'aGameTypes',
        'status'=> 'aStatus',
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

    public static $withAppends = false;

    protected function getArrayableAppends() {
        if(!self::$withAppends){
            return [];
        }
        return parent::getArrayableAppends();
    }

    public function games() {
        return $this->belongsToMany('App\Models\Game\Game', 'bill_game')->withPivot(BillGame::$columnsForPivot)->withTimestamps();
    }

    public function program() {
        return $this->belongsTo('App\Models\Bet\Program', 'program_id');
    }

    public function getAmountDisplayAttribute() {
        return number_format($this->amount, 2, '.', ',');
    }
    public function getPrizeDisplayAttribute() {
        return number_format($this->prize, 2, '.', ',');
    }
    public function getStatusDisplayAttribute() {
        return is_numeric($this->status) ? __('_bill.' . static::$status[$this->status]) : '';
    }
    public function getGateTypeDisplayAttribute() {
        return is_numeric($this->gate_type) ? __('_method.' . Method::$types[$this->gate_type]) : '';
    }
    public function getMethodIdsAttribute() {
        $aBetContent = json_decode($this->bet_content, true);
        $aGateIdentities = $aBetContent['gate'];
        $aMethodIds = Method::whereIn('identity', $aGateIdentities)->lists('id')->toArray();
        return $aMethodIds;
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
        $this->prize_sent_at != '0000-00-00 00:00:00' or $this->prize_sent_at = null;
        return parent::beforeValidate();
    }

    /**
     * 验证是否超出最大串关次数
     * @param $iMaxGate
     * @param $aOriginalGates
     * @param $aGates
     * @return bool
     */
    private function verifyGate($iMaxGate, $aOriginalGates, $aGates) {
        if ($iMaxGate < max($aGates)) return false; // 如果拆分后的最大过关场次大于根据所选赔率单元计算的最大过关场次，返回false；
        $sGates = implode(',', $aOriginalGates) . ',';
        $iGatesNum = count($aOriginalGates);
        $bSimpleGateType = strpos($sGates, 'x1,') > -1; // 是否自由过关
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

    private function verifyMaxGate($aBillContent) {
        // if (intavl($aBillContent['force_danguan'])) return 1;
        $aOddMaxGates = WayOdd::all()->pluck('max_connect_games', 'identity')->toArray();
        $aOddIdentities = [];
        $iMaxGate = 8;
        foreach ($aBillContent['games'] as $key => $aOdds) {
            foreach ($aOdds as $item) {
                $sOddIdentity = explode('_', $item)[0];
                $iMaxGate = min($iMaxGate, $aOddMaxGates[$sOddIdentity]);
            }
        }
        // 根据所选赔率单元，判断实际允许的最大串关场次，如果和传入的参数不符，返回false；
        if (!$iMaxGate || $iMaxGate != $aBillContent['max_gate']) return false;
        return $iMaxGate;
    }

    public function getGates($aOriginalGates) {
        $oMethods = Method::doWhere(['gt_id' => ['=' => $this->gt_id], 'identity' => ['in', $aOriginalGates]])->get();
        $aGates = $oMethods->count() == 1 ? explode(',', $oMethods[0]->combination) : $oMethods->pluck('combination')->toArray();
        return $aGates;
    }

    /**
     * [verifyBillData 验证注单数据]
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
    public function verifyBillData( & $aBillContent) {
        if (!$iMaxGate = $this->verifyMaxGate($aBillContent)) return false;
        $aBillContent['max_gate'] = $iMaxGate;
        if (!$aGates = $this->getGates($aBillContent['gate'])) return false;
        if (!array_key_exists('gate', $aBillContent) || !$this->verifyGate($iMaxGate, $aBillContent['gate'], $aGates)) return false;
        $iBetsNum = $this->caculateOrderCount($aBillContent['games'], $aGates, $aBillContent['dan']);
        if ($iBetsNum != $aBillContent['bet_num']) return false;
        if ($iBetsNum * $aBillContent['multiple'] * self::PRICE != $aBillContent['amount']) return false;
        return true;
    }

    public function updateBetData($oBusinessPartner, & $aBillGameExtraData = [], & $aFailedDesc = []) {
        $bSucc               = true;
        $sErrorCode          = null;
        $aBillContent        = json_decode($this->bet_content, true);
        $aBillContentDisplay = json_decode($this->bet_odds_display, true);
        $aGameBns            = array_keys($aBillContent);
        $oOnSaleBillGames    = $this->games()->get(); // ->where('games.status', Game::STATUS_SALE_ON)->get(); // TIP 可能存在赛事已截止销售，但是出票队列还未执行的情况
        if ($oOnSaleBillGames->count() != count($aBillContent)) {
            $sErrorCode = -105;
            $aFailedDesc[] = __(Config::get('custom-code.' . $sErrorCode));
            return false; // 如果数据库中记录数和传入的场次数不一致，返回错误(可能部分赛事已截止销售)
        }
        $aOddNames = WayOdd::getWayOddNameOrConnectNumByGameType($this->gt_id, 1);
        $aNewBetContent = $aNewBetOddsForDisplay = [];
        foreach ($oOnSaleBillGames as $oGame) {
            $oOdds                                            = $oGame->odds;
            $aLatestOdds                                      = $oOdds->pluck('odd', 'wo_identity')->toArray();
            $aOddItemsSingle                                  = $oOdds->pluck('single', 'wo_identity')->toArray();
            list($sOddIdentity, $iOddValue)                   = explode('_', $aBillContent[$oGame->bn][0]);
            if (!array_key_exists($sOddIdentity, $aLatestOdds) || ($this->gate_type == 0 && !intval($aOddItemsSingle[$sOddIdentity]))) { // 没有赔率数据，或者选择了单关但是注单数据有非单关的赔率单元
                $bSucc = false;
                $sErrorCode = -106;
                $aFailedDesc[] = __(Config::get('custom-code.' . $sErrorCode));
                break;
            }
            $sOddName                                         = $aOddNames[$sOddIdentity];
            $aNewBetContent[$oGame->bn]                       = [$sOddIdentity . '_' . $aLatestOdds[$sOddIdentity]];
            $aNewBetContentDisplay[$oGame->bn][$sOddIdentity] = $sOddName . '[' . number_format($aLatestOdds[$sOddIdentity], 2) . ']';
            // $aBillGameExtraData[$oGame->id] = $this->compileBillGamesData($oBusinessPartner, $aBillData, $oGame);
        }
        if (!$bSucc) return false;
        $aUpdateData = [
            'bet_content' =>json_encode($aNewBetContent),
            'bet_odds_display' => json_encode($aNewBetContentDisplay),
        ];
        $bSucc = $this->update($aUpdateData);
        return $bSucc;
    }

    public function rebuildBillData($oBusinessPartner, & $aBillData, & $aBillContent, & $aBillGameExtraData = [], & $aBetOddsForDisplay = [], & $aFailedDesc = 0) {
        // if (!$aBillData['bet_content']) return false;
        // $aBillContent = json_decode($aBillData['bet_content'], true);
        $bSucc = true;
        $sErrorCode = null;
        $aGameBns = array_keys($aBillContent['games']);
        $aDan = $aBillContent['dan'];
        $aGates = $this->getGates($aBillContent['gate']);
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
        $aOddNames = WayOdd::where('gt_id', $this->gt_id)->get()->pluck('name', 'identity')->toArray();
        // pr($aOddNames);
        foreach ($oGames as $oGame) {
            $oOdds = $oGame->odds;
            $aOddItemsFromDB = $oOdds->pluck('odd', 'wo_identity')->toArray();
            $aOddItemsSingle = $oOdds->pluck('single', 'wo_identity')->toArray();
            $aOddItems = $aBillContent['games'][$oGame->bn];
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
                $aBillGameExtraData[$oGame->id] = $this->compileBillGamesData($oGame->bn);
                // $aOddDatas[] = $this->compileBillOddData($oBusinessPartner, $aBillData, $bEssential, $oGame, $sOddIdentity, $iRealOdd);
            }
        }
        // pr($aBillGameExtraData);
        // pr($aBetOddsForDisplay);
        // exit;
        if (!$bSucc) return false;
        $aBillContent['games']         = $aOddsFromDB;
        $aBillData['bet_content']      = json_encode($aBillContent);
        $aBillData['bet_odds_display'] = json_encode($aBetOddsForDisplay);
        return true;
    }
    /**
     * [updateWinPrize 计奖]
     * @return [Boolean] [成功/失败]
     */
    public function updateWinPrize(& $aReturnData = []) {
        $bSucc = true;
        $sErrMsg = '';
        $iPrize = 0;
        $oFinishedGames = $this->games()->whereIn('games.status', [Game::STATUS_FINISHED, Game::STATUS_CANCELED])->get();
        // $oFinishedGames = $this->games()->get();
        // pr($oFinishedGames->toArray());exit;
        // $oFinishedGames[0]->status = Game::STATUS_CANCELED;
        if ($this->status != self::STATUS_TICKET_PRINTED) {
            $bSucc = false;
            $sResponseCode = -305;
        }
        if ($bSucc && $oFinishedGames->count() != $this->game_num) {
            $bSucc = false;
            $sResponseCode = -200;
        }
        // $oFinishedGames[0]->status = Game::STATUS_CANCELED;
        // 单关注单且对应赛事已取消，做撤单处理
        if ($bSucc && $this->game_num == 1 && $oFinishedGames[0]->status == Game::STATUS_CANCELED) {
            return $this->cancelBill() > 0;
        }
        if ($bSucc) {
            $iPrize = $this->is_rank ? $this->calculateRankBillPrize($oFinishedGames[0]) : $this->calculatePrizeNew($oFinishedGames);
            if (!is_numeric($iPrize)) {
                $bSucc = false;
                $sResponseCode = -202;
            }
        }
        $iTax = 0;
        $oCustomer = BusinessPartner::find($this->bp_id);
        if ($bSucc && $oCustomer && $oCustomer->taxable) {
            $iTaxPercent = Config::get('custom-sysconfig.tax-percent');
            $iTaxLimit = Config::get('custom-sysconfig.tax-limit');
            if ($iPrize / $this->multiple >= $iTaxLimit) {
                $iPrize *= (1 - $iTaxPercent);
                $iTax = $iPrize * $iTaxPercent;
            }
        }
        if ($bSucc) {
            $data = [
                'finished_game_num' => $this->game_num,
                'status'            => ([self::STATUS_LOSS, self::STATUS_WIN][intval($iPrize > 0)]),
                'prize'             => $iPrize,
                'tax'               => $iTax,
                'status_count'      => self::STATUS_COUNT_FINISH,
                'counted_at'        => Carbon::now()->toDateTimeString()
            ];
            // pr($data);exit;
            $this->fill($data);
            // pr($this->toArray());
            if (!$bSucc = $this->save()) {
                $sErrMsg = $this->getValidationErrorString();
                // pr($this->getValidationErrorString());exit;
                $sResponseCode = -203;
            } else {
                $sResponseCode = 198;
            }
        }
        // TODO 增减可提额和不可提现额
        if ($oAccount = User::find($this->user_id)->account) {
            $fProhibitAmountReduceSpeed = SysConfig::readValue('prohibit_amount_reduce_speed');
            $fProhibitReduceAmount = $this->amount * $fProhibitAmountReduceSpeed;
            $aAccountInfo = [
                'withdrawable' => $oAccount->withdrawable + $fProhibitReduceAmount,
                'prohibit_amount' => $oAccount->prohibit_amount - $fProhibitReduceAmount,
            ];
            $oAccount->update($aAccountInfo);
        }

        $aReturnData = ['conding' => $sResponseCode, 'msg' => __(Config::get('custom-code.' . $sResponseCode)) . ';' . $sErrMsg, 'data' => $this->toArray()];
        return $bSucc;
    }

    public function cancelBill($sAdminUser = null) {
        if (in_array($this->status, [static::STATUS_CANCELED, static::STATUS_SYSTEM_CANCELED])) {
            return static::ERRNO_ERR_PRE_STATUS;
        }
        $oCustomer = BusinessPartner::find($this->bp_id);
        if ($oCustomer->is_mortgage) {
            $oPointJournal = PointJournal::where('bill_id', $this->id)->first();
        }
        $oTransactions = Transaction::where('bill_id', $this->id)->get();
        DB::beginTransaction();
        if ($oCustomer->is_mortgage) {
            if (!$oWallet = Wallet::lock($oPointJournal->wallet_id, $iLocker)) {
                DB::rollback();
                return Wallet::ERRNO_LOCK_FAILED;
            }
            // pr($oPointJournal->toArray());exit;
            if ($oPointJournal->reverse($oWallet) != PointJournal::ERRNO_CREATE_SUCCESSFUL) {
                DB::rollback();
                return PointJournal::ERRNO_CREATE_ERROR_SAVE;
            }
            Wallet::unLock($oWallet->id, $iLocker, false);
        }
        foreach ($oTransactions as $oTransaction) {
            if (!$oAccount = Account::lock($oTransaction->account_id, $iLocker)) {
                DB::rollback();
                return Account::ERRNO_LOCK_FAILED;
            }
            if ($oTransaction->reverse($oAccount) != Transaction::ERRNO_CREATE_SUCCESSFUL) {
                DB::rollback();
                return Transaction::ERRNO_CREATE_ERROR_SAVE;
            }
            Account::unLock($oAccount->id, $iLocker, false);
        }
        $aExtraData = [
            'canceled_by' => $sAdminUser ? $sAdminUser : 'single_game_canceled',
            'canceled_at' => Carbon::now()->toDateTimeString(),
            'status_count' => static::STATUS_COUNT_FINISH,
        ];
        if (!$bSucc = $this->updateValidStatus(static::STATUS_SYSTEM_CANCELED, $aExtraData)) {
            DB::rollback();
            // pr($this->validationErrors->toArray());exit;
            return static::ERRNO_ERR_PRE_STATUS;
        }
        DB::commit();
        return 1;
    }

    public function calculateRankBillPrize($oFinishedGame) {
        $aBillContent = json_decode($this->bet_content, true);
        $iOdd = explode('_', $aBillContent[$oFinishedGame->bn][0])[1];
        $iPrize = 0;
        if (intval($oFinishedGame->won)) $iPrize = floatval($iOdd) * $this->multiple * static::PRICE;
        return $iPrize;
    }

    public function calculatePrizeNew($oFinishedGames) {
        $aFinishedGameWonOdds = []; // 已结束和取消的赛事
        $aCanceledGames = []; // 取消的赛事

        // 获取该注单所选赛事的所有赛果
        foreach ($oFinishedGames as $oGame) {
            $aFinishedGameWonOdds[$oGame->bn] = $oGame->calculateWonResults();
            if ($oGame->status == Game::STATUS_CANCELED) $aCanceledGames[] = $oGame->bn;
        }
        // $aFinishedGameWonOdds =["20160523YX001" => ["a"], "20160523YX002" => ["a"]];
        // $aFinishedGameWonOdds = ['20160523YX002' => ['a']];
        // $aCanceledGames = ['20160523YX001'];
        $aBillContent = json_decode($this->bet_content, true);
        // // 单关的注单，如果赛事取消，做撤单处理
        // if ($this->game_num == 1 && in_array(array_keys($aBillContent)[0], $aCanceledGames) && $this->cancelBill()) {
        //     return 0;
        // }
        $aWonGameOddNames = []; // 该注单中和赛果匹配上的中奖的赔率单元编码
        $aWonGameOddValues = []; // 中奖的赔率数据
        foreach ($aBillContent as $key => $aOdds) {
            $sOdd = $aOdds[0];
            $aNameAndOdd = explode('_', $sOdd);
            // 如果赛事取消，则所有投注的赔率单元均算中奖，但是赔率为1.00
            if ($aCanceledGames && in_array($key, $aCanceledGames)) {
                $sWonOddName = $aNameAndOdd[0];
            } else {
                $sWonOddName = (array_key_exists($key, $aFinishedGameWonOdds) && in_array($aNameAndOdd[0], $aFinishedGameWonOdds[$key])) ? $aNameAndOdd[0] : null;
            }
            if ($sWonOddName) $aWonGameOddValues[] = in_array($key, $aCanceledGames) ? 1.00 : $aNameAndOdd[1];
        }
        // 注单关联的赛事没有全部有中奖的赛果，则该注单未中奖
        if (count($aWonGameOddValues) != $this->game_num) return 0;
        $iMaxGatePrize = $this->game_num < 7 ? SysConfig::get('max_prize_gate_' . $this->game_num) : SysConfig::get('bet_max_prize');
        $iMultpile = $this->multiple < 99 ? 1 : $this->multiple % 99 + 1;
        $iRealPrize = min($iMaxGatePrize * $iMultpile, array_product($aWonGameOddValues) * $this->multiple * static::PRICE);
        return floor($iRealPrize * 100) / 100;
    }
    // 计算奖金
    public function calculatePrize($oFinishedGames) {
        $aFinishedGameWonOdds = []; // 已结束和取消的赛事
        $aCanceledGames = []; // 取消的赛事
        // 获取该注单所选赛事的所有赛果
        foreach ($oFinishedGames as $oGame) {
            $aFinishedGameWonOdds[$oGame->bn] = $oGame->calculateWonResults();
            if ($oGame->status == Game::STATUS_CANCELED) $aCanceledGames[] = $oGame->bn;
        }
        // pr($aFinishedGameWonOdds);
        $aBillContent = json_decode($this->bet_content, true);
        $aGates = $this->getGates($aBillContent['gate']);
        // pr($aGates);
        // 根据过关方式拆分赛事组合
        $aSplittedGames = $this->splitGames($aBillContent['games'], $aGates, $aBillContent['dan']);
        // pr($aSplittedGames);exit;
        $aOddNames = [];
        $aOddValues = [];
        $aWonGameOddNames = []; // 该注单中和赛果匹配上的中奖的赔率单元编码
        $aWonGameOddValues = []; // 中奖的赔率数据
        foreach ($aBillContent['games'] as $key => $aOdds) {
            $aOddNames[$key] = [];
            foreach ($aOdds as $sOdd) {
                $aNameAndOdd = explode('_', $sOdd);
                $aOddNames[$key][] = $aNameAndOdd[0];
                $aOddValues[$key.'_'.$aNameAndOdd[0]] = $aNameAndOdd[1];
            }
            // 如果赛事取消，则所有投注的赔率单元均算中奖，但是赔率为1.00
            if ($aCanceledGames && in_array($key, $aCanceledGames)) {
                $aWonOddNames = $aOddNames[$key];
            } else {
                $aWonOddNames = array_intersect($aFinishedGameWonOdds[$key], $aOddNames[$key]);
            }
            if ($aWonOddNames) {
                $aWonGameOddNames[$key] = $aWonOddNames;
                foreach ($aWonOddNames as $sWonOddName) {
                    $aWonGameOddValues[$key][] = in_array($key, $aCanceledGames) ? 1.00 : $aOddValues[$key.'_'.$sWonOddName];
                }
            }
        }
        // pr($aWonGameOddValues);
        // pr($aWonGameOddNames);
        // exit;
        $aFiltedWonGameOdds = []; // 拆分后的已中奖的赔率组合
        foreach ($aSplittedGames as $key => $aGameBns) {
            $bWon = true;
            foreach ($aGameBns as $sGameBn) {
                if (!array_key_exists($sGameBn, $aWonGameOddNames)) {
                    $bWon = false;
                    continue 2;
                }
            }
            if ($bWon) {
                if (!isset($aWonGameOddValues[$key])) $aWonGameOddValues[$key] = [];
                foreach ($aGameBns as $sGameBn) {
                    $aFiltedWonGameOdds[$key][] = $aWonGameOddValues[$sGameBn];
                }
            }
        }
        $iPrize = 0; // 奖金
        $iFloorPrize = 0; // 舍掉的小数点2位之后的金额
        // pr($aFiltedWonGameOdds);
        foreach ($aFiltedWonGameOdds as $aWonGameOddValue) {
            if (count($aWonGameOddValue) > 1) $aSplittedGameOdds = descartes($aWonGameOddValue);
            else if (count($aWonGameOddValue) == 1) {
                $aSplittedGameOdds = [];
                foreach ($aWonGameOddValue[0] as $key => $value) {
                    $aSplittedGameOdds[] = [$value];
                }
            }
            // pr($aSplittedGameOdds);
            foreach ($aSplittedGameOdds as $aWonOddValue) {
                $iGateNum = count($aWonOddValue);
                if ($iGateNum < 1) continue;
                $iMaxGatePrize = $iGateNum < 7 ? SysConfig::get('max_prize_gate_' . $iGateNum) : SysConfig::get('bet_max_prize');
                $iMultpile = $this->multiple < 99 ? 1 : $this->multiple % 99 + 1;
                $iRealPrize = min($iMaxGatePrize * $iMultpile, array_product($aWonOddValue) * $this->multiple * static::PRICE);
                $iSFloorPrize = floor($iRealPrize * 100) / 100;
                $iFloorPrize += $iRealPrize - $iSFloorPrize;
                $iPrize += $iSFloorPrize;
            }
        }
        // pr($iFloorPrize);
        return $iPrize;
    }

    public function compileBillGamesData($sGameBn) {
        $data = [
            'bp_id'     => $this->bp_id ? $this->bp_id : 0,
            'bp_name'   => $this->bp_name ? $this->bp_name : 0,
            'gt_id'     => $this->gt_id,
            // 'game_id'   => $oGame->id,
            'game_bn'   => $sGameBn,
            'bill_sn'   => $this->sn,
            'user_id'   => $this->user_id,
            'username'  => $this->username,
            'is_tester' => $this->is_tester,
        ];
        return $data;
    }
    /**
     * [compileBillOddData 拼装注单所选赔率单元的数据]
     * @param  [type] $oBusinessPartner [description]
     * @param  [type] $aBillData        [description]
     * @param  [type] $bEssential       [description]
     * @param  [type] $oGame            [description]
     * @param  [type] $sOddIdentity     [description]
     * @param  [type] $iOdd             [description]
     * @return [type]                   [description]
     */
    private function compileBillOddData ($oBusinessPartner, $aBillData, $bEssential, $oGame, $sOddIdentity, $iOdd) {
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
    public function caculateOrderCount($aGames, $aGates, $aDan) {
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
    public function lockForSendPrize() {
        $aConditions = [
            'id' => ['=', $this->id],
            'status_prize' => ['=', self::STATUS_PRIZE_WAITING]
        ];
        $data = [
            'locked_prize' => $iThreadId = DbTool::getDbThreadId(),
            'status_prize' => self::STATUS_PRIZE_SENDING
        ];
        if ($bSucc = $this->strictUpdate($aConditions, $data)) {
            $this->status_prize = self::STATUS_PRIZE_SENDING;
            $this->locked_prize = $iThreadId;
//            $this->deleteCache($this->id);
        }
        return $bSucc;
    }

    /**
     * 解奖金发送锁
     * @return bool
     */
    public function unlockForSendPrize() {
        $aConditions = [
            'id' => ['=', $this->id],
            'status_prize' => ['=', self::STATUS_PRIZE_SENDING],
            'locked_prize' => $this->locked_prize
        ];
        $data = [
            'locked_prize' => 0,
            'status_prize' => self::STATUS_PRIZE_WAITING
        ];
        $bSucc = $this->strictUpdate($aConditions, $data);
        return $bSucc;
    }

    /**
     * 设置派奖状态为已完成
     * @return bool
     */
    public function setPrizeSentStatus() {
        $aConditions = [
            'id'           => ['=', $this->id],
            'status'       => ['=', self::STATUS_WIN],
            'status_prize' => ['in', [self::STATUS_PRIZE_SENDING]]
        ];
        $data = [
            'status'        => self::STATUS_AWARD,
            'status_prize'  => self::STATUS_PRIZE_FINISH,
            'locked_prize'  => 0,
            'prize_sent_at' => Carbon::now()->toDateTimeString(),
        ];
        if (!$bSucc = $this->strictUpdate($aConditions, $data)) {
            $this->prize_sent_at = null;
            $this->status_prize = $this->original['status_prize'];
        }
        return $bSucc;
    }

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

    public static function getUnSentPrizeBills($aIds = []) {
        $aConditions = [
            'status'       => ['=', self::STATUS_WIN],
            'status_prize' => ['in', [self::STATUS_PRIZE_WAITING, self::STATUS_PRIZE_PART]],
        ];
        if (count($aIds)) $aConditions['id'] = ['in', $aIds];
        return self::complexWhere($aConditions)->orderBy('id', 'asc')->get();
    }
}
