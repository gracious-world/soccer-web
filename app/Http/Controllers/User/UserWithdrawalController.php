<?php
namespace App\Http\Controllers;
# 提现
use Illuminate\Support\MessageBag;
use Session;
use Request;
use Input;
use Redirect;
use DB;
use Carbon\Carbon;
use App\Models\User\Role;
use App\Models\User\RoleUser;
use App\Models\AppUser\UserUser;
use App\Models\AppUser\UserUserBankCard;
use App\Models\Payment\PaymentPlatform;
use App\Models\AppUser\UserWithdrawal;
use App\Models\Fund\Account;
use App\Models\Func\SysConfig;
use App\Models\Basic\Bank;
use App\Models\Fund\Transaction;
use App\Models\Fund\TransactionType;
class UserWithdrawalController extends UserBaseController {

    protected $resourceView = 'userCenter.withdrawal';
    protected $modelName = 'App\Models\AppUser\UserWithdrawal';

    protected function beforeRender() {
        parent::beforeRender();
        $iUserId = Session::get('user_id');
        $sUsername = Session::get('username');
        $oAccount = Account::getAccountInfoByUserId($iUserId);

        $iMinWithdrawAmount = SysConfig::readValue('withdraw_default_min_amount');
        $iMaxWithdrawAmount = SysConfig::readValue('withdraw_default_max_amount');
        $this->setVars(compact('iMinWithdrawAmount', 'iMaxWithdrawAmount'));
        switch ($this->action) {
            case 'index':
//                $aSum = $this->getSumData(['amount', 'transaction_amount', 'transaction_charge'], true);
//                $aSum['money_change'] = (int)$aSum['amount_sum'] - (int)$aSum['transaction_charge_sum'];
                $this->setVars('reportName', 'withdrawApply');
                $aStatusDesc = UserWithdrawal::getTranslateValidStatus();
                $this->setVars(compact('aStatusDesc'));
            case 'confirm':
                $iCardId = trim(Input::get('id'));
                $oBankCard = UserUserBankCard::find($iCardId);
                $aInputData = trimArray(Input::all());
                $this->setVars(compact('oBankCard', 'oAccount', 'aInputData'));
                break;
            case 'withdraw':
                $iUserId = Session::get('user_id');
                $iBindedCardsNum = UserUserBankCard::getUserBankCardsCount($iUserId);
                $this->setVars(compact('iBindedCardsNum'));
                $this->setVars('iLimitCardsNum',UserUserBankCard::BIND_CARD_NUM_LIMIT);
                $bLocked = UserUserBankCard::getUserCardsLockStatus($iUserId);
                $aBankCards =UserUserBankCard::getUserCardsInfo($iUserId, ['id', 'account', 'account_name', 'bank', 'bank_id']);
                $oBank = new Bank();
                $aBanks = $oBank->getValueListArray('identifier', [], [], true);
                $iWithdrawLimitNum = SysConfig::readValue('withdraw_max_times_daily');
                $iMaxWithdrawAmount = SysConfig::readValue('withdraw_default_max_amount');
                $iWithdrawalNum = UserWithdrawal::getWithdrawalNumPerDay($iUserId);
                // pr($aBankCards);exit;
                // pr($aSeriesLotteries);exit;

                $this->setVars(compact('aBankCards', 'sUsername', 'oAccount', 'iWithdrawLimitNum', 'iWithdrawalNum', 'iMaxWithdrawAmount', 'aBanks', 'bLocked'));
                break;
        }
    }

    public function index() {
        // $this->params['user_id'] = Session::get('user_id');
        $iLoginUserId = Session::get('user_id');
        // 如果是代理并且有username参数，则精准查找该代理下用户名为输入参数的子用户的提现列表
        // 否则，查询该代理的提现列表
        if (Session::get('is_agent') && isset($this->params['username']) && $this->params['username']) {
            $oUser =UserUser::getUserByParams(['username' => $this->params['username'], 'forefather_ids' => $iLoginUserId], ['forefather_ids']);
            if ($oUser) {
                $this->params['user_id'] = $oUser->id;
            } else {
                $aReplace = ['username' => $this->params['username']];
                return $this->goBack('error', __('_basic.not-your-user', $aReplace));
            }
        } else {
            $this->params['user_id'] = $iLoginUserId;
        }
        return parent::index();
    }


