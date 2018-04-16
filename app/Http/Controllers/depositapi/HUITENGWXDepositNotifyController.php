<?php

class HUITENGWXDepositNotifyController extends BaseDepositNotifyController {

    protected $platformIdentifier = 'huitengwx';
    protected $test = false;

    protected function & mkTestData() {
        $data = array (
            'respCode' => '1',
            'orderNo' => '331375341597999d6c59b9',
            'orderDate' => '20170727154504',
            'transId' => '1',
            'respDesc' => '操作成功',
            'type' => '1',
            'transAmt' => '100',
            'signature' => 'C0E8C80AFB40CB42B692D39373F0EA1C',
            'merNo' => '820170425140697',
        );
        return $data;
    }
}
