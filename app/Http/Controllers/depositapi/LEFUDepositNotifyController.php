<?php

class LEFUDepositNotifyController extends BaseDepositNotifyController {
    protected $platformIdentifier = 'lefu';
    protected $test = false;
    protected $aContent;
    protected $amountColumn = "amount_str";
    protected $sContentJson;
    protected $fAmount;

    protected function & mkTestData() {
//        $id = 780;
//        $oDeposit = UserDeposit::find($id);
//        $content = '{"trade_id":"TT2017021312877214","out_trade_no":"6874332558a19df8ab2e9","amount_str":1000.00,"amount_fee":16.000000,"status":1,"for_trade_id":null,"business_type":11,"create_time":"2017-02-13 12:05:14","modified_time":"2017-02-13 12:05:33","remark":null}';
//        $data = [
//                'input_charset' => "UTF-8",
//                'sign_type' => 'MD5',
//                'sign' => 'd5708a22d40578e34a541fba92d1a888',
//                'request_time' => $oDeposit->created_at->toDateTimeString(),
//                'content' => $content,
//                'out_trade_no' => $oDeposit->order_no,
//                'status' => 1, //1交易状态成功  2失败
//                'amount_str' => $oDeposit->amount,//TODO fix
//                'status' => 1,
//                'tran_ip' => $oDeposit->ip
//        ];
//        $data['sign_type'] = 'MD5';


//        contentÃ÷ÎÄ:{"trade_id":"TT2017021612366348","out_trade_no":"90355926958a52fc9c3ffc","amount_str":1.000000,"amount_fee":0.004500,"status":1,"for_trade_id":null,"business_type":7,"create_time":"2017-02-16 12:51:22","modified_time":"2017-02-16 12:53:32","remark":null}
        $aData = [
                'out_trade_no' => '9594696958a5986beeeb6',
                'request_time' => '2017-02-16 20:20:38',
                'input_charset' => 'utf-8',
                'sign' => 'xOa7SHATq%2Bl6oiLUYD9VwCEkZ3h6tl8csYEZwE0DZwwyDUn35y84m2wxTEEMbV5Bt4Qh0H0aQ%2FPZpBkRK3LVXZm%2FYbKlVpZ%2Fta0xfgL%2FDZxkjNFE2ySIce2GJb2Z%2FtII7J%2FP0f6XwrsT6szj4kC6U%2F5qHMkUqIx4qcqokbOpRos%3D',
                'sign_type' => 'SHA1WithRSA',
                "content"=>"Qu%2BPQorIR6smYj621h%2BBWUaiBcPpdJF3dJ%2B3KjxE2Xfl1p3kZAl9IYc1%2B61InMcetHaw1mdlLFGQhdDUYLzFDWX37pec25JdpA2%2BxjYOx6qP3C5MqF%2FnLsvLCmidEgqPO4PxUIy9bbYuaosiaQ0XZnHy4%2BbFZNS6fbM7HMBudMRcLONwUXWWjDr6E7df%2Fy9QDc0Nfz%2FQHBUQ0fu1wIHsNgvx1bHN7LaQWptLK8GlUwPpsi5VPUv84cwM1NGARwS%2BVeNZPqnbeORoPogMkK65xeta39Eaar2ZWcRWNgu%2FHx2r%2FsBgRM7Sl7x0unqgH2Aw5yBEkjhu4SO%2BqrVvIASczTGGmS6ZmUJUG6NXYk%2FuNNKZ7WCqFPQ83Kkmkn6YF5gICDTZ%2BvG7LQJqLquQ05NStsRxtzK2n1ExjyPPVceAWHCDiMoS2hGjpuM8it%2Fj8gmgXcOQ26DfOIZgBGDF8A0tmIoazfcGVExVeRqhMXmFRKzfx3p3je0Sbdu3TXxkT8Mu",
                'status' => 1
        ];
        return $aData;

//        $data = [
//                'out_trade_no' => '166407483658a43a6309708',
//                'request_time' => '2017-02-15 19:45:40',
//                'input_charset' => 'utf-8',
//                'sign' => 'j96c3itpB2%2FzGFLvQ4MqkB72%2FLGTp2ganZgYNZIwvCgQfT172H8MxPNNb%2BXHGRznQWgv4nFiPZVkrSOFcUP1Dr1ZjmKha%2FJl8zuQgqjjqknlbA7YjNqCKVA4cx46zQjKVKnKlRzgqWYCeGOoskJJEqdVm3DxC1nvlRTQcCsCjtQ%3D',
//                'sign_type' => 'SHA1WithRSA',
//                'content' => 'VAS2xkhSle%2FnLSfv7af%2B9fGWQkgkTWjJDxd4P2mE7hNjFjBvo8StEaf7%2B3FLXeaUE5mRCYRPldA7NDEUv1NIityvNuDHIpiaVaA0Sn4nW%2FDot%2Fd28MskpGuYvZdkdyITh7XS5MpOSIvy7AonFF93iZJD%2FZjb9lV44QurVaT4%2Fzt6c0iH3xeDI8v1kaOUbH5QJ5zACy6mgNBDpKnHuuvGYXS7MH5lRzQUI2N%2B72EelVtXQXVJGgKnksDUuIv%2FWawsHYuIX5llBR7bdfBAak6tnNyNY3mrNPXL8XSwWYQG1nQZg8s%2BsM%2BlCUXICxke%2BLvmWgqrpI4ny3HC8pvOku814RyF9Q%2FNtd9Kf54yI3ga0%2FGj0RuwBLmq892U02tprJA81D0ElstjT4x0vG4kp6NaqsIvQPvjZ%2FlnMEeLoTgaP7ytDSToPl2uCaBK%2BE2OMLE%2FlyXWHL1ajGqNFwjf8goWH4AvH55XJoKT%2F82NxUN7PNIjRqr2QYmB63%2BcXE6zXF48',
//                'status' => '1',
//                'merchant_code' => '100179',
//                'order_no' => '166407483658a43a6309708',
//        ];
//        return $data;
    }