    /**
     * [withdraw 通过Sessin中保存的状态值判断当前进行到提现的哪一步]
     * @return [Response]       [description]
     */
    public function withdraw() {
        $iUserId = Session::get('user_id');
        $iStep = isset($this->params['step']) ? $this->params['step'] : 0;
        if (RoleUser::checkUserRoleRelation(Role::WITHDRAW_BLACK, $iUserId)){
            return $this->goBack('error', __('_user.withdraw-now-allowed'));
        }
        if (Session::get('is_tester') && !RoleUser::checkUserRoleRelation(Role::WITHDRAW_WHITE, $iUserId)){
            return $this->goBack('error', __('_user.withdraw-now-allowed'));
        }
        Session::put($this->redictKey, Request::fullUrl());
        $oUser = UserUser::find($iUserId);
        if (!is_object($oUser)) {
            return $this->goBack('error', __('_user.missing-user'));
        }
        if ($oUser->blocked == UserUser::BLOCK_FUND_OPERATE) {
            return $this->goBack('error', __('_user.withdraw-not-allowed'));
        }
        if (!$iUserCardNum = UserUserBankCard::getUserBankCardsCount($iUserId)) {
            // pr($iUserCardNum);exit;
            // return View::make('centerUser.withdrawal.noCard');// Redirect::route('user-withdrawal.withdrawal-card');
            $this->view = 'userCenter.userBankCard.noCard';
            return $this->render();
        }
        // 检查是否有进行中请求
//        if ($iInProgressCount = UserWithdrawal::getInProgressCount($iUserId)){
//            return $this->goBack('error', __('_withdrawal.has-in-progress-applications',['count' => $iInProgressCount]));
//        }
        switch ($iStep) {
            case 0:
                return $this->withdrawForm();
                break;
            case 1:
                return $this->confirm();
                break;
            default:
                return $this->doWithdraw();
                break;
        }
    }

    /**
     * [withdraw 发起提现]
     * @return [Response] [description]
     */
    private function withdrawForm() {
        return $this->render();
    }

    /**
     * [confirm 确认提现]
     * @return [Response] [description]
     */
    private function confirm() {
        // pr(Session::get(self::WITHDRAWAL_STATUS));
        if (Request::method() != 'POST' || $this->params['step'] != 1){
            return Redirect::route('user-withdrawal.index')->with('error', __('_basic.data-error', $this->langVars));
        }

        if(!isset($this->params['amount']) || $this->params['amount'] == 0){
            return $this->goBack('error', __('_withdrawal.withdrawal-failed',['reason'=>'提现金额不对'] ));
        }

        $iUserId = Session::get('user_id');
        $oAccount = Account::getAccountInfoByUserId($iUserId);
        if($this->params['amount'] > $oAccount->withdrawable) {
            return $this->goBack('error', __('_withdrawal.withdrawal-failed', ['reason' => '可提现余额不够']));
        }


//            Session::put(self::WITHDRAWAL_STATUS, 1);
        $this->action = 'confirm';
        $iCardId = trim(Input::get('id'));
        $oUserBankCard = UserUserBankCard::find($iCardId);
        if (!$iCardId || !is_object($oUserBankCard) || !$oUserBankCard->exists()) {
            // $this->action = 'requireWithdrawal';
            // return Redirect::route('user-withdrawal.withdraw', 0)->with('error', '没有收款银行卡');
//                Session::put(self::WITHDRAWAL_STATUS, 0);
            $this->langVars['reason'] = '没有收款银行卡';
            return $this->goBack('error', __('_withdrawal.withdrawal-failed', $this->langVars));
        }
        return $this->render();
    }

