<?php
namespace App\Models\Stat;

use App\Models\BaseModel;
use App\Models\User\User;

use String;
use Config;
/**
 * 用户盈亏表
 */
class UserProfit extends BaseModel {

    protected $table                         = 'user_profits';
    public static $resourceName              = 'UserProfit';
    public static $htmlOriginalNumberColumns = [
        'prize_group'
    ];
    public static $amountAccuracy            = 6;
    public static $htmlNumberColumns         = [
        'deposit'         => 2,
        'withdrawal'      => 2,
        'turnover'        => 4,
        'prize'           => 6,
        'profit'          => 6,
        // 'commission'      => 6,
        // 'lose_commission' => 0,
    ];
    public static $columnForList             = [
        'date',
        'username',
        'is_tester',
        'deposit',
        'withdrawal',
        'turnover',
        'prize',
        'profit',
    ];
    public static $totalColumns              = [
        'deposit',
        'withdrawal',
        'turnover',
        'prize',
        'profit',
    ];
    public static $listColumnMaps            = [
        'turnover'   => 'turnover_formatted',
        'prize'      => 'prize_formatted',
        'profit'     => 'profit_formatted',
        'is_tester'  => 'is_tester_formatted',
    ];
    protected $fillable                      = [
        'bp_id',
        'bp_name',
        'date',
        'user_id',
        'is_tester',
        'username',
        'deposit',
        'withdrawal',
        'turnover',
        'prize',
        'bonus',
        'commission',
        'lose_commission',
        'profit',
    ];
    public static $rules                     = [
        'bp_id'           => 'required|integer',
        'bp_name'         => 'String',
        'date'            => 'required|date',
        'user_id'         => 'required|integer',
        'username'        => 'required|max:16',
        'deposit'         => 'numeric|min:0',
        'withdrawal'      => 'numeric|min:0',
        'turnover'        => 'numeric',
        'prize'           => 'numeric',
        'profit'          => 'numeric',
        'commission'      => 'numeric',
        'bonus'           => 'numeric',
        'lose_commission' => 'numeric',
    ];

    public static $htmlSelectColumns = [
        'bp_id'    => 'aCustomers',
    ];

    public $orderColumns                     = [
        'date' => 'desc'
    ];
    public static $mainParamColumn           = 'user_id';
    public static $titleColumn               = 'username';

    /**
     * 返回UserProfit对象
     *
     * @param String $sDate
     * @param String $iUserId
     * @return UserProfit
     */
    public static function getUserProfitObject($sDate, $iUserId, $sUsername = null) {
        if (!$sUsername) {
            $oUser = User::find($iUserId);
            $sUsername = $oUser->username;
        }
        $aParam = [
            'bp_id'     => $oUser->bp_id,
            'bp_name'   => $oUser->bp_name,
            'user_id'   => $iUserId,
            'username'  => $sUsername,
            'is_tester' => $oUser->is_tester,
            'date'      => $sDate
        ];
        return self::firstOrCreate($aParam);
    }

    /**
     * 返回包含直接销售额，直接盈亏记录和团队销售额的数组
     *
     * @param String $sDate     只有年和月,格式：2014-01-01
     * @param String $iUserId   用户id
     * @return array
     */
    public static function getUserProfitByDate($sBeginDate, $sEndDate, $iUserId) {
        $oQuery = static::where('user_id', '=', $iUserId);
        if (!is_null($sBeginDate)) {
            $oQuery->where('date', '>=', $sBeginDate);
        }
        if (!is_null($sEndDate)) {
            $oQuery->where('date', '<=', $sEndDate);
        }
        $aUserProfits = $oQuery->get(['turnover', 'profit']);
        $data         = [];
        $i            = 0;
        foreach ($aUserProfits as $oUserProfit) {
            $data[$i]['turnover'] = $oUserProfit->turnover;
            $data[$i]['profit']   = $oUserProfit->profit;
            $i++;
        }
        return $data;
    }

