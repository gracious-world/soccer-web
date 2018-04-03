<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use LaravelArdent\Ardent\Ardent;
use Illuminate\Support\Str;
use Config;
use Cache;
use Schema;
use DB;
/**
* 自定义基类
*/
class BaseModel extends Ardent
{
    // use SoftDeletes;
    // No Cache
    const CACHE_LEVEL_NONE                = 0;
    // Level 1 cache
    const CACHE_LEVEL_FIRST               = 1;
    // Level 2 cache
    const CACHE_LEVEL_SECOND              = 2;
    // Level 3 cache
    const CACHE_LEVEL_THIRD               = 3;
    // 默认缓存级别
    protected static $cacheLevel          = self::CACHE_LEVEL_NONE;
    // 缓存有效时间，默认0为永久有效
    protected static $cacheMinutes        = 0;

    protected static $cacheUseParentClass = false;

    // 缓存驱动
    protected static $cacheDrivers = [
        self::CACHE_LEVEL_FIRST  => 'memcached',
        self::CACHE_LEVEL_SECOND => 'redis',
        self::CACHE_LEVEL_THIRD  => 'mongo'
    ];
    // 缓存级别描述
    public static $validCacheLevels = [
        self::CACHE_LEVEL_NONE   => 'None',
        self::CACHE_LEVEL_FIRST  => 'First',
        self::CACHE_LEVEL_SECOND => 'Second',
        self::CACHE_LEVEL_THIRD  => 'Third'
    ];

    /**
     * 默认语言包
     * @var StringTool
     */
    public static $defaultLangPack;

    /**
     * 是否启用手动排序
     * @var true
     */
    public static $sequencable = false;

    /**
     * 手动排序表字段名称
     * @var StringTool
     */
    public static $sequenceColumn = 'sequence';

    /**
     * 是否支持批量处理
     * @var boolean
     */
    public static $enabledBatchAction = false;

    /**
     * 自定义验证消息
     * @var array
     */
    protected $validatorMessages = [];

    /**
     * 资源名称
     * @var StringTool
     */
    public static $resourceName = '';

    /**
     * 是否支持软删除
     * @var boolean
     */
    protected $softDelete = false;
    // protected $dates = ['delete_at'];

    /**
     * 建立实例时获取的字段数组
     * @var array
     */
    protected $defaultColumns = [ '*'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $visible = [];

    /**
     * 模型是否树形结构
     * @var Bool
     */
    public static $treeable = false;

    /**
     * 标志一个树形模型结点是否发生了移动
     * @var bool
     */
    public $treeNodeMoved = false;

    /**
     * 树形模型祖先id字段
     * @var Bool
     */
    public static $foreFatherIDColumn = '';

    /**
     * 树形模型祖先字段
     * @var Bool
     */
    public static $foreFatherColumn = '';

    /**
     * 列表页面可展示的模型字段数组
     * @var array
     */
    public static $columnForList = [];

    /**
     * 需要显示页面小计的字段数组
     * @var array
     */
    public static $totalColumns = [];

    /**
     * 总计数据中的比例列字段数组
     * @var array
     */
    public static $totalRateColumns = [];

    /**
     * 加粗显示的字段数组
     * @var array
     */
    public static $weightFields = [];

    /**
     * 显示为不同颜色的字段数组
     * @var array
     */
    public static $classGradeFields = [];
    public static $floatDisplayFields = [];

    /**
     * 不显示orderby按钮的字段数组，供列表页使用
     * @var array
     */
    public static $noOrderByColumns = [];

    /**
     * 详情页面中需要忽略的字段数组
     * @var array
     */
    public static $ignoreColumnsInView = [];

    /**
     * 编辑页面中需要忽略的字段数组
     * @var array
     */
    public static $ignoreColumnsInEdit = [];

    /**
     * 编辑页面中欧诺个显示为只读的字段数组
     * @var array
     */
    public static $readonlyColumnsInEdit = [];
    /**
     * index视图显示时使用，用于某些列有特定格式，且定义了虚拟列的情况
     * @var array
     */
    public static $listColumnMaps = [];

    /**
     * view视图显示时使用，用于某些列有特定格式，且定义了虚拟列的情况
     * @var array
     */
    public static $viewColumnMaps = [];

    /**
     * 下拉列表框字段配置
     * @var array
     */
    public static $htmlSelectColumns = [];

    /**
     * 编辑框字段配置
     * @var array
     */
    public static $htmlTextAreaColumns = [];

    /**
     * number字段数组
     * @var array
     */
    public static $htmlNumberColumns = [];
    /**
     * 显示原始数字的字段数组
     * @var array
     */
    public static $htmlOriginalNumberColumns = [];

    /**
     * 金额字段的存储精度
     * @var int
     */
    public static $amountAccuracy = 0;

    /**
     * Columns
     * @var array
     */
    public static $originalColumns;

    /**
     * 显示为链接的字段配置
     * 键为文本列，值为URL列
     * @var array
     */
    public static $linkColumns = [];
    /**
     * Column Settings
     * @var array
     */
    public $columnSettings = [];

    /**
     * 排序字段数组
     * @var array
     */
    public $orderColumns = [];

    /**
     * 标题字段
     * @var StringTool
     */
    public static $titleColumn = 'title';

    /**
     * 列表页面的主要查询参数字段
     * @var StringTool
     */
    public static $mainParamColumn = 'parent_id';

    /**
     * 字段类型数组
     * @var array
     */
    public $columnTypes = [];
    function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->comaileLangPack();
    }
    /**
     * 访问器：友好的创建时间
     * @return StringTool
     */
    protected function getFriendlyCreatedAtAttribute() {
        return friendly_date($this->created_at);
    }

