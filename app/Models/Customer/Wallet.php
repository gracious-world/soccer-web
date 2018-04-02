<?php

namespace App\Models\Customer;

use App\Models\BaseModel;

use App\Models\BaseTask;
use DB;
use DbTool;
use Cache;

class Wallet extends BaseModel {
    static $cacheLevel = self::CACHE_LEVEL_NONE;
    protected static $cacheMinutes = 0;

    const ERRNO_LOCK_FAILED = -402;

    const STATUS_ACTIVATED = 1;
    const STATUS_INACTIVATED = 0;

    const RELEASE_DEAD_LOCK_NONE    = 0;
    const RELEASE_DEAD_LOCK_RUNNING = 1;
    const RELEASE_DEAD_LOCK_SUCCESS = 2;
    const RELEASE_DEAD_LOCK_FAILED  = 3;

    public static $releaseDeadLockMessages = [
        self::RELEASE_DEAD_LOCK_NONE    => 'Unlocked',
        self::RELEASE_DEAD_LOCK_RUNNING => 'The Locker is Still Runing',
        self::RELEASE_DEAD_LOCK_SUCCESS => 'Released',
        self::RELEASE_DEAD_LOCK_FAILED  => 'Unlock Failed!!!',
    ];

    protected $table = 'wallets';
    public static $resourceName = 'Wallet';

    public static $columnForList = [
        // 'bp_id',
        'bp_name',
        // 'is_tester',
        'balance',
        // 'frozen',
        // 'available',
        // 'withdrawable',
        // 'prohibit_amount',
        'status',
        // 'locked',
    ];
    public static $mainParamColumn = 'bp_identity';

    public static $rules = [
        'bp_id'           => 'required|integer',
        'bp_identity'     => 'required|max:50|unique:wallets,bp_identity,',
        'bp_name'         => 'max:50',
        'is_tester'       => 'boolean',
        'balance'         => 'numeric',
        'frozen'          => 'numeric',
        'available'       => 'numeric',
        'withdrawable'    => 'numeric',
        'prohibit_amount' => 'numeric',
        'status'          => 'required|boolean',
        'locked'          => 'integer',
    ];
    protected $fillable = [
        'bp_id',
        'bp_identity',
        'bp_name',
        'is_tester',
        'balance',
        'frozen',
        'available',
        'withdrawable',
        'prohibit_amount',
        'status',
        'locked',
    ];

    // public static $amountAccuracy = 6;
    public static $htmlNumberColumns = [
        'balance'         => 2,
        'available'       => 2,
        'withdrawable'    => 6,
        'frozen'          => 2,
        'prohibit_amount' => 2,
    ];

    public function customer() {
        return $this->hasOne('App\\Models\\Basic\\BusinessPartner', 'wallet_id');
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

    protected function getWithdrawableFormattedAttribute() {
        return $this->getFormattedNumberForHtml('withdrawable',true);
    }

    protected function getProhibitAmountFormattedAttribute() {
        return $this->getFormattedNumberForHtml('prohibit_amount',true);
    }

    public static function getActivateWallet($sIdentity) {
        return self::where('bp_identity', $sIdentity)->where('status', self::STATUS_ACTIVATED)->first();
    }

    /**
     * [getFormattedNumberForHtml 获取格式化后的数字，用于金额显示]
     * @param  StringTool  $sColumn   [要格式化的字段]
     * @param  boolean $bTruncate [是否去除多余小数]
     * @return int                [格式化后的数字]
     */
    protected function getFormattedNumberForHtml($sColumn, $bTruncate = false) {
        $iAccuracy = isset(static::$htmlNumberColumns[$sColumn]) ? static::$htmlNumberColumns[$sColumn] : static::$amountAccuracy;
        $fNumber = $this->{ $sColumn };
        if ($bTruncate){
            $iBaseNumber = pow(10,$iAccuracy);
            return number_format(intval($fNumber * $iBaseNumber) / $iBaseNumber, $iAccuracy);
        }
        else{
            return number_format($this->{ $sColumn }, $iAccuracy);
        }
    }

    public static function createNewWallet($oBusinessPartner) {
        $aData = [
            'bp_id'       => $oBusinessPartner->id,
            'bp_identity' => $oBusinessPartner->identity,
            'bp_name'     => $oBusinessPartner->name,
            'is_tester'   => $oBusinessPartner->is_tester,
            'status'      => self::STATUS_ACTIVATED,
        ];
        $oWallet = new Wallet($aData);
        $bSucc = $oWallet->save(self::$rules);
        return $bSucc ? $oWallet : $bSucc;
    }

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

    public static function addReleaseLockTask($id) {
        return BaseTask::addTask('ReleaseDeadWalletLock', ['id' => $id], 'main');
    }

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
     * [getLockedWallets Get all locked accounts]
     * @return [Object Array] [Locked accounts array]
     */
    public static function getLockedWallets() {
        return static::where('locked', '>', 0)->get(['id', 'locked']);
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
        $oWallet = static::where('id', '=', $id)->get(['locked'])->first();
        return empty($oWallet) ? null : $oWallet->getAttribute('locked');
    }

}
