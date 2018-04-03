<?php
namespace App\Models\Fund;

use App\Models\BaseModel;
use App\Models\Report\ReportDownloadConfig;
use App\Models\BaseTask;
use App\Models\Func\SysConfig;
use App\Models\Fund\Transaction;
use App\Models\Fund\TransactionType;
use App\Models\Basic\ThirdPlat;

use DB;
use DbTool;
use Cache;
use Illuminate\Support\Facades\Log;
use Tool;
use Carbon;
use Curl;

class Account extends BaseModel {

    const ERRNO_LOCK_FAILED         = -302;
    const RELEASE_DEAD_LOCK_NONE    = 0;
    const RELEASE_DEAD_LOCK_RUNNING = 1;
    const RELEASE_DEAD_LOCK_SUCCESS = 2;
    const RELEASE_DEAD_LOCK_FAILED  = 3;

    public static $releaseDeadLockMessages = [
        self::RELEASE_DEAD_LOCK_NONE => 'Unlocked',
        self::RELEASE_DEAD_LOCK_RUNNING => 'The Locker is Still Runing',
        self::RELEASE_DEAD_LOCK_SUCCESS => 'Released',
        self::RELEASE_DEAD_LOCK_FAILED => 'Unlock Failed!!!',
    ];
    public static $resourceName = 'Account';
    public static $amountAccuracy = 6;
    public static $htmlNumberColumns = [
        'balance' => 2,
        'available' => 2,
        'withdrawable' => 6,
        'frozen' => 2,
        'prohibit_amount' => 2,
    ];

    /**
     * The rules to be applied to the data.
     *
     * @var array
     */
    public static $rules = [
//        'bp_id' => 'required',
//        'bp_name' => 'required',
        'user_id' => 'required',
        'username' => 'required',
        'balance' => 'numeric',
        'frozen' => 'numeric',
        'available' => 'numeric',
        'withdrawable' => 'numeric',
        'prohibit_amount' => 'numeric',
        'status' => 'required|integer',
        'locked' => 'integer',
        'parent_id'    => 'required',
        'parent'       => 'integer',
        'forefathers'     => 'between:0,1024',
        'forefather_ids'  => 'between:0,100',
       ];
    public static $aReportType = [
        ReportDownloadConfig::TYPE_ACCOUNT => 0,
    ];
    protected $fillable = [
        'bp_id',
        'bp_name',
        'user_id',
        'username',
        'is_tester',
        'balance',
        'frozen',
        'available',
        'withdrawable',
        'prohibit_amount',
        'status',
        'locked',
    ];
    protected $table = 'accounts';
    protected $availableChanged = null;

    public function user() {
        return $this->hasOne('App\\Models\\User\\User', 'account_id');
    }

//    public static $columns = include(app_path() . '/lang/en/basic.php');

    protected function afterSave($oSavedModel) {
        if (!parent::afterSave($oSavedModel)) {
            return false;
        }
//        if ($this->availableChanged){
        $this->deleteAvailableBalanceCache();
//        }
        return true;
    }

    protected function beforeValidate() {
        parent::beforeValidate();
        $this->prohibit_amount >= 0 or $this->prohibit_amount = 0;
        $this->withdrawable >= 0 or $this->withdrawable = 0;
        $this->availableChanged = $this->isDirty('available');
    }

    public static function createAccount($oUser){
        $oAccount = new static;
//        $oAccount->bp_id = $oUser->bp_id;
//        $oAccount->bp_name = $oUser->bp_name;
        $oAccount->user_id = $oUser->id;
        $oAccount->username = $oUser->username;
        $oAccount->is_tester = $oUser->is_tester;
        $oAccount->withdrawable = 0;
        $oAccount->status = 1;
        $oAccount->parent_id = $oUser->parent_id ? $oUser->parent_id : 0;
        $oAccount->parent = $oUser->parent;
        $oAccount->forefathers = $oUser->forefathers;
        $oAccount->forefather_ids = $oUser->forefather_ids;
       $oAccount->save();
        Log::info($oAccount->getValidationErrorString());
//        var_dump($oAccount->errors());

        return $oAccount->save() ? $oAccount : false;
    }

