<?php
namespace App\Http\Controllers;
use Request;
use Session;
use Input;
use App\Models\AppUser\UserUser;
use App\Models\Fund\Account;
use App\Models\User\UserPrizeSetQuota;
use App\Models\User\UserPrizeSet;
use App\Models\AppUser\UserUserBankCard;
use App\Models\Activity\ActivityUserBonus;
use App\Models\User\UserPercentSet;
use App\Models\User\User;
use Illuminate\Support\Facades\Redirect;
use App\Models\Func\SysConfig;
use App\Models\Lotteries\PrizeGroup;
use App\Models\User\RegisterLink;
use App\Models\Lotteries\PercentWay;
use Illuminate\Support\Facades\DB;
use UserTool;
use App\Models\BaseTask;
/**
 * 用户管理(代理用户才有)
 */
class UserUserController extends UserBaseController {

    protected $resourceView = 'userCenter.user';
    protected $modelName = 'App\Models\AppUser\UserUser';

//    protected function beforeRender() {
//         parent::beforeRender();
//        switch ($this->action) {
//            case 'index':
////                $this->generateData();
//                break;
//            case 'changePassword':
//            case 'changeFundPassword':
//            case 'safeChangeFundPassword':
//                $sFundPassword = $this->oUser->fund_password;
//                $bFundPasswordSetted = (int) ($sFundPassword != null);
//                $this->setVars(compact('bFundPasswordSetted', 'oUser'));
//                break;
//        }
//    }