    /**
     * 获取指定用户的销售总额
     * @param int $iUserId  用户id
     * @return float        销售总额
     */
    public static function getUserTotalTurnover($sBeginDate, $sEndDate, $iUserId) {
        $aUserProfits = static::getUserProfitByDate($sBeginDate, $sEndDate, $iUserId);
        $aTurnovers   = [];
        foreach ($aUserProfits as $data) {
            $aTurnovers[] = $data['turnover'];
        }
        $fTotalTurnover = array_sum($aTurnovers);
        return $fTotalTurnover;
    }

    /**
     * 获取指定用户用户盈亏
     * @param int $iUserId  用户id
     * @return float        用户盈亏
     */
    public static function getUserTotalProfit($sBeginDate, $sEndDate, $iUserId) {
        $aUserProfits = static::getUserProfitByDate($sBeginDate, $sEndDate, $iUserId);
        $aProfits     = [];
        foreach ($aUserProfits as $data) {
            $aProfits[] = $data['profit'];
        }
        $fTotalProfit = array_sum($aProfits);
        return $fTotalProfit;
    }

    /**
     * 累加充值额
     * @param float $fAmount
     * @return boolean
     */
    public function addDeposit($fAmount) {
        $this->deposit += $fAmount;
        return $this->save();
    }

    /**
     * 累加提现额
     * @param float $fAmount
     * @return boolean
     */
    public function addWithdrawal($fAmount) {
        $this->withdrawal += $fAmount;
        return $this->save();
    }

    /**
     * 累加个人销售额
     * @param float $fAmount
     * @return boolean
     */
    public function addTurnover($fAmount) {
        $this->turnover += $fAmount;
        $this->profit = $this->countProfit();
        return $this->save();
    }

    /**
     * 累加奖金
     *
     * @param float $fAmount
     * @return boolean
     */
    public function addPrize($fAmount) {
        $this->prize += $fAmount;
        $this->profit = $this->countProfit();
        return $this->save();
    }

    /**
     * 累加输值佣金
     *
     * @param float $fAmount
     * @return boolean
     */
    public function addLoseCommission($fAmount) {
        $this->lose_commission += $fAmount;
        $this->profit = $this->countProfit();
        return $this->save();
    }

    /**
     * 累加促销奖金
     *
     * @param float $fAmount
     * @return boolean
     */
    public function addBonus($fAmount) {
        $this->bonus += $fAmount;
        $this->profit = $this->countProfit();
        return $this->save();
    }

    public function countProfit() {
        return $this->prize + $this->bonus + $this->commission + $this->lose_commission - $this->turnover;
    }

    /**
     * 代理盈亏总计
     * @param String $sBeginDate  开始日期
     * @param String $sEndDate    结束日期
     * @param int $iUserId         用户id
     * @return array
     */
    public static function getAgentSumInfo($sBeginDate, $sEndDate, $iUserId, $username = '') {
        $sSql     = 'select sum(deposit) total_deposit, sum(withdrawal) total_withdrawal,sum(turnover) total_turnover,sum(commission) total_commission, sum(profit) total_profit,sum(prize) total_prize, sum(lose_commission) total_lose_commission, sum(bonus) total_bonus from user_profits where (parent_user_id = ? ';
        $aValue   = [$iUserId];
        !$username ? $sSql .=" or user_id=?)" : $sSql.=")";
        $username or $aValue[] = $iUserId;
        !$sBeginDate or $sSql .=" and date>=?";
        !$sBeginDate or $aValue[] = $sBeginDate;
        !$sEndDate or $sSql .=" and date<=?";
        !$sEndDate or $aValue[] = $sEndDate;
        if ($username) {
            $sSql .= ' and username = ?';
            array_push($aValue, $username);
        }
        $results = DB::select($sSql, $aValue);
        return objectToArray($results[0]);
    }

