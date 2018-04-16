<?php

class HUIYUANDepositNotifyController extends BaseDepositNotifyController {
    protected $platformIdentifier = 'huiyuan';
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
            'merchantId'            => 'yaofacai01',
            'orderId'               => '106427044958368f9d326ef',
            'realAmount'            => '56.00',
            'payResult'             => '1',
            'hmac'                  => '528c0381cd117c1fe44a397737451d7d',
            'bankId'                => 'test1479977027',
            'cardNo'                => '',
            'remark'                => '',
            'msg'                   => '',
        ];
        return $aData;

    }

}
