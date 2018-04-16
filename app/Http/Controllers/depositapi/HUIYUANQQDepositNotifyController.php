<?php

class HUIYUANQQDepositNotifyController extends BaseDepositNotifyController {
    protected $platformIdentifier = 'huiyuanqq';
    protected $test = false;

    protected function & mkTestData() {
        $aData          = [
            'sign'              => 'c7f67f08c39643131e26cb2c9a02fe47',
            'pay_amt'           => '2.00',
            'result'            => '1',
            'jnet_bill_no' => '20161125000013978581',
            'pay_type' => '30',
            'agent_bill_id' => '4334116565897d93d23796',
            'agent_id' => 'yaofacai01',
            'remark' => 'huiyuanwx_query'
        ];
        return $aData;
    }

    protected function checkSign(& $sSign) {
        $data = $this->test ? $this->mkTestData() :$this->params;
        $sPostedSign          = $this->params[$this->Platform->signColumn];
        $this->clearNoSignValues();
        $this->PaymentAccount = PaymentAccount::getAccountByNo($this->Payment->id, $this->params[$this->Platform->accountColumn]);
        $sSign                = $this->Payment->compileSignReturn($this->PaymentAccount, $data);
        
        return $sSign == $sPostedSign;
    }
}
