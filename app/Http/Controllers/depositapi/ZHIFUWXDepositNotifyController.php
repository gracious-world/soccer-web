<?php

class ZHIFUWXDepositNotifyController extends BaseDepositNotifyController {
    protected $platformIdentifier = 'zhifuwx';
    protected $test = false;

    protected function & mkTestData() {
        $data=array (
            'trade_no' => '1195040167',
            'sign_type' => 'RSA-S',
            'notify_type' => 'offline_notify',
            'merchant_code' => '1111110166',
            'order_no' => '244023674578f02b10fa2d',
            'trade_status' => 'SUCCESS',
            'sign' => 'fmwcQ5iumm8y96q+zZkGVICu5YaF44dE1eP/s4OimQ9DxfVF6Gnuj9LSInDc9zlW9OUDG5ZFj95vlZLBhmF2WQCdTqdcwgz2GuCXecqC3pUbpmbJlOhkkGdWHctC5CCIkXdTm64QWR5YF/bEoBl+yxGk0A3ZHCjOqlGn2shMuck=',
            'order_amount' => '0.1',
            'interface_version' => 'V3.0',
            'bank_seq_no' => '7551410036201607205494891680',
            'order_time' => '2016-07-20 12:48:49',
            'notify_id' => '59bcbbcb621448e48e7f684ebb52c728',
            'trade_time' => '2016-07-20 12:48:50',
        );
        return $data;
    }


    public function checkSign(& $sSign)
    {
        $aData  = $this->test ? $this->mkTestData() : trimArray(Input::all());
        $this->Platform->dataAll=$aData;
        $sPostedSign = $aData[$this->Platform->signColumn];
        $this->clearNoSignValues();
        $this->PaymentAccount = PaymentAccount::getAccountByNo($this->Payment->id, $aData[$this->Platform->accountColumn]);
        return $this->Platform->compileSignReturn($aData,$sPostedSign,$this->PaymentAccount->public_key);
    }
    
}