    protected function beforeRender() {
        $this->setVars('sPageName','User Center');
        parent::beforeRender();
        $iUserId = Session::get('user_id');
        $aBalances = [];
        $oUser = UserUser::find($iUserId);
        $oAccount = Account::find($oUser->account_id);
        switch ($this->action) {
            case 'index':
                $datas = $this->viewVars['datas'];
                $aUserNames = [];
                foreach ($datas as $data) {
                    $aUserNames[] = $data->username;
                }
                $aBalances = $oAccount->balance_formatted;
                $this->setVars(compact('aBalances'));
                $this->generateData();
                break;
            case 'personal':
                $oUser = UserUser::find($iUserId);
                $this->setVars(compact('oUser'));
                break;
            case 'trueName':
                $oUser = UserUser::find($iUserId);
                $this->setVars(compact('oUser'));
                break;
            case 'user':
                $aQuota = UserPrizeSetQuota::getUserAllPrizeSetQuota($iUserId);
                $aUserPrizeSet = UserPrizeSet::getUserLotteriesPrizeSets($iUserId);
                $iUserBankCardCount = UserUserBankCard::getUserBankCardsCount($iUserId);
                $iAvailableHBCount = ActivityUserBonus::getAvailableHBCount($iUserId);
                $iReceivedHBCount = ActivityUserBonus::getReceivedHBCount($iUserId);
                $oAccount = Account::find($oUser->account_id);
                $fWithdrawable = $oAccount->balance_formatted;
                if ($fWithdrawable != 'data-error') {
                    $fWithdrawable = $fWithdrawable + $oAccount->available;
                }
                $oLotteriesPrizeSets = UserPrizeSet::getUserLotteriesPrizeSets($iUserId);
                $fAvailableHBTotalAmount = ActivityUserBonus::getAvailableHBTotalAmount($iUserId);
                $aPercentSets = UserPercentSet::getUserPercentBonusMap($oUser->id, 31);
                $oUserBankCards = UserUserBankCard::getUserCardsInfo($iUserId);
                if (count($oUserBankCards) > 0) $oUserBankCard = $oUserBankCards[0];
                else $oUserBankCard = null;
                $this->setVars(compact('aPercentSets', 'oUserBankCard', 'oUser', 'oAccount', 'fWithdrawable', 'aQuota', 'iUserBankCardCount', 'iReceivedHBCount', 'iAvailableHBCount', 'oLotteriesPrizeSets', 'fAvailableHBTotalAmount'));
                break;
            case 'bindEmail':
                $data = UserUser::find($iUserId);
                $this->setVars(compact('data'));
                break;
            // case 'userList':
            //     $aUsers = $this->generateUsers();
            //     $this->setVars(compact('aUsers'));
            case 'changePassword':
                break;
            case 'changeFundPassword':
            case 'safeChangeFundPassword':
                $oUser = UserUser::find($iUserId);
                $sFundPassword = UserUser::find($iUserId)->fund_password;
                $bFundPasswordSetted = (int)($sFundPassword != null);
                $this->setVars(compact('bFundPasswordSetted', 'oUser'));
                break;
            case 'accurateCreate':
                $iUserId = Session::get('user_id');
                $aLotteriesPrizeSets = UserPrizeSet::generateLotteriesPrizeWithSeries($iUserId);
                $oUser = User::find($iUserId);
                $this->setVars('currentUserPrizeGroup', $oUser->prize_group);
                $this->setVars('bUseQuota', SysConfig::readValue('use_quota'));
                // 获取玩家的奖金组范围
                $iPlayerMaxPrizeGroup = Sysconfig::readValue('player_max_grize_group');
                $aCurrentPrizeGroups = $aLotteriesPrizeSets[0]['children'][0];              // TODO 链接开户的奖金组选择，页面设计里没有体现时时彩和乐透彩的区别，先用时时彩
                $iSeriesId = $aCurrentPrizeGroups['series_id']; // TODO 链接开户的奖金组选择，页面设计里没有体现时时彩和乐透彩的区别，先用时时彩
                $iPlayerMinPrizeGroupRange = abs(SysConfig::readValue('min_diff_between_player_agent'));
                if ($iPlayerMaxPrizeGroup < $aCurrentPrizeGroups['classic_prize']) {
                    $iCurrentPrize = $iPlayerMaxPrizeGroup;
                    $bInclude = true;
                } else {
                    $bInclude = false;
                    $iCurrentPrize = $aCurrentPrizeGroups['classic_prize'];
                }
                $iPlayerMinPrizeGroup = Sysconfig::readValue('player_min_grize_group');
                // 获取低于当前代理奖金组的玩家可能的6个奖金组
                $iMaxPlayerGroup = $iCurrentPrize - $iPlayerMinPrizeGroupRange;
                $oPossiblePrizeGroups = PrizeGroup::getPrizeGroupsBelowExistGroup($iMaxPlayerGroup, $iSeriesId, 8, $iPlayerMinPrizeGroup, 'desc', true);

                $oAllPossiblePrizeGroups = PrizeGroup::getPrizeGroupsBelowExistGroup($iCurrentPrize, $iSeriesId, null, $iPlayerMinPrizeGroup, 'asc', true);

                // 如果是总代开户，获取代理的奖金组范围
                $oPossibleAgentPrizeGroups = [];
                if (Session::get('is_agent')) {
                    $iAgentMaxPrizeGroup = Sysconfig::readValue('agent_max_grize_group');
                    $aCurrentPrizeGroups = $aLotteriesPrizeSets[0]['children'][0];              // TODO 链接开户的奖金组选择，页面设计里没有体现时时彩和乐透彩的区别，先用时时彩
                    if ($iAgentMaxPrizeGroup < $aCurrentPrizeGroups['classic_prize']) {
                        $iAgentCurrentPrize = $iAgentMaxPrizeGroup;
                    } else {
                        $iAgentCurrentPrize = $aCurrentPrizeGroups['classic_prize'];
                    }
                    $iAgentMinPrizeGroup = Sysconfig::readValue('agent_min_grize_group');
                    $oPossibleAgentPrizeGroups = PrizeGroup::getPrizeGroupsBelowExistGroup($iAgentCurrentPrize, $iSeriesId, 8, $iAgentMinPrizeGroup, 'desc', Session::get('is_top_agent'));
                    $oAllPossibleAgentPrizeGroups = PrizeGroup::getPrizeGroupsBelowExistGroup($iAgentCurrentPrize, $iSeriesId, null, $iAgentMinPrizeGroup, 'asc', Session::get('is_top_agent'));
                    $aUserAllPrizeSetQuota = UserPrizeSetQuota::getUserAllPrizeSetQuota($iUserId);
                }
                $aDefaultMaxPrizeGroups = RegisterLink::$aDefaultMaxPrizeGroups;
                $aDefaultPrizeGroups = RegisterLink::$aDefaultPrizeGroups;
                $bUseGroupQuota = SysConfig::get('use_group_quota');

                $this->setVars(compact('oAllPossibleAgentPrizeGroups', 'oAllPossiblePrizeGroups', 'aUserAllPrizeSetQuota'));
                $this->setVars(compact('oPossiblePrizeGroups', 'oPossibleAgentPrizeGroups', 'aLotteriesPrizeSets', 'iAgentCurrentPrize', 'iCurrentPrize', 'aDefaultPrizeGroups', 'aDefaultMaxPrizeGroups', 'iAgentMinPrizeGroup', 'iPlayerMinPrizeGroup', 'bUseGroupQuota'));
                //获取当前用户返点
                $fUserSinglePercentValue = UserPercentSet::getPercentValueByUser($iUserId, UserPercentSet::$iFootBallLotteryId, PercentWay::$jcWays['single']);
                $fUserMultiPercentValue = UserPercentSet::getPercentValueByUser($iUserId, UserPercentSet::$iFootBallLotteryId, PercentWay::$jcWays['multi']);
                $this->setVars(compact('fUserSinglePercentValue', 'fUserMultiPercentValue'));
                break;
        }
    }

