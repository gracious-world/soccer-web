<?php
/**
 * Created by PhpStorm.
 * User: damon
 * Date: 10/22/15
 * Time: 1:13 PM
 */
class GFBDepositNotifyController extends BaseDepositNotifyController {
    protected $platformIdentifier = 'gfb';
    protected $test = false;

    protected function & mkTestData() {
        $iTestDeposit      = 28;
        $oDeposit          = Deposit::find($iTestDeposit);
        $data          = [
            'version'   => '2.1',
            'charset'   => '1',
            'language'  => '1',
            'signType'  => '1',
            'tranCode'  => '8888',
            'merchantID'    => $oDeposit->merchant_code,//商户代码
            'merOrderNum'   => $oDeposit->order_no, //订单号
            'tranAmt'       => $oDeposit->amount,//金额
            'feeAmt'        => '',//佣金
            'frontMerUrl'   => '',//通知接口
            'backgroundMerUrl'  => '', //异步通知接口
            'tranDateTime'      => date("YmdHis",strtotime($oDeposit->created_at)), //订单生成时间
            'tranIP'            => $oDeposit->ip,//ip
            'respCode'          => '0000', //响应码
            'msgExt'            => '', //响应结果附加信息
            'orderId'           => 123, //国付宝内部订单号
            'gopayOutOrderId'   => 456, //网关发往银行的流水号
            'bankCode'          => 'ICBC', //银行简称
            'tranFinishTime'    => date('YmdHis'),//交易完成时间
            'signValue'         => '9231967c8c4ed3fa9da6c311cbb0b443'//秘文串
        ];
        $data['sign']  = UserDeposit::compileSignZf($data, $oDeposit->merchant_key);
        $data['sign_type'] = 'MD5';
        return $data;
    }


}