<?php
namespace App\Models\Func;

use App\Models\BaseModel;
use App\Models\Admin\AdminUser;
use App\Models\Func\Functionality;

use Illuminate\Support\Str;

use Config;
use Cache;
use Route;

class FunctionalityRelation extends BaseModel {

    const POS_ITEM  = 1;
    const POS_PAGE  = 2;
    const POS_BATCH = 3;

    protected $table            = 'functionality_relations';
    public static $resourceName = 'FunctionalityRelation';
    public static $sequencable  = true;
    public static $ignoreColumnsInEdit = [
        'realm'
    ];

    /**
     * the columns for list page
     * @var array
     */
    public static $columnForList = [
        'functionality_id',
        'r_functionality_id',
        'label',
        'realm',
        'precondition',
        'params',
        'position',
//        'for_page',
//        'for_item',
//        'for_page_batch',
        'new_window',
        'use_redirector',
        'disabled',
        'sequence',
        'updated_at',
    ];

    /**
     * 可用的按钮位置数组
     * @var array
     */
    public static $validPositions = [
        self::POS_ITEM => 'for-item',
        self::POS_PAGE => 'for-page',
        self::POS_BATCH => 'for-batch'
    ];

    public static $listColumnMaps = [
        'position' => 'position_formatted'
    ];

    public static $viewColumnMaps = [
        'position' => 'position_formatted'
    ];

    /**
     * order by config
     * @var array
     */
    public $orderColumns = [
        'sequence' => 'asc'
    ];

    /**
     * If Tree Model
     * @var Bool
     */
    public static $treeable = false;
    /**
     * 下拉列表框字段配置
     * @var array
     */

    public static $htmlSelectColumns = [
        'functionality_id' => 'aFunctionalities',
        'r_functionality_id' => 'aFunctionalities',
        'position' => 'aValidPositions',
        'realm' => 'aValidRealms',
    ];

    /**
     * The rules to be applied to the data.
     *
     * @var array
     */
    public static $rules = [
        'functionality_id'      => 'integer',
        'r_functionality_id'    => 'integer',
        'realm'                 => 'integer',
        'precondition'          => 'max:200',
        'params'                => 'max:200',
        'label'                 => 'between:0,50',
        'position'              => 'required|in:1,2,3',
        'button_onclick'        => 'max:64',
        'confirm_msg_key'       => 'max:64',
        'for_page'              => 'in:0, 1',
        'for_item'              => 'in:0, 1',
        'for_page_batch'        => 'in:0, 1',
        'new_window'            => 'in:0, 1',
        'use_redirector'        => 'in:0, 1',
        'disabled'              => 'in:0, 1',
        'sequence'              => 'integer',
    ];

    /**
     * 软删除
     * @var boolean
     */
    protected $softDelete = false;
    protected $fillable = [
        'id',
        'functionality_id',
        'r_functionality_id',
        'realm',
        'position',
        'button_onclick',
        'confirm_msg_key',
        'for_page',
        'for_item',
        'for_page_batch',
        'label',
        'precondition',
        'params',
        'new_window',
        'use_redirector',
        'disabled',
        'sequence',
    ];

    /**
     * the main param for index page
     * @var StringTool
     */
    public static $mainParamColumn = 'functionality_id';

    public $autoPurgeRedundantAttributes = true;

    public function functionality_relations()
    {
        return $this->hasMany('FunctionalityRelation');
    }

    public function roles()
    {
        return $this->belongsToMany('AdminRole')->withTimestamps();
    }

    public function admin_menus()
    {
        return $this->hasMany('AdminMenu', 'functionality_id')->withTimestamps();
    }

    /**
     * Explode the rules into an array of rules.
     *
     * @param  StringTool|array  $rules
     * @return array
     */
    protected function explodeRules($rules)
    {
        foreach ($rules as $key => &$rule)
        {
            $rule = (is_string($rule)) ? explode('|', $rule) : $rule;
        }

        return $rules;
    }

    protected function beforeValidate(){
//        if (!$this->label){
        $oRightFunctionality = Functionality::find($this->r_functionality_id);
        $this->realm = $oRightFunctionality->realm;
        $this->label or $this->label = $oRightFunctionality->title;
//        }
        return parent::beforeValidate();
    }

    /**
     * 根据前置条件来判断是否显示
     * @param model $model
     * @return bool
     */
    public function isAvailable($model){
        if (!$this->precondition) return true;
        $this->precondition = str_replace('.', '->', $this->precondition);
        $function = '$valid = ' . $this->precondition . ';';
       // pr($function);exit;
        eval($function);
        return $valid;
    }

    public function compileItemButtonHref($model){
        if (!$this->isAvailable($model)){
            return false;
        }
        $sOnclick = '';
        $mParamsOfRoute = $this->button_type == Functionality::BUTTON_TYPE_NORMAL ? [$this->para_name => $model->id] : $model->id;
        $sHref = $this->route_name ? route($this->route_name, $mParamsOfRoute) : 'javascript:void(0);';
        if ($this->button_type == Functionality::BUTTON_TYPE_DANGEROUS){
            $sUrl = $sHref;
            $sHref = 'javascript:void(0)';
            $class = get_class($model);
            $titleColumn = $class::$titleColumn;
            $sOnclick = "javascript:{$this->button_onclick}('$sUrl', '{$model->{$titleColumn}}')";
        }
        return [
            'href' => $sHref,
            'onclick' => $sOnclick
        ];
    }

    public function compilePageButtonHref($aParams){
//        if (!$this->isAvailable($model)){
//            return false;
//        }
        $sOnclick = '';
        pr($aParams);
        pr($this->para_name);
        $this->url or $this->url = $this->para_name && isset($aParams[$this->para_name]) ? route($this->route_name, $aParams[$this->para_name]) : route($this->route_name);
        if ($this->button_type == Functionality::BUTTON_TYPE_DANGEROUS){
//            $class = get_class($model);
//            $titleColumn = $class::$titleColumn;
            $sOnclick = "javascript:{$this->button_onclick}('$this->url')";
        }
        return [
            'url' => $this->url,
            'onclick' => $sOnclick
        ];
    }

    protected function getPositionFormattedAttribute() {
        return __('_functionalityrelation.' . strtolower(Str::slug(static::$validPositions[$this->attributes['position']])));
    }

}