    protected function getBalanceFormattedAttribute() {
        return $this->getFormattedNumberForHtml('balance',true);
    }

    protected function getFrozenFormattedAttribute() {
        return $this->getFormattedNumberForHtml('frozen',true);
    }

    protected function getAvailableFormattedAttribute() {
        return $this->getFormattedNumberForHtml('available',true);
    }

    /**
     * [getWithdrawableFormattedAttribute User's withdrawal money is the min value between available and withdrawal]
     * @return [Float] [Formatted number]
     */
    protected function getWithdrawableFormattedAttribute() {
        return number_format($this->getWithdrawableAmount(), 2);
//        return number_format(min($this->attributes['withdrawable'], $this->attributes['available']), static::$htmlNumberColumns['withdrawable']);
    }

    protected function getProhibitAmountFormattedAttribute() {
        return $this->getFormattedNumberForHtml('prohibit_amount',true);
//        return number_format($this->attributes['prohibit_amount'], static::$htmlNumberColumns['prohibit_amount']);
    }

    /**
     * 根据用户ID返回Account对象
     * @param int|array $mUserID
     * @return Collection|Account
     */
    public static function getAccountInfoByUserId($mUserID) {
        if (!$mUserID)
            return false;
        if (is_array($mUserID)) {
            return static::whereIn('user_id', $mUserID)->get();
        } else {
            return static::where('user_id', '=', $mUserID)->first();
        }
    }

    /**
     * 获取指定用户的可用余额
     * @param int $user_id
     * @return float
     */
    public static function getAvaliable($iUserId) {
//        Cache::setDefaultDriver('memcached');
//        $sCacheKey = static::compileAvailableBalanceCacheKey($iUserId);
//        if (!$fAvaliable = Cache::get($sCacheKey)){
        $oAccount = static::getAccountInfoByUserId($iUserId);
        $fAvaliable = $oAccount->available;
        unset($oAccount);
//            Cache::put($sCacheKey, $fAvaliable, 60);
//        }
        return number_format($fAvaliable, 4, '.', '');
    }

    public function deleteAvailableBalanceCache() {
        Cache::setDefaultDriver('memcached');
        $sCacheKey = static::compileAvailableBalanceCacheKey($this->user_id);
        Cache::forget($sCacheKey);
    }

    protected static function compileAvailableBalanceCacheKey($iUserId) {
        return 'user-avaliable-balance-' . $iUserId;
    }

    public function checkBalance() {
        if ($this->frozen < 0) {
            return false;
        }
        return $this->balance == number_format($this->frozen + $this->available, static::$amountAccuracy, '.', '');
    }

    /**
     * Lock Account
     * @param int $id
     * @param int $iLocker
     * @return Account|boolean
     */
    public static function lock($id, & $iLocker) {
        $iThreadId = DbTool::getDbThreadId();
        $iCount = static::where('id', '=', $id)->where('locked', '=', 0)->update(['locked' => $iThreadId]);
        if ($iCount > 0) {
            $iLocker = $iThreadId;
            return static::find($id);
        } else {
            static::addReleaseLockTask($id);
        }
        return false;
    }

    /**
     * Lock Account By User ID
     * @param int $iUserId
     * @param int $iLocker
     * @return Account|boolean
     */
    public static function lockByUserId($iUserId, & $iLocker) {
        $iThreadId = DbTool::getDbThreadId();
        $iCount = static::where('user_id', '=', $iUserId)->where('locked', '=', 0)->update(['locked' => $iThreadId]);
        if ($iCount > 0) {
            $iLocker = $iThreadId;
            return static::where('user_id', '=', $iUserId)->get()->first();
        }
        return false;
    }

    /**
     * 一次性对多个账户加锁
     * @param array $aUserIds
     * @param int $iLocker
     * @return boolean
     */
    public static function lockManyOfUsers($aUserIds, & $iLocker) {
        if (empty($aUserIds)) {
            return false;
        }
        is_array($aUserIds) or $aUserIds = explode(',', $aUserIds);
        $iCount = count($aUserIds);
//        pr($aUserIds);
//        pr($iCount);
        $iThreadId = DbTool::getDbThreadId();
        $iLockedCount = static::whereIn('user_id', $aUserIds)->where('locked', '=', 0)->update(['locked' => $iThreadId]);
//        pr($iLockedCount);
//        exit;
        if ($iLockedCount == $iCount) {
            $iLocker = $iThreadId;
            return static::whereIn('user_id', $aUserIds)->get();
        }
        return false;
    }

