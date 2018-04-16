<?php

class YUNSHENGDepositNotifyController extends BaseDepositNotifyController {
    protected $platformIdentifier = 'yunsheng';
    protected $test = false;

    protected function & mkTestData() {

        $data=array (
            'billno' => '149120977958e38c650dc15',
            'merchantId' => '2000002137',
            'orderId' => '20170404201311849233384357105664',
            'amount' => '2.00',
            'orderDate' => '20170401200701',
            'success'=>'Success',
            'sign'=>'d07263a80b274aa2625f48a971ce721a'
        );
        return $data;
    }
}
