<?php
namespace App\Http\Controllers;

use Request;
use Session;
use App\Models\AppUser\UserUser;
use App\Models\Basic\Bank;
use App\Models\Func\SysConfig;
use App\Models\Payment\PaymentPlatformBankCard;
use App\Models\Payment\PaymentPlatform;
use App\Models\Payment\PaymentPlatformBank;
use App\Models\AppUser\UserBankDeposit;
use App\Models\Payment\PaymentBankCard;

class UserBankDepositController extends UserBaseController {

    protected $resourceView = 'userCenter.deposit';
    protected $modelName = 'App\Models\AppUser\UserBankDeposit';
    public $resourceName = 'UserBankDeposit';

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
     * 银行转账充值
     */
    public function netbank($id) {
        if (Request::method() == 'POST') {
            return $this->confirmNetBank($id);
        } else {
            return $this->depositFormNetBank($id);
        }
    }

    /**
     * 充值确认
     */
    private function confirmNetBank($iPlatformId) {
//        pr($this->params);
//        exit;
        $oUser = UserUser::find(Session::get('user_id'));
        $oBank = Bank::find($this->params['bank']);
        if (empty($oBank) || $oBank->status != Bank::BANK_STATUS_AVAILABLE) {
            return $this->goBack('error', __('_deposit.missing-bank'));
        }
        $oPlatform = PaymentPlatform::find($iPlatformId);
        $oBankCard = PaymentPlatformBankCard::getBankcardForDeposit($iPlatformId,$oBank->id);
        if(!$oBankCard){
            $oBankCard = PaymentBankCard::getBankcardForDeposit($oBank->id);
        }
        $sPayerName = null;
        if(!empty($this->params['payer_name'])){
            $sPayerName = $this->params['payer_name'];
        }
        $oUserDeposit = UserBankDeposit::addDeposit( $oUser, $oBank, null, $oPlatform, $oBankCard->toArray(),$sPayerName);
        $this->setVars(compact('oBank', 'iDepositMode', 'oUserDeposit','oPlatform'));
        $this->view = $this->resourceView . '.netbankConfirm';
        return $this->render();
    }

    /**
     * 银行转账
     */
    private function depositFormNetBank($iPlatformId) {
        $iUserId = Session::get('user_id');
        $oUser = UserUser::find($iUserId);
        if (!is_object($oUser)) {
            return $this->goBack('error', __('_user.missing-user'));
        }
        if ($oUser->blocked == UserUser::BLOCK_FUND_OPERATE) {
            return $this->goBack('error', __('_user.deposit-not-allowed'));
        }
        if ($oUser->fund_password == null) {
            $this->saveUrlToSession();
            return Redirect::route('users.safe-reset-fund-password');
        }
        $bSetFundPassword = !empty($oUser->fund_password); // 是否已设置资金密码
        $oAllBanks = PaymentPlatformBank::getBanks($iPlatformId);
        $aUserBankCards = [];
        $checkUserBankCard = $this->checkUserBankCard; // 是否需要检查用户绑卡情况（如有需要可改为配置方式）
        if ($checkUserBankCard) {
            $oUserBankCards = UserUserBankCard::getUserCardsInfo($iUserId, ['id', 'bank_id', 'account_name', 'account']);
            foreach ($oUserBankCards as $bankcard) {
                $aUserBankCards[$bankcard->bank_id][$bankcard->id] = [
                    'id' => $bankcard->id,
                    'name' => $bankcard->account_name,
                    'number' => $bankcard->account_hidden,
                    'isdefault' => false,
                ];
            }
        }
        $aAllBanks = []; // 页面JS数据接口
        foreach ($oAllBanks as $oPlatformBank) {
            $bank = Bank::find($oPlatformBank->bank_id);
            $bank->is_band_card = !$checkUserBankCard || !empty($aUserBankCards[$bank->id]); // 显示用户是否有绑卡，当不检查绑卡时默认值为true
            $aAllBanks[$bank->id] = [
                'id' => $bank->id,
                'name' => $bank->name,
                'min' => $bank->min_load,
                'max' => $bank->max_load,
                'text' => $bank->notice,
                'identifier' => $bank->identifier,
                'userAccountList' => !empty($aUserBankCards[$bank->id]) ? $aUserBankCards[$bank->id] : [],
            ];
        }
//        pr($aAllBanks);
//        exit;
        $sAllBanksJs = json_encode($aAllBanks); // 页面JS数据接口
        /* 验证（以下验证不再需要 @20141104） */
        // 是否需要输入资金密码（用于资金密码框显示）
        // 是否至少绑定了一张银行卡
        // 系统是否有可用充值银行
        // 是否达到充值次数上限
        // return View::make($this->resourceView . '.netbank');
        $iStatus = Session::get('is_tester') ? PaymentPlatform::STATUS_AVAILABLE_FOR_TESTER : PaymentPlatform::STATUS_AVAILABLE;
        $oPlatforms = PaymentPlatform::getAvailabelPlatforms($iStatus);
        $oPlatform = PaymentPlatform::find($iPlatformId);
        $this->setVars(compact('aAllBanks', 'sAllBanksJs', 'checkUserBankCard', 'bSetFundPassword', 'oPlatforms', 'iPlatformId','oPlatform'));
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