    /**
     * 一次性解锁多个账户
     * @param array $aUserIds
     * @param int $iLocker
     * @return boolean
     */
    public static function unlockManyOfUsers($aUserIds, $iLocker) {
        is_array($aUserIds) or $aUserIds = explode(',', $aUserIds);
        $iCount = count($aUserIds);
        $iThreadId = DbTool::getDbThreadId();
        $iLockedCount = static::whereIn('user_id', $aUserIds)->where('locked', '=', $iLocker)->update(['locked' => 0]);
        return $iLockedCount == $iCount;
    }

    /**
     * Unlock Account
     * @param int $id
     * @param int $iLocker
     * @param bool $bReturnObject
     * @return Account|boolean
     */
    public static function unLock($id, & $iLocker, $bReturnObject = true) {
        if (empty($iLocker))
            return true;
        $iCount = static::where('id', '=', $id)->where('locked', '=', $iLocker)->update(['locked' => 0]);
        if ($iCount > 0) {
            $iLocker = 0;
            return $bReturnObject ? static::find($id) : true;
        }
        return false;
    }

    /**
     * [getLockedAccounts Get all locked accounts]
     * @return [Object Array] [Locked accounts array]
     */
    public static function getLockedAccounts() {
        return static::where('locked', '>', 0)->get(['id', 'locked']);
    }

    /**
     * 根据用户ID返回Account对象
     * @param int|array $mUserID
     * @return Collection|Account
     */
    public static function getUserIdsByAvailable($fFromAccount, $fToAccount) {
        if (!empty($fFromAccount) && !empty($fToAccount)) {
            $aConditions['available'] = [ 'between', [$fFromAccount, $fToAccount]];
        } else if (!empty($fFromAccount)) {
            $aConditions['available'] = [ '>=', $fFromAccount];
        } else if (!empty($fToAccount)) {
            $aConditions['available'] = [ '<=', $fToAccount];
        }
        $aUserIds = [];
        if (isset($aConditions)) {
            $aColumns = ['id', 'user_id'];
            $oQuery = static::doWhere($aConditions);
            $aAccounts = $oQuery->get($aColumns);
            foreach ($aAccounts as $oAccount) {
                $aUserIds[] = $oAccount->user_id;
            }
        }
        return $aUserIds;
    }

    /**
     * 获取实际可提现余额，即账户余额和可提现余额中较小的金额
     * @return float
     */
    public function getWithdrawableAmount() {
        if (SysConfig::check('withdraw_use_prohibit_amount', true)) {
            $fWithdrawable = $this->available - $this->prohibit_amount;
        } else {
            $fWithdrawable = min($this->available, $this->withdrawable);
        }
        $fWithdrawable >= 0 or $fWithdrawable = 0;
        return formatNumber(intval($fWithdrawable * 100) / 100, 2);
    }

    /**
     * 强制解锁，用于解开未及时解开的锁。
     * 强烈提示：本方法不检查加锁者是否是当前进程，因此，需特别小心！！
     * @param int $id
     * @param int $iLocker
     * @return int
     *      self;:RELEASE_DEAD_LOCK_NONE: 未锁定
     *      self;:RELEASE_DEAD_LOCK_RUNNING：加锁的进程仍在运行中
     *      self;:RELEASE_DEAD_LOCK_SUCCESS：解锁成功
     *      self::RELEASE_DEAD_LOCK_FAILED：解锁失败
     */
    public static function releaseDeadLock($id, $iLocker = null) {
        !is_null($iLocker) or $iLocker = static::getLocker($id);
        if (!$iLocker) {
            return self::RELEASE_DEAD_LOCK_NONE;
        }
        $aDbThreads = DbTool::getDbThreads();
        if (!in_array($iLocker, $aDbThreads)) {
            return static::unLock($id, $iLocker, false) ? self::RELEASE_DEAD_LOCK_SUCCESS : self::RELEASE_DEAD_LOCK_FAILED;
        }
        return self::RELEASE_DEAD_LOCK_RUNNING;
    }

