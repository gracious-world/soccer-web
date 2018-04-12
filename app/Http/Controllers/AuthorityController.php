<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User\Role;
use App\Models\User\User;
use App\Models\User\UserLogin;
use App\Models\User\UserLoginIP;
use App\Models\Basic\BusinessPartner;
use App\Models\Func\SysConfig;
use App\Models\BaseTask;

use Session;
use Input;
use Redirect;
use Carbon;
use Tool;
use SessionTool;
use Validator;
use Hash;
use Auth;
use DB;
use Config;
use UserRegisterLink;
use App\Models\User\UserUser;
use RegisterlinkUser;

class AuthorityController extends Controller {
    protected $request;
    /**
     * 页面：登录
     * @return Response
     */
    public function signin(Request $request) {
//        var_dump(Hash::make(md5(md5(md5('toptop123qwe')))));
//        exit;
        if ($request->isMethod('POST')) {
            return $this->postSignin($request);
        } else {
            $sPageName = 'sign in';
            $this->setVars(compact('sPageName'));
            return view('authority.signin')->with($this->viewVars);
        }
    }

    /**
     * 动作：登录
     * @return Response
     */
    public function postSignin(Request $request) {
        $iMaxCleanLoginTimes = 3;
        $iMaxRetryTimes = SysConfig::get('user_login_failed_max_times'); //最多密码错误的次数
        $iLoginTimes = (int)Session::get('LOGIN_TIMES', 0);
        Session::put('LOGIN_TIMES', ++$iLoginTimes);
        $aRandom = explode('_', trim(Input::get('_random')));
        if (count($aRandom) != 2 || (count($aRandom) == 2 && ($aRandom[1] != Session::get($aRandom[0])))) {
            Session::forget($aRandom[0]);
            return $this->goBackForAttempt(__('_basic.login-fail-wrong'));
        }
        Session::forget($aRandom[0]);
        // 如果没有设置锁定帐号.则验证 验证码.默认前3次登录不用验证码, 3次登录失败后需要验证码, 登录成功则清空登录次数
        if (isset($iLoginTimes) && ($iLoginTimes > $iMaxCleanLoginTimes)) {
            // 验证码校验
//            if (!$this->validateCaptcha($sErrorMsg)) {
//                return $this->goBackForAttempt($sErrorMsg);
//            }
        }

        $sUsername = Input::get('username');
        $sPassword = Input::get('password');

        // 取得用户
        $oUser = User::getUserByUsername($sUsername, false);
        if (empty($oUser)) {
            return $this->goBackForAttempt(__('_basic.login-fail-wrong'));
        }

        switch ($oUser->blocked) {
            //被冻结, 禁止登入
            case User::BLOCK_LOGIN:
                return $this->goBackForAttempt(__('_basic.locked-contact-custom-service-to-unlocked'));
                break;

            //被冻结, 禁止登入, 安全防护
            case User::BLOCK_LOGIN_SAFE:
                return $this->goBackForAttempt(__('_basic.safe-locked-contact-custom-service-to-unlocked'));
                break;

            //被冻结, 禁止登入, 密码错误次数过多
            case User::BLOCK_LOGIN_WITH_PWD_ERROR:
                return $this->goBackForAttempt(__('_basic.locked-with-passeord-error-contact-custom-service-to-unlocked'));
                break;
        }

        // 校验密码
        if (!Hash::check($sPassword, $oUser->password)) { // 检验失败
            if ($iMaxRetryTimes > 0) { // 自动冻结判断
                $oUser->incrementLoginTimes();
                $iRetryTimes = $oUser->getLoginTimes();
                $iDiffer = $iMaxRetryTimes - $iRetryTimes;

                if ($iDiffer <= 0) {
                    //冻结帐号
                    DB::connection()->beginTransaction();
                    $oUser->blocked = User::BLOCK_LOGIN_WITH_PWD_ERROR;
                    if ($bSucc = $oUser->save()) {
                        $bSucc = UserManageLog::createLog($oUser->id, $this->blockFunctionalityId, $this->blockFunctionalityTitle, __('_user.block-login-with-pwd-error'));
                    }

                    $bSucc ? DB::connection()->commit() : DB::connection()->rollback();
                    $oUser->flushLoginTimes();

                    return $this->goBackForAttempt(__('_basic.locked-with-passeord-error-contact-custom-service-to-unlocked'));
                } else {
                    $aReplace = ['differ' => $iDiffer];
                    return $this->goBackForAttempt(__('_basic.login-fail-wrong-login-times', $aReplace));
                }
            } else {
                return $this->goBackForAttempt(__('_basic.login-fail-wrong'));
            }
        }

        // 判断是否是禁止登录用户
        $aRoles = $this->_getUserRole($oUser);
        if (in_array(Role::DENY, $aRoles)) {
            return $this->goBackForAttempt(__('_basic.login-fail-wrong'));
        }

        // 登录成功
        if ($iMaxRetryTimes > 0) {
            $oUser->flushLoginTimes();
        }

        if (!$oUser->is_from_link && !$oUser->signin_at) {
            Session::put('first_login', true);
        }
        // 保存登录时间和IP
        $oUser->signin_at = Carbon::now()->toDateTimeString();
        $oUser->login_ip = Tool::getClientIp();
        $bSucc = $oUser->save();

        // 踢出前一SESSION
        SessionTool::deleteSession(false, $oUser->username);
        SessionTool::saveSessionId(false, $oUser->username, Session::getId());

        // 保存SESSION
        $this->_saveSession($oUser, $aRoles);

        // 默认前3次登录不用验证码, 3次登录失败后需要验证码, 登录成功则清空登录次数
        Session::forget('LOGIN_TIMES');

        // 保存登录历史
        UserLogin::createLoginRecord($oUser);
        UserLoginIP::createLoginIPRecord($oUser);

        $oUser->is_tester or BaseTask::addTask('StatUpdateLoginCountOfProfit', ['date' => $oUser->signin_at, 'user_id' => $oUser->id], 'stat');
        $sToUrl = Session::get('__returnUrl');
        if ($sToUrl == '' || $sToUrl == '/') {
            $sToUrl = route('games.index');
        }
        // if ($oUser->account->available > 0) $this->transferToJcPlat($oUser);
        return Redirect::to($sToUrl);
   }

