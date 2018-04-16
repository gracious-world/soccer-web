<?php

class HUITENGZFBOLDDepositNotifyController extends HUITENGWXDepositNotifyController {

    protected $platformIdentifier = 'huitengzfbold';
    protected $test = false;

    protected function & mkTestData() {
        $data =array (
            'orderNo' => '895271091130474496',
            'status' => '02',
            'beginTime' => '20170809211037',
            'endTime' => '20170809211133',
            'userOrderNo' => '513907351598b09cb3c3a1',
            'orderAmt' => '1.00',
            'signature' => 'EB15C9F61FF842056E7212675C871592',
        );

        return $data;
    }

    /**
     * 没有返回商户号，所以只适合一个商户号的情况
     * @param type $sSign
     * @return type
     */
    protected function checkSign(& $sSign) {
        $sPostedSign = $this->params[$this->Platform->signColumn];
        $this->PaymentAccount = PaymentAccount::getAvailableAccounts($this->Payment->id, false,true,true)->first();
        if (!is_object($this->PaymentAccount)) {
            $this->writeLog('missing payment account,payment:' . $this->Payment->id);
            exit;
        }
        $sSign = $this->Payment->compileSignReturn($this->PaymentAccount, $this->params);

        return strcasecmp($sSign, $sPostedSign) === 0 ;
    }

    protected function query(& $sMsg) {
        $sOrderNo = $this->params[$this->Platform->paymentOrderNoColumn];
        $this->writeLog('Query: ' . $this->Payment->query_on_callback);
        if (!$this->Payment->query_on_callback) {
            return true;
        }
        $this->writeLog('Query: Starting Query');
        $iQueryResult = $this->Payment->queryFromPlatform($this->PaymentAccount, $sOrderNo, null, $aResonses);
        $this->writeLog(var_export($aResonses, true));
//        pr($aResonses);
//        exit;
        $iQueryResult = intval($iQueryResult);
        $bSucc = false;
        switch ($iQueryResult) {
            case BasePlatform::PAY_SUCCESS:
                $bSucc = true;
                break;
            case BasePlatform::PAY_QUERY_FAILED:
                $sMsg = 'Query Success, But Error:' . $aResonses[$this->Payment->queryResultColumn];
                break;
            case BasePlatform::PAY_QUERY_FAILED:
                $sMsg = 'Query Fail';
                break;
            case BasePlatform::PAY_QUERY_PARSE_ERROR:
                $sMsg = 'Query Success, But Parse Error';
                break;
            case BasePlatform::PAY_SIGN_ERROR:
                $sMsg = 'Query Success, Sign Error';
                break;
            case BasePlatform::PAY_NO_ORDER:
                $sMsg = 'Query Success, But No Order';
                break;
            case BasePlatform::PAY_UNPAY:
                $sMsg = 'Query Success, But UnPay';
                break;
            default:
                $bSucc = false;
        }
        return $bSucc;
    }
}
