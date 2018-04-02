<?php
namespace App\Jobs;

use App\Models\BaseTask;
use App\Models\Customer\Wallet;
/**
 * 检查指定的用户资金账户，如果加锁者已停止运行，则强制解开，如果失败，则会恢复任务
 *
 * @author Winter
 */
class ReleaseDeadWalletLock extends BaseTask {

    protected function doCommand() {
        extract($this->data, EXTR_PREFIX_ALL, 'wal');
        if (!$wal_id) return self::TASK_SUCCESS;
        isset($wal_locker) or $wal_locker = null;

        $iReturn = Wallet::releaseDeadLock($wal_id,$wal_locker);
        $this->log = " $iReturn: " . Wallet::$releaseDeadLockMessages[$iReturn];
        return ($iReturn == Wallet::RELEASE_DEAD_LOCK_FAILED) ? self::TASK_RESTORE : self::TASK_SUCCESS;
    }

}