    /**
     * get user deposits
     *
     * @author lucky
     */
    protected function init() {
        parent::init();
        $this->writeLog(json_encode($this->params));
        if (empty($this->params) || !isset($this->params['status'])) {
            $this->halt('invalid callback', false);
        }

        //get the user deposits
        $iOrderNo = $this->params['out_trade_no'];
        if (!$this->Platform->oDeposit = UserDeposit::Where("order_no", $iOrderNo)->first()) {
            $this->writeLog("order_no=$iOrderNo not found deposits");
            exit;
        }

        $this->PaymentAccount = PaymentAccount::getAccountByNo($this->Payment->id, $this->Platform->oDeposit->merchant_code);
        //解密content
        $this->sContentJson = $this->params['content'] = $this->Platform->decryptContents($this->PaymentAccount, urldecode($this->params['content']), false);
        $this->params['content'] = json_decode($this->params['content']);
        $this->params[$this->Platform->accountColumn] = $this->Platform->oDeposit->merchant_code;
        $this->params[$this->Platform->orderNoColumn] = $iOrderNo;
        $this->params = array_merge($this->params, (array) $this->params['content']);
        $this->Platform->iTradeId = $this->params['trade_id'];

        //TODO improve
        unset($this->params[$this->amountColumn]);
        //手续费
        //TODO IMPROVE
        if (isset($this->Platform->feeColumn) && array_key_exists($this->Platform->feeColumn, $this->params)) {
            $this->Platform->oDeposit->fee = $this->params[$this->Platform->feeColumn];
            if (!$this->Platform->oDeposit->save()) {
                $this->writeLog("order_no=$iOrderNo " . $this->Platform->oDeposit->getValidationErrorString());
            }
        }
    }


    /**
     * 验签
     * just overide the parent method checkSign
     *
     * @param $sSign
     *
     * @return bool
     */
    public function checkSign(&$sSign) {
        return $this->Platform->validSign($this->PaymentAccount, $this->sContentJson, urldecode($this->params['sign']));
    }
}
