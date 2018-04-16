<?php

class HUITENGDepositNotifyController extends BaseDepositNotifyController {


    protected $platformIdentifier = 'huiteng';
    protected $test = false;

    protected function & mkTestData() {
        $iTestDeposit = 388272;
        $oDeposit = Deposit::find($iTestDeposit);
        $data = array (
        'respCode' => '0000',
        'orderNo' => $oDeposit->order_no,
        'orderDate' => '20170519',
        'transId' => '1',
        'respDesc' => '交易成功',
        'transAmt' => $oDeposit->amount,
        'merNo' => '820170425140697',
        'productId' => '0111',
        );
        $data['signature'] = md5($data['orderNo'] . $data['productId'] . '082EADC1628904889AD5BF3070CB9B7F');
        return $data;
        
    }

    /**
     * 没有返回商户号，所以只适合一个商户号的情况
     * @param type $sSign
     * @return type
     */
    protected function checkSign(& $sSign) {
        $sPostedSign = $this->params[$this->Platform->signColumn];
//        $this->clearNoSignValues();
//        pr($this->params);
        $this->PaymentAccount = PaymentAccount::getAccountByNo($this->Payment->id, '820170425140697');
//        pr($this->PaymentAccount->toArray());
//        pr($this->params);
        $sSign = $this->Payment->compileSignReturn($this->PaymentAccount, $this->params);
//        pr($sSign);
//        exit;
        return strcasecmp($sSign, $sPostedSign) === 0 ;
    }

}
