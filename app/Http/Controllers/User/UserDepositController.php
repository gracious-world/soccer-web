<?php
namespace App\Http\Controllers;

use Session;
use Request;
use Validator;
use App\Models\Func\SysConfig;
use App\Models\Payment\PaymentPlatform;
use App\Models\Payment\PaymentPlatformBank;
use App\Models\AppUser\UserUser;
use App\Models\AppUser\UserDeposit;
use App\Models\Payment\PaymentAccount;
use App\Models\User\User;
use App\Models\User\UserBankCard;

use App\Models\Fund\Deposit;
use Illuminate\Support\Facades\Redirect;

class UserDepositController extends UserBaseController {

    protected $resourceView = 'userCenter.deposit';
    protected $modelName = 'App\Models\AppUser\UserDeposit';
    public $resourceName = '';

     /**
     * 是否需要检查用户绑卡情况（如有需要可改为配置方式）
     * @var boolean
     */
    protected $checkUserBankCard = false;

    /**
     * 是否需要验证用户资金密码（如有需要可改为配置方式）
     * @var boolean
     */
    protected $checkFundPassword = false;

   /**
     * 充值查询列表
     * @see BaseController::index()
     * @return Response
     */
    public function index() {
//        pr($this->params);
        $iLoginUserId = Session::get('user_id');
        if (Session::get('is_agent') && isset($this->params['username']) && $this->params['username']) {
            $oUser = User::getUserByParams(['username' => $this->params['username'], 'forefather_ids' => $iLoginUserId], ['forefather_ids']);
            if ($oUser) {
                $this->params['user_id'] = $oUser->id;
            } else {
                $aReplace = ['username' => $this->params['username']];
                return $this->goBack('error', __('_basic.not-your-user', $aReplace));
            }
        } else {
            $this->params['user_id'] = $iLoginUserId;
        }
        if (!key_exists('request_time_from', $this->params) && !key_exists('request_time_to', $this->params)) {
            $this->params['request_time_from'] = $this->params['request_time_to'] = date('Y-m-d');
        }
//            if (!array_get($this->params, 'request_time_from') && !array_get($this->params, 'request_time_to')) {
//                $this->params['request_time_from'] = $this->params['request_time_to'] = date('Y-m-d');
//            }
        if ($this->dataDisplayDays){
//            $sTimeTo = substr($this->params['request_time_to'],10);
            $dMinDate = date('Y-m-d',strtotime("-$this->dataDisplayDays days"));
            if ($this->params['request_time_from'] < $dMinDate){
                $sTimeFrom = substr($this->params['request_time_from'],10);
                $this->params['request_time_from'] = $dMinDate . $sTimeFrom;
            }
            unset($dMinDate);
        }
//        pr($this->params);
        return parent::index();
    }

