<?php

namespace App\Jobs;

use App\Models\BaseTask;
use App\Models\Bet\Program;
use App\Models\Bet\Bill;
use App\Models\Basic\BusinessPartner;

use App\Services\ServiceFactory;
use DB;
use Carbon;

class PrintTicket extends BaseTask
{

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    protected function doCommand() {
        $iProgramId = $this->data['program_id'];
        $oProgram = Program::find($iProgramId);
        $sMsg = "Program $oProgram->sn print ticket ";
        DB::beginTransaction();
        if (!$bSucc = $oProgram->updateValidStatus(Program::STATUS_TICKET_PRINTED)) {
            $sMsg .= ['failed', 'succeed'][intval($bSucc)];
            $this->logger->addInfo($sMsg);
            DB::rollback();
            return self::TASK_RESTORE;
        }
        $oCustomer = BusinessPartner::find($oProgram->bp_id);
        foreach ($oProgram->bills()->get() as $key => $oBill) {
            if (!$bSucc = $oBill->updateValidStatus(Bill::STATUS_TICKET_PRINTED)) break;
            // TODO 出票时更新赔率
            // $aBillData = $oBill->toArray();
            // $aBillContent = json_decode($oBill->bet_content, true);
            if (!$bSucc = $oBill->updateBetData($oCustomer, $aBillGameExtraData, $aFailedDesc)) {
                $this->logger->addInfo('update bill odd failed. errors: ' . implode(', ', $aFailedDesc));
                break;
            }
            $this->logger->addInfo('update bill odd :' . var_export($oBill->toArray(), true));
        }
        if (!$bSucc) {
            $sMsg .= ['failed', 'succeed'][intval($bSucc)];
            $this->logger->addInfo($sMsg);
            DB::rollback();
            return self::TASK_RESTORE;
        }


        DB::commit();
        $aBaseStatData = [
            'user_id'    => $oProgram->user_id,
            'bp_id'      => $oProgram->bp_id,
        ];
        $aTurnoverData = array_merge($aBaseStatData, [
            'type'       => 'turnover',
            'amount'     => $oProgram->amount,
            'date'       => $oProgram->bought_at ? Carbon::parse($oProgram->bought_at)->toDateString() : Carbon::now()->toDateString(),
        ]);
        BaseTask::addTask('StatUpdateProfit', $aTurnoverData, 'stat');
        return self::TASK_SUCCESS;
    }
}
