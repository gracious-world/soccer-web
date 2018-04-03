<?php namespace App\Models\Func;

use App\Models\BaseModel;
use App\Models\Admin\AdminUser;
use App\Models\Admin\AdminRole;
use App\Models\Admin\Menu;
use Config;
use Cache;
use Route;
use App\Models\Func\FunctionalityRelation;

class Functionality extends BaseModel {
    static $cacheLevel = self::CACHE_LEVEL_FIRST;
    protected static $cacheMinutes = 0;
    /**
     * The database table used by the model.
     *
     * @var StringTool
     */
    protected $table = 'functionalities';
    /**
     * 资源名称
     * @var StringTool
     */
    public static $resourceName = 'Functionality';

    public static $sequencable = true;
    /**
     * the columns for list page
     * @var array
     */
    public static $columnForList = [
        'title',
        'description',
        'controller',
        'action',
        'realm',
        'refresh_cycle',
        'menu',
        'need_curd',
        'need_search',
        'need_log',
        'disabled',
        'sequence',
    ];

    /**
     * the main param for index page
     * @var StringTool
     */
    public static $mainParamColumn = 'parent_id';


    /**
     * 下拉列表框字段配置
     * @var array
     */

    public static $htmlSelectColumns = [
        'parent_id'        => 'functionalitiesTree',
        'realm'            => 'aValidRealms',
        'search_config_id' =>'aSearchConfigs',
        'button_type'      => 'aButtonTypes',
        'popup_id'         => 'aPopups',
    ];


    /**
     * If Tree Model
     * @var Bool
     */
    public static $treeable = true;
    /**
     * forefather id field
     * @var Bool
     */
    public static $foreFatherIDColumn = 'forefather_ids';
    public static $foreFatherColumn = 'forefathers';

    /**
     * The rules to be applied to the data.
     *
     * @var array
     */
    public static $rules = [
        'title'            => 'required|between:1,64|unique:functionalities,title,%s,id,parent_id,%s',
        'controller'       => 'required|alpha_dash|between:1,40',
        'search_config_id' => 'integer',
        'action'           => 'required|alpha_dash|between:1,40',
        'button_type'      => 'required|integer|in:1,2,3',
        'button_onclick'   => 'max:64',
        'popup_id'         => 'integer',
        'popup_title'      => 'max:64',
        'confirm_msg_key'  => 'max:200',
        'description'      => 'between:1,255',
        'refresh_cycle'    => 'integer',
        'menu'             => 'boolean',
        'need_curd'        => 'boolean',
        'need_search'      => 'boolean',
        'need_log'         => 'boolean',
        'disabled'         => 'boolean',
        'realm'            => 'required|in:0,1,2',
        'sequence'         => 'integer',
    ];

