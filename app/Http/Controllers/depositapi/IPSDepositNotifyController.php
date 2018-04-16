<?php

class IPSDepositNotifyController extends BaseDepositNotifyController {
    protected $platformIdentifier = 'ips';
    protected $test = false;

    protected function & mkTestData() {
        $iTestDeposit = 10141;
        $oPayment = PaymentPlatform::getObject('ips');
        $oDeposit = Deposit::find($iTestDeposit);
        $oPaymentAccount = PaymentAccount::getAccountByNo($oPayment->id, $oDeposit->merchant_code);
        //var_dump($oPaymentAccount->card_number);
        dd();
        $aXmlHead = [
            'RspCode' => '000000',
            'ReqDate' => str_replace(['-',' ',':'], '', substr($oDeposit->created_at, 0, 10)),
            'RspDate' => date('YmdHis'),
            'Signature' => ''
        ];
        $aXmlBody = [
            'MerBillNo' => $oDeposit->order_no,
            'CurrencyType' => 'RMB',
            'Amount' => $oDeposit->amount,
            'Date' => str_replace('-', '', substr($oDeposit->created_at, 0, 10)),
            'Status' => 'Y',
            'Msg' => '',
            'Attach' => '',
            'IpsBillNo' => 'Ips'. uniqid(mt_rand()),
            'IpsTradeNo' => 'IpsTrad'.uniqid(mt_rand()),
            'RetEncodeType' => '17',
            'BankBillNo' => 'BankBill'.uniqid(mt_rand()),
            'ResultType' => 0,
            'IpsBillTime' => date('YmdHis'),
        ];

        $data['paymentResult'] = $oPayment->platform->createXml($oPaymentAccount, $aXmlBody, $aXmlHead, true);
        return  $data;
    }

    /**
     * 回调方法
     */
    public function doCallback() {
        $this->init();
        $this->serverIP = Tool::getClientIp();
        $this->writeLog(date('Y-m-d H:i:s'));
        $this->writeLog(var_export($this->params, true));
        $this->writeLog($this->serverIP);

        if (!isset($this->params['paymentResult'])) {
            $this->halt('invalid callback', false);
        }


        $originXml = $this->params['paymentResult'];
        $this->params = $this->Platform->parseXml($originXml);

        if (!isset($this->params['rspcode']) || $this->params['rspcode'] != '000000' || !isset($this->params['merbillno'])) {
            $this->halt('invalid callback', false);
        }

        $this->params['originxml'] = $originXml;
        $this->params['mercode'] = Deposit::doWhere(['order_no' => ['=', $this->params['merbillno']]])->pluck('merchant_code');


        //记录回调日志
        $this->saveCallbackHistory();


        //校验签名
        if (!$this->checkSign($sSign)) {
            $this->Callback->setSignError();
            $this->writeLog($sSign);
            $this->halt("Sign Error", false);
        }

        //校验IP
        if (!$this->checkIP()) {
            $this->Callback->setIpError();
            $this->halt("Error IP: $this->serverIP", false);
        }

        //校验充值是否成功
        if (!$this->checkSuccessFlag()) {
            $this->Callback->setPaymentError();
            $this->writeLog("Service Failed");
            $this->successReponse();
        }

        //主动查询订单
        if (!$this->query($sErrMsg)) {
            $this->writeLog($sErrMsg);
            $this->halt($sErrMsg);
        }

        $sOrderNo = $this->params[$this->Platform->orderNoColumn];
        $oUserDeposit = Deposit::getDepositByNo($sOrderNo);

        if (empty($oUserDeposit)) {
            $this->Callback->setMissingData();
            $this->halt("Deposit $sOrderNo Not Exists");
        }
        if ($this->Platform->getPayAmount($this->params) != $oUserDeposit->amount) {
            $this->Callback->setAmountError();
            $this->halt("Wrong Deposit Amount");
        }

        // 判断状态
        if (!$this->checkStatus($oUserDeposit)) {
            $this->Callback->setDepositStatusError();
            $this->writeLog('Status Error: ' . $oUserDeposit->status);
            $this->successReponse();
        }

        $oUserDeposit->service_order_status = $this->params[$this->Platform->successColumn];
        $oUserDeposit->service_order_no = $this->params[$this->Platform->paymentOrderNoColumn];
        if ($this->Platform->bankNoColumn && array_key_exists($this->Platform->bankNoColumn, $this->params)) {
            $oUserDeposit->service_bank_seq_no = $this->params[$this->Platform->bankNoColumn];
        }
        if ($this->Platform->serviceOrderTimeColumn && array_key_exists($this->Platform->serviceOrderTimeColumn, $this->params)) {
            $oUserDeposit->service_time = $this->params[$this->Platform->serviceOrderTimeColumn];
        }

        if (isset($this->params[$this->Platform->bankTimeColumn])) {
            $oUserDeposit->pay_time = date('Y-m-d H:i:s', strtotime($this->params[$this->Platform->bankTimeColumn]));
        }

        if (!$bSucc = $oUserDeposit->save()) {
            $this->halt(var_export($oUserDeposit->validationErrors->toArray(), 1));
        }

        $this->writeLog('Service Infomation Saved');
        if (!$bSucc = $oUserDeposit->setWaitingLoad()) {
            $this->Callback->setDepositStatusSetError();
            $this->halt("Set Status To Waiting Failed");
        }

        $this->writeLog("Set Status To Waiting Success");

        // 加币
        if (!$bSucc = Deposit::addDepositTask($oUserDeposit->id)) {
            $this->Callback->setAddTaskError();
            $this->halt("Add Deposit Task Failed!");
            exit;
        } else {
            $this->PaymentAccount->updateStat($oUserDeposit);
            $this->Callback->setSuccessful();
            $this->writeLog("Add Deposit Task Success");
        }
        !$bSucc or $this->successReponse();
    }

    /**
     * 校验签名
     *
     * @param $sSign
     *
     * @return bool
     */
    protected function checkSign(& $sSign) {
        if (!preg_match('#<body>.*</body>#is', $this->params['originxml'], $sXmlBody)) {
            return false;
        }
        $sPostedSign = $this->params[$this->Platform->signColumn];
        $this->PaymentAccount = PaymentAccount::getAccountByNo($this->Payment->id, $this->params[$this->Platform->accountColumn]);

        $sSign = $this->Payment->compileSignReturn($this->PaymentAccount, $sXmlBody[0]);

        return $sSign == $sPostedSign;
    }



}