    private function doDepositQuick($iPlatformId) {
        if (Request::method() == 'POST') {
            $oPayment = PaymentPlatform::find($iPlatformId);
            $fMinLoad = !empty(floatval($oPayment->deposit_min_amount)) ? $oPayment->deposit_min_amount : number_format(SysConfig::readValue('deposit_3rdpart_min_amount '), 2, '.', '');
            $fMaxLoad = !empty(floatval($oPayment->deposit_max_amount)) ? $oPayment->deposit_max_amount : number_format(SysConfig::readValue('deposit_3rdpart_max_amount'), 2, '.', '');
            $this->params['amount'] = number_format($this->params['amount'], 2, '.', '');
            if ($this->params['amount'] < $fMinLoad || $this->params['amount'] > $fMaxLoad) {
                $aReplace = [
                    'min' => number_format($fMinLoad, 2),
                    'max' => number_format($fMaxLoad, 2),
                ];
                return $this->goBack('error', __('_deposit.amount-error', $aReplace));
            }


            if (!is_object($oPayment)) {
                return $this->goBack('error', __('_basic.no-data', ['data' => '支付渠道']));
            }

            $oPayment = PaymentPlatform::getObject($oPayment->identifier);

            $oPaymentAccount = PaymentAccount::getAccountForDeposit($oPayment->id, false, true, $this->params['amount']);
            if (empty($oPaymentAccount)) {
                return $this->goBack('error', __('_deposit.no-payment-account'));
            }

            $oUser = User::find(Session::get('user_id'));

            //需要银行卡的前提下做必要判断
            $oBank = null;
            if ($oPayment->need_bank) {
                $iBank = (int)$this->params['bank'];
                if (!$iBank){
                    return $this->goBack('error', __('_deposit.bank_error'));
                }
                $oBank = PaymentPlatformBank::find($iBank);
                if (!$oBank || !isset($oBank->bank_id)) {
                    return $this->goBack('error', __('_deposit.payment_platform_bank_is_not_exists'));
                }
            }

            $oOriginalBank = $oBank ? Bank::find($oBank->bank_id) : null;
            $oDeposit = UserDeposit::addDeposit(UserDeposit::DEPOSIT_MODE_THIRD_PART, $oUser, $oOriginalBank, $this->params['amount'], $oPayment, null, $oPaymentAccount);
            if (empty($oDeposit)) {
                return $this->goBack('error', __('_deposit.save-failed'));
            }

            UserDeposit::addCheckTask($oDeposit->id,30);
            $aInputData = $oPayment->compileInputData($oPaymentAccount, $oDeposit, $oBank, $sSafeStr);//提交给 支付平台的 字符串
            $sUrl = $oPayment->getLoadUrl($oPaymentAccount);//支付平台接收数据的网址
            $sRealUrl = $oPaymentAccount->is_test ? $oPayment->test_load_url : $oPayment->load_url;

            //如果支付方式是微信
            if (strtoupper($oPayment->identifier) == 'ZHIFUWX' || strtoupper($oPayment->identifier) == 'HUIYUANWX'|| strtoupper($oPayment->identifier) == 'LEFUWX' || strtoupper($oPayment->identifier) == 'LEFUZFB' ) {
                $sWxQrcode = $oPayment->qrCode($aInputData, $sRealUrl,$oPaymentAccount);
                $this->setVars('sIdentifier', $oPayment->display_name);
                $this->setVars('sWxQrcode', $sWxQrcode);
                $this->setVars('sDisplayAmount', $this->params['amount']);
                $this->view = $this->resourceView . '.doDepositQuickWx';
            }else {
                $this->setVars(compact('aInputData', 'sUrl'));
                $this->setVars('___DepositUrl', $sRealUrl);
                $this->view = $this->resourceView . '.doDepositQuick';
            }

            return $this->render();
        }
        exit;
    }
    /**
     * 第三方充值
     * @return type
     */
    public function quick($id) {
        if (!$id) {
            die('参数错误');
        }
        $oPayment = PaymentPlatform::find($id);

        $iUserId = Session::get('user_id');

        Session::put($this->redictKey, Request::fullUrl());

        if (!UserBankCard::getUserBankCardsCount($iUserId)){
            return Redirect::route('bank-cards.index')->with('error', __('_userbankcard.no-bankcards'));
        }

        $iNeedStatus = Session::get('is_tester') ? PaymentPlatform::STATUS_AVAILABLE_FOR_TESTER : PaymentPlatform::STATUS_AVAILABLE;
        //TODO uncomment
        if (empty($oPayment) || ($oPayment->status & $iNeedStatus) != $iNeedStatus){
            return $this->goBack('error', __('_paymentplatform.not-available'));
        }

        if (Request::method() == 'POST') {
            if (isset($this->params['dodespoit'])) {
                return $this->doDepositQuick($id);
            } else {
                return $this->confirmQuick($id);
            }
        } else {
            $this->setVars('iDepositMode',UserDeposit::DEPOSIT_MODE_THIRD_PART);
            return $this->depositForm($id);
        }
    }

    private function depositForm($iPlatformId) {
        $oPlatform = PaymentPlatform::find($iPlatformId);
        if (!is_object($oPlatform)) {
            return $this->goBack('error', '支付渠道不存在');
        }
        $iUserId = Session::get('user_id');
        $oUser = UserUser::find($iUserId);

        if ($oUser->fund_password == null) {
            $this->saveUrlToSession();
            return Redirect::route('users.safe-reset-fund-password');
        }
        $bSetFundPassword = !empty($oUser->fund_password); // 是否已设置资金密码
//        if ($oPlatform->need_bank){
//        $oAllBanks = Bank::getSupportThirdPartBank();
        if ($oPlatform->need_bank){
            $oAllBanks = PaymentPlatformBank::getBanks($oPlatform->id);
            $aAllBanks = []; // 页面JS数据接口
            foreach ($oAllBanks as $bank) {
                $aAllBanks[$bank->id] = [
                    'id' => $bank->id,
                    'identifier' => $bank->identifier,
                    'bank_identifier' => $bank->bank_identifier,
                    'name' => $bank->bank_name,
                    //'min'        => max($fMinLoad, $bank->min_load),
                    //'max'        => min($fMaxLoad, $bank->max_load),
                ];
            }
            $sAllBanksJs = json_encode($aAllBanks); // 页面JS数据接口
            $this->setVars(compact('aAllBanks', 'sAllBanksJs'));
        }
//        }
        $iStatus = Session::get('is_tester') ? PaymentPlatform::STATUS_AVAILABLE_FOR_TESTER : PaymentPlatform::STATUS_AVAILABLE;
        $oPlatforms = PaymentPlatform::getAvailabelPlatforms($iStatus);

        $fMinLoad = !empty(floatval($oPlatform->deposit_min_amount)) ? number_format($oPlatform->deposit_min_amount,2) : number_format(SysConfig::readValue('deposit_3rdpart_min_amount '), 2);
        $fMaxLoad = !empty(floatval($oPlatform->deposit_max_amount)) ? number_format($oPlatform->deposit_max_amount,2) : number_format(SysConfig::readValue('deposit_3rdpart_max_amount'), 2);
        $this->setVars(compact('oAllBanks', 'bSetFundPassword', 'fMinLoad', 'fMaxLoad', 'oPlatforms', 'iPlatformId', 'oPlatform'));
        return $this->render();
    }