    //用户中心首页
    public function index() {
        // TIP 已经在路由中过滤只有代理能访问
        // if (!Session::get('is_agent')) {
        //     return $this->goBack('error', __('_basic.agent-only', $this->langVars));user_manage_logs
        // }$this->params['parent_id'] = Session::get('user_id');

        $iUserId = $this->params['parent_id'] = Session::get('user_id');
        $oQuery = Account::query()->where('parent_id',$iUserId);

        //TODO remove balance_from balance_to in the searach items
        $fBalanceFrom = @$this->params['balance_from'];
        $fBalanceTo = @$this->params['balance_to'];
        if(isset($this->params['balance_from'])) unset($this->params['balance_from']);
        if(isset($this->params['balance_to'])) unset($this->params['balance_to']);

        if ($fBalanceFrom) {
            $oQuery = $oQuery->where('balance', ">=", $fBalanceFrom);
        }

        if ($fBalanceTo) {
            $oQuery = $oQuery->where('balance', "<=", $fBalanceTo);
        }

        //TODO edit
        $this->params['id'] = implode(",", $oQuery->lists("user_id")->toArray());
        $this->view = "userCenter.teamManager.checkChild";
        return parent::index();
    }


    /**
     * 用户中心页面
     * @return RedirectResponse|Response
     */
    public function user() {
        if (Request::method() == 'PUT') {
            return $this->resetPersonalInfo();
        } else {
            Session::put($this->redictKey, Request::fullUrl());
            return $this->render();
        }
    }

    /**
     * [resetPersonalInfo 重置用户个人信息]
     * @return [Response] [description]
     */
    private function resetPersonalInfo() {
        $iUserId = Session::get('user_id');
        $oUser = UserUser::find($iUserId);
        $sNickname = trim(Input::get('nickname'));
        // $oUser->nickname = $sNickname;
        $bSucc = $oUser->update(['nickname' => $sNickname]); // User::$rules['nickname']
        $sErrorMsg = & $oUser->getValidationErrorString();
        $sDesc = $bSucc ? '用户昵称更新成功！' : $sErrorMsg;
        if ($bSucc) {
            $oUser->nickname = $sNickname;
            Session::set('nickname', $oUser->nickname);
        }
        return $this->renderReturn($bSucc, $sDesc);
    }



    /**
     * [getLoginUserMonetaryInfo ajax方式请求用户/代理可用余额，代理昨日销售额]
     * @return [Json] [用户/代理可用余额，代理昨日销售额]
     */
    public function getLoginUserMonetaryInfo() {
        $data = [];
        if (!$iUserId = $this->request->session()->get('user_id')) {
            $fAvailable = 0;
        } else {
            $fAvailable = formatNumber(Account::getAvaliable($iUserId), 2);
        }
        // $data['available'] = $fAvailable;
        return $this->renderData(['coding' => 1, 'data' => ['amount' => $fAvailable]]);
    }

    /**
     * 查询代理下级用户信息
     * @return type
     */
    public function subUsers($pid) {
        if (!isset($pid)) {
            return $this->goBack('error', '_user.missing-parent_id');
        }
        $iUserId = Session::get('user_id');
        $oUser = User::find($pid);
        $this->setVars(compact('oUser'));
        $aUsers = UserUser::getAllUsersBelongsToAgent($iUserId);
        if (in_array($pid, $aUsers) || $pid == $iUserId) {
            $this->params['parent_id'] = $pid;
            $this->action = 'index';
            return parent::index();
        } else {
            return $this->goBack('error', '_user.search-forbidden');
        }
    }