    /**
     * 访问器：友好的更新时间
     * @return StringTool
     */
    protected function getFriendlyUpdatedAtAttribute() {
        return friendly_date($this->updated_at);
    }

    /**
     * 访问器：友好的删除时间
     * @return StringTool
     */
    protected function getFriendlyDeletedAtAttribute() {
        return friendly_date($this->deleted_at);
    }

    protected function getDeletedAtColumn() {
        return $this->deleted_at;
    }

    public function scopeComplexWhere($oQuery, $aConditions) {
        if (!is_array($aConditions)) {
            return $oQuery;
        }
        $oQuery = $oQuery->where(($this->primaryKey ? $this->primaryKey : 'id'), '>', 0);
        foreach ($aConditions as $sColumn => $aCondition) {
            if (!is_array($aCondition)) {
                $aCondition = ['=', $aCondition];
            } else if (count($aCondition) == 1) {
                $aCondition = ['=', $aCondition[0]];
            }
            switch ($aCondition[0]) {
                case '=':
                    $oQuery = is_null($aCondition[1]) ? $oQuery->whereNull($sColumn) : $oQuery->where($sColumn, '=', $aCondition[1]);
                    break;
                case 'in':
                    if (!$aCondition[1]) continue;
                    $aInData = is_array($aCondition[1]) ? $aCondition[1] : explode(',', $aCondition[1]);
                    if (is_array($aInData)) {
                        $oQuery = $oQuery->whereIn($sColumn, $aInData);
                    }
                    break;
                case 'not in':
                    if (!$aCondition[1]) continue;
                    $aNotInData = is_array($aCondition[1]) ? $aCondition[1] : explode(',', $aCondition[1]);
                    if (is_array($aNotInData)) {
                        $oQuery = $oQuery->whereNotIn($sColumn, $aNotInData);
                    }
                    break;
                case '>=':
                case '<=':
                case '<':
                case '>':
                case 'like':
                case '<>':
                case '!=':
                    $oQuery = is_null($aCondition[1]) ? $oQuery->whereNotNull($sColumn) : $oQuery->where($sColumn,$aCondition[0],$aCondition[1]);
                    break;
                case 'between':
                    $aBetweenData = is_array($aCondition[1]) ? $aCondition[1] : explode(',', $aCondition[1]);
                    if (is_array($aBetweenData) && count($aBetweenData) == 2) {
                        $oQuery = $oQuery->whereBetween($sColumn, $aBetweenData);
                    }
                    break;
            }
        }
        return $oQuery;
    }
    /**
     * [find 覆盖find方法，加入缓存机制]
     * @param  int    $id      [记录id]
     * @param  array  $columns [要获取的字段]
     * @return object          [相应的数据对象]
     */
    public static function find($id, $columns = ['*']) {
        if (static::$cacheLevel == self::CACHE_LEVEL_NONE) {
            return parent::find($id, $columns);
        }
        Cache::setDefaultDriver(static::$cacheDrivers[static::$cacheLevel]);
        $key = static::generateCacheKey($id);
        if ($aAttributes = Cache::get($key)) {
            $obj = new static;
            $obj = $obj->newFromBuilder($aAttributes);
        } else {
            $obj = parent::find($id);
            if (!is_object($obj)) {
                return false;
            }
            $data = $obj->getAttributes();
            if (static::$cacheMinutes){
                Cache::put($key, $data, static::$cacheMinutes);
            } else {
                Cache::forever($key, $data);
            }
        }
        // 移除不需要的属性
        if (is_array($columns) && !empty($columns) && !in_array('*', $columns)) {
            $aAllColumns = array_keys($obj->attributes);
            $aExpertColumns = array_diff($aAllColumns, $columns);
            foreach ($aExpertColumns as $sColumn) {
                unset($obj->attributes[$sColumn]);
            }
        }
        return $obj;
    }
    /**
     * [deleteCache 删除缓存]
     * @param  StringTool $sCacheKey [缓存key]
     * @return boolean           [删除成功/失败]
     */
    public static function deleteCache($sCacheKey) {
        if (static::$cacheLevel == self::CACHE_LEVEL_NONE) {
            return true;
        }
        $key = static::generateCacheKey($sCacheKey);
        Cache::setDefaultDriver(static::$cacheDrivers[static::$cacheLevel]);
        !Cache::has($key) or Cache::forget($key);
    }
    /**
     * [generateCacheKey 生成真正的缓存key]
     * @param  StringTool $data [缓存key]
     * @return StringTool       [加前缀的缓存key]
     */
    protected static function generateCacheKey($data) {
        return static::getCachePrefix() . $data;
    }
    // 生成非模型的缓存key
    // public static function generateComplexDataCacheKey($key) {
    //     return Config::get('cache.prefix') . $key;
    // }
    /**
     * [getCachePrefix 获取缓存key的前缀]
     * @param  boolean $bPlural [是否复数形式]
     * @return StringTool           [缓存前缀]
     */
    protected static function getCachePrefix($bPlural = false) {
        $sClass = static::getRealClassForCache();
        !$bPlural or $sClass = Str::plural($sClass);
        return Config::get('cache.prefix') . $sClass . '-';
    }
    /**
     * [makeColumnConfigures 根据表结构及各项属性设置生成数据表配置数组，为内部调用]
     * @param  boolean $bForEdit [是否用于编辑事件]
     * @return void              [No Response]
     */
    public function makeColumnConfigures($bForEdit = true) {
        static::$originalColumns = Schema::getColumnListing($this->table);
        $this->columnTypes = $this->getColumnTypes();
        $rules = $this->explodeRules(static::$rules);
        $aColumnRules = [];

        // 处理在编辑表单中忽略的字段信息
        if ($bForEdit) {
            $aIgnoreColumns = [
                $this->primaryKey,
                $this->getCreatedAtColumn(),
                $this->getUpdatedAtColumn(),
                $this->getDeletedAtColumn(),
            ];
            $aIgnoreColumns = array_merge(static::$ignoreColumnsInEdit, $aIgnoreColumns);
        } else {
            $aIgnoreColumns = static::$ignoreColumnsInView;
        }
        if (static::$treeable) {
            $bForEdit or $aIgnoreColumns[] = 'parent_id';
            if (static::$foreFatherIDColumn) {
                $aIgnoreColumns[] = static::$foreFatherIDColumn;
                $aIgnoreColumns[] = static::$foreFatherColumn;
            }
        }
        $aIgnoreColumns = array_unique($aIgnoreColumns);
        foreach (static::$originalColumns as $sColumn) {
            if (in_array($sColumn, $aIgnoreColumns)) {
                continue;
            }
            $bDone = false;

            $aColumnRules[$sColumn]['readonly'] = in_array($sColumn, static::$readonlyColumnsInEdit);
            if (isset(static::$htmlSelectColumns[$sColumn])) {
                $bDone = true;
                $aColumnRules[$sColumn]['type'] = 'select';
                $aColumnRules[$sColumn]['form_type'] = 'select';
                $aColumnRules[$sColumn]['options'] = static::$htmlSelectColumns[$sColumn];
                continue;
            }
            if (in_array($sColumn, static::$htmlTextAreaColumns)) {
                $bDone = true;
                $aColumnRules[$sColumn]['type'] = 'text';
                $aColumnRules[$sColumn]['form_type'] = 'textarea';
                continue;
            }
            if (isset($rules[$sColumn])) {
                $bDone = true;
                $sFormType = 'text';
                $bRequired = false;
                foreach ($rules[$sColumn] as $sRule) {
                    $a = explode(':', $sRule);
                    switch ($a[0]) {
                        case 'required':
                            $bRequired = true;
                            $sType = 'text';
                            break;
                        case 'in':
                            if (str_replace(' ', '', $a[1]) == '0,1') {
                                $sType = 'bool';
                                $sFormType = 'bool';
                            } else {
                                $sFormType = 'select';
                                $sType = 'select';
                            }
                            break;
                        case 'between':
                            $sFormType = 'text';
                            $sType = 'String';
                            break;
                        case 'numeric':
                        case 'integer':
                            $sFormType = 'text';
                            $sType = $a[0];
                            break;
                        case 'min';
                        case 'max':
                            if (!isset($aColumnRules[$sColumn]['type'])) {
                                $sFormType = 'text';
                                $sType = 'String';
                            }
                            break;
                        case 'date':
                            $sFormType = 'date';
                            $sType = 'text';
                            break;
                        case 'boolean':
                            $sType = 'bool';
                            $sFormType = 'bool';
                            break;
                        default:
                            $sFormType = 'text';
                            $sType = 'String';
                    }
                    $aColumnRules[$sColumn]['required'] = $bRequired;
                    $aColumnRules[$sColumn]['type'] = $sType;
                    $aColumnRules[$sColumn]['form_type'] = $sFormType;
                }
            }
            if (!$bDone) {
                $aColumnRules[$sColumn]['form_type'] = 'ignore';
                $aColumnRules[$sColumn]['type'] = 'text';
            }
        }
        $this->columnSettings = $aColumnRules;
    }
    /**
     * [getColumnTypes 获取模型对应的表的字段类型]
     * @return array [模型对应的表的字段类型]
     */
    public function & getColumnTypes() {
        if (empty($this->columnTypes)) {
            $sDatabase = $this->getConnection()->getConfig('database');
            $sql = "select column_name, data_type from information_schema.columns where table_schema = '$sDatabase' and table_name = '{$this->table}' order by ordinal_position;";
            $aColumns = DB::select($sql);
            $data = [];
            foreach ($aColumns as $aConfig) {
                $data[$aConfig->column_name] = $aConfig->data_type;
            }
            $this->columnTypes = $data;
            return $data;
        } else {
            return $this->columnTypes;
        }
    }

