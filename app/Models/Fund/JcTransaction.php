<?php
namespace App\Models\Fund;
use App\Models\BaseModel;

class JcTransaction extends BaseModel {

    protected $table = 'jc_transactions';
    protected $softDelete = false;
    protected $fillable = [
        'id',
        'serial_number',
        'user_id',
        'username',
        'is_tester',
        'is_agent',
        'parent_user_id',
        'parent_user',
        'forefather_user_ids',
        'forefather_users',
        'bp_id',
        'bp_name',
        'account_id',
        'type_id',
        'is_income',
        'gt_id',
        'method_id',
        'way_id',
        'coefficient',
        'description',
        'amount',
        'note',
        'bill_id',
        'bill_sn',
        'previous_balance',
        'previous_frozen',
        'previous_available',
        'previous_withdrawable',
        'previous_prohibit_amount',
        'balance',
        'frozen',
        'available',
        'withdrawable',
        'prohibit_amount',
        'tag',
        'admin_user_id',
        'administrator',
        'ip',
        'proxy_ip',
    ];
    public static $resourceName = 'Transaction';
    public static $amountAccuracy = 6;
    public static $htmlNumberColumns = [
        'amount'                   => 2,
        'available'                => 2,
        'balance'                  => 2,
        'frozen'                   => 6,
        'withdrawable'             => 6,
        'prohibit_amount'          => 2,
        'previous_available'       => 6,
        'previous_frozen'          => 6,
        'previous_balance'         => 6,
        'previous_withdrawable'    => 6,
        'previous_prohibit_amount' => 2,
    ];

    /**
     * the columns for list page
     * @var array
     */
    public static $columnForList = [
        'serial_number',
        'created_at',
        'bp_name',
        'username',
        'is_tester',
        'description',
        'gt_id',
        'way_id',
        'coefficient',
        'amount',
        'available',
        'note',
        'tag',
        'ip',
        'administrator',
    ];
    public static $totalColumns = [
        'amount',
    ];
    public static $listColumnMaps = [
        'description'   => 'friendly_description',
        'amount'        => 'amount_formatted',
        'available'     => 'available_formatted',
        'is_tester'     => 'formatted_is_tester',
        'serial_number' => 'serial_number_short',
    ];
    public static $viewColumnMaps = [
        'is_tester'                => 'formatted_is_tester',
        // 'is_agent'                 => 'formatted_is_agent',
        'description'              => 'friendly_description',
        'amount'                   => 'amount_formatted',
        'available'                => 'available_formatted',
        'frozen'                   => 'frozen_formatted',
        'balance'                  => 'balance_formatted',
        'withdrawable'             => 'withdrawable_formatted',
        'prohibit_amount'          => 'prohibit_amount_formatted',
        'previous_available'       => 'previous_available_formatted',
        'previous_frozen'          => 'previous_frozen_formatted',
        'previous_balance'         => 'previous_balance_formatted',
        'previous_withdrawable'    => 'previous_withdrawable_formatted',
        'previous_prohibit_amount' => 'previous_prohibit_amount_formatted',
        'serial_number'            => 'serial_number_short',
    ];

    /**
     * 下拉列表框字段配置
     * @var array
     */
    public static $htmlSelectColumns = [
        'gt_id'       => 'aGameTypes',
        'way_id'      => 'aWays',
        'coefficient' => 'aCoefficients',
    ];
    public static $ignoreColumnsInView = [
        'account_id',
        'user_id',
        // 'user_forefather_ids',
        'type_id',
        'method_id',
        'is_income',
        'bet_number',
        'prize_added',
        'total_prize',
        'locked_prize',
        'locked_commission',
        'prize_set',
        'admin_user_id',
        'previous_balance',
        'previous_frozen',
        'previous_available',
        'previous_withdrawable',
        'balance',
        'frozen',
        'withdrawable',
        'safekey',
    ];