    /**
     * [generateData 生成用户数据]
     * @return [type] [description]
     */
    private function generateData() {
        $iAccountFrom = Input::get('account_from');
        $iAccountTo = Input::get('account_to');
        // TODO 有优化空间，目前是每次循环都查询团队余额，所属用户组，下级户数
        foreach ($this->viewVars['datas'] as $key => $oUser) {
            $iAccountSum = $oUser->getGroupAccountSum();
            if ($iAccountFrom && $iAccountSum < $iAccountFrom) {
                array_forget($this->viewVars['datas'], $key);
                continue;
            }
            if ($iAccountTo && $iAccountSum > $iAccountTo) {
                array_forget($this->viewVars['datas'], $key);
                continue;
            }
            // $oUser->role_desc = $oUser->getUserRoleNames();
            $oUser->children_num = $oUser->getAgentDirectChildrenNum();
            $oUser->group_account_sum = number_format($iAccountSum, 4);
            // pr($oUser->toArray());exit;
        }
    }


    public function personal() {
        if (Request::method() == 'PUT') {
            return $this->resetPersonalInfo();
        } else {
            return $this->render();
        }
    }

    public function trueName() {
        if (Request::method() == 'PUT') {
            $sName = $this->params['name'];
            $oUser = User::find(Session::get('user_id'));
            if ($bSucc = $oUser->setTrueName($sName)){
                return $this->goBack('success', '真实姓名已设置为' . $sName);
            }
            else{
                return $this->goBack('error', '真实姓名设置失败');
            }
        } else {
            return $this->render();
        }
    }

    public function portrait() {
        if (Request::method() == 'POST') {
            $iPortrait = array_get($this->params, 'portrait');
            if (!$iPortrait || !in_array($iPortrait, [1, 2, 3, 4, 5, 6])) {
                return json_encode(['msgType' => 'error']);
            }
            $oUser = UserUser::find(Session::get('user_id'));
            $bSucc = $oUser->update(['portrait_code' => $iPortrait]);
            if ($bSucc) {
                Session::set('portraitCode', $oUser->portrait_code);
                return json_encode(['msgType' => 'success']);
            } else {
                return json_encode(['msgType' => 'error']);
            }
        } else {
            return $this->render();
        }
    }

    /**
     * [safeChangeFundPassword 第一次设置资金密码]
     * @return [Response] [description]
     */
    public function safeChangeFundPassword($isWin = false) {
        $oUser = User::find(Session::get('user_id'));
        if ($oUser->fund_password){
            return $this->goBack('error', '使用方式错误');
        }
        if (Request::method() == 'PUT') {
            $iId = Session::get('user_id');
            return $this->changeFundPassword($iId, true);
        } else {
            if ($isWin) {
                $this->view = $this->resourceView . '.safeChangeWinPassword';
            }

            $this->setVars('isWin', $isWin);
            return $this->render();
        }
    }

    /**
     * [changePassword 改变用户密码]
     * @return [Response]      [description]
     */
    public function changePassword() {
        if (Request::method() == 'PUT') {
            $sOldPassword = trim(Input::get('old_password'));
            $sNewPassword = trim(Input::get('password'));
            $sNewPasswordConfirmation = trim(Input::get('password_confirmation'));
            $this->model = $this->model->find(Session::get('user_id'));
            $sOldPwd = md5(md5(md5($this->model->username . $sOldPassword)));
            if (!$this->model->checkPassword($sOldPassword)) {
                return $this->goBack('error', __('_user.validate-password-fail'));
            }
            if ($this->model->checkFundPassword($sNewPassword)) {
                return $this->goBack('error', __('_user.same-with-fund-password'));
            }
            $aFormData = [
                'password' => $sNewPassword,
                'password_confirmation' => $sNewPasswordConfirmation,
            ];
            $aReturnMsg = $this->model->resetPassword($aFormData);
            if (!$bSucc = $aReturnMsg['success']) {
                $this->langVars['reason'] = $aReturnMsg['msg'];
            }
            // pr($aReturnMsg);exit;
            $sDesc = $bSucc ? __('_user.password-updated') : __('_user.update-password-fail', $this->langVars);
            return $this->renderReturn($bSucc, $sDesc);
        } else {
            $this->saveUrlToSession();
            return $this->render();
        }
    }

