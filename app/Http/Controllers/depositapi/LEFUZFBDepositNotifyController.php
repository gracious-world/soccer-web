<?php
/*
 * 乐付支付宝回调
 */
class LEFUZFBDepositNotifyController extends BaseDepositNotifyController {

    protected $platformIdentifier = 'lefuzfb';
    protected $test = false;
    private $sContentDecrypted;

    protected function & mkTestData() {

        $aData = [
            'input_charset' => 'utf-8',
            'sign_type' => 'SHA1WITHRSA',
            'sign' => 'HfxpOWhhSsmNuCR70MAxoK%2FQwut7L0uuoWviGwIdftJRJjK0SmWe4PEZuNaE%2FH75WnHHkPlTgDxA8GUpwRYnbnJGaY605ZBXEdMp0jrhjRUFK3VQes0BwI%2BzsKXuNZVH14Sx66s%2BwshkkOeK4Jy2B4%2FeuBNCT4HY%2Fb0ZmSHoSkw%3D',
            'content' => 'hP9iTa%2BkrgXD6m%2BCIA%2BgjcqTBJrgBbUEZ4BY%2BHpusTtDIe8LLtLxaopavboWV%2BhnGrgWdMoAfrhZ0AfPjvNYdzysfqcAjPYIir9gqghhaAosXBu07X4zcslpw9eF%2By3SlQ9hSwIs5Ub%2BWBV%2BDcKQ0bfsvwJAW5Jty0I1RtMQQ8ZIR3V8eeNC4loqYRJbSKBQE8YxpgRvFU40otSDkBqDPgnAxBIKxk8TUt1nX8oPm%2FRdSr1r65dbLMeMcwMs631z9UjWB3CDZX%2Fjfh7e0LQeR063vj%2BOrGnsAkbDvi2tV4eAya9CIZOdQa89dGturIaulnxJhCDAziKZyuhHkcAg4KdlzB2l8Oz8LkhM7sbhPDsii8FboM6KqjBOySnA%2Bz47O9eyO5adHwE%2Bq2udKxLr2BPyegxCiXY45AP9C2ubocdbtlI47OPC4wVNVxEJh4ocVpqq7TTA734QrJMoZ%2BlS7wRScIQR5Sdpa5PruUZ5BKDr9ZFl3HO8F%2FyLcoSidXzD',
            'out_trade_no' => '33999871958a42ef3b685a',
            'status' => 1,
            'request_time' => '2017-02-15 18:36:05'
        ];
        return $aData;
    }


    protected function init() {
        parent::init();

        if (empty($this->params)) {
            $this->halt('invalid callback', false);
        }
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