    /**
     * order by config
     * @var array
     */
    public $orderColumns = [
        'id' => 'desc'
    ];

    /**
     * If Tree Model
     * @var Bool
     */
    public static $treeable = false;

    /**
     * the main param for index page
     * @var string
     */
    public static $mainParamColumn = 'user_id';
    public static $rules = [
        'serial_number'         => 'required|max:255',
        'user_id'               => 'integer',
        'username'              => 'max:16',
        'is_tester'             => 'in:0,1',
        'is_agent'              => 'in:0,1',
        'bp_id'                 => 'required|integer',
        'bp_name'               => 'max:50',
        'account_id'            => 'integer',
        'type_id'               => 'required|integer',
        'is_income'             => 'required|in:0,1',
        'gt_id'                 => 'integer',
        'method_id'             => 'max:100',
        'way_id'                => 'integer|in:0,1,2',
        'coefficient'           => 'numeric|in:1.00,0.50,0.10,0.01',
        'description'           => 'required|max:50',
        'amount'                => 'required|numeric|min:0',
        'note'                  => 'max:100',
        'bill_id'               => 'integer',
        'bill_sn'               => 'max:32',
        'previous_balance'      => 'numeric',
        'previous_frozen'       => 'numeric',
        'previous_available'    => 'numeric',
        'previous_withdrawable' => 'numeric',
        'balance'               => 'numeric',
        'frozen'                => 'numeric',
        'withdrawable'          => 'numeric',
        'available'             => 'numeric',
        'tag'                   => 'max:30',
        'admin_user_id'         => 'integer',
        'administrator'         => 'max:16',
        'ip'                    => 'ip',
        'proxy_ip'              => 'ip',
    ];
    // public static $aReportType = [
    //     ReportDownloadConfig::TYPE_TRANSACTION => 0,
    //     ReportDownloadConfig::TYPE_TRANSACTION_DEPOSIT => TransactionType::TYPE_DEPOSIT,
    //     ReportDownloadConfig::TYPE_TRANSACTION_WITHDRAWAL => TransactionType::TYPE_WITHDRAW,
    // ];

    const ERRNO_CREATE_SUCCESSFUL    = 599;
    const ERRNO_CREATE_ERROR_DATA    = -500;
    const ERRNO_CREATE_ERROR_SAVE    = -501;
    const ERRNO_CREATE_ERROR_BALANCE = -502;
    const ERRNO_CREATE_LOW_BALANCE   = -301;

    public static function makeSeriesNumber($iUserId) {
        return uniqid($iUserId, true);
//        return md5($iUserId . microtime(true) . mt_rand());
    }

    protected function beforeValidate() {
        // pr($this->toArray());
        // exit;
        $this->serial_number = static::makeSeriesNumber($this->user_id);
        $this->makeSafeKey();
        return parent::beforeValidate();
    }

    public function makeSafeKey() {
        $aFields = [
            'user_id',
            'type_id',
            'account_id',
            'amount',
            'way_id',
            'method_id',
            'coefficient',
            'description',
            'amount',
            'admin_user_id',
            'ip',
            'proxy_ip'
        ];
        $aData = [];
        foreach ($aFields as $sField) {
            $aData[] = $this->$sField;
        }
        return $this->safekey = md5(implode('|', $aData));
    }

    protected function setAmountAttribute($fAmount) {
        $this->attributes['amount'] = formatNumber($fAmount, static::$amountAccuracy);
    }

    protected function setBalanceAttribute($fAmount) {
        $this->attributes['balance'] = formatNumber($fAmount, static::$amountAccuracy);
    }

    protected function setAvailableAttribute($fAmount) {
        $this->attributes['available'] = formatNumber($fAmount, static::$amountAccuracy);
    }

    protected function setFrozenAttribute($fAmount) {
        $this->attributes['frozen'] = formatNumber($fAmount, static::$amountAccuracy);
    }