    /**
     * get tree array
     * @staticvar int   $deep
     * @param array     $aTree           to save the array
     * @param int       $iParentId       parent_id
     * @param StringTool    $sTitlePrev      the prefix for sub title
     * @return void
     */
    public static function getTree(& $aTree, $iParentId = null, $aConditions = [], $aOrderBy = [], $sTitlePrev = '--') {
        if (!static::$treeable)
            return false;

        static $deep = 0;

        $aConditions['parent_id'] = ['=', $iParentId];

        $obj = new static;
        $oQuery = $obj->doWhere($aConditions);
        $oQuery = $obj->doOrderBy($oQuery, $aOrderBy);

        $deep++;

        $aModels = $oQuery->get([ 'id', static::$titleColumn]);
        foreach ($aModels as $oModel) {
            $sTitle = empty($sTitlePrev) ? $oModel->{static::$titleColumn} : str_repeat($sTitlePrev, ($deep - 1)) . $oModel->{static::$titleColumn};
            $aTree[$oModel->id] = $sTitle;
            $obj->getTree($aTree, $oModel->id, $aConditions, $aOrderBy, $sTitlePrev);
        }
        $deep--;
    }
    /**
     * [doWhere 构造条件查询]
     * @param  array  $aConditions [条件数组]
     * @return builder             [查询构造器对象]
     */
    public static function doWhere($aConditions = []) {
        is_array($aConditions) or $aConditions = [];
        $oQuery = static::where('id', '>', 0);
        foreach ($aConditions as $sColumn => $aCondition) {
            switch ($aCondition[0]) {
                case '=':
                    $oQuery = is_null($aCondition[1]) ? $oQuery->whereNull($sColumn) : $oQuery->where($sColumn, '=', $aCondition[1]);
                    break;
                case 'in':
                    $aInData = is_array($aCondition[1]) ? $aCondition[1] : explode(',', $aCondition[1]);
                    $oQuery = $oQuery->whereIn($sColumn, $aInData);
                    break;
                case '>=':
                case '<=':
                case '<':
                case '>':
                case 'like':
                case '<>':
                case '!=':
                    $oQuery = is_null($aCondition[1]) ? $oQuery->whereNotNull($sColumn) : $oQuery->where($sColumn,$aCondition[0],$aCondition[1]);
                    break;
                case 'between':
                    $aBetweenData = is_array($aCondition[1]) ? $aCondition[1] : explode(',', $aCondition[1]);
                    $oQuery = $oQuery->whereBetween($sColumn, $aBetweenData);
                    break;
            }
        }
        return $oQuery;
    }