    /**
     * [changeFundPassword 改变用户资金密码]
     * @param  [boolean] $bIsFirst [是否初次设置]
     * @return [Response]            [description]
     */
    public function changeFundPassword($bIsFirst = false) {
        if (Request::method() == 'PUT') {
            $sOldFundPassword = trim(Input::get('old_fund_password'));
            $sNewFundPassword = trim(Input::get('fund_password'));
            $sNewFundPasswordConfirmation = trim(Input::get('fund_password_confirmation'));
            $this->model = $this->model->find(Session::get('user_id'));
            if (!$bIsFirst && $sOldFundPassword) {
                if (!$this->model->checkFundPassword($sOldFundPassword)) {
                    return $this->goBack('error', __('_user.validate-fund-password-fail'));
                }
            }
            if ($this->model->checkPassword($sNewFundPassword)) {
                return $this->goBack('error', __('_user.same-with-password'));
            }
            $aFormData = [
                'fund_password' => $sNewFundPassword,
                'fund_password_confirmation' => $sNewFundPasswordConfirmation,
            ];
            $aReturnMsg = $this->model->resetFundPassword($aFormData);
            if (!$bSucc = $aReturnMsg['success']) {
                $this->langVars['reason'] = $aReturnMsg['msg'];
            }
            // pr($bSucc);
            // pr($this->model->getValidationErrorString());exit;
            // pr($this->langVars);exit;
            if ($bSucc) {
                $sUrl = $this->getUrlFromSession();
                return Redirect::to($sUrl)->with('success', __('_user.fund-password-updated'));
            } else {
                return $this->goBack('error', __('_user.update-fund-password-fail', $this->langVars));
            }
        } else {
            $this->saveUrlToSession();
            return $this->render();
        }
    }

    /**
     * [renderReturn 响应函数]
     * @param  [Boolean] $bSucc [是否成功]
     * @param  [String] $sDesc [响应描述]
     * @return [Response]        [响应]
     */
    public function renderReturn($bSucc, $sDesc) {
        // pr($this->model->validationErrors);exit;
        if ($bSucc) {
            return $this->goBack('success', $sDesc);
        } else {
            return $this->goBack('error', $sDesc);
        }
    }

    /**
     * [accurateCreate 精准开户
     *         注册流程:
     *            1. 判断随机码是否正确
     *            2. 判断是否代理用户(一代只能开玩家用户)
     *            3. 判断用户名是否已经存在
     *            4. 获取开户奖金组信息
     *            5. 生成用户信息
     *            6. 新建用户
     *            7. 新建用户的账户
     *            8. 更新用户的account_id字段
     *            9. 创建用户奖金组
     * ]
     * @return [Response] [description]
     */
    public function accurateCreate() {
        $this->view = 'userCenter.teamManager.openAccount';
        if (Request::method() == 'POST') {
            return $this->doCreate();
        }
        else {
            return $this->render();
        }
    }

    private function checkData(& $sErrorMsg){
        $aRandom = explode('_', trim(Input::get('_random')));
        if ($aRandom[1] != Session::get($aRandom[0])) {
            $sErrorMsg = '注册失败！';
            return false;
        }
        // 只有代理才能开户
        if (!Session::get('is_agent') || array_get($this->params, 'is_agent') != 0) {
            $sErrorMsg = __('_basic.no-rights', $this->langVars);
            return false;
        }
        //TODO fix
//        if (Session::get('is_top_agent') && array_get($this->params, 'is_agent') == 0) {
//            $sErrorMsg = __('_basic.no-rights', $this->langVars);
//            return false;
//        }
        // 验证用户名是否存在
        if (!$this->validateUsernameExist($sErrorMsg)) {
            return false;
        }
        return true;
    }