    protected function setWithdrawableAttribute($fAmount) {
        $this->attributes['withdrawable'] = formatNumber($fAmount, static::$amountAccuracy);
    }

    protected function setPreviousBalanceAttribute($fAmount) {
        $this->attributes['previous_balance'] = formatNumber($fAmount, static::$amountAccuracy);
    }

    protected function setPreviousAvailableAttribute($fAmount) {
        $this->attributes['previous_available'] = formatNumber($fAmount, static::$amountAccuracy);
    }

    protected function setPreviousFrozenAttribute($fAmount) {
        $this->attributes['previous_frozen'] = formatNumber($fAmount, static::$amountAccuracy);
    }

    protected function setPreviousWithdrawableAttribute($fAmount) {
        $this->attributes['previous_withdrawable'] = formatNumber($fAmount, static::$amountAccuracy);
    }

    protected function setSerialNumberAttribute($sSerialNumber) {
        $this->attributes['serial_number'] = strtoupper($sSerialNumber);
    }

    protected function getAmountFormattedAttribute() {
        return ($this->is_income ? '+' : '-') . $this->getFormattedNumberForHtml('amount');
    }

    protected function getDirectAmountAttribute() {
        return ($this->is_income ? '' : '-') . formatNumber($this->attributes['amount'], static::$htmlNumberColumns['amount']);
    }

    protected function getSerialNumberShortAttribute() {
        return substr($this->attributes['serial_number'], -6);
    }

    protected function getAvailableFormattedAttribute() {
        return $this->getFormattedNumberForHtml('available');
    }

    protected function getFrozenFormattedAttribute() {
        return $this->getFormattedNumberForHtml('frozen');
    }

    protected function getBalanceFormattedAttribute() {
        return $this->getFormattedNumberForHtml('balance');
    }

    protected function getWithdrawableFormattedAttribute() {
        return $this->getFormattedNumberForHtml('withdrawable');
    }

    protected function getProhibitAmountFormattedAttribute() {
        return $this->getFormattedNumberForHtml('prohibit_amount');
    }

    protected function getPreviousBalanceFormattedAttribute() {
        return $this->getFormattedNumberForHtml('previous_balance');
    }

    protected function getPreviousFrozenFormattedAttribute() {
        return $this->getFormattedNumberForHtml('previous_frozen');
    }

    protected function getPreviousAvailableFormattedAttribute() {
        return $this->getFormattedNumberForHtml('previous_available');
    }

    protected function getPreviousWithdrawableFormattedAttribute() {
        return $this->getFormattedNumberForHtml('previous_withdrawable');
    }

    protected function getPreviousProhibitAmountFormattedAttribute() {
        return $this->getFormattedNumberForHtml('previous_prohibit_amount');
    }

    protected function getGateTypeDisplayAttribute() {
       return !is_null($this->way_id) ? __('_method.' . Method::$types[$this->way_id]) : '';
    }

    protected function getFriendlyDescriptionAttribute() {
        return __('_transactiontype.' . strtolower(Str::slug($this->attributes['description'])));
    }

    protected function getUpdatedAtDayAttribute() {
// $sDay = explode(' ', $this->updated_at);
        return substr($this->updated_at, 5, 5);
    }

    protected function getUpdatedAtTimeAttribute() {
        return substr($this->updated_at, 11, 5);
    }


    public static function getTransactions($iTypeId, $iLotteryId, $sIssue, $iProjectId = null, $iOffset = null, $iLimit = null) {
        $aConditions = [
            'type_id' => ['=', $iTypeId],
            'gt_id' => ['=', $iLotteryId],
            'issue' => ['=', $sIssue],
        ];
        is_null($iProjectId) or $aConditions['project_id'] = ['=', $iProjectId];
        $oQuery = static::doWhere($aConditions)->orderBy('id', 'asc');
        empty($iOffset) or $oQuery = $oQuery->offset($iOffset);
        empty($iLimit) or $oQuery = $oQuery->limit($iLimit);
//        pr($aConditions);
//        exit;
        return $oQuery->get();
    }

