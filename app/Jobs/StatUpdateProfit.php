<?php

namespace App\Jobs;

use App\Models\BaseModel;
use App\Models\BaseTask;

use App\Models\Stat\UserProfit;
use App\Models\Stat\BpProfit;
use App\Models\User\User;

use Carbon;
use DB;
/**
 * 更新盈亏程序
 */
class StatUpdateProfit extends BaseTask {

    protected $logNormal = true;

    public function fire($job, $data) {
        parent::fire($job, $data);
    }

    protected function doCommand() {
        // pr($this->data);
        extract($this->data);

        if (!$type || !$user_id || !$bp_id || !$amount || !$date) {
            $this->log = "ERROR: Invalid Data, Exiting";
            return self::TASK_SUCCESS;
        }
        $oUser = User::find($user_id);
        if ($oUser->is_tester) return self::TASK_SUCCESS;
        $date = Carbon::parse($date)->toDateString();
        // pr($date);
        DB::beginTransaction();

        // 更新用户盈亏数据
        if (!$bSucc = UserProfit::updateProfitData($type, $date, $user_id, $amount)) {
            DB::rollback();
            $this->log = "User Profit Update Failed";
            return self::TASK_RESTORE;
        }

        // 更新商户盈亏数据
        if (!$bSucc = BpProfit::updateProfitData($type, $date, $bp_id, $amount)) {
            DB::rollback();
            $this->log = "Business Partner Profit Update Failed";
            return self::TASK_RESTORE;
        }

        // 更新日盈亏数据
        // if (!$bSucc = Profit::updateProfitData($type, $date, $amount, $oUser)) {
        //     DB::rollback();
        //     $this->log = "Profit Update Failed";
        //     return self::TASK_RESTORE;
        // }

        if (!isset($lottery_id)) {
            DB::commit();
            return self::TASK_SUCCESS;
        }

        // 更新彩种数据 $sType, $sDate, $iLotteryId, $oUser, $fAmount
        // $oLottery = ManLottery::find($lottery_id);
        // if (!$bSucc = LotteryProfit::updateProfitData($type, $date, $lottery_id, $oUser, $amount)) {
        //     DB::rollback();
        //     $this->log = "ManLottery Profit Update Failed";
        //     return self::TASK_RESTORE;
        // }

        // // 更新奖期数据
        // if (!$oLottery->is_instant){
        //     $bSucc = IssueProfit::updateProfitData($type, $lottery_id, $issue, $oUser, $amount);
        // }
        // if (!$bSucc) {
        //     DB::rollback();
        //     $this->log = "Issue Profit Update Failed";
        //     return self::TASK_RESTORE;
        // } else {
        //     $this->log = "Successful";
        //     DB::commit();
        //     return self::TASK_SUCCESS;
        // }
    }

}
