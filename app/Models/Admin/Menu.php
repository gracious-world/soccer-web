<?php namespace App\Models\Admin;

use App\Models\BaseModel;
use App\Models\Admin\User;
use App\Models\Func\Functionality;
use Config;
use Cache;
use Route;
use Auth;

# 菜单模型
class Menu extends BaseModel {

    static $cacheLevel = self::CACHE_LEVEL_NONE;
    static $cacheMinutes = 60;
    protected $table = 'menus';

    // static $defaultNamespace = 'App\\Http\\Controllers\\';

    public static $treeable    = true;
    public static $sequencable = true;
    public static $columnForList = [
        'title',
        'parent',
        'functionality_id',
        'controller',
        'action',
        'description',
        'params',
        'new_window',
        'disabled',
        'sequence',
    ];

    public static $ignoreColumnsInEdit = [
        'realm',
        'controller',
        'action',
    ];
    /**
     * 下拉列表框字段配置
     * @var array
     */

    public static $htmlSelectColumns = [
        'parent_id'        => 'aMenuTree',
        'functionality_id' => 'aFunctionalities',
    ];

    protected $softDelete = false;

    protected $fillable = [
        'title',
        'parent_id',
        'parent',
        'functionality_id',
        'controller',
        'action',
        'realm',
        'description',
        'params',
        'new_window',
        'disabled',
        'sequence',
    ];

    public static $rules = [
        'realm'      => 'integer',
        'title'      => 'max:64',
        'params'     => 'max:100',
        'disabled'   => 'boolean',
        'new_window' => 'boolean',
        'sequence'   => 'integer'
    ];

    /**
     * order by config
     * @var array
     */
    public $orderColumns = [
        'sequence' => 'asc'
    ];

    /**
     * The array of custom error messages.
     *
     * @var array
     */
    public static $customMessages = [

    ];

    // public function functionalities()
    // {
    //     return $this->hasMany('FunctionalityRelation');
    // }

    // public function roles()
    // {
    //     return $this->belongsToMany('Role')->withTimestamps();
    // }

    // public function menus()
    // {
    //     return $this->hasMany('Menu', 'functionality_id')->withTimestamps();
    // }

    protected function afterCreate($oCreatedModel) {
        static::deleteMenuCache();
        return true;
    }

    protected function afterDelete($oDeletedModel) {
        static::deleteMenuCache();
        return true;
    }

    protected function afterSave($oSavedModel) {
        if (!parent::afterSave($oSavedModel)) {
            return false;
        }
        static::deleteMenuCache();
        return true;
    }

    /**
     * run before save()
     */
    protected function beforeValidate(){
        if ($this->functionality_id){
            if (!$oFunctionality = Functionality::find($this->functionality_id)){
                return false;
            }
            $this->controller = $oFunctionality->controller;
            $this->action     = $oFunctionality->action;
            $this->realm      = $oFunctionality->realm;
            $this->title or $this->title = $oFunctionality->title;
            $this->description or $this->description = $oFunctionality->description;
        }

        return true;
    }

    public static function compileMenuCacheKey() {
        $iUserId = Auth::user()->id;
        return self::getCachePrefix(true) . 'menus-' . $iUserId;
    }

    public static function deleteMenuCache() {
        if (static::$cacheLevel == self::CACHE_LEVEL_NONE) {
            return true;
        }
        $sCacheKey = self::compileMenuCacheKey();
        Cache::setDefaultDriver(static::$cacheDrivers[static::$cacheLevel]);
        return Cache::forget($sCacheKey);
    }

    public static function & getMenus($aRights = [], $aRealm = []) {
        if (static::$cacheLevel != self::CACHE_LEVEL_NONE){
            $sCacheKey = self::compileMenuCacheKey();
            Cache::setDefaultDriver(static::$cacheDrivers[static::$cacheLevel]);
            if ($aMenus = Cache::get($sCacheKey)){
                return $aMenus;
            }
            else{
                $aMenus = self::_getAvailableMenus($aRights, $aRealm);
                Cache::put($sCacheKey, $aMenus, static::$cacheMinutes);
            }
        }

        isset($aMenus) or $aMenus = self::_getAvailableMenus($aRights, $aRealm);
        return $aMenus;
    }

    private static function & _getAvailableMenus($aRights = [], $aRealm = [Functionality::REALM_ADMIN]) {
        $aColumns = ['id', 'parent_id', 'functionality_id', 'title', 'controller', 'action', 'params', 'realm', 'new_window', 'sequence'];
        $oAllAbledMenus = static::where('disabled', '=', 0)->orderBy('sequence', 'asc')->get($aColumns);
        // $oAllAbledMenus = collect($oAllAbledMenus);
        // pr($oAllAbledMenus->toArray());exit;
        $oMainMenus = $oAllAbledMenus->filter(function($item) {
            return is_null($item->parent_id);
        });
        $aMenus = [];
        $sDefaultNamespace = Config::get('namespace-map.CustomerControllerDefaultNamespace'); // static::$defaultNamespace;
        // pr($aRealm);
        // pr(implode(',', $aRights));
        foreach ($oMainMenus as $oMainMenu) {
            $aMenus[$oMainMenu->id] = $oMainMenu->getAttributes();
            $aMenus[$oMainMenu->id]['children'] = $oAllAbledMenus->filter(function ($item) use ($sDefaultNamespace) {
                $sRouter = $item->controller . '@' . $item->action;
                if ($sRouter != '_main@_main') {
                    $sRouter = $sDefaultNamespace . $sRouter;
                    // pr($sRouter);
                    $oRouter = Route::getRoutes()->getByAction($sRouter);
                    // pr($oRouter);
                    $sRouteName = $oRouter ? $oRouter->getName() : '';
                    // pr($sRouteName);
                    // exit;
                    $item->route_name = $sRouteName;
                }
                return $item->getAttributes();
            })->reject(function ($item) use ($oMainMenu, $aRights, $aRealm) {
                // pr('reject: ' . $oMainMenu->id . ', ' . $item->parent_id . ', ' . $item->realm . ', ' . $item->functionality_id);
                return ($item->parent_id != $oMainMenu->id) || (! in_array($item->realm, $aRealm)) || (count($aRights) && ! in_array($item->functionality_id, $aRights));
            })->toArray();
        }
        unset( $oMainMenus, $oMainMenu, $rightIds, $aColumns);
        // exit;
        return $aMenus;
    }

}