<?php

class YUNSHENGZFBDepositNotifyController extends BaseDepositNotifyController {
    protected $platformIdentifier = 'yunshengzfb';
    protected $test = false;

    protected function & mkTestData() {

        $data = array (
            'billno' => '4492905675901927f98eef',
            'merchantId' => '2000002137',
            'orderId' => '20170427144104857484726208958464',
            'success' => 'Success',
            'amount' => '1.000000',
            'orderDate' => '20170427144103',
            'sign' => '10252e7897d94b9478b2cf32de3f47e7',
        );

        return $data;
    }
}