    /**
     * 返回加锁者
     * @param int $id
     * @return int | false
     */
    private static function getLocker($id) {
        if (empty($id)) {
            return false;
        }
        $oAccount = static::where('id', '=', $id)->get(['locked'])->first();
        return empty($oAccount) ? null : $oAccount->getAttribute('locked');
    }

    /**
     * 向队列增加解锁任务
     * @param int $id
     * @return bool
     */
    public static function addReleaseLockTask($id) {
        return BaseTask::addTask('ReleaseDeadAccountLock', ['id' => $id], 'main');
    }

    public function setWithdrawable($fAddAmount) {
        $this->withdrawable += $fAddAmount;
        return $this->save();
    }

    public function setProhibitAmount($fAddAmount) {
        $this->prohibit_amount += $fAddAmount;
        return $this->save();
    }

    /**
     * 代理盈亏总计
     * @param StringTool $sBeginDate  开始日期
     * @param StringTool $sEndDate    结束日期
     * @param int $iUserId         用户id
     * @return array
     */
    public static function getAccountSumInfo($aConditions) {
        $oQuery = DB::table('accounts')->select(DB::raw('sum(balance) total_balance, sum(frozen) total_frozen,sum(available) total_available, sum(prohibit_amount) total_prohibit_amount'));
        $aResult = static::queryByConditions($oQuery, $aConditions)->first();
        return objectToArray($aResult);
    }

    /**
     * 批量设置查询条件，返回Query实例
     *
     * @param array $aConditions
     * @return Query
     */
    public static function queryByConditions($oQuery, $aConditions = []) {
        is_array($aConditions) or $aConditions = [];
        foreach ($aConditions as $sColumn => $aCondition) {
            $statement = '';
            switch ($aCondition[0]) {
                case '=':
                    if (is_null($aCondition[1])) {
                        $oQuery = $oQuery->whereNull($sColumn);
                    } else {
                        $oQuery = $oQuery->where($sColumn, '=', $aCondition[1]);
                    }
                    break;
                case 'in':
                    $array = is_array($aCondition[1]) ? $aCondition[1] : explode(',', $aCondition[1]);
                    $oQuery = $oQuery->whereIn($sColumn, $array);
                    break;
                case '>=':
                case '<=':
                case '<':
                case '>':
                case 'like':
                    if (is_null($aCondition[1])) {
                        $oQuery = $oQuery->whereNotNull($sColumn);
                    } else {
                        $oQuery = $oQuery->where($sColumn, $aCondition[0], $aCondition[1]);
                    }
                    break;
                case '<>':
                case '!=':
                    if (is_null($aCondition[1])) {
                        $oQuery = $oQuery->whereNotNull($sColumn);
                    } else {
                        $oQuery = $oQuery->where($sColumn, '<>', $aCondition[1]);
                    }
                    break;
                case 'between':
                    $oQuery = $oQuery->whereBetween($sColumn, $aCondition[1]);
                    break;
            }
        }
//        exit;
        if (!isset($oQuery)) {
            $oQuery = static::where('id', '>', '0');
        }
        return $oQuery;
    }

    /**
     * 下载报表实现类，根据不同model，下载报表内容不同
     * @param int $iReportType      报表类型
     * @param int $iFreqType        下载频率类型，如：每天，每周，每月等
     */
    public function download($iReportType, $aDownloadTime, $sFileName, $sDir = './') {
        $oQuery = static::where('is_tester', '=', 0);
        $aConvertFields = [
            'is_tester' => 'boolean',
        ];

        $aUser = User::getTitleList();
        $aColumn = ManAccount::$columnForList;
        $aData = $oQuery->get($aColumn);
        $aData = $this->makeData($aData, $aColumn, $aConvertFields, null, $aUser);
        return $this->downloadExcel($aColumn, $aData, $sFileName, $sDir);
    }

