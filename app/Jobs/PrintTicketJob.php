<?php

namespace App\Jobs;

use App\Jobs\BaseJob;

use App\Models\Bet\Bill;
// use App\Models\Basic\BusinessPartner;

// use Curl;

# TODO 已完成计奖的注单推送给对应商户
class PrintTicketJob extends BaseJob
{
    // protected $logName = 'PrintTicket';
    protected $logFile = 'update-bill-status';
    public $bill;
    public function __construct(Bill $bill)
    {
        parent::__construct();
        $this->bill = $bill;
    }


    public function handle()
    {
        if ($this->bill->status == Bill::STATUS_WAITING && $bSucc = $this->bill->update(['status' => Bill::STATUS_TICKET_PRINTED])) {
            $this->writeLog('Print ticket success.', ['bill' => $this->bill->toArray()]);
        }
    }
}