    /**
     * [doOrderBy 构造排序查询]
     * @param  builder     $oQuery   [查询对象]
     * @param  array       $aGroupBy [排序字段]
     * @return Query|Model           [查询构造器对象/数据模型]
     */
    public function doOrderBy($oQuery = null, $aOrderBy = []) {
        $aOrderBy or $aOrderBy = $this->orderColumns;
        $oQuery or $oQuery = $this;
        foreach ($aOrderBy as $sColumn => $sDirection) {
            $oQuery = $oQuery->orderBy($sColumn, $sDirection);
        }
        return isset($oQuery) ? $oQuery : $this;
    }
    /**
     * [doGroupBy 构造分组查询]
     * @param  builder     $oQuery   [查询对象]
     * @param  array       $aGroupBy [分组字段]
     * @return Query|Model           [查询构造器对象/数据模型]
     */
    public function doGroupBy($oQuery = null, $aGroupBy = []) {
        empty($aGroupBy) or $aGroupBy = $this->groupByColumns;
        $oQuery or $oQuery = $this;
        foreach ($aGroupBy as $sColumn) {
            $oQuery = $oQuery->groupBy($sColumn);
        }
        return isset($oQuery) ? $oQuery : $this;
    }

    protected function setParentIdAttribute($iParentId) {
        $this->attributes['parent_id'] = $iParentId;
        $sModelName = get_class($this);
        if ($iParentId) {
            $oParent = $sModelName::find($this->parent_id);
            $this->parent = $oParent->{static::$titleColumn};
        } else {
            $this->parent = '';
        }
        if (static::$foreFatherIDColumn) {
            $this->setForeFather();
        }
    }