  /**
     * 页面：注册
     * @return Response
     */
  public function signup(Request $request) {
        // pr($sKeyword);exit;
        if ($request->isMethod('POST')) {
            return $this->postSignup();
        }

        $sKeyword = trim(Input::get('prize'));
        // pr($sKeyword);exit;
        $oRegisterLink = null;
        if (!$sKeyword) {
            $sViewFileName = 'authority.signup';
        } else {
            $oRegisterLink = UserRegisterLink::getRegisterLinkByPrizeKeyword($sKeyword);

            if (empty($oRegisterLink) || ($oRegisterLink->is_top && $oRegisterLink->created_count)) {
                $sReason = '该链接已失效！';
                return Redirect::route('home')->with('error', '该链接已失效！');
//                return ('error', '注册失败！' . $sReason);
            }

            if (!$oRegisterLink = UserRegisterLink::getRegisterLinkByPrizeKeyword($sKeyword)) {
                return view('authority.signup')->with(compact('sKeyword', 'oRegisterLink'));
            }

            if ($oRegisterLink->is_top) {
                $sViewFileName = 'authority.signup';
            } else {
                if ($oRegisterLink->is_agent) {
                    $sViewFileName = 'authority.reg-z-y';
                } else {
                    $sViewFileName = 'authority.reg-d-u';
                }
            }

        }
        // pr($oRegisterLink->toArray());exit;
        // $sKeyword or $sKeyword = 'experience';
        return view($sViewFileName)->with(compact('sKeyword', 'oRegisterLink'));
    }

