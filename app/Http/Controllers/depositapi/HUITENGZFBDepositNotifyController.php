<?php

class HUITENGZFBDepositNotifyController extends HUITENGWXDepositNotifyController {

    protected $platformIdentifier = 'huitengzfb';
    protected $test = false;

    protected function & mkTestData() {
        $iTestDeposit = 388260;
        $oDeposit = Deposit::find($iTestDeposit);
        $data = [
            'orderNo' => $oDeposit->order_no,
            'status' => '02',
            'beginTime' => '20170517172347',
            'endTime' => '20170517172409',
            'userOrderNo' => '1897814653591c16a1696fb',
            'orderAmt' => $oDeposit->amount,
            'signature' => '92E3D91AC00D8B58E79DCB6EB91A2246',
        ];
        $data['signature'] = md5($data['orderNo'] . $data['userOrderNo'] . '082EADC1628904889AD5BF3070CB9B7F');
        return $data;
    }
}