    const BUTTON_TYPE_NORMAL    = 1;
    const BUTTON_TYPE_EDIT      = 2;
    const BUTTON_TYPE_DANGEROUS = 3;
    public static $buttonTypes = [
        self::BUTTON_TYPE_NORMAL    => 'Normal',
        self::BUTTON_TYPE_EDIT      => 'Edit',
        self::BUTTON_TYPE_DANGEROUS => 'Dangerous',
    ];
    const REALM_SYSTEM = 0;
    const REALM_ADMIN  = 1;
    const REALM_USER   = 2;
    public static $realms = [
        self::REALM_SYSTEM => 'System',
        self::REALM_ADMIN  => 'Admin',
        self::REALM_USER   => 'User',
    ];
    public static $ignoreColumnsInView = [
        'parent_id'
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

    /**
     * 软删除
     * @var boolean
     */
    protected $softDelete = false;
    protected $fillable = [
        'id',
        'parent_id',
        'forefathers',
        'forefather_ids',
        'search_config_id',
        'title',
        'controller',
        'action',
        'button_type',
        'popup_id',
        'popup_title',
        'confirm_msg_key',
        'description',
        'refresh_cycle',
        'menu',
        'need_curd',
        'need_search',
        'realm',
        'need_log',
        'disabled',
        'sequence'
    ];

    public $autoPurgeRedundantAttributes = true;

    public function __construct(array $attributes = []) {
        parent::__construct($attributes);
    }

    public function functionality_relations()
    {
        return $this->hasMany('App\Models\Func\FunctionalityRelation');
    }

    public function menus()
    {
        return $this->hasMany('App\Models\Admin\Menu', 'functionality_id');
    }

    protected function afterCreate($oCreatedModel){
        AdminRole::deleteRightCache();
        return true;
    }

    /**
     * run after save
     * @param bool $bSucc
     * @return bool
     */
    protected function afterSave($oSavedModel){
        if (!parent::afterSave($oSavedModel)){
            return false;
        }
        $this->deleteCACache();
        AdminRole::deleteRightCache();
        $bSucc = true;
        if ($oMenus = Menu::where('functionality_id', '=', $this->id)->get()){
            foreach($oMenus as $oMenu){
                $oMenu->controller = $this->controller;
                $oMenu->action     = $this->action;
                $oMenu->realm      = $this->realm;
                if (!$bSucc = $oMenu->save()){
                    break;
                }
            }
        }
        if ($bSucc){
            if ($oRelations = FunctionalityRelation::where('r_functionality_id', '=', $this->id)->get()){
                $bSucc = true;
                foreach($oRelations as $oRelation){
                    $oRelation->realm = $this->realm;
                    if (!$bSucc = $oRelation->save()){
                        break;
                    }
                }
            }
        }
//            return parent::afterSave($oSavedModel);
        return $bSucc;
    }

    protected function afterDelete($oDeletedModel){
        AdminRole::deleteRightCache();
        return true;
    }

    public function getRelationFunctionalities($aFunctionalityIds = null, $aNeedRealm, & $aRelationIds){
        !empty($aRelationIds) or $aRelationIds = [];
        $aConditions = [
            'functionality_id' => ['=', $this->id],
            'realm'            => ['in', $aNeedRealm],
            'disabled'         => ['=',0]
        ];
        is_null($aFunctionalityIds) or $aConditions[ 'r_functionality_id' ] = ['in', $aFunctionalityIds];
        $oQuery     = FunctionalityRelation::doWhere($aConditions);
        $oQuery     = app()->make('App\Models\Func\FunctionalityRelation')->doOrderBy($oQuery);
        $oRelations = $oQuery->get();
        foreach ($oRelations as $oRelation){
            $aRelationIds[] = $oRelation->r_functionality_id;
        }
        return $oRelations;
    }

    protected function beforeValidate() {
        $this->refresh_cycle    or $this->refresh_cycle    = null;
        $this->search_config_id or $this->search_config_id = null;
        $this->popup_id         or $this->popup_id         = null;
        $this->need_search      or $this->need_search      = 0;
        return parent::beforeValidate();
    }

    public static function & getActionArray($aFunctionalityIds = null){
        $data = [];
        if (empty($aFunctionalityIds)){
            return $data;
        }
        $aColumns = ['id','title','controller','action','button_type','button_onclick','confirm_msg_key','popup_id','popup_title'];
        $oFunctionalities = static::whereIn('id', $aFunctionalityIds)->where('disabled', '=', 0)->orderBy('id', 'asc')->get($aColumns);
        foreach ($oFunctionalities as $oFunctionality){
            $data[ $oFunctionality->id ] = [
                'title'           => $oFunctionality->title,
                'controller'      => $oFunctionality->controller,
                'action'          => $oFunctionality->action,
                'button_type'     => $oFunctionality->button_type,
                'button_onclick'  => $oFunctionality->button_onclick,
                'confirm_msg_key' => $oFunctionality->confirm_msg_key,
                'popup_id'        => $oFunctionality->popup_id,
                'popup_title'     => $oFunctionality->popup_title,
            ];
        }
        return $data;
    }

    public static function getByCA($sController, $sAction, $aNeedReams = [self::REALM_ADMIN]) {
        if (static::$cacheLevel != self::CACHE_LEVEL_NONE) {
            $sCacheKey = static::getCacheKeyByCA($sController,$sAction);
            Cache::setDefaultDriver(static::$cacheDrivers[static::$cacheLevel]);
            if ($aAttributes = Cache::get($sCacheKey)){
                $obj = new static;
                $obj = $obj->newFromBuilder($aAttributes);
            } else {
                $obj = static::getByCAFromDb($sController,$sAction);
                if (is_object($obj)){
                    Cache::forever($sCacheKey, $obj->getAttributes());
                } else {
                    return false;
                }
            }
            if ($aNeedReams && !in_array($obj->realm, $aNeedReams)){
                unset($obj);
            }
        }
        if (!isset($obj)){
            $obj = static::getByCAFromDb($sController,$sAction,$aNeedReams);
        }
        return $obj;
    }

    private static function getByCAFromDb($sController, $sAction, $aNeedReams = false){
        $aCondtions = [
            'controller' => [ '=', $sController ],
            'action'     => [ '=', $sAction ],
        ];
        !$aNeedReams or $aCondtions['realm'] = [ 'in', $aNeedReams];
        return static::doWhere($aCondtions)->get()->first();
    }

    private static function getCacheKeyByCA($sController = null, $sAction = null){
        return static::getCachePrefix() .  $sController . '-' . $sAction;
    }

    public function deleteCACache(){
        if (static::$cacheLevel == self::CACHE_LEVEL_NONE) return true;
        $sCacheKey = $this->getCacheKeyByCA($this->controller, $this->action);
        Cache::setDefaultDriver(static::$cacheDrivers[static::$cacheLevel]);
        !Cache::has($sCacheKey) or Cache::forget($sCacheKey);
    }

    /**
     * 将用户类型的可能的全部功能信息数组放在$aTree
     * @param int $iRealm
     */
    public static function getTreeArray(& $aTree, $iParentId = null, $iRealm = null){
        static $deep = 0;

        $aColumns = ['id', 'title', 'parent_id', 'forefather_ids', 'description', 'controller', 'action', 'realm', 'disabled'];
        $oQuery = static::orderBy('sequence');
        $oQuery = is_null($iParentId) ? $oQuery->whereNull('parent_id') : $oQuery->where('parent_id' , '=', $iParentId);
        !$iRealm or $oQuery = $oQuery->where('realm','=',$iRealm);
        $oFunctionalities = $oQuery->get($aColumns);

        $deep++;
        foreach ($oFunctionalities as $oFunctionality){
            $aTree[ $oFunctionality->id ] = $oFunctionality->getAttributes();
            $aTree[ $oFunctionality->id]['level'] = $deep;
            static::getTreeArray($aTree,$oFunctionality->id, $iRealm);
        }
        $deep--;
    }
}