    public function makeData($aData, $aFields, $aConvertFields, $aBanks = null, $aUser = null) {
        $aResult = array();
        foreach ($aData as $oDeposit) {
            $a = [];
            foreach ($aFields as $key) {
                if ($oDeposit->$key === '') {
                    $a[] = $oDeposit->$key;
                    continue;
                }
                if (array_key_exists($key, $aConvertFields)) {
                    switch ($aConvertFields[$key]) {
                        case 'boolean':
                            $a[] = $oDeposit->$key ? __('Yes') : __('No');
                            break;
                    }
                } else {
                    $a[] = $oDeposit->$key;
                }
            }
            $aResult[] = $a;
        }
        return $aResult;
    }

    /**
     * [autoCharge 自动充值]
     * @param  [Object] $oCustomer [商户对象]
     * @param  [Array] $aParams    [参数数组]
     * @return [Boolean]           [是否充值成功]
     */
    public function autoCharge($oCustomer, $aParams, & $aResponse) {
        $oUser = $this->user;
        $aAutoChargeCustomerInfo = config('auto-charge-customers.' . $oCustomer->identity);
        $sUrl = $aAutoChargeCustomerInfo['url'];
        $aDefaultParams = $aAutoChargeCustomerInfo['default_param'];
        ksort($aParams);
        $aDefaultParams['data'] = json_encode($aParams);
        ksort($aDefaultParams);

        $sSign = md5(http_build_query($aDefaultParams) . $oCustomer->key);
        $aDefaultParams['sign'] = $sSign;
        // pr($sUrl);
        // pr($aParams);
        // pr($aDefaultParams);
        $aResponse = Curl::to($sUrl)->withData($aDefaultParams)->post();
        @file_put_contents('/tmp/debug', 'url: ' . $sUrl . ', params: ' . json_encode($aDefaultParams) . ', data: ' . $aResponse . ', decode data: ' . json_encode(json_decode(base64_decode($aResponse), true)) . "\n", FILE_APPEND);
        $aResponse = json_decode(base64_decode($aResponse), true);
        // pr($aResponse);exit;
        if ($aResponse && isset($aResponse['errno']) && intval($aResponse['errno']) === 0) {
            $aAttributes   = [
                'client_ip'   => Tool::getClientIp(),
                'proxy_ip'    => Tool::getProxyIp(),
                'coefficient' => 1.000,
                'bp_id'       => $oCustomer->id,
                'bp_name'     => $oCustomer->name,
            ];
            $iReturn = Transaction::addTransaction($oUser, $this, TransactionType::TYPE_DEPOSIT, $aParams['amount'], $aAttributes);
            if ($iReturn != Transaction::ERRNO_CREATE_SUCCESSFUL) {
                return false;
            }
            // 添加充提统计
            $statData    = [
                'type'    => 'withdrawal',
                'user_id' => $oUser->id,
                'bp_id'   => $oUser->bp_id,
                'amount'  => $aParams['amount'],
                'date'    => Carbon::now()->toDateString(),
            ];
            BaseTask::addTask('StatUpdateProfit', $statData, 'stat');
            return true;
        }
        return false;
    }

    public static function userPlatBalance($userName) {
      $aPlats = ThirdPlat::get();
      $oPlat = $aPlats[0];
      $aConfig = [
          'api_key' => $oPlat->key,
          'api_server' => [
              'data-api' => $oPlat->data_url
          ],
          'identity' => $oPlat->plat_identity
      ];
      $oTransferService = ServiceFactory::getService('transfer', $aConfig, true);
      $result = $oTransferService->balance($userName);
      $fPlatBalance = @$result['code'] == 1 ? @$result['data']['balance'] : 'date-error';
      return $fPlatBalance;
    }

    public static function userPlatBalances($aUsernames) {



      $aPlats = ThirdPlat::get();
      $oPlat = $aPlats[0];
      $aConfig = [
          'api_key' => $oPlat->key,
          'api_server' => [
              'data-api' => $oPlat->data_url
          ],
          'identity' => $oPlat->plat_identity
      ];
      $oTransferService = ServiceFactory::getService('transfer', $aConfig, true);
      $result = $oTransferService->userBalances($aUsernames);
      $aPlatBalance = @$result['code'] == 1 ? @$result['data']['user_balances'] : [];
      return $aPlatBalance;
    }



}
