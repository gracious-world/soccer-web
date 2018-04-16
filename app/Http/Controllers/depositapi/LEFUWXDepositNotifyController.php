<?php

/*
 * 乐付微信回调
 */

class LEFUWXDepositNotifyController extends BaseDepositNotifyController {
    protected $platformIdentifier = 'lefuwx';
    protected $test = false;
    private $sContentDecrypted;

    protected function & mkTestData() {

        $aData = [
            'input_charset' => 'utf-8',
            'sign_type' => 'SHA1WITHRSA',
            'sign' => 'wiPDXX5YU8rfXRAcmZm5ZgXn9HWpQM2%2BrZ3b7rUqKxh5n6Rvo9vXeTKxBWxszKYCRZJu60Lon4UO90%2BGi4JmSsOKqulhDhhioOTR9gA9DWSYWbIe%2FWhPC7OS3%2BEWzNT%2BcOaBD6cK1LfQ3wyu0%2FqCRluiKr6v6kvlzkKYLwLwYcM%3D',
            'content' => 'Fr9WeTSCokGG6OCOtur5%2BgFn1q6lqMk37%2FiyaWg%2BxIketduCrarY7IUfDUQ2ugP4JgBDVL8qPH8o1CVYD%2BrjxHXERoELS39lM1nI8RW3xcJBgQbPkhGkEUlWgBULSyp7VSZTft0%2Fv7ZB4%2Bg6Xbo2FKsN6kMO69CzQLPwiLj5oGEFrb3DVID10cDR5YfjW5m3Y6nZMTT2CYa%2Fw2p1mexYon3F8SBDxO6nrk50QO1cksAVPPF2gxSE4abIRuhVu8LQkILHykyv6oTN7KH9%2FrddGloK7Vw6b2J6hXbyxgsGC9bP9vo28YiSJqs5wCU7jSKoMiJkTxQeUeJzphUSN98h8zHBHs1izLajIGzjN6yBBznE9KyW01JW54cwH1EgyaUG9JWe4f4%2B2am1T48FSSd0AKntRo2TNBZJ0u4hUhcuwSHy3a9LT9h34h5B9VCee6twY2tdBsXc1ZgUKZUBKBTdZBO09dCLjbQ4GA0KgYlIGlCqXhvHBW3T4Quhstq45EMO',
            'out_trade_no' => '130322527358a42a1f4350e',
            'status' => 1,
            'request_time' => '2017-02-15 18:18:30'
        ];
        return $aData;
    }

    protected function init() {
        parent::init();
        if (empty($this->params)) {
            $this->halt('invalid callback', false);
        }
//        foreach($this->params as $key){
//            if(! in_array($key,$this->Platform->signNeedColumnsForNotify)){
//                $this->halt('invalid callback', false);
//            }
//        }
        $oDeposit = Deposit::getDepositByNo($this->params['out_trade_no']);
        $this->PaymentAccount = PaymentAccount::getAccountByNo($oDeposit->platform_id, $oDeposit->merchant_code);
        //解密content重组
        $this->sContentDecrypted = $this->Platform->decryptContents($this->PaymentAccount, urldecode($this->params['content']), false);
        if (! $this->sContentDecrypted) {
            $this->halt('decrypt content failed', false);
        }
        $this->params['content'] = json_decode($this->sContentDecrypted, true);
        $this->params['amount_str'] = $this->params['content']['amount_str'];
        $this->params['trade_id'] = $this->params['content']['trade_id'];
    }


    protected function checkSign(& $sSign) {
        $sPostedSign = $this->params[$this->Platform->signColumn];
        $bValidSign = $this->Payment->compileSignReturn($this->PaymentAccount, $this->sContentDecrypted, urldecode($sPostedSign));
//        var_dump($bValidSign);exit;
        return $bValidSign;
    }
}