    /**
     * 累加个人佣金
     * @param float $fAmount
     * @return boolean
     */
    public function addCommission($fAmount) {
        $this->commission += $fAmount;
        $this->profit = $this->countProfit();
        return $this->save();
    }

    public static function & comipleTurnover($oUser, $fAmount) {
        $aForeFathers = explode(',', $oUser->forefather_ids);
        $aTurnovers   = [];
        foreach ($aForeFathers as $iForeFatherId) {
            $aTurnovers[$iForeFatherId] = $fAmount;
        }
        $aTurnovers[$oUser->id] = $fAmount;
        return $aTurnovers;
    }

    public static function updateTurnOver($sDate, $oUser, $fAmount) {
        return static::updateProfitData('turnover', $sDate, $oUser, $fAmount);
    }

    public static function updatePrize($sDate, $oUser, $fAmount) {
        return static::updateProfitData('prize', $sDate, $oUser, $fAmount);
    }

    public static function updateBonus($sDate, $oUser, $fAmount) {
        return static::updateProfitData('bonus', $sDate, $oUser, $fAmount);
    }

    public static function updateCommission($sDate, $oUser, $fAmount) {
        return static::updateProfitData('commission', $sDate, $oUser, $fAmount);
    }

    public static function updateProfitData($sType, $sDate, $id, $fAmount) {
        $sFunction = 'add' . ucfirst(String::camel($sType));
//        $sFunction = 'add' . ucfirst($sType);
        $oProfit   = self::getUserProfitObject($sDate, $id);
        if (!is_object($oProfit)) {
            return false;
        }
        // pr($oProfit->toArray());exit;
        $bSucc = $oProfit->$sFunction($fAmount);
//        pr($bSucc);
        return $bSucc;
    }

    public static function clearProfitData($sDate, $oUser) {
        $oProfit = static::getUserProfitObject($sDate, $oUser->id);
        if ($oProfit->id) {
            $oProfit->deposit    = $oProfit->withdrawal = $oProfit->turnover   = $oProfit->prize      = $oProfit->bonus - $oProfit->commission = $oProfit->profit     = 0;
            $oProfit->save();
        }
    }

    // protected function getUserTypeFormattedAttribute() {
    //     // return static::$aUserTypes[($this->parent_user_id != null ? 'not_null' : 'null')];
    //     return __('_userprofit.' . strtolower(static::$aUserTypes[intval($this->parent_user_id != null) - 1]));
    // }

    protected function getUserTypeFormattedAttribute() {
        if ($this->parent_user_id)
            $sUserType = User::$userTypes[$this->is_agent];
        else
            $sUserType = User::$userTypes[User::TYPE_TOP_AGENT];
        return __('_user.' . $sUserType);
    }

    protected function getDepositFormattedAttribute() {
        return $this->getFormattedNumberForHtml('deposit');
    }

    protected function getWithdrawalFormattedAttribute() {
        return $this->getFormattedNumberForHtml('withdrawal');
    }

    protected function getTurnoverFormattedAttribute() {
        return $this->getFormattedNumberForHtml('turnover');
    }

    protected function getPrizeFormattedAttribute() {
        return $this->getFormattedNumberForHtml('prize');
    }

    protected function getBonusFormattedAttribute() {
        return $this->getFormattedNumberForHtml('bonus');
    }

    protected function getCommissionFormattedAttribute() {
        return $this->getFormattedNumberForHtml('commission');
    }

    protected function getLoseCommissionFormattedAttribute() {
        return number_format($this->attributes['lose_commission'], 2);
    }

    protected function getProfitFormattedAttribute() {
        return $this->getFormattedNumberForHtml('profit');
    }

    protected function getIsTesterFormattedAttribute() {
        return is_null($this->is_tester) ? '' : __('_basic.' . strtolower(Config::get('custom-sysconfig.boolean')[$this->is_tester]));
    }

}