    public function setForeFather() {
        if (!static::$treeable) {
            return false;
        }
        $sColumn = static::$foreFatherIDColumn;
        $oParentModel = $this->find($this->parent_id);
        $this->$sColumn = empty($oParentModel->$sColumn) ? $this->parent_id : ($oParentModel->$sColumn . ',' . $this->parent_id);
        if ($this->$sColumn) {
            if ($this->parent_id) {
                $oParentModel = $this->find($this->parent_id);
                if ($sForeColumn = static::$foreFatherColumn) {
                    $this->$sForeColumn = empty($oParentModel->$sForeColumn) ? $oParentModel->{static::$titleColumn} : ($oParentModel->$sForeColumn . ',' . $oParentModel->{static::$titleColumn});
                }
            }
        } else {
            $this->attributes[static::$foreFatherIDColumn] = '';
            if ($sForeColumn = static::$foreFatherColumn) {
                $this->attributes[$sForeColumn] = '';
            }
        }
    }

    protected function beforeValidate() {
        if (static::$treeable) {
            if($this->treeNodeMoved = $this->isDirty('parent_id')){
                $this->parent_id = $this->parent_id;
            }
        }
        return true;
    }

    protected function afterSave($oSavedModel) {
        $sModelName = get_class($oSavedModel);
        $this->deleteCache($this->id);
        $bSucc = true;
        if ($sModelName::$treeable && $oSavedModel->treeNodeMoved) {
            $aSubs = & $oSavedModel->getSubObjectArray($this->id);
            if ($aSubs) {
                foreach ($aSubs as $oModel) {
                    $oModel->parent_id = $this->id;
                    if (!$bSucc = $oModel->save()) {
                        break;
                    }
                }
            }
        }
        return $bSucc;
    }

