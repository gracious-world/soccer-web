<?php namespace App\Models\Admin;

use Illuminate\Auth\Authenticatable;
// use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

use App\Models\BaseModel;
use App\Models\Admin\AdminRole;
use App\Models\Admin\AdminRoleUser;

use Validator;

class AdminUser extends BaseModel implements AuthenticatableContract,
                                    AuthorizableContract,
                                    CanResetPasswordContract
{
    use Authenticatable, Authorizable, CanResetPassword;

    const LOGIN_SUCCESS             = 0;
    const LOGIN_FAILED_CAPTCHA      = 1;
    const LOGIN_FAILED_PASSWD       = 2;
    const LOGIN_FAILED_NON_ACTIVIED = 3;
    const LOGIN_FAILED_SECURD       = 4;
    const LOGIN_FAILED_DENY         = 5;


    protected $table      = 'admin_users';
    protected $softDelete = false;
    protected $fillable   = ['username', 'name', 'email', 'password', 'password_confirmation', 'language', 'menu_link', 'menu_context', 'actived', 'bp_id', 'signin_at'];
    protected $hidden     = ['remember_token'];

    public static $titleColumn         = 'username';
    public static $enabledBatchAction  = true;
    public static $columnForList       = [ 'id', 'username', 'name', 'bp_id', 'language', 'actived'];
    public static $ignoreColumnsInView = ['password','remember_token'];
    public static $ignoreColumnsInEdit = ['password'];
    public static $mainParamColumn     = 'username';

    public static $passwordAttributes  = ['password'];

    public $autoPurgeRedundantAttributes = true;
    public $autoHashPasswordAttributes   = true;

    /**
     * 下拉列表框字段配置
     * @var array
     */
    public static $htmlSelectColumns = [
        'language' => 'aLanguages',
        'bp_id'    => 'aCustomers',
    ];

    public static $rules = [
        'username'              => 'required|alpha_dash|between:4,32|unique:admin_users,username,',
        'name'                  => 'between:0,50',
        'email'                 => 'email|between:0,200',
        // 'password'              => 'required|between:6,16|confirmed',
        // 'password_confirmation' => 'required|between:6,16',
        'language'              => 'between:0,10',
        'menu_link'             => 'boolean',
        'menu_context'          => 'boolean',
        'bp_id'                 => 'integer',
        'actived'               => 'boolean',
        'secure_card_number'    => 'between:0,10'
    ];



    // 单独提取出密码的验证规则, 以便在hash之前完成验证并将password字段替换为username . password三次md5后的字符串
    // 正则表达式: 大小写字母+数字, 长度8-16, 不能连续3位字符相同
    public static $passwordRules = [
        'password'              => 'required|custom_admin_password|confirmed',
        'password_confirmation' => 'required',
    ];

    public $orderColumns = [
        'username' => 'asc'
    ];

    public function __construct(array $attributes = []) {
        parent::__construct($attributes);
    }

    public function roles()
    {
        return $this->belongsToMany('App\Models\Admin\AdminRole', 'admin_role_admin_user', 'user_id', 'role_id')->withPivot(AdminRoleUser::$columnsForPivot)->withTimestamps();
    }

    public function beforeValidate()
    {
        $this->username = strtolower($this->username);
        if ($this->username && empty($this->name)){
            $this->name = ucfirst($this->username);
        }
        if ($this->id) {
            static::$rules['username'] = 'required|between:4,32|unique:users,username,' . $this->id;
        }
        // pr(static::$rules);exit;
        return parent::beforeValidate();
    }
    // 系统级管理员不可删除
    public function beforeDelete(){
        if ($this->username == 'system' || $this->id == 1){
            return false;
        }
        if (AdminRoleUser::checkUserRoleRelation(Role::ADMIN, $this->id)){
            return false;
        }
        return true;
    }

    public function getFriendlySigninAtAttribute()
    {
        return is_null($this->signin_at) ? __('Not login before') : friendly_date($this->signin_at);
    }

    public function getRoleIds() {
        $oRoles = $this->roles()->get();
        $aRoleIds = [];
        foreach ($oRoles as $key => $oRole) {
            $aRoleIds[] = $oRole->pivot->role_id;
        }
        return $aRoleIds;
    }

    /**
     * [_getUserRole 获取用户角色数据]
     * @return [type] [存入Session, key为CurUserRole]
     */
    public function getUserRoles() {
        $aRoleIds = $this->getRoleIds();
        $iAdminRoleId = AdminRole::ADMIN;
        $iEveryOneId  = AdminRole::EVERYONE;

        // 如果用户有Administrators角色，使用系统管理员权限；否则，默认添加everyone角色
        array_push($aRoleIds, $iEveryOneId);
        $aRoleIds = array_map(function($value){
            return intval($value);
        }, $aRoleIds);
        array_unique($aRoleIds);
        return $aRoleIds;
    }

    public function resetPassword($aFormData) {
        $this->password              = $aFormData['password'];
        $this->password_confirmation = $aFormData['password_confirmation'];
        // pr($aFormData);exit;
        $aReturnMsg = $this->generatePasswordStr();
        // pr($aReturnMsg);exit;
        if ($aReturnMsg['success']) {
            $this->password = $aReturnMsg['msg'];
            if ($bSucc = $this->updateUniques()) {
                $aReturnMsg['msg'] = __('_adminuser.password-updated');
            } else {
                $aReturnMsg['msg'] = $this->getValidationErrorString();
            }
        }
        return $aReturnMsg;
    }

    public function generateUserInfo($data) {
        $data['username'] = strtolower($data['username']);
        (isset($data['nickname']) && $data['nickname']) or $data['nickname'] = $data['username']; // TODO 页面没有填写nickname字段，先用username替代nickname
        // pr($data);
        // 验证成功，添加用户
        $this->fill($data);
        // pr($this->toArray());exit;
        $aReturnMsg = ['success' => true, 'msg' => __('_user.user-info-generated')]; // ['success' => true, 'msg' => __('_user.user-info-generated')];
        if (!$this->password){
            $this->password = $this->password_confirmation = Config::get('auth.default_passwd');
        }

        if ($this->password) {
            $aReturnMsg = $this->generatePasswordStr();
            if ($aReturnMsg['success']) {
                $this->password = $aReturnMsg['msg'];
                $aReturnMsg['msg'] = __('_user.password-generated');
            }
            unset($this->password_confirmation);
        } else {
            return ['success' => false, 'msg' => __('_user.no-password')];
        }
        // pr($this->toArray());exit;

        return $aReturnMsg;
    }

    /**
     * [generatePasswordStr 生成3次md5后的密码字符串]
     * @return [Array]    ['success' => true/false:验证成功/失败, 'msg' => 返回消息, 成功: 加密后的密码字符串, 失败: 错误信息]
     */
    public function generatePasswordStr()
    {
        $aPwdRules = static::$passwordRules;
        $sPwdName = 'password';

        $customAttributes = [
            "password"                   => __('_adminuser.password'),
            "password_confirmation"      => __('_adminuser.password_confirmation'),
            "username"                   => __('_adminuser.username'),
        ];
        $oValidator = Validator::make($this->toArray(), $aPwdRules);
        $oValidator->setAttributeNames($customAttributes);

        if (! $oValidator->passes()) {
            $aErrMsg = [];
            foreach ($oValidator->errors()->toArray() as $sColumn => $sMsg) {
                $aErrMsg[] = implode(', ', $sMsg);
            }
            $sError = implode(' ', $aErrMsg);
            return ['success' => false, 'msg' => $sError];
        }

        $sPwd = strtolower($this->username) . $this->{$sPwdName};
        $sPwd = md5(md5(md5($sPwd)));
        return ['success' => true, 'msg' => $sPwd];
    }

}
