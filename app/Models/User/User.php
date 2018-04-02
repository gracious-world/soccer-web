<?php
namespace App\Models\User;

use App\Models\BaseModel;

use Hash;
use Tool;
use Illuminate\Auth\Authenticatable;
use Illuminate\Support\Facades\Validator;
use App\Extension\Validation\CustomValidator;
// use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use App\Models\Fund\Account;
use App\Models\Basic\BusinessPartner;
use Cache;
use Carbon\Carbon;

class User extends BaseModel implements AuthenticatableContract,
    AuthorizableContract,
    CanResetPasswordContract {
    use Authenticatable, Authorizable, CanResetPassword;

    protected static $cacheLevel = self::CACHE_LEVEL_FIRST;
    protected $table = 'users';

    const TYPE_TOP_AGENT     = 2;
    const TYPE_AGENT         = 1;
    const TYPE_USER          = 0;
    const UNBLOCK            = 0;
    const BLOCK_LOGIN        = 1;
    const BLOCK_BUY          = 2;
    const BLOCK_FUND_OPERATE = 3;
    const BLOCK_LOGIN_SAFE        = 4;
    const BLOCK_LOGIN_WITH_PWD_ERROR = 5;

    public static $blockedTypes = [
        self::UNBLOCK            => 'unblock',
        self::BLOCK_LOGIN        => 'block-login',
        self::BLOCK_BUY          => 'block-bet',
        self::BLOCK_FUND_OPERATE => 'block-fund',
        self::BLOCK_LOGIN_SAFE => 'block-safe',
        self::BLOCK_LOGIN_WITH_PWD_ERROR => 'block-login-with-pwd-error',
    ];
    const REGISTER_ERROR_NO_PASSWD = 1;
    const REGISTER_ERROR_PASSWD_WRONG = 2;
    const REGISTER_ERROR_CREATE_ACCOUNT_FAILED = 3;
    const REGISTER_ERROR_CREATE_QUOTA_FAILED = 4;
    const REGISTER_ERROR_CREATE_PRIZE_GROUP_SET = 5;
    const REGISTER_ERROR_USER_SAVE_ERROR = 6;
    const REGISTER_ERROR_PRIZE_GROUP_ERROR = 7;
    const REGISTER_ERROR_QUOTA_NOT_ENOUGH = 8;
    const PASSWD_TYPE_LOGIN = 1;
    const PASSWD_TYPE_FUND = 2;
//    const REGISTER_ERROR_NO_PASSWD = 1;
    protected $softDelete = true;
    protected $fillable = [
        'account_id',
        'bp_id',
        'bp_identity',
        'bp_name',
        'username',
        'portrait_code',
        'nickname',
        'email',
        'activated_at',
        'signin_at',
        'register_at',
        'is_tester',
        'password',
        'register_ip',
        'login_ip',
        'bet_coefficient',
        'bet_multiple',
        'name',
        'qq',
        'mobile',
        'skype',
    ];
    // protected $hidden = ['password', 'fund_password'];
    /**
     * 资源名称
     * @var StringTool
     */
    public static $resourceName = 'User';

    /**
     * If Tree Model
     * @var Bool
     */
    public static $treeable = false;
    // public static $foreFatherIDColumn = 'forefather_ids';

    /**
     * forefather field
     * @var Bool
     */
    // public static $foreFatherColumn = 'forefathers';

    /**
     * the columns for list page
     * @var array
     */
    public static $columnForList = [
        // 'bp_identity',
        'bp_name',
        // 'parent',
        'username',
        'nickname',
        // 'user_type_formatted',
        'available_display',
        // 'group_account_sum',
//        'email',
        // 'blocked',
//        'activated_at',
        'signin_at',
        'created_at',
        // 'is_agent',
        // 'is_tester'
    ];
    public static $noOrderByColumns = [
        'account_available'
    ];
    public static $listColumnMaps = [
        // 'account_available' => 'account_available_formatted',
        // 'is_agent' => 'user_type_formatted',
        'signin_at' => 'friendly_signin_at',
        // 'created_at'   => 'friendly_created_at',
        'activated_at' => 'friendly_activated_at',
        // 'blocked' => 'friendly_block_type',
        'is_tester' => 'friendly_is_tester',
    ];
    public static $ignoreColumnsInView = ['id', 'password', 'remember_token'];
    public static $ignoreColumnsInEdit = ['password'];

    /**
     * the main param for index page
     * @var StringTool
     */
    public static $mainParamColumn = 'bp_id';
    public static $titleColumn = 'username';

    /**
     * 下拉列表框字段配置
     * @var array
     */
    public static $htmlSelectColumns = [
        'bp_id' => 'aCustomers',
        // 'blocked' => 'aBlockedTypes',
        'bet_coefficient' => 'aCoefficient'
    ];
    public static $userTypes = [
        self::TYPE_USER => 'Player',
        self::TYPE_AGENT => 'Agent',
        self::TYPE_TOP_AGENT => 'general-agent',
    ];
    public $autoPurgeRedundantAttributes = true;
    public $autoHashPasswordAttributes = true;
    public static $passwordAttributes = ['password','fund_password'];
    public static $rules = [
//        'bp_id'           => 'required|integer',
//        'bp_identity'     => 'required|alpha_dash|max:50',
//        'bp_name'         => 'max:50',
        'username'        => 'required|alpha_dash|between:6,16',
        'parent_id'       => 'integer',
        'parent'       => 'max:16',
        'forefathers'     => 'between:0,1024',
        'forefather_ids'  => 'between:0,100',
        'blocked'         => 'in:0,1,2,3,4,5,6',
        'is_agent'        => 'in:0, 1',
        'nickname'        => 'required|between:2,16',
        'email'           => 'email|between:0, 50',
        'name'            => 'max:30',
        'qq'              => 'integer',
        'mobile'          => 'max:20',
        'skype'           => 'max:50',
        'account_id'      => 'integer',
        'is_tester'       => 'boolean',
        'activated_at'    => 'date',
        'signin_at'       => 'date',
        'register_at'     => 'date',
        'register_ip'     => 'between:0,15',
        'login_ip'        => 'between:0,15',
        'bet_coefficient' => 'in:1,0.1,0.01,0.5,0.05,0.001',
        'bet_multiple'    => 'integer',
    ];
    // 单独提取出密码的验证规则, 以便在hash之前完成验证并将password字段替换为username . password三次md5后的字符串
    // 正则表达式: 大小写字母+数字, 长度6-16, 不能连续3位字符相同, 不能和资金密码字段相同
    public static $passwordRules = [
        'password' => 'required|different:username',
//        'password_confirmation' => 'required',
    ];
    // 单独提取出资金密码的验证规则, 以便在hash之前完成验证并将fund_password字段替换为username . fund_password三次md5后的字符串
    // 正则表达式: 大小写字母+数字, 长度6-16, 不能连续3位字符相同, 不能和密码字段相同
    public static $fundPasswordRules = [
        'fund_password' => 'required|confirmed|different:username',
        'fund_password_confirmation' => 'required',
    ];


    public $orderColumns = [
        'username' => 'asc'
    ];

    // public function roles() {
    //     return $this->belongsToMany('App\Models\User\Role', 'role_users', 'user_id', 'role_id')->withTimestamps();
    // }

    // public function parents() {
    //     return $this->belongsTo('App\Models\User\User', 'parent_id');
    // }

    // public function children() {
    //     return $this->hasMany('App\Models\User\User', 'parent_id');
    // }

    // public function msg_messages() {
    //     return $this->belongsToMany('App\Models\message\MsgMessage', 'msg_user', 'receiver_id', 'msg_id')->withTimestamps();
    // }

    /**
     * 账户信息关系
     *
     * @return mixed
     */
    public function account() {
        return $this->hasOne('App\\Models\\Fund\\Account', 'user_id', 'id');
    }

    public function customer() {
        return $this->belongsTo('App\\Models\\Basic\\BusinessPartner', 'bp_id', 'id');
    }

    // public function user_bank_cards()
    // {
    //     return $this->hasMany('UserBankCard', '');
    // }

    // public function create_user_links() {
    //     return $this->belongsToMany('RegisterLink', 'register_link_users', 'user_id', 'register_link_id')->withTimestamps();
    // }

    public function __construct(array $attributes = []) {
        parent::__construct($attributes);
    }

    /**
     * Get the unique identifier for the user.
     *
     * @return mixed
     */
    public function getAuthIdentifier() {
        return $this->getKey();
    }

    /**
     * Get the password for the user.
     *
     * @return StringTool
     */
    public function getAuthPassword() {
        return $this->password;
    }

    /**
     * Get the token value for the "remember me" session.
     *
     * @return StringTool
     */
    public function getRememberToken() {
        return $this->remember_token;
    }

    /**
     * Set the token value for the "remember me" session.
     *
     * @param  StringTool  $value
     * @return void
     */
    public function setRememberToken($value) {
        $this->remember_token = $value;
    }

    /**
     * Get the column name for the "remember me" token.
     *
     * @return StringTool
     */
    public function getRememberTokenName() {
        return 'remember_token';
    }

    /**
     * Get the e-mail address where password reminders are sent.
     *
     * @return StringTool
     */
    public function getReminderEmail() {
        return $this->email;
    }

    /**
     * 判断该账户是否激活
     *
     * @return bool 是否激活
     */
    public function isActivated() {
        return $this->email && $this->activated_at;
    }

    /**
     * 发送激活邮件
     *
     * @return mixed
     */
    public function sendActivateMail() {
        //给用户发送一封激活邮件
        $code = mt_rand(1000000, 9999999);

        Cache::section('bindEmail')->put($this->id, $code, 1440);

        $user = $this;

        return Mail::send('emails.auth.activation', [
                    'code' => $code,
                    'user' => $this,
                        ], function($message) use ($user) {
                    $message->to($user->email, $user->username)->subject('绑定邮箱确认');
                });
    }

    /**
     * 访问器：友好的最后登录时间
     * @return StringTool
     */
    protected function getFriendlySigninAtAttribute() {
        if (is_null($this->signin_at))
            return __('_user.not-before'); // '新账号尚未登录'
        else
            return friendly_date($this->signin_at);
    }

    protected function getFriendlyCreatedAtAttribute() {
        // return friendly_date($this->created_at);
        return $this->created_at->toDateTimeString();
    }

    protected function getFriendlyBlockTypeAttribute() {
        return __('_user.' . static::$blockedTypes[$this->blocked]);
    }

    protected function getFriendlyIsTesterAttribute() {
        return yes_no(intval($this->is_tester));
    }

    protected function getUserTypeFormattedAttribute() {
        if ($this->parent_id)
            $sUserType = static::$userTypes[$this->is_agent];
        else
            $sUserType = static::$userTypes[self::TYPE_TOP_AGENT];
        return __('_user.' . $sUserType);
    }

    public function compilePasswordString($iPwdType = self::PASSWD_TYPE_LOGIN, $bConfirmPasswd = true) {
        if ($iPwdType == self::PASSWD_TYPE_FUND) {
            $aPwdRules = static::$fundPasswordRules;
            $sPwdName = 'fund_password';
        } else {
            $aPwdRules = static::$passwordRules;
            $sPwdName = 'password';
        }
        if ($bConfirmPasswd){
            $sConfirmPwdName = $sPwdName . '_confirmation';
            if ($this->{$sPwdName} != $this->{$sConfirmPwdName}){
                return false;
            }
        }
        $oValidator = Validator::make($this->toArray(), $aPwdRules);
        if (!$oValidator->passes()){
            return false;
        }
        $sPwd = strtolower($this->username) . $this->{$sPwdName};
        $this->{$sPwdName} = md5(md5(md5($sPwd)));
        return true;
    }

    /**
     * [generatePasswordStr 生成3次md5后的密码字符串]
     * @param  [Integer] $iPwdType [密码字段类型]
     * @return [Array]    ['success' => true/false:验证成功/失败, 'msg' => 返回消息, 成功: 加密后的密码字符串, 失败: 错误信息]
     */
    public function generatePasswordStr($iPwdType = 1) {
        if ($iPwdType == 2) {
            $aPwdRules = static::$fundPasswordRules;
            $sPwdName = 'fund_password';
        } else {
            $aPwdRules = static::$passwordRules;
            $sPwdName = 'password';
        }
        // pr($this->toArray());
        // pr($aPwdRules);
        // exit;
        $customAttributes = [
            "password" => __('_user.login-password'),
            "password_confirmation" => __('_user.password_confirmation'),
            "fund_password" => __('_user.fund_password'),
            "fund_password_confirmation" => __('_user.fund_password_confirmation'),
            "username" => __('_user.login-username'),
        ];
        $oValidator = Validator::make($this->toArray(), $aPwdRules);
        $oValidator->setAttributeNames($customAttributes);

        if (!$oValidator->passes()) {
            // pr($oValidator->errors()->toArray());exit;
            // $aErrMsg = [];
            foreach ($oValidator->errors()->toArray() as $sColumn => $sMsg) {
                // $aErrMsg[] = implode(',', $sMsg);
                // TIP 只取第一个验证错误信息
                $sError = $sMsg[0];
                break;
            }
            // pr($aErrMsg);exit;
            // $sError = implode(' ', $aErrMsg);
            // pr($sError);exit;
            return ['success' => false, 'msg' => $sError];
        }
        // pr($oValidator->errors());exit;
        $sPwd = strtolower($this->username) . $this->{$sPwdName};
        $sPwd = md5(md5(md5($sPwd)));
        // pr($sPwd);exit;
        return ['success' => true, 'msg' => $sPwd];
    }

    /**
     * [resetPassword 重置密码]
     * @param  [Array] $aFormData [数据数组]
     * @return [Array]    [['success' => true/false:验证成功/失败, 'msg' => 返回消息, 成功: 加密后的密码字符串, 失败: 错误信息]]
     */
    public function resetPassword($aFormData) {
        $this->password = $aFormData['password'];
        $this->password_confirmation = $aFormData['password_confirmation'];

        $aReturnMsg = $this->generatePasswordStr(1);
        if ($aReturnMsg['success']) {
            $this->password = $aReturnMsg['msg'];
            if ($bSucc = $this->save()) {
                $aReturnMsg['msg'] = __('_user.password-updated');
            }
        }
        return $aReturnMsg;
    }

    /**
     * [resetFundPassword 重置资金密码]
     * @param  [Array] $aFormData [数据数组]
     * @return [type]           [description]
     */
    public function resetFundPassword($aFormData) {
        $this->fund_password = $aFormData['fund_password'];
        $this->fund_password_confirmation = $aFormData['fund_password_confirmation'];

        $aReturnMsg = $this->generatePasswordStr(2);
        if ($aReturnMsg['success']) {
            $this->fund_password = $aReturnMsg['msg'];
            if ($bSucc = $this->save()) {
                $aReturnMsg['msg'] = __('_user.fund-password-updated');
            }
        }
        return $aReturnMsg;
    }

    /**
     * [checkPassword 检查密码]
     * @param  [String] $sPassword [密码字符串]
     * @return [Boolean]           [验证成功/失败]
     */
    public function checkPassword($sPassword) {
        $sPwd = strtolower($this->username) . $sPassword;
        $sUserPassword = md5(md5(md5($sPwd)));
        // pr($sUserPassword);exit;
        return Hash::check($sUserPassword, $this->password);
    }

    /**
     * [checkFundPassword 检查资金密码]
     * @param  [String] $sFundPassword [资金密码字符串]
     * @return [Boolean]               [验证成功/失败]
     */
    public function checkFundPassword($sFundPassword) {
        $sPwd = strtolower($this->username) . $sFundPassword;
        $sUserFundPassword = md5(md5(md5($sPwd)));
        return Hash::check($sUserFundPassword, $this->fund_password);
    }

    /**
     * [checkUsernameExist 判断用户名是否存在]
     * @param  [String] $sUsername [用户名]
     * @return [Boolean]           [true:存在, false:不存在]
     */
    public static function checkUsernameExist($sUsername) {
        return User::complexWhere(['username' => $sUsername])->exists();
    }

    /**
     * [checkEmailExist 判断邮箱是否已经被绑定]
     * @param  [String] $sEmail [邮箱名]
     * @return [Boolean]           [true:存在, false:不存在]
     */
    public static function checkEmailExist($sEmail) {
        return User::where('email', '=', $sEmail)->whereNotNull('activated_at')->exists();
    }

    public static function getAllUserNameArrayByUserType($iUserType = self::TYPE_USER, $iAgentLevel = null) {
        $data = [];
        $aColumns = ['id', 'username'];
        if ($iUserType == 'all') {
            $aUsers = User::all($aColumns);
        } else {
            $oQuery = User::where('is_agent', '=', $iUserType);

            switch ($iAgentLevel) {
                case 1:
                    $oQuery = $oQuery->whereNull('parent_id');
                    break;
                case 2:
                    $oQuery = $oQuery->whereNotNull('parent_id');
                    break;
            }
            $aUsers = $oQuery->get($aColumns);
        }

        foreach ($aUsers as $key => $value) {
            $data[$value->id] = $value->username;
        }
        return $data;
    }

    /**
     * [getRoleIds 获取用户的角色id]
     * @return [Array] [用户的角色id数组]
     */
    public function getRoleIds() {
        if (!$aRoles = RoleUser::where('user_id', '=', $this->id)->get())
            return false;
        $aRoleId = [];
        foreach ($aRoles as $oRole) {
            $aRoleId[] = $oRole->role_id;
        }
        // $aRoleId = explode(',', $this->role_ids);
        return $aRoleId;
    }

    /**
     * [getUserRoleNames 获取用户组 ]
     * @return [String]          [用户组]
     */
    public function getUserRoleNames() {
        // $aRoles = User::find($iUserId)->roles()->get();
        $aRoles = $this->roles()->get();
        $aRoleNames = [];
        foreach ($aRoles as $oRole) {
            if (in_array($oRole->role_type, [Role::ADMIN_ROLE, Role::USER_ROLE])) {
                $aRoleNames[] = $oRole->name;
            }
        }
        return implode(',', $aRoleNames);
    }

    /**
     * [getAgentDirectChildrenNum 获取代理的直属用户数量]
     * @return [Int]          [直属用户数量]
     */
    public function getAgentDirectChildrenNum() {
        // $oUser = User::find($iUserId);
        if (!$this->is_agent)
            return 0;
        $iNum = $this->children()->count();
        return $iNum;
    }

    /**
     * [getGroupAccountSum 获取代理的团队余额]
     * @param  [Boolean] [返回值类型, true: 团队余额, flase: 包含团队余额的代理用户信息]
     * @return [Float/Object]          [true: 玩家或代理团队账户余额, flase: 包含团队余额的玩家或代理信息]
     */
    public function getGroupAccountSum($bOnlySum = true) {
        // TODO 当代理下的用户数较多时，计算比较费时，需要优化
        $oAccount = Account::getAccountInfoByUserId($this->id);
        $iGroupAccountSum = $oAccount->available;
        $this->group_account_sum = $iGroupAccountSum;
        if (!$this->is_agent)
            return $bOnlySum ? $iGroupAccountSum : $this;
        // $aUsers = $this->children()->get();
        $aUserIds = static::getAllUsersBelongsToAgent($this->id);
        // pr($this->toArray());exit;
        $oAccounts = Account::getAccountInfoByUserId($aUserIds);
        if ($oAccounts && count($oAccounts)) {
            foreach ($oAccounts as $oAccount) {
                $iGroupAccountSum += $oAccount->available;
            }
        }
        $this->group_account_sum = $iGroupAccountSum;
        return $bOnlySum ? $iGroupAccountSum : $this;
    }

    public function getGroupBalance() {
        $oAccount = Account::find($this->account_id);
        $fGroupBalance = $oAccount->balance;
        if ($aUserIds = static::getAllUsersBelongsToAgent($this->id)){
            $aBalances = Account::whereIn('user_id',$aUserIds)->lists('balance');
            $fGroupBalance += array_sum($aBalances);
        }
        return $fGroupBalance;
    }

    public function getBalance(){
        return Account::where('id','=',$this->account_id)->pluck('balance');
    }

    protected function getBalanceAttribute(){
        return number_format($this->getBalance(), 4);
    }

    protected function getAvailableDisplayAttribute() {
        return number_format($this->account->available, 4);
    }

    public function getGroupBalanceAttribute() {
        return number_format($this->getGroupBalance(), 4);
    }


    /**
     * [getAllUsersBelongsToAgent 查询属于某代理的所有下级的id ]
     * @param  [Integer] $iAgentId [代理id]
     * @return [Array]           [id数组]
     */
    public static function getAllUsersBelongsToAgent($iAgentId) {
        $aColumns = ['id', 'username', 'is_agent'];
        $aUsers = User::whereRaw(' find_in_set(?, forefather_ids)', [$iAgentId])->get($aColumns);
        // $queries = DB::getQueryLog();
        // $last_query = end($queries);
        // pr($last_query);exit;
        $aUserIds = [];
        foreach ($aUsers as $oUser) {
            $aUserIds[] = $oUser->id;
        }
        return $aUserIds;
    }

    /**
     * [getAllUsersBelongsToAgentByUsername 按用户名称查询属于某代理的所有下级的id ]
     * @param  [Integer] $iAgentId [代理id]
     * @return [Array]           [id数组]
     */
    public static function getAllUsersBelongsToAgentByUsername($sAgentName, $bIncludeSelf = TRUE) {
        $aColumns = ['id', 'username', 'is_agent'];
        $oQuery = User::whereRaw(' find_in_set(?, forefathers)', [$sAgentName]);
        if ($bIncludeSelf) {
            $aUsers = $oQuery->orwhereRaw('username=?', [$sAgentName])->get($aColumns);
        } else {
            $aUsers = $oQuery->get($aColumns);
        }
        // $queries = DB::getQueryLog();
        // $last_query = end($queries);
        // pr($last_query);exit;
        $aUserIds = [];
        foreach ($aUsers as $oUser) {
            $aUserIds[] = $oUser->id;
        }
        return $aUserIds;
    }

    /**
     * [getUsersByIds 根据用户id数组获取用户信息]
     * @param  [Array] $aUserIds [用户id数组]
     * @param  [Array] $aColumns [要返回的列]
     * @return [Array]           [用户信息数组]
     */
    public static function getUsersByIds($aUserIds, $aColumns = null) {
        if (!$aUserIds) {
            return [];
        }
        is_array($aUserIds) or $aUserIds = explode(',', $aUserIds);
        $aColumns or $aColumns = ['id', 'username'];
        $aUsers = static::whereIn('id', $aUserIds)->get($aColumns);
        return $aUsers;
    }

    /**
     * [getUsersByUsernames 根据用户名数组获取用户信息]
     * @param  [array]   $aUsernames [用户名数组]
     * @param  [boolean] $bNeedCount [是否返回数据总数]
     * @param  [Array]  $aColumns   [要返回的列]
     * @return [type]              [用户信息数组]
     */
    public static function getUsersByUsernames(array $aUsernames, $bNeedCount = false, $aColumns = null) {
        $aColumns or $aColumns = ['id', 'username', 'is_agent', 'forefather_ids'];
        // pr($aColumns);exit;
        $oQuery = static::whereIn('username', $aUsernames);
        if ($bNeedCount)
            $result = $oQuery->count('id');
        else
            $result = $oQuery->get($aColumns);
        // if (!$bNeedCount) {
        //     $result = [];
        //     foreach ($aUsers as $oUser) {
        //         $result[$oUser->id] = $oUser->username;
        //     }
        // }

        return $result;
    }

    /**
     * [getUsersBelongsToAgent 获取代理的所有直接下级用户]
     * @return [Object]           [代理的所有直接下级用户]
     */
    public function getUsersBelongsToAgent() {
        $aColumns = ['id', 'username', 'is_agent'];
        // pr($iAgentId);
        $aUsers = $this->children()->get($aColumns);
        // pr($aUsers->toArray());exit;
        return $aUsers;
    }

    protected function beforeValidate() {
        $this->portrait_code or $this->portrait_code = 1;
        isset($this->is_tester) or $this->is_tester = 0;
        $this->signin_at or $this->signin_at = null;
//        $oBusinessPartner = BusinessPartner::where('id', $this->bp_id)->orWhere('identity', $this->bp_identity)->first();
//        if (!$this->bp_name || !$this->bp_id || !$this->bp_identity) {
//            if ($this->bp_id) {
//                $oBusinessPartner = BusinessPartner::find($this->bp_id);
//            } else if ($this->bp_identity) {
//                $oBusinessPartner = BusinessPartner::getActivateBusinessParnter($this->bp_identity);
//            }
//            $this->bp_identity or $this->bp_identity = $oBusinessPartner->identity;
//            $this->bp_name or $this->bp_name = $oBusinessPartner->name;
//        }
        return parent::beforeValidate();
    }

    /**
     * 取得玩法设置数组，供渲染投注页面或奖金页面使用
     * @param int $iUserId
     * @param Lottery $oLottery
     * @param bool $bForBet
     * @return array &
     */
    public static function & getWaySettings($iUserId, $oLottery, $bForBet = false, & $sGroupName = null) {
        $iGroupId = UserPrizeSet::getGroupId($iUserId, $oLottery->id, $sGroupName);
        if (empty($iGroupId)) {
            $a = [];
            return $a;
        }
        // pr($iGroupId);exit;
        // $iGroupId = 512;
        $aPrizes = & PrizeGroup::getPrizeDetails($iGroupId);
//        pr($aPrizes);

        $fMaxPrize = $bForBet ? static::getPrizeLimit($iUserId) : null;
        return WayGroup::getWayInfos($oLottery->series_id, $aPrizes, $fMaxPrize);
    }

    public static function & getPrizeSettingsOfUser($iUserId, $iLotteryId, & $sGroupName) {
        $iGroupId = UserPrizeSet::getGroupId($iUserId, $iLotteryId, $sGroupName);
        if (empty($iGroupId)) {
            $aPrizes = [];
        } else {
            $aPrizes = & PrizeGroup::getPrizeDetails($iGroupId);
        }
        return $aPrizes;
    }

    /**
     * 取得奖金限额
     *
     * @param int $iUserId
     * @return int
     */
    public static function getPrizeLimit($iUserId) {
        return SysConfig::readValue('bet_max_prize');
    }

    /**
     * [checkUserBelongsToAgent 检查用户是否属于当前登录的代理]
     * @param  [Integer] $iUserId [用户ID]
     * @return [Boolean]          [true/false: 属于/不属于]
     */
    public function checkUserBelongsToAgent($iUserId, $bDirect = false) {
        // $iUserId or $iUserId = Session::get('user_id');
        if ($this->is_agent) {
            // $oUser = User::find($iUserId);
            $oToCheckUser = static::find($iUserId);
            if ($bDirect) {
                return $oToCheckUser->parent_id == $this->id;
            }
            if (!$oToCheckUser->forefather_ids) {
                return false;
            }
            $aForeIds = explode(',', $oToCheckUser->forefather_ids);
            return in_array($this->id, $aForeIds);
//            $aUsers   = $this->getUsersBelongsToAgent();
//            $aUserIds = [];
//            foreach ($aUsers as $oUser) {
//                $aUserIds[] = $oUser->id;
//            }
//            return in_array($iUserId, $aUserIds);
        }
        return false;
    }


    /**
     * [getUserLevelAttribute 获取用户级别]
     * @return [Integer] [用户级别]
     */
    protected function getUserLevel() {
        return !is_null($this->parent_id) ? count(explode(',', $this->forefather_ids)) : 0;
    }


    /**
     * [getUserByParams 根据参数查询用户对象]
     * @param  [Array] $aParams [参数数组]
     * @param  [Array] $aInSetKeys [需要使用find_in_set函数的查询条件的key值数组]
     * @return [Object]          [用户对象]
     */
    public static function getUserByParams(array $aParams = ['*'], $aInSetKeys) {
        $oQuery = static::where('id', '>', 0);
        foreach ($aParams as $key => $value) {
            if (in_array($key, $aInSetKeys)) {
                $oQuery = $oQuery->whereRaw(' find_in_set(?, ' . $key . ')', [$value]);
            } else {
                $oQuery = $oQuery->where($key, '=', $value);
            }
        }
        return $oQuery->get()->first();
    }

    /**
     * [generateAccountInfo 根据用户对象创建账户对象]
     * @return [Object]        [账户对象]
     */
    public function generateAccountInfo() {
        $oAccount = new Account;
        $oAccount->user_id = $this->id;
        $oAccount->username = $this->username;
        $oAccount->is_tester = $this->is_tester;
        $oAccount->withdrawable = 0;
        $oAccount->status = 1;
        return $oAccount;
    }

    public function createAccount(){
        if ($oAccount = Account::createAccount($this)){
            $this->account_id = $oAccount->id;
            $bSucc = $this->save();
        }
        else{
            $bSucc = false;
        }
        return $bSucc;
    }

    /**
     * [generateUserInfo 生成新建用户的信息]
     * @param [String] $sPrizeGroup [如果是代理, 则prize_group为其奖金组, 玩家有多种奖金组, 所以置空值]
     * @param [Array] $data         [表单参数]
     * @return [Array]              [生成成功/失败提示信息]
     */
    public function generateUserInfo( $data) {
        $data['username'] = strtolower($data['username']);
        (isset($data['fund_password']) && $data['fund_password']) or $data['fund_password'] = '';
        $data['register_ip'] = Tool::getClientIp();
        $data['register_at'] = date('Y-m-d H:i:s');
        // pr($data);
        // 验证成功，添加用户
        $this->fill($data);
        // pr($this->toArray());exit;
        // TODO 这两个字段不能为空, parent_str可能已经被弃用, 后续可以考虑写到User模型的beforeValidate里
//        $this->parent_str = $this->forefather_ids;
        $aReturnMsg = ['success' => true, 'msg' => __('_user.user-info-generated')];
        if ($this->password) {
            $aReturnMsg = $this->compilePasswordString(self::PASSWD_TYPE_LOGIN);
            if ($aReturnMsg['success']) {
                $this->password = $aReturnMsg['msg'];
                $aReturnMsg['msg'] = __('_user.password-generated');
            }
            unset($this->password_confirmation);
        } else {
            return ['success' => false, 'msg' => __('_user.no-password')];
        }
        // if ($this->fund_password) {
        //     $aReturnMsg = $this->generatePasswordStr(2);
        //     if ( $aReturnMsg['success'] ) {
        //         $this->fund_password = $aReturnMsg['msg'];
        //         $aReturnMsg['msg'] = __('_user.fund-password-generated');
        //     }
        //     unset($this->fund_password_confirmation);
        // }
        // pr($this->toArray());exit;

        return $aReturnMsg;
    }

    public static function createUser($aData, $sPrizeGroup, $iParentId, $iRegisterLinkId, $bConfirmPasswd = false, & $oUser, & $iErrno, & $sErrmsg) {
        if (!$data = static::compileUserData($aData, $sPrizeGroup, $iParentId, $iRegisterLinkId, $iErrNo)) {
            return false;
        }
        $oUser = new static($data);
//        pr($oUser->toArray());
//        exit;
        if (!$oUser->compilePasswordString(self::PASSWD_TYPE_LOGIN, $bConfirmPasswd)) {
            $iErrno = self::REGISTER_ERROR_PASSWD_WRONG;
            return false;
        }
        if (!$oUser->save()) {
            $iErrNo  = self::REGISTER_ERROR_USER_SAVE_ERROR;
            $sErrmsg = $oUser->getValidationErrorString();
            return false;
        }
        if (!$oUser->createAccount()) {
            $iErrno = User::REGISTER_ERROR_CREATE_ACCOUNT_FAILED;
            return false;
        }
        if (!$bSucc = $oUser->initPrizeSet($sPrizeGroup)) {
            $iErrno = User::REGISTER_ERROR_CREATE_PRIZE_GROUP_SET;
            return false;
        }
        if ($iParentId) {
            $bSucc = ZeroCommissionSet::createRecord($oUser);
        }
        return $bSucc;
    }

    public function initPrizeSet($sPrizeGroup){
        return UserPrizeSet::initUserPrizeGroup($this, $sPrizeGroup);
    }

   public static function & compileUserData($aData, $sPrizeGroup, $iParentId, $iRegisterLinkId = null, & $iErrno) {
        $data = [
            'username'     => $aData['username'],
            'password'     => $aData['password'],
            'nickname'     => $aData['nickname'],
//            'name' => empty($aData['name']) ? null : $aData['name'],
            'is_agent'     => $aData['is_agent'],
            'email'        => isset($aData['email']) ? $aData['email'] : null,
            'qq'           => isset($aData['qq']) ? $aData['qq'] : null,
            'weixin'           => isset($aData['weixin']) ? $aData['weixin'] : null,
            'mobile'       => isset($aData['mobile']) ? $aData['mobile'] : null,
            'skype'        => isset($aData['skype']) ? $aData['skype'] : null,
            'prize_group'  => $sPrizeGroup,
            'register_ip'  => Tool::getClientIp(),
            'register_at'  => ($sCurTime      = Carbon::now()->toDateTimeString()),
            'activated_at' => $sCurTime,
        ];
        if ($iParentId) {
            $oAgent               = static::find($iParentId);
            $data['parent_id']    = $oAgent->id;
            $data['parent']    = $oAgent->username;
            $data['forefathers'] = trim($oAgent->forefathers.','.$oAgent->username,',');
            $data['forefather_ids'] = trim($oAgent->forefather_ids.','.$oAgent->id,',');
            $data['is_tester']    = $oAgent->is_tester;
            $data['is_from_link'] = intval($iRegisterLinkId > 0);
        } else {
            $data['is_tester']    = $aData['is_tester'];
            $data['is_from_link'] = intval($iRegisterLinkId > 0);
        }
        return $data;
    }

    public static function compileUserObject($data, & $iErrno, $bConfirmPasswd = true) {
        if (!$data['password']){
            $iErrno = self::REGISTER_ERROR_NO_PASSWD;
            return false;
        }
        $data['username'] = strtolower($data['username']);
        (isset($data['fund_password']) && $data['fund_password']) or $data['fund_password'] = '';
        $data['register_ip'] = Tool::getClientIp();
        $data['register_at'] = date('Y-m-d H:i:s');
        $oUser = new static($data);
        $aReturnMsg = ['success' => true, 'msg' => __('_user.user-info-generated')];
        if (!$oUser->compilePasswordString(self::PASSWD_TYPE_LOGIN,$bConfirmPasswd)){
            $iErrno = self::REGISTER_ERROR_PASSWD_WRONG;
            return false;
        }
        unset($oUser->password_confirmation);
        return $oUser;
    }

    public static function getAllUserArrayByUserType($iUserType = self::TYPE_USER, $aExtraColumn = []) {
        $aColumns = ['id', 'username', 'blocked', 'parent_id', 'parent', 'account_id'];
        $aColumns = array_merge($aColumns, $aExtraColumn);
        if ($iUserType == 'all') {
            $aUsers = User::all($aColumns);
        } else {
            if ($iUserType == self::TYPE_TOP_AGENT) {
                $oQuery = User::where('is_agent', '=', self::TYPE_AGENT)->whereNull('parent_id');
            } else {
                $oQuery = User::where('is_agent', '=', $iUserType);
            }
            $aUsers = $oQuery->get($aColumns);
        }
        return $aUsers;
    }

    /**
     * 根据用户名查找
     *
     * @param $username
     * @return \LaravelBook\Ardent\Ardent|\LaravelBook\Ardent\Collection|static
     */
    public static function findUser($username) {
        if (static::$cacheLevel == self::CACHE_LEVEL_NONE) {
            return parent::where('username', '=', $username)->first();
        }
        Cache::setDefaultDriver(static::$cacheDrivers[static::$cacheLevel]);

        $key = static::createCacheKey($username);
        if ($aAttributes = Cache::get($key)) {
            $obj = new static;
            $obj = $obj->newFromBuilder($aAttributes);
        } else {
            $obj = parent::where('username', '=', $username)->first();
            if (!is_object($obj)) {
                return false;
            }
            Cache::forever($key, $obj->getAttributes());
        }

        return $obj;
    }

    /**
     * 保存之后出发的事件
     *
     * @param $oSavedModel
     * @return bool
     */
    protected function afterSave($oSavedModel) {
        $this->deleteCache($this->username);
        return parent::afterSave($oSavedModel);
    }

    public function setBetParams($iMultiple, $fCoefficient) {
        if ($this->bet_multiple == $iMultiple && $this->bet_coefficient == $fCoefficient) {
            return true;
        }
        $data = [
            'bet_multiple' => $iMultiple,
            'bet_coefficient' => $fCoefficient,
        ];
        $aConditions = [
            'id' => ['=',$this->id]
        ];
        if ($bSucc = $this->strictUpdate($aConditions,$data)) {
            $this->bet_multiple = $iMultiple;
            $this->bet_coefficient = $fCoefficient;
        }
        return $bSucc;
    }

    public function & getDirectChildrenArray() {
        $oChildrens = static::where('parent_id', '=', $this->id)->orderBy('username', 'asc')->get(['id', 'username']);
        $aChildren = [];
        foreach ($oChildrens as $oChildren) {
            $aChildren[$oChildren->id] = $oChildren->username;
        }
        return $aChildren;
    }

    public function isChild($iUserId, $bDirect = true, & $oChildren = null) {
        $oUser = $oChildren = User::find($iUserId);
        if (empty($oUser)) {
            return false;
        }
        if (!$oUser->parent_id) {
            return false;
        }
        if ($bDirect) {
            return $oUser->parent_id == $this->id;
        } else {
            if ($oUser->forefather_ids) {
                $aForeId = explode(',', $oUser->forefather_ids);
                return in_array($this->id, $aForeId);
            }
        }
    }

    public function getTopAgentId() {
        if (!$this->parent_id) {
            return $this->id;
        } else {
            $aFores = explode(',', $this->forefather_ids);
            return $aFores[0];
        }
    }

    public function getDirectParent() {
        $aColumns = ['id', 'username', 'is_agent'];
        return  $this->parent_id ? User::find($this->parent_id, $aColumns) : null;

    }

    public function getTopAgentUserName() {
        if (!$this->parent_id) {
            return $this->username;
        } else {
            $aFores = explode(',', $this->forefathers);
            return $aFores[0];
        }
    }

    public static function getRegisterCount($sDate, $bOnlyTop = false) {
        $sSql = "select count(distinct id) count from users where register_at between '$sDate' and '$sDate 23:59:59' and is_tester = 0";
        // !$bOnlyTop or $sSql .= " and parent_id is null";
        $aResults = DB::select($sSql);
        return $aResults[0]->count ? $aResults[0]->count : 0;
    }

    public function setTrueName($sName){
        if ($this->name){
            return false;
        }
        $this->name = $sName;
        return $this->save();
    }

    public static function getUserTypes(){
        return parent::_getArrayAttributes(__FUNCTION__);
    }

     /**
     * 根据用户名数组返回用户对象
     * @param  string  $sUsername 用户名
     * @return User
     */
    public static function getUserByUsername($sUserName,$bWithDeleted = true) {
        $oQuery = static::where('username', $sUserName);
        !$bWithDeleted or $oQuery = $oQuery->withTrashed();
        return $oQuery->first();
    }

     /**
     * 登录重试次数+1
     */
    public function incrementLoginTimes(){
        $sKey = $this->compileLoginTimesKey();
        Cache::setDefaultDriver(static::$cacheDrivers[self::CACHE_LEVEL_FIRST]);
        Cache::has($sKey) or Cache::forever($sKey, 0);
        Cache::increment($sKey);
    }

    /**
     * 生成登录重试次数缓存key
     * @return string
     */
    private function compileLoginTimesKey(){
        return $this->getCachePrefix() . 'login-try-times-' . $this->username;
    }

    /**
     * 返回登录重试次数
     * @return int
     */
    public function getLoginTimes(){
        $sKey = $this->compileLoginTimesKey();
        Cache::setDefaultDriver(static::$cacheDrivers[self::CACHE_LEVEL_FIRST]);
        return Cache::get($sKey);
    }

    /**
     * 清除登录重试次数
     */
    public function flushLoginTimes(){
        $sKey = $this->compileLoginTimesKey();
        Cache::setDefaultDriver(static::$cacheDrivers[self::CACHE_LEVEL_FIRST]);
        Cache::forget($sKey);
    }

}