    protected function afterDelete($oDeletedModel) {
        $this->deleteCache($oDeletedModel->id);
        return true;
    }

    protected function afterUpdate() {
        $this->deleteCache($this->id);
    }
    /**
     * [getSubObjectArray description]
     * @param  int    $iParentId   [父id]
     * @param  array  $aConditions [条件数组]
     * @param  array  $aOrderBy    [排序数组]
     * @return array               [id => 数据对象 的数组]
     */
    public function & getSubObjectArray($iParentId = null, $aConditions = [], $aOrderBy = []) {
        if (!static::$treeable) return false;

        $data = [];
        !empty($aConditions) or $aConditions = [];
        $aConditions['parent_id'] = [ '=', $iParentId];
        $oQuery = $this->doWhere($aConditions);
        $oQuery = $this->doOrderBy($oQuery, $aOrderBy);
        $oModels = $oQuery->get();
        foreach ($oModels as $oModel) {
            $data[$oModel->id] = $oModel;
        }
        return $data;
    }
    /**
     * [getValueListArray     获取名值数组]
     *
     * @param StringTool $sColumn         [要获取值的字段]
     * @param array  $aConditions     [查询条件]
     * @param array  $aOrderBy        [排序条件]
     * @param bool   $bUsePrimaryKey  [是否使用主键值做数组的key]
     * @return array                  [符合条件的键值对数组]
     */
    function getValueListArray($sColumn = null, $aConditions = [], $aOrderBy = [], $bUsePrimaryKey = false) {
        $sColumn or $sColumn = static::$titleColumn;
        $aColumns = $bUsePrimaryKey ? [ 'id', $sColumn] : [ $sColumn];
        $aOrderBy or $aOrderBy = [ $sColumn => 'asc'];
        $oQuery = $this->doWhere($aConditions);
        $oQuery = $this->doOrderBy($oQuery, $aOrderBy);
        $oModels = $oQuery->get($aColumns);
        $data = [];
        foreach ($oModels as $oModel) {
            $sKeyField = $bUsePrimaryKey ? $oModel->id : $oModel->$sColumn;
            $data[$sKeyField] = $oModel->$sColumn;
        }
        return $data;
    }

    public static function & getTitleList($bOrderByTitle = true) {
        $aColumns = [ 'id', static::$titleColumn];
        $sOrderColumn = $bOrderByTitle ? static::$titleColumn : 'id';
        $oModels = static::orderBy($sOrderColumn, 'asc')->get($aColumns);
        $data = [];
        foreach ($oModels as $oModel) {
            $data[$oModel->id] = $oModel->{static::$titleColumn};
        }
        return $data;
    }

    // public static function getTitleListFromCollections() {
    //     $data = [];
    //     foreach ($oRecords as $data) {
    //         # code...
    //     }
    // }

    public static function getObjectByParams(array $aParams = ['*']) {
        return static::getObjectCollectionByParams($aParams)->first();
    }

    public static function getObjectCollectionByParams(array $aParams = ['*']) {
        foreach ($aParams as $key => $value) {
            if (isset($oQuery) && is_object($oQuery)) {
                $oQuery = $oQuery->where($key, '=', $value);
            } else {
                $oQuery = static::where($key, '=', $value);
            }
        }
        return $oQuery->get();
    }

