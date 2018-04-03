<?php
namespace App\Models\User;

use App\Models\BaseModel;
use Config;
use Tool;
use Session;
use Carbon;
/**
 * 用户登录记录
 *
 * @author white
 */
class UserLogin extends BaseModel {
    static $cacheLevel = self::CACHE_LEVEL_FIRST;
    protected $table = 'user_logins';
    protected $softDelete = false;
    protected $fillable = [
        'user_id',
        'username',
        'is_tester',
        'nickname',
        'ip',
        'signed_time',
        'session_id',
        'http_user_agent',
    ];
    public static $resourceName = 'UserLogin';

    public static $columnForList = [
        'username',
        'is_tester',
        'nickname',
        'ip',
        'signed_time',
    ];

    public $orderColumns = [
        'signed_time' => 'desc',
        'username' => 'asc',
    ];

    public static $listColumnMaps = [
        'signed_time'      => 'formatted_signed_time',
        'is_tester' => 'formatted_is_tester',
    ];

    public static $viewColumnMaps = [
        'signed_time'      => 'formatted_signed_time',
        'is_tester' => 'formatted_is_tester',
    ];

    public static function createLoginRecord($oUser){
        $oUserLogin = new static;
        $oUserLogin->fill(
                [
                    'user_id' => $oUser->id,
                    'username' => $oUser->username,
                    'is_tester' => $oUser->is_tester,
                    'nickname' => $oUser->nickname,
                    'ip' => Tool::getClientIp(),
                    'signed_time' => time(),
                    'session_id' => Session::getId(),
                    'http_user_agent' => $_SERVER['HTTP_USER_AGENT'],
                ]
            );
         return $oUserLogin->save();
    }

    public static function getLoginUserCount($sBeginDate, $sEndDate = null){
        $sEndDate or $sEndDate = Carbon::parse($sBeginDate)->addDay()->toDateTimeString();
        $iCount = self::whereBetween('created_at', [$sBeginDate, $sEndDate])->where('is_tester', 0)->distinct()->count(['user_id']);
        return $iCount;
    }

    protected function getFormattedSignedTimeAttribute() {
        return date('Y-m-d H:i:s', $this->attributes['signed_time']);
    }

    protected function getFormattedIsTesterAttribute() {
        if ($this->attributes['is_tester'] !== null) {
            return __('_basic.' . strtolower(Config::get('var.boolean')[$this->attributes['is_tester']]));
        } else {
            return '';
        }
    }

}
