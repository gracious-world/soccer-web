<?php

class LEFUKJDepositNotifyController extends BaseDepositNotifyController {
    protected $platformIdentifier = 'lefukj';
    protected $test = false;
    protected $aContent;
    protected $amountColumn = "amount_str";
    protected $sContentJson;
    protected $fAmount;

    protected function & mkTestData() {
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
        $bValidSign = $this->Platform->compileSignReturn($this->PaymentAccount, $this->sContentJson, urldecode($this->params['sign']));
        return $bValidSign;
    }
}