    /**
     * [getValidationErrorString 拼装错误信息数组为字符串]
     * @return StringTool [错误信息字符串]
     */
    public function & getValidationErrorString() {
        $aErrMsg = $this->exists ? [ $this->id . ':'] : [ $this->{static::$titleColumn} . ':'];
        foreach ($this->validationErrors->toArray() as $sColumn => $sMsg) {
            $aErrMsg[] = $sColumn . ': ' . implode(',', $sMsg);
        }
        $sError = implode(' ', $aErrMsg);
        return $sError;
    }
    /**
     * [getFormattedNumberForHtml 获取格式化后的数字，用于金额显示]
     * @param  StringTool  $sColumn   [要格式化的字段]
     * @param  boolean $bTruncate [是否去除多余小数]
     * @return int                [格式化后的数字]
     */
    protected function getFormattedNumberForHtml($sColumn, $bTruncate = false) {
        $iAccuracy = isset(static::$htmlNumberColumns[$sColumn]) ? static::$htmlNumberColumns[$sColumn] : static::$amountAccuracy;
        $fNumber = $this->{ $sColumn };
        if ($bTruncate){
            $iBaseNumber = pow(10,$iAccuracy);
            return number_format(intval($fNumber * $iBaseNumber) / $iBaseNumber, $iAccuracy);
        }
        else{
            return number_format($this->{ $sColumn }, $iAccuracy);
        }
    }

    /**
     * Explode the rules into an array of rules.
     *
     * @param  StringTool|array  $rules
     * @return array
     */
    protected function explodeRules($rules) {
        foreach ($rules as $key => &$rule) {
            $rule = (is_string($rule)) ? explode('|', $rule) : $rule;
        }
        return $rules;
    }

    protected static function _getArrayAttributes($sFuncName){
        $sName = Str::camel(substr($sFuncName,3));
        $data = static::$$sName;
        static::translateArray($data);
        return $data;
    }

    public static function comaileLangPack() {
        $sClass = static::getRealClassForCache();
        return static::$defaultLangPack = '_' . strtolower($sClass);
    }

    protected static function getRealClassForCache() {
        $sClass = get_called_class();
        !static::$cacheUseParentClass or $sClass = get_parent_class($sClass);
        $sShortClass = substr($sClass, strrpos($sClass, '\\') - strlen($sClass) + 1);
        return $sShortClass;
    }

    public static function translate($sText, $iUcType = 3, $aReplace = []) {
        return __(static::$defaultLangPack . '.' . strtolower($sText), $aReplace, $iUcType);
    }

    public static function translateArray(& $aTexts, $iUcType = 2, $aReplace = []) {
        static::comaileLangPack();
        foreach ($aTexts as $key => $sText) {
            $aTexts[$key] = __(static::$defaultLangPack . '.' . strtolower($sText), $aReplace, $iUcType);
        }
    }

    protected function strictUpdate($aConditions, $data) {
        if ($bSucc = $this->doWhere($aConditions)->update($data) > 0) {
            $this->afterUpdate();
        }
        return $bSucc;
    }


    /**
     * 返回excel文件到浏览器端
     * @param array $aTitles        excel表头信息
     * @param array $aData           excel数据
     * @param StringTool $sFileName  excel文件名称
     * @return excel内容或者跳转到index页面
     */
    // public function downloadExcel($aTitles, $aData, $sFileName, $sDir='./') {
    //     if (count($aData) <= 0) {
    //         return false;
    //     }
    //     PHPExcel_Settings::setCacheStorageMethod(PHPExcel_CachedObjectStorageFactory::cache_in_memory_serialized);
    //     $oDownExcel = new DownExcel;
    //     $sModelName = get_class($this);
    //     $sModelName = starts_with($sModelName, 'Man') ? substr($sModelName, 3) : $sModelName;
    //     $oDownExcel->setTitle(strtolower($sModelName), $aTitles);
    //     $oDownExcel->setData($aData);
    //     $oDownExcel->setActiveSheetIndex(0);
    //     $oDownExcel->setSheetTitle($sFileName);
    //     $oDownExcel->setEncoding('gb2312');
    //     return $oDownExcel->Download($sDir . $sFileName, 'Excel2007', 'Command');
    // }
}