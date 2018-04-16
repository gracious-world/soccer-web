<?php

class HUIYUANZFBDepositNotifyController extends BaseDepositNotifyController {
    protected $platformIdentifier = 'huiyuanzfb';
    protected $test = false;

    protected function & mkTestData() {
        /*
        $iTestDeposit      = 183;
        $oDeposit          = Deposit::find($iTestDeposit);
        $data          = [
            'merchant_code'      => $oDeposit->merchant_code,
            'notify_type'        => 'page_notify', //通知类型
            'notify_id'          => '235dst58fd1dwe21354fdafdaesfdsaf', //通知校验ID
            'interface_version'  => 'V3.0', //接口版本
            'order_no'           => $oDeposit->order_no, //商家订单号
            'order_time'         => $oDeposit->created_at, //商家订单时间
            'order_amount'       => $oDeposit->amount, //商家订单金额
            'extra_return_param' => '', //回传参数
            'trade_no'           => mt_rand(1000000, 9999999), //智付交易定单号
            'trade_time'         => date('Y-m-d H:i:s'), //智付交易时间
            'trade_status'       => 'SUCCESS', //交易状态 SUCCESS 成功  FAILED 失败
            'bank_seq_no'        => mt_rand(1000000, 9999999), //银行交易流水号
        ];
        $data['sign']  = UserDeposit::compileSignZf($data, $oDeposit->merchant_key);
        $data['sign_type'] = 'MD5';
        return $data;
        */

        $aData          = [
            'sign'              => 'a26c1d7dbcc3a00fc191c87e5316f9bf',
            'pay_amt'           => '2.00',
            'result'            => '1',
            'jnet_bill_no' => '20161125000013978581',
            'remark' => '',
            'pay_type' => '50',
            'agent_bill_id' => '182708140758382ee1b2766',
            'agent_id' => 'yaofacai01',
            'pay_message' => '支付宝支付成功',
        ];
        return $aData;

    }

}
