<?php
namespace App\Models\Customer;

use App\Models\BaseModel;
use App\Models\Basic\BusinessPartner;
use App\Models\Customer\PointType;
use App\Models\User\User;

use Illuminate\Support\Facades\Redis;

use Coefficient;
use Config;
use Illuminate\Support\Str;


# 账变模型
class PointJournal extends BaseModel {
    protected $table = 'point_journals';
    protected $fillable = [
        'serial_number',
        'bp_id',
        'bp_name',
        'user_id',
        'username',
        // 'is_tester',
        'wallet_id',
        'type_id',
        'type_name',
        'is_income',
        'gt_id',
        'method_id',
        'way_id',
        'coefficient',
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
        'amount' => 6,
        'available' => 6,
        'balance' => 6,
        'frozen' => 6,
        'withdrawable' => 6,
        'prohibit_amount' => 2,
        'previous_available' => 6,
        'previous_frozen' => 6,
        'previous_balance' => 6,
        'previous_withdrawable' => 6,
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
        // 'is_tester',
        'type_name',
        'gt_id',
        'method_id',
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
        'type_name'     => 'friendly_type_name',
        'amount'        => 'amount_formatted',
        'available'     => 'available_formatted',
        'is_tester'     => 'formatted_is_tester',
        'serial_number' => 'serial_number_short',
    ];
    public static $viewColumnMaps = [
        'is_tester'                => 'formatted_is_tester',
        'type_name'                => 'friendly_type_name',
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
        'type_id'     => 'aPointTypes',
        'gt_id'       => 'aGameTypes',
        'method_id'   => 'aGates',
        'coefficient' => 'aCoefficients',
    ];
    public static $ignoreColumnsInView = [
        'wallet_id',
        'type_id',
        'way_id',
        'is_income',
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

    public static $mainParamColumn = 'bp_id';
    public static $rules = [
        'serial number'         => 'max:20',
        'bp_id'                 => 'required|integer',
        'bp_name'               => 'max:16',
        'user_id'               => 'integer',
        'username'              => 'string|between:6,16',
        'is_tester'             => 'boolean',
        'wallet_id'             => 'required|integer',
        'type_id'               => 'required|integer',
        'type_name'             => 'required|max:50',
        'is_income'             => 'required|in:0,1',
        'gt_id'                 => 'integer',
        'method_id'             => 'string|max:100',
        'way_id'                => 'integer',
        'coefficient'           => 'numeric|in:1.000,0.500,0.100,0.010',
        'amount'                => 'required|numeric|min:0',
        'note'                  => 'max:100',
        'bill_id'               => 'integer',
        'bill_sn'               => 'string|max:32',
        'previous_balance'      => 'numeric',
        'previous_frozen'       => 'numeric',
        'previous_available'    => 'numeric',
        'previous_withdrawable' => 'numeric',
        'balance'               => 'numeric',
        'frozen'                => 'numeric',
        'available'             => 'numeric',
        'withdrawable'          => 'numeric',
        'tag'                   => 'max:30',
        'ip'                    => 'ip',
        'proxy_ip'              => 'ip',
        'admin_user_id'         => 'integer',
        'administrator'         => 'max:16',
    ];

    const ERRNO_CREATE_SUCCESSFUL    =  699;
    const ERRNO_CREATE_ERROR_DATA    = -600;
    const ERRNO_CREATE_ERROR_SAVE    = -601;
    const ERRNO_CREATE_ERROR_BALANCE = -602;
    const ERRNO_CREATE_LOW_BALANCE   = -301;

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

//    protected function getBalanceFormattedAttribute() {
//        return $this->getFormattedNumberForHtml('balance');
//    }

    protected function getUpdatedAtDayAttribute() {
        return substr($this->updated_at, 5, 5);
    }

    protected function getUpdatedAtTimeAttribute() {
        return substr($this->updated_at, 11, 5);
    }

    protected function getFormattedIsTesterAttribute() {
        return __('_basic.' . strtolower(Config::get('custom-sysconfig.boolean')[$this->attributes['is_tester']]));
    }

    protected function getFormattedCoefficientAttribute() {
        return !is_null($this->coefficient) ? Coefficient::getCoefficientText($this->coefficient) : '';
    }

    protected function afterSave($oSavedModel) {
        parent::afterSave($oSavedModel);
        $oSavedModel->deleteUserDataListCache();
    }

    protected function getFriendlyTypeNameAttribute() {
        return __('_pointtype.' . strtolower(Str::slug($this->attributes['type_name'])));
    }

    protected function beforeValidate() {
        $this->serial_number = static::makeSeriesNumber($this->bp_id);
        $this->safekey       = $this->makeSafeKey();
        return parent::beforeValidate();
    }

    public static function makeSeriesNumber($iBpId) {
        return uniqid($iBpId, true);
    }

    public function makeSafeKey() {
        $aFields = [
            'bp_id',
            'type_id',
            'wallet_id',
            'amount',
            'gt_id',
            'way_id',
            'coefficient',
            'type_name',
            'bill_id',
            'amount',
            'admin_user_id',
            'ip',
            'proxy_ip'
        ];
        $aData = [];
        foreach ($aFields as $sField) {
            $aData[] = $this->$sField;
        }
        return md5(implode('|', $aData));
    }

    /**
     * 增加新的账变
     * @param BusinessPartner      $oCustomer
     * @param Wallet               $oWallet
     * @param int                  $iTypeId
     * @param float                $fAmount
     * @param array                $aExtraData
     * @param PointJournal         & $oPointJournal
     * @return int      0: 成功; -1: 数据错误; -2: 账变保存失败; -3: 账户余额保存失败
     */
    public static function addPointJournal($oCustomer, $oUser, $oWallet, $iTypeId, $fAmount, $aExtraData = [], & $oPointJournal = null) {
        if ($fAmount <= 0) {
            return self::ERRNO_CREATE_ERROR_DATA;
        }
        if (!$aAttributes = static::compileData($oCustomer, $oUser, $oWallet, $iTypeId, $fAmount, $aNewBalance, $aExtraData)) {
            return self::ERRNO_CREATE_ERROR_DATA;
        }
        $oPointJournal = new PointJournal($aAttributes);
        // pr($oPointJournal->toArray());exit;
        $rules = & static::compileRules();
        if (!$oPointJournal->save($rules)) {
            // pr($oPointJournal->validationErrors->toArray());exit;
            return self::ERRNO_CREATE_ERROR_SAVE;
        }
        $oWallet->fill($aNewBalance);
        if (!$oWallet->save()) {
            // pr($oWallet->validationErrors->toArray());exit;
            return self::ERRNO_CREATE_ERROR_BALANCE;
        }
        return self::ERRNO_CREATE_SUCCESSFUL;
    }

    /**
     * 反转，即进行逆操作
     *
     * @param Wallet $oWallet
     * @return int      0: 成功; -1: 数据错误; -2: 账变保存失败; -3: 账户余额保存失败
     */
    public function reverse($oWallet) {
        $oType = PointType::find($this->type_id);
        if (empty($oType) || empty($oType->reverse_type)) {
            return true;
        }
        $oCustomer = BusinessPartner::find($this->bp_id);
        $oUser = $this->user_id ? User::find($this->user_id) : null;
        $aExtractData = $this->getAttributes();
        unset($aExtractData['id']);
        return static::addPointJournal($oCustomer, $oUser, $oWallet, $oType->reverse_type, $this->amount, $aExtractData);
    }

    private static function & compileRules() {
        $rules = static::$rules;
        $rules['coefficient'] = 'numeric|in:' . implode(',', Coefficient::getValidCoefficientValues());
        return $rules;
    }

    protected static function compileListCacheKeyPrefix() {
        return static::getCachePrefix(true) . 'for-customer-';
    }

    protected static function compileListCacheKey($iCustomerId = null, $iPage = 1) {
        $sKey = static::compileUserDataListCachePrefix($iCustomerId);
        empty($iPage) or $sKey .= $iPage;
        return $sKey;
    }

    protected static function compileCustomerDataListCachePrefix($iCustomerId) {
        return static::compileListCacheKeyPrefix() . $iCustomerId . '-';
    }

    public function deleteUserDataListCache() {
        $sKeyPrifix = static::compileCustomerDataListCachePrefix($this->bp_id);
        $redis = Redis::connection();
        if ($aKeys = $redis->keys($sKeyPrifix . '*')) {
            foreach ($aKeys as $sKey) {
                $redis->del($sKey);
            }
        }
    }

    public static function getTransactions($iTypeId, $iLotteryId, $sIssue, $iProjectId = null, $iOffset = null, $iLimit = null) {
        $aConditions = [
            'type_id' => ['=', $iTypeId],
            'lottery_id' => ['=', $iLotteryId],
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

     private static function compileData($oCustomer, $oUser, $oWallet, $iTypeId, $fAmount, & $aNewBalance, & $aExtraData = []) {
        $oPointType = PointType::find($iTypeId);
        $fAmount = formatNumber($fAmount, static::$amountAccuracy);
        $aAttributes = [
            'bp_id'                    => $oCustomer->id,
            // 'bp_identity'              => $oCustomer->identity,
            'bp_name'                  => $oCustomer->name,
            'user_id'                  => $oUser ? $oUser->id : '',
            'username'                 => $oUser ? $oUser->username : '',
            'is_tester'                => $oCustomer->is_tester,
            'wallet_id'                => $oWallet->id,
            'type_id'                  => $iTypeId,
            'type_name'                => $oPointType->name,
            'is_income'                => $oPointType->credit,
            'amount'                   => $fAmount,
            'previous_balance'         => $oWallet->balance,
            'previous_frozen'          => $oWallet->frozen,
            'previous_available'       => $oWallet->available,
            'previous_withdrawable'    => $oWallet->withdrawable,
            'previous_prohibit_amount' => $oWallet->prohibit_amount,
            'balance'                  => $oWallet->balance,
            'frozen'                   => $oWallet->frozen,
            'available'                => $oWallet->available,
            'withdrawable'             => $oWallet->withdrawable,
            'prohibit_amount'          => $oWallet->prohibit_amount,
        ];

        if ($oPointType->project_linked) {
            if (!isset($aExtraData['bill_id']) || !isset($aExtraData['bill_sn']) || !isset($aExtraData['gt_id']) || !isset($aExtraData['way_id']) || !isset($aExtraData['method_id']) || !isset($aExtraData['coefficient'])
            ) {
                return false;
            }
            $aAttributes['bill_id']     = $aExtraData['bill_id'];
            $aAttributes['bill_sn']     = $aExtraData['bill_sn'];
            $aAttributes['gt_id']       = $aExtraData['gt_id'];
            $aAttributes['way_id']      = $aExtraData['way_id'];
            $aAttributes['method_id']   = $aExtraData['method_id'];
            $aAttributes['coefficient'] = $aExtraData['coefficient'];
        }
        !isset($aExtraData['admin_user_id']) or $aAttributes['admin_user_id'] = $aExtraData['admin_user_id'];
        !isset($aExtraData['administrator']) or $aAttributes['administrator'] = $aExtraData['administrator'];
        !isset($aExtraData['note']) or $aAttributes['note'] = $aExtraData['note'];
        !isset($aExtraData['tag']) or $aAttributes['tag'] = $aExtraData['tag'];
        !isset($aExtraData['ip']) or $aAttributes['client_ip'] = $aExtraData['client_ip'];
        !isset($aExtraData['proxy_ip']) or $aAttributes['proxy_ip'] = $aExtraData['proxy_ip'];

        $aSubAccounts = ['balance', 'available', 'frozen', 'withdrawable', 'prohibit_amount'];
        foreach ($aSubAccounts as $sField) {
            if (!$oPointType->$sField) {
                continue;
            }
            $aAttributes[$sField] += $oPointType->$sField * $fAmount;
            $aNewBalance[$sField] = $aAttributes[$sField];
        }
        $aAttributes['withdrawable'] >= 0 or $aNewBalance['withdrawable'] = $aAttributes['withdrawable'] = 0;
        $aAttributes['prohibit_amount'] >= 0 or $aNewBalance['prohibit_amount'] = $aAttributes['prohibit_amount'] = 0;
        return $aAttributes;
    }

}