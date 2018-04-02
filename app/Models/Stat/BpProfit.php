<?php
namespace App\Models\Stat;

use App\Models\BaseModel;
use App\Models\Basic\BusinessPartner;

use String;
/**
 * 用户盈亏表
 */
class BpProfit extends BaseModel {

    protected $table                         = 'bp_profits';
    public static $resourceName              = 'BpProfit';
    public static $htmlOriginalNumberColumns = [
        // 'prize_group'
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
        'bp_name',
        // 'is_tester',
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
        'date',
        'bp_id',
        'bp_identity',
        'bp_name',
        'deposit',
        'withdrawal',
        'turnover',
        'prize',
        'profit',
        'commission',
        'bonus',
        'lose_commission',
    ];
    public static $rules                     = [
        'date'            => 'required|date',
        'bp_id'           => 'required|integer',
        'bp_identity'     => 'required|max:50',
        'bp_name'         => 'required|max:50',
        'deposit'         => 'numeric|min:0',
        'withdrawal'      => 'numeric|min:0',
        'turnover'        => 'numeric',
        'prize'           => 'numeric',
        'profit'          => 'numeric',
        'bonus'           => 'numeric',
        'lose_commission' => 'numeric',
    ];

    public static $htmlSelectColumns = [
        'bp_id'    => 'aCustomers',
    ];

    public $orderColumns = [
        'date' => 'desc'
    ];
    public static $mainParamColumn = 'bp_id';
    public static $titleColumn     = 'bp_name';

    public static function getBpProfitObject($sDate, $iBpId, $sBpIdentity = null, $sBpName = null) {
        if (!$sBpIdentity || $sBpName) {
            $oCustomer = BusinessPartner::find($iBpId);
            $sBpIdentity = $oCustomer->identity;
            $sBpName = $oCustomer->name;
        }
        $aParam = [
            'bp_id'       => $iBpId,
            'bp_name'     => $sBpName,
            'bp_identity' => $sBpIdentity,
            'date'        => $sDate
        ];
        return self::firstOrCreate($aParam);
    }

    /**
     * 返回包含直接销售额，直接盈亏记录和团队销售额的数组
     *
     * @param String $sDate     只有年和月,格式：2014-01-01
     * @param String $iBpId     商户id
     * @return array
     */
    public static function getBpProfitByDate($sBeginDate, $sEndDate, $iBpId) {
        $oQuery = static::where('bp_id', '=', $iBpId);
        if (!is_null($sBeginDate)) {
            $oQuery->where('date', '>=', $sBeginDate);
        }
        if (!is_null($sEndDate)) {
            $oQuery->where('date', '<=', $sEndDate);
        }
        $oBpProfits = $oQuery->get(['turnover', 'profit']);
        $data         = [];
        $i            = 0;
        foreach ($oBpProfits as $oBpProfit) {
            $data[$i]['turnover'] = $oBpProfit->turnover;
            $data[$i]['profit']   = $oBpProfit->profit;
            $i++;
        }
        return $data;
    }

    /**
     * 获取指定商户的销售总额
     * @param int $iBpId  商户id
     * @return float        销售总额
     */
    public static function getBpTotalTurnover($sBeginDate, $sEndDate, $iBpId) {
        $aBpProfits = static::getBpProfitByDate($sBeginDate, $sEndDate, $iBpId);
        $aTurnovers   = [];
        foreach ($aBpProfits as $data) {
            $aTurnovers[] = $data['turnover'];
        }
        $fTotalTurnover = array_sum($aTurnovers);
        return $fTotalTurnover;
    }

    /**
     * 获取指定用户商户盈亏
     * @param int $iBpId
     * Id  商户id
     * @return float        商户盈亏
     */
    public static function getBpTotalProfit($sBeginDate, $sEndDate, $iBpId) {
        $aBpProfits = static::getBpProfitByDate($sBeginDate, $sEndDate, $iBpId);
        $aProfits     = [];
        foreach ($aBpProfits as $data) {
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

    // /**
    //  * 累加输值佣金
    //  *
    //  * @param float $fAmount
    //  * @return boolean
    //  */
    // public function addLoseCommission($fAmount) {
    //     $this->lose_commission += $fAmount;
    //     $this->profit = $this->countProfit();
    //     return $this->save();
    // }

    // /**
    //  * 累加促销奖金
    //  *
    //  * @param float $fAmount
    //  * @return boolean
    //  */
    // public function addBonus($fAmount) {
    //     $this->bonus += $fAmount;
    //     $this->profit = $this->countProfit();
    //     return $this->save();
    // }

    public function countProfit() {
        return $this->turnover - ($this->prize + $this->commission);
    }

    /**
     * 盈亏总计
     * @param String $sBeginDate  开始日期
     * @param String $sEndDate    结束日期
     * @param int $iBpId         用户id
     * @return array
     */
    public static function getBpSumInfo($sBeginDate, $sEndDate, $iBpId) {
        $sSql     = 'select sum(deposit) total_deposit, sum(withdrawal) total_withdrawal,sum(turnover) total_turnover, sum(profit) total_profit, sum(prize) total_prize from bp_profits where bp_id = ? ';
        $aValue   = [$iBpId];
        if ($sBeginDate) {
            $sSql .=" and date >= ? ";
            $aValue[] = $sBeginDate;
        }
        if ($sEndDate) {
            $sSql .=" and date <= ? ";
            $aValue[] = $sEndDate;
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

    // public static function & comipleTurnover($oUser, $fAmount) {
    //     $aForeFathers = explode(',', $oUser->forefather_ids);
    //     $aTurnovers   = [];
    //     foreach ($aForeFathers as $iForeFatherId) {
    //         $aTurnovers[$iForeFatherId] = $fAmount;
    //     }
    //     $aTurnovers[$oUser->id] = $fAmount;
    //     return $aTurnovers;
    // }

    public static function updateTurnOver($sDate, $iBpId, $fAmount) {
        return static::updateProfitData('turnover', $sDate, $iBpId, $fAmount);
    }

    public static function updatePrize($sDate, $iBpId, $fAmount) {
        return static::updateProfitData('prize', $sDate, $iBpId, $fAmount);
    }

    public static function updateBonus($sDate, $iBpId, $fAmount) {
        return static::updateProfitData('bonus', $sDate, $iBpId, $fAmount);
    }

    public static function updateCommission($sDate, $iBpId, $fAmount) {
        return static::updateProfitData('commission', $sDate, $iBpId, $fAmount);
    }

    public static function updateProfitData($sType, $sDate, $iBpId, $fAmount) {
        $sFunction = 'add' . ucfirst(String::camel($sType));
        $oProfit   = self::getBpProfitObject($sDate, $iBpId);
        if (!is_object($oProfit)) {
            return false;
        }
        $bSucc = $oProfit->$sFunction($fAmount);
        return $bSucc;
    }

    public static function clearProfitData($sDate, $iBpId) {
        $oProfit = static::getBpProfitObject($sDate, $iBpId);
        if ($oProfit->id) {
            $oProfit->deposit = $oProfit->withdrawal = $oProfit->turnover = $oProfit->prize = $oProfit->bonus - $oProfit->commission = $oProfit->profit = 0;
            $oProfit->save();
        }
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
        return number_format($this->attributes['lose_commission'], 2) ;
    }

    protected function getProfitFormattedAttribute() {
        return $this->getFormattedNumberForHtml('profit');
    }

    protected function getIsTesterFormattedAttribute() {
        return is_null($this->attributes['is_tester']) ? '' : __('_basic.' . strtolower(Config::get('var.boolean')[$this->attributes['is_tester']]));
    }

}