    /**
     * [postSignup 实际处理注册流程
     *         注册流程:
     *            1. 判断随机码是否正确
     *            2. 判断验证码是否正确
     *            3. 判断用户名是否已经存在
     *            4. 获取开户奖金组信息, 如果有链接开户的特征码, 则获取对应的奖金组信息, 否则, 获取体验账户的奖金组
     *            5. 生成用户信息
     *            6. 新建用户
     *            7. 新建用户的账户
     *            8. 更新用户的account_id字段
     *            9. 创建用户奖金组
     *            10.(链接开户) 更新链接开户数
     *            11.(链接开户) 更新链接所开用户的关联表(register_links表的created_count字段)
     * ]
     * @return [Response] [description]
     */
    public function postSignup() {
        if (!$this->checkData($aData, $oRegisterLink, $sErrorMsg)) {
            return $this->renderReturn(false, $sErrorMsg);
        }

        $aData['is_agent'] = 0;//新注册的都是玩家
        $aData['is_tester'] = 0;//都不是测试用户
        DB::connection()->beginTransaction();

        if (!UserUser::createUserDirect($aData, Session::get('user_id'), null, false, $oUser, $iErrno, $sErrMsg)) {

            DB::connection()->rollback();
            $this->langVars['resource'] = __('_model.user');
            $this->langVars['reason'] = UserTool::getErrMsg($iErrno, $sErrMsg);
            return $this->goBack('error', __('_basic.create-fail', $this->langVars));
        }

        //add start 添加register_link_users表记录 2016-01-08 damon
        $aRegisterLinkUserData = [
            'register_link_id' => $oRegisterLink->id,
            'user_id' => $oUser->id,
            'username' => $oUser->username,
            'url' => $oRegisterLink->url,
        ];
        $oRegisterLinkUser = new RegisterLinkUser($aRegisterLinkUserData);
        if (!$oRegisterLinkUser->save()) {
            DB::connection()->rollback();
            return $this->goBack('error', __('_basic.create-fail', $this->langVars));
        }
        //add end
        $oRegisterLink->updateCreateCount();
        DB::connection()->commit();

        //add start 注册用户推送活动队列 2016-01-12 damon
//        BaseTask::addTask('RegisterActivity', ['user_id' => $oUser->id, 'register_link_id' => $oRegisterLink->id], 'activity');
        //add end
        //给用户发送一封激活邮件
        $sRegisterMail = $oUser->email;
        $oUser->sendActivateMail();
        $oUser->is_tester or BaseTask::addTask('StatUpdateRegisterCountOfProfit', ['date' => $oUser->register_at, 'user_id' => $oUser->id], 'stat');
        BaseTask::addTask('EventTaskQueue', ['event' => 'auth.regist', 'user_id' => $oUser->id, 'data' => []], 'activity');
        return view('authority.signupSuccess');
    }



    private function checkData(&$aData, & $oRegisterLink, & $sErrorMsg) {
        $aData = trimArray(Input::except(['_token', '_random']));
        $aRandom = explode('_', trim(Input::get('_random')));
        if ($aRandom[1] != Session::get($aRandom[0])) {
            $sErrorMsg = '注册失败！';
            return false;
        }

        //检查验证码是否正确
        if (!$this->validateCaptcha($sErrorMsg)) {
            return false;
        }

        if (!$aData['prize'] || !$aData['password'] || !$aData['email'] || !$aData['username']) {
            $sErrorMsg = '注册失败！信息不完整！';
            return false;
        }

        //检查两次的密码是否一致
        if ($aData['password'] != $aData['password_confirmation']) {
            $sErrorMsg = '注册失败！两次输入的密码不一致！';
            return false;
        }

//        //检查两次的资金密码是否一致
//        if ( $aData['fund_password'] != $aData['fund_password_confirmation'] ) {
//            $sErrorMsg = '注册失败！两次输入的资金密码不一致！';
//            return false;
//        }
//
//        //检查密码和资金密码是否相同
//        if ( $aData['password'] == $aData['fund_password'] ) {
//            $sErrorMsg = '注册失败！登录密码和资金密码不能相同！';
//            return false;
//        }

        // 验证邮箱是否存在
        if ($this->validateEmailExist($sErrorMsg)) {
            return false;
        }

        // 验证用户名是否存在
        if (!$this->validateUsernameExist($sErrorMsg)) {
            return false;
        }

//        $oRegisterLink = UserRegisterLink::getRegisterLinkByPrizeKeyword($aData['prize']);
//        if (empty($oRegisterLink) || ($oRegisterLink->is_top && $oRegisterLink->created_count)) {
//            $sErrorMsg = '注册失败！推广码无效！';
//            return false;
//        }

        return true;
    }

   /**
     * 验证验证码
     * @return Boolean 验证成功/失败
     */
    private function validateCaptcha(& $sErrorMsg) {
        $aDatas = ['captcha' => trim(Input::get('captcha'))];
        $aRules = ['captcha' => 'required|captcha'];

        $oValidator = Validator::make($aDatas, $aRules);
        if (!$bSucc = $oValidator->passes()) {
            $sErrorMsg = __('_basic.captcha-error');
        }
        Session::forget('captchaHash');
        return $bSucc;
    }