    private function doWithdraw(){

        if (Request::method() != 'POST' && $this->params['step'] != 2) {
            return Redirect::route('user-withdrawal.index')->with('error', __('_basic.data-error', $this->langVars));
        }
        $iUserId = Session::get('user_id');
        $oUser =UserUser::find($iUserId);
        $aCheckResult = $this->_checkWithdrawData($oUser, $fAmount);
        if (!$aCheckResult['success']){
            return Redirect::route($aCheckResult['route'])->with('error', $aCheckResult['message']);
        }

//        pr($fAmount);
//        exit;
        $oAccount = Account::lock($oUser->account_id, $iLocker);
        if (empty($oAccount)) {
            echo 1;exit;
            $this->langVars['reason'] = Account::ERRNO_LOCK_FAILED;
            return Redirect::route('user-withdrawal.index')->with('error', __('_withdrawal.withdrawal-failed', $this->langVars));
        }

        //校验用户可提现余额是否符合要求
        if (!$bValidated = $fAmount <= $oAccount->getWithdrawableAmount()) {
            Account::unlock($oUser->account_id, $iLocker);
            $this->langVars['reason'] = __('_withdrawal.overflow',['max_amount' => $oAccount->withdrawable_formatted]);
            return Redirect::route('user-withdrawal.index')->with('error', __('_withdrawal.withdrawal-failed', $this->langVars));
//                return $this->goBackToIndex('error', __('_withdrawal.withdrawal-failed', $this->langVars));
        }

        DB::connection()->beginTransaction();
        if (!$oWidthdrawal = UserWithdrawal::createWithdrawal($this->params['id'], $fAmount)) {
            DB::connection()->rollback();
            Account::unlock($oUser->account_id, $iLocker);
            $this->langVars['reason'] = $oWidthdrawal->getValidationErrorString();
            return Redirect::route('user-withdrawal.index')->with('error', __('_withdrawal.withdrawal-failed', $this->langVars));
        }
        $aExtraData['note'] = 'withdrawal: ' . $oWidthdrawal->id;
        $iReturn = Transaction::addTransaction($oUser, $oAccount, TransactionType::TYPE_FREEZE_FOR_WITHDRAWAL, $fAmount, $aExtraData);
        // pr($iReturn);exit;
        if ($iReturn != Transaction::ERRNO_CREATE_SUCCESSFUL) {
            DB::connection()->rollback();
            Account::unlock($oUser->account_id, $iLocker);
            $this->langVars['reason'] = $iReturn;
            return Redirect::route('user-withdrawal.index')->with('error', __('_withdrawal.withdrawal-failed', $this->langVars));
        }
        DB::connection()->commit();
        $oWidthdrawal->setNewFlag();
        Account::unlock($oUser->account_id, $iLocker);
        return Redirect::route('user-withdrawal.index')->with('success', __('_withdrawal.withdrawal-success'));
    }

    private function & _compileReturnData($bSuccess, $sRedirectRoute = null, $sMessage = null){
        $aReturnData = [
            'success' => $bSuccess,
        ];
        if (!$bSuccess){
            $this->langVars['reason'] = $sMessage;
            $aReturnData['route'] = $sRedirectRoute;
            $aReturnData['message'] = __('_withdrawal.withdrawal-failed', $this->langVars);
        }
        return $aReturnData;
    }

    private function _checkWithdrawData($oUser, & $fAmount = null){
        $sFundPassword = trim(Input::get('fund_password'));
        if (!$bValidated = $oUser->checkFundPassword($sFundPassword)) {
            return $this->_compileReturnData(false, 'user-withdrawal.index', __('_user.fund_password_error'));
        }
        if (!$oUserBandCard =UserUserBankCard::find($this->params['id'])){
            return $this->_compileReturnData(false, 'user-withdrawal.index', __('_basic.data-not-exists',['data' => __('_model.bankcard')]));
        }
        // 新增/修改卡后2个小时才可以提现
        $iBufferTime = SysConfig::get('bankcard_buffer_time');
        if (Carbon::now()->subMinutes($iBufferTime)->toDateTimeString() < $oUserBandCard->updated_at) {
            return $this->_compileReturnData(false, 'user-withdrawal.withdraw',
                __('_userbankcard.too_short_time_after_binded', ['time' => $iBufferTime . __('_basic.minute')]));
        }
        $iWithdrawLimitNum = SysConfig::readValue('withdraw_max_times_daily');
        $iWithdrawalNum = UserWithdrawal::getWithdrawalNumPerDay($oUser->id);
        if ($iWithdrawLimitNum > 0 && $iWithdrawalNum >= $iWithdrawLimitNum) {
            return $this->_compileReturnData(false, 'user-withdrawal.withdraw',
                __('_withdrawal.overtimes', ['times' => $iWithdrawLimitNum]));
        }
//        pr($this->params);
        $fAmountOriginal = floatval(str_replace(',','',$this->params['amount']));
//        pr($fAmountOriginal);
//        $fAmountOriginal = '105579.560000';
        $fAmount = formatNumber($fAmountOriginal, 2);
//        $fAmount = intval($fAmount);
        if ($fAmount != $fAmountOriginal){
            return $this->_compileReturnData(false, 'user-withdrawal.withdraw', __('_withdrawal.amount-format-error'));
        }

        // TODO 提现金额最小值，应该等同于所选银行卡的最小提现金额
        $fMinWithdrawAmount = SysConfig::readValue('withdraw_default_min_amount');
        $fMaxWithdrawAmount = SysConfig::readValue('withdraw_default_max_amount');
        if (!$bValidated = $fAmount >= $fMinWithdrawAmount && $fAmount <= $fMaxWithdrawAmount ) {
            return $this->_compileReturnData(false, 'user-withdrawal.index',
                __('_withdrawal.out-of-range',['min' => $fMinWithdrawAmount, 'max' => $fMaxWithdrawAmount]));
        }

        return $this->_compileReturnData(true);
    }
}