    protected function getFormattedIsTesterAttribute() {
        return __('_basic.' . strtolower(Config::get('custom-sysconfig.boolean')[$this->is_tester]));
    }

    protected function getFormattedIsAgentAttribute() {
        return __('_basic.' . strtolower(Config::get('custom-sysconfig.boolean')[$this->attributes['is_agent']]));
    }

    protected function getFormattedCoefficientAttribute() {
        return !is_null($this->coefficient) ? Coefficient::getCoefficientText($this->coefficient) : '';
    }

    private static function & compileRules() {
        $rules = static::$rules;
        $rules['coefficient'] = 'numeric|in:' . implode(',', Coefficient::getValidCoefficientValues());
        return $rules;
    }

    /**
     * 下载报表实现类，根据不同model，下载报表内容不同
     * @param int $iReportType      报表类型
     * @param int $iFreqType        下载频率类型，如：每天，每周，每月等
     */
    public function download($iReportType, $aDownloadTime, $sFileName, $sDir = './') {
        $oQuery = static::whereBetween('created_at', array_values($aDownloadTime))->where('is_tester', '=', 0);
        $iReportType == 0 or $oQuery->where('type_id', '=', $iReportType);
//         $queries = DB::getQueryLog();
//         $last_query = end($queries);
//         pr($last_query);exit;
        $aConvertFields = [
            'gt_id' => 'lottery',
            'way_id' => 'way',
            'amount' => 'transaction_amount_formatted',
            'coefficient' => 'coefficient',
            'description' => 'friendly_description',
            'is_tester' => 'boolean',
        ];

        $aLotteries = Lottery::getTitleList();
        $aWays = SeriesWay::getTitleList();
        $aData = $oQuery->get(array_merge(['is_income'], Transaction::$columnForList))->toArray();
        $aData = $this->makeData($aData, Transaction::$columnForList, $aConvertFields, $aWays, $aLotteries);
        return $this->downloadExcel(Transaction::$columnForList, $aData, $sFileName, $sDir);
    }

    public function makeData($aData, $aFields, $aConvertFields, $aWays = null, $aLotteries = null) {
        $aResult = array();
        foreach ($aData as $oDeposit) {
            $a = [];
            foreach ($aFields as $key) {
                if ($oDeposit[$key] === '') {
                    $a[] = $oDeposit[$key];
                    continue;
                }
                if (array_key_exists($key, $aConvertFields)) {
                    switch ($aConvertFields[$key]) {
                        case 'transaction_amount_formatted':
                            $a[] = ($oDeposit['is_income'] ? '+' : '-') . $oDeposit['amount'];
                            break;
                        case 'lottery':
                            if (array_key_exists($oDeposit[$key], $aLotteries)) {
                                $a[] = $aLotteries[$oDeposit[$key]];
                            } else {
                                $a[] = '';
                            }
                            break;
                        case 'boolean':
                            $a[] = $oDeposit[$key] ? __('Yes') : __('No');
                            break;
                        case 'way':
                            if (array_key_exists($oDeposit[$key], $aWays)) {
                                $a[] = $aWays[$oDeposit[$key]];
                            } else {
                                $a[] = '';
                            }
                            break;
                        case 'coefficient':
                            $aCoefficients = Coefficient::$coefficients;
                            $a[] = key_exists($oDeposit[$key], $aCoefficients) ? $aCoefficients[$oDeposit[$key]] : null;
                            break;
                        case 'friendly_description':
                            $a[] = __('_transactiontype.' . strtolower(Str::slug($oDeposit['description'])));
                            break;
                    }
                } else {
                    $a[] = $oDeposit[$key];
                }
            }
            $aResult[] = $a;
        }
        return $aResult;
    }

}