    protected function goBackForAttempt($sErrorMsg) {
        return Redirect::back()
            ->withInput()
            ->withErrors(['attempt' => $sErrorMsg]);
    }
    /**
     * 动作：退出
     * @return Response
     */
    public function logout() {
        Session::flush();
        if (!$bIsAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
            return Redirect::route('games.index');
        }
    }

    private function createUser($sBpIdentity, $sUsername) {
        $oCustomer = BusinessPartner::getActivateBusinessParnter($sBpIdentity);
        $sPwd = strtolower($sUsername) . strtolower($sBpIdentity) . $sUsername;
        $sPwd = md5(md5(md5($sPwd)));
        $aUserData = [
            'bp_id'       => $oCustomer->id,
            'bp_identity' => $oCustomer->identity,
            'bp_name'     => $oCustomer->name,
            'username'    => $sUsername,
            'nickname'    => $sUsername,
            'password'    => $sPwd,
        ];
        $oUser = new User($aUserData);
        DB::beginTransaction();
        if (!$oUser->save()) {
            pr($oUser->getValidationErrorString());exit;
            DB::rollback();
            return false;
        }
        if (!$oUser->createAccount()) {
            DB::rollback();
            return false;
        }
        DB::commit();
        // pr($oUser->toArray());exit;
        return $oUser;
    }

    protected function _getUserRole($oUser) {
        $roles = $oUser->getRoleIds();

        $aDefaultRoles[] = Role::EVERY_USER;

        if ($oUser->is_agent) {
            $aDefaultRoles[] = Role::AGENT;
            if (empty($oUser->parent_id)) {
                $aDefaultRoles[] = Role::TOP_AGENT;
            }
        } else {
            $aDefaultRoles[] = Role::PLAYER;
        }
        $roles = array_merge($roles, $aDefaultRoles);
        $roles = array_unique($roles);
        $roles = array_map(function($value) {
            return (int) $value;
        }, $roles);

        return $roles;
    }

    /**
     * set view vars
     * @param StringTool|array $sKey
     * @param mixed $mValue
     */
    function setVars($sKey, $mValue = null)
    {
        if (is_array($sKey)) {
            foreach ($sKey as $key => $value) {
                $this->setVars($key, $value);
            }
        } else {
            $this->viewVars[$sKey] = $mValue;
        }
    }

    private function _saveSession($oUser, & $aRoles) {
        Session::put('user_id', $oUser->id);
        Session::put('username', $oUser->username);
        Session::put('nickname', $oUser->nickname);
        Session::put('language', $oUser->language);
        Session::put('user_level', $oUser->user_level);
        Session::put('account_id', $oUser->account_id);
        Session::put('forefather_ids', $oUser->forefather_ids);
        Session::put('is_agent', $oUser->is_agent);
        Session::put('prize_group', $oUser->prize_group);
        Session::put('is_tester', $oUser->is_tester);
        Session::put('is_top_agent', $oUser->is_agent && empty($oUser->parent_id));
        Session::put('is_player', !$oUser->is_agent);
        Session::put('CurUserRole', $aRoles);
        Session::put('portraitCode', $oUser->portrait_code);
        $aTerminalConfig = Config::get('terminal');
        Session::put('terminal_id', $aTerminalConfig['id']);
        Session::put('terminal_key', $aTerminalConfig['key']);
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
        } else if (User::checkEmailExist($sEmail)) {
            $sErrorMsg = '该邮箱已被注册，请重新输入！';
            return true;
        }
        return false;
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
     * go back
     * @param String $sMsgType in list: success, error, warning, info
     * @param String $sMessage
     * @return RedirectResponse
     */
    protected function goBack($sMsgType, $sMessage, $bWithModelErrors = false)
    {
//        $oRedirectResponse = Session::get($this->redictKey) ? Redirect::back() : Redirect::route('home');
        $oRedirectResponse = Redirect::back();
        $oRedirectResponse->withInput()->with($sMsgType, $sMessage);
        !$bWithModelErrors or $oRedirectResponse = $oRedirectResponse->withErrors($this->model->validationErrors);
        return $oRedirectResponse;
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
        } else if (User::checkUsernameExist($sUsername)) {
            $sErrorMsg = '该用户名已被注册，请重新输入！';
            return false;
        }
        return true;
    }

}
