<?php
namespace App\Models\Admin;

use App\Models\BaseModel;
use App\Models\Admin\AdminRole;
use App\Models\Admin\AdminUser;

use Session;

# Deprecated 管理员角色关联
class AdminRoleUser extends BaseModel {
    /**
     * The database table used by the model.
     *
     * @var StringTool
     */
    protected $table = 'admin_role_admin_user';
    public static $resourceName = 'AdminRoleUser';

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];
    protected $guarded = [];
    /**
     * 软删除
     * @var boolean
     */
    protected $softDelete = false;
    protected $fillable = [
        'id',
        'user_id',
        'role_id',
        'username',
        'role_name',
    ];
    public static $columnForList = [
        'username',
        'role_name',
        'created_at',
        'updated_at',
    ];
    /**
     * 下拉列表框字段配置
     * @var array
     */
    public static $htmlSelectColumns = [
        'user_id' => 'aUsers',
        'role_id' => 'aRoles',
    ];
    public $orderColumns = [
        'updated_at' => 'asc'
    ];
    public static $rules = [
        'user_id' => 'required|integer',
        'role_id' => 'required|integer',
    ];

    public static $columnsForPivot = ['username', 'role_name'];

    protected function beforeValidate(){
        if (!$this->role_id || !$this->user_id){
            return false;
        }
        $oRole = AdminRole::find($this->role_id);
        if (empty($oRole)){
            return false;
        }
        if (!$oRole->user_settable){
            return false;
        }
        if ($oRole->id == 1 && !Session::get('IsAdmin')){
            return false;
        }
        $this->role_name = $oRole->name;
        $oAdmin = AdminUser::find($this->user_id);
        if (empty($oAdmin)){
            return false;
        }
        $this->username = $oAdmin->username;
        return parent::beforeValidate();
    }

    public static function checkUserRoleRelation ($role_id, $user_id)
    {
        if (!$role_id || !$user_id) return false;
        return static::where('role_id', '=', $role_id)->where('user_id', '=', $user_id)->exists();
    }
}