    /**
     * 充值确认
     */
    private function confirmQuick($iPlatformId) {
        $oPlatform = PaymentPlatform::find($iPlatformId);
        if (!is_object($oPlatform)) {
            return $this->goBack('error', '支付渠道不存在');
        }
        $oUser = UserUser::find(Session::get('user_id'));
        /* Step 1: 验证 */
        $aFormRules = [
            //'bank' => 'required|numeric',
            'amount' => 'required|regex:/^[0-9]+(.[0-9]{1,2})?$/',
            'fund_password' => ($this->checkFundPassword ? 'required|' : '') . 'between:0, 60',
            'deposit_mode' => 'required|in:' . UserDeposit::DEPOSIT_MODE_BANK_CARD . ',' . UserDeposit::DEPOSIT_MODE_THIRD_PART
        ];
        // 验证表单
        $validator = Validator::make($this->params, $aFormRules);
        if (!$validator->passes()) { // 表单未通过验证
            return $this->goBack('error', __('_deposit.deposit-error-00'));
        }

        // 1 资金密码
        if ($this->checkFundPassword && !$oUser->checkFundPassword($this->params['fund_password'])) {
            return $this->goBack('error', __('_deposit.wrong-fund-passwd'));
        }
        // 2 是否绑定银行卡
        // 3 当前银行是否可用
        $iDepositMode = $this->params['deposit_mode']; // 充值方式
        if ($oPlatform->need_bank) {
            $oBank = PaymentPlatformBank::find($this->params['bank']);
            if (!$oBank || !$oBank->enabled) {
                return $this->goBack('error', __('_deposit.missing-bank'));
            }
        }
        if ($iDepositMode == UserDeposit::DEPOSIT_MODE_THIRD_PART) { // 用户选择第三方充值
//            if ($oPlatform->need_bank) {
//                if (!in_array($oBank->mode, [Bank::BANK_MODE_ALL, Bank::BANK_MODE_THIRD_PART])) { // 当前银行是否支持第三方充值
//                    return $this->goBack('error', __('_deposit.missing-bank'));
//                }
//            }
            $fMinLoad = !empty(floatval($oPlatform->deposit_min_amount)) ? !empty(floatval($oPlatform->deposit_min_amount)) : number_format(SysConfig::readValue('deposit_3rdpart_min_amount '), 2, '.', '');
            $fMaxLoad = !empty(floatval($oPlatform->deposit_max_amount)) ? !empty(floatval($oPlatform->deposit_max_amount)) : number_format(SysConfig::readValue('deposit_3rdpart_max_amount'), 2, '.', '');
//            $fMinLoad         = max($oBank->min_load, $fMinLoadOfSystem);
//            $fMaxLoad         = min($oBank->max_load, $fMaxLoadOfSystem);
            if ($this->params['amount'] < $fMinLoad || $this->params['amount'] > $fMaxLoad) { // 金额超出范围
                return $this->goBack('error', __('_deposit.amount-out-range'));
            }
        }
        // 4 是否达到充值次数上限
//        $sRequestMethod = Request::method();
        //echo $this->params['amount'];
        //exit;

        $fAmount = $this->params['amount'];
        $sDisplayAmount = number_format($fAmount, 2, '.', ',');
        $this->setVars(compact('oBank', 'iDepositMode', 'fAmount', 'sDisplayAmount', 'iPlatformId', 'oPlatform'));
        $this->view = $this->resourceView . '.confirmQuick';
        return $this->render();
    }

    /**
     * 写充值日志
     * @param string|array $msg
     */
    protected function writeLog($msg) {
        !is_array($msg) or $msg = var_export($msg, true);
        @file_put_contents('/tmp/deposit', $msg . "\n", FILE_APPEND);
    }

    protected function beforeRender() {
        parent::beforeRender();
        $this->setVars('checkFundPassword', $this->checkFundPassword);
        $this->setVars('bOpenBankDeposit', SysConfig::get('enabled_bank_deposit'));
        switch ($this->action) {
            case 'index':
                $this->setVars('reportName', 'depositApply');
                break;
        }
    }

}
