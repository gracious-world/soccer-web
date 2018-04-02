<?php
namespace App\Models\User;

use App\Models\BaseModel;
use Config;
use Tool;
/**
 * 用户登录记录
 *
 * @author white
 */
class UserLoginIP extends BaseModel {

    static $cacheLevel = self::CACHE_LEVEL_FIRST;
    protected $table = 'user_login_ips';
    protected $softDelete = false;
    protected $fillable = [
        'user_id',
        'username',
        'is_tester',
        'nickname',
        'ip',
    ];
    public static $resourceName = 'UserLoginIP';
    public static $columnForList = [
        'username',
        'ip',
        'is_tester',
        'top_agent',
        'nickname',
        'created_at',
    ];
    public $orderColumns = [
        'username' => 'asc',
    ];
    public static $listColumnMaps = [
        'is_tester' => 'formatted_is_tester',
    ];
    public static $viewColumnMaps = [
        'is_tester' => 'formatted_is_tester',
    ];

    public static function createLoginIPRecord($oUser) {
        $sIP = Tool::getClientIp();
        $oUserLoginIP = static::getObjectByParams(['user_id' => $oUser->id, 'ip' => $sIP]);
        if (is_object($oUserLoginIP)) {
            return true;
        }
        $oUserLoginIP = new static;
        $oUserLoginIP->fill(
                [
                    'user_id' => $oUser->id,
                    'username' => $oUser->username,
                    'is_tester' => $oUser->is_tester,
                    'nickname' => $oUser->nickname,
                    'ip' => $sIP,
                ]
        );
        return $oUserLoginIP->save();
    }

    public static function getLoginUserCount($sBeginDate, $sEndDate = null) {
        $sEndDate or $sEndDate = Carbon::parse($sBeginDate)->addDay()->toDateTimeString();
        $iCount = self::whereBetween('created_at', [$sBeginDate, $sEndDate])->where('is_tester', 0)->distinct()->count(['user_id']);
        return $iCount;
    }

    protected function getFormattedIsTesterAttribute() {
        if ($this->attributes['is_tester'] !== null) {
            return __('_basic.' . strtolower(Config::get('var.boolean')[$this->attributes['is_tester']]));
        } else {
            return '';
        }
    }

}