    private function doCreate(){
        if (!$this->checkData($sErrorMsg)){
            return $this->renderReturn(false, $sErrorMsg);
        }
        $data = trimArray(Input::except(['_token', '_random']));

         //TODO change
        $data['prize_group_id'] = 1;
        $data['nickname'] = $data['username'];
        //只能是玩家
        $data['is_agent'] = 0;
        $data['weixin'] = isset($data['weixin']) ? $data['weixin'] : "";

        DB::connection()->beginTransaction();
        if (!UserTool::createUser($data, Session::get('user_id'), null, $oUser, $iErrno, $sErrMsg)){
            DB::connection()->rollback();
            $this->langVars['reason'] = UserTool::getErrMsg($iErrno, $sErrMsg);
            return $this->goBack('error', __('_basic.create-fail', $this->langVars));
        }
        DB::connection()->commit();
        //给用户发送一封激活邮件
        $sRegisterMail = $oUser->email;
        // $oUser->sendActivateMail();
        $oUser->is_tester or BaseTask::addTask('StatUpdateRegisterCountOfProfit', ['date' => $oUser->register_at, 'user_id' => $oUser->id], 'stat');
        BaseTask::addTask('EventTaskQueue', ['event' => 'auth.regist', 'user_id' => $oUser->id, 'data' => []], 'activity');
        return View::make('authority.signupSuccess')->with(compact('sRegisterMail'));
    }

    /**
     * [validateUsernameExist 验证用户名是否存在]
     * @return [Boolean] [true: 存在, false: 不存在]
     */
    private function validateUsernameExist(& $sErrorMsg) {
        $sUsername = trim(Input::get('username'));
        if (!$sUsername) {
            $sErrorMsg = '请填写用户名！';
            return false;
        } else if (UserUser::checkUsernameExist($sUsername)) {
            $sErrorMsg = '该用户名已被注册，请重新输入！';
            return false;
        }
        return true;
    }

    /**
     * [validateEmailExist 验证邮箱是否存在]
     * @return [Boolean] [true: 存在, false: 不存在]
     */
    private function validateEmailExist(& $sErrorMsg) {
        $sEmail = trim(Input::get('email'));
        // $sPassword = trim(Input::get('password'));
        if (!$sEmail) {
            $sErrorMsg = '请填写邮箱！';
            return true;
        } else if (UserUser::checkEmailExist($sEmail)) {
            $sErrorMsg = '该邮箱已被注册，请重新输入！';
            return true;
        }
        return false;
    }

    /**
     * 绑定用户邮箱
     *
     * @return RedirectResponse|Response
     */
    public function bindEmail() {
        if (Request::method() == 'PUT') {
            return $this->bindEmailSave();
        }
        //申请绑定邮箱
        else {
            return $this->render();
        }
    }

    /**
     * 保存邮箱信息并给用户发送确认邮件
     *
     * @return RedirectResponse
     */
    private function bindEmailSave() {
        $user_id = Session::get('user_id');
        $email = trim(Input::get('email'));

        $user = UserUser::find($user_id);

        if (!$user->isActivated()) {
            //邮箱已被绑定
            if (User::checkEmailExist($email)) {
                return $this->goBack('error', '您的邮箱已被绑定，请重新输入邮箱！');
            }

            $user->email = $email;
            $user->save();

            //给用户发送一封激活邮件
            $user->sendActivateMail();

            return $this->goBack('success', '链接已发送，24小时之内有效，请从邮箱激活！');
        }

        return $this->goBack('success', '您已绑定邮箱，无需重复绑定！');
    }

    /**
     * 激活邮箱
     *
     * @return RedirectResponse
     */
    public function activateEmail() {
        $user_id = trim(Input::get('u'));
        $code = trim(Input::get('c'));
        $suser_id = Session::get('user_id');

        //如果用户返回的信息有效，则成功
        $this->viewVars['msg'] = [
            0 => ['class' => 'alert-error', 'backUrl' => route('users.personal'), 'backMsg' => '重新绑定', 'msg' => '验证失败，邮件激活链接无效或已过期。'],
            1 => ['class' => 'alert-success', 'backUrl' => route('home'), 'backMsg' => '返回首页', 'msg' => '恭喜您，邮箱验证成功。'],
        ];

        $this->viewVars['state'] = 0;

        //登陆的用户需要一直才行
        if ($suser_id == $user_id && Cache::section('bindEmail')->get($user_id) == $code) {
            $this->viewVars['state'] = 1;
            //更新用户绑定时间，清空cache
            $user = UserUser::find($user_id);
            $user->activated_at = Carbon::now()->toDateTimeString();
            $user->save();

            Cache::section('bindEmail')->forget($user_id);
        }

        return $this->render();
    }

}
