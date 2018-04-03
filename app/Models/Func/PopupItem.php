<?php

namespace App\Models\Func;

use App\Models\BaseModel;

/**
 * 弹出窗口输入项配置
 *
 * @author system
 */

class PopupItem extends BaseModel {

    protected $table = 'popup_items';

    protected static $cacheUseParentClass = false;

    protected static $cacheLevel = self::CACHE_LEVEL_FIRST;

    protected static $cacheMinutes = 0;

    const TYPE_NONE     = 0;
    const TYPE_TEXT     = 1;
    const TYPE_SELECT   = 2;
    const TYPE_TEXTAREA = 3;
    const TYPE_CHECKBOX = 4;

    public static $validTypes = [
        self::TYPE_NONE     => 'none',
        self::TYPE_TEXT     => 'text',
        self::TYPE_SELECT   => 'select',
        self::TYPE_TEXTAREA => 'textarea',
        self::TYPE_CHECKBOX => 'checkbox',
    ];
    protected $fillable = [
        'id',
        'popup_id',
        'field',
        'label',
        'type',
        'required',
        'min_length',
        'max_length',
        'sequence',
        'created_at',
        'updated_at',
    ];

    public static $sequencable = false;

    public static $enabledBatchAction = false;

    protected $validatorMessages = [];

    protected $isAdmin = true;

    public static $resourceName = 'PopupItem';

    protected $softDelete = false;

    protected $defaultColumns = [ '*' ];

    protected $hidden = [];

    protected $visible = [];

    public static $treeable = '';

    public static $foreFatherIDColumn = '';

    public static $foreFatherColumn = '';

    public static $columnForList = [
        'popup_id',
        'field',
        'label',
        'type',
        'required',
        'min_length',
        'max_length',
        'sequence',
    ];

    public static $totalColumns = [];

    public static $totalRateColumns = [];

    public static $weightFields = [];

    public static $classGradeFields = [];

    public static $floatDisplayFields = [];

    public static $noOrderByColumns = [];

    public static $ignoreColumnsInView =[
    ];

    public static $ignoreColumnsInEdit = [
        'id',
        'created_at',
        'updated_at',
    ];

    public static $listColumnMaps = [];

    public static $viewColumnMaps = [];

    public static $htmlSelectColumns = [
        'popup_id' => 'aPopups'
    ];

    public static $htmlTextAreaColumns = [];

    public static $htmlNumberColumns = [];

    public static $htmlOriginalNumberColumns = [];

    public static $amountAccuracy = 0;

    public static $originalColumns;

    public $orderColumns = [];

    public static $titleColumn = 'field';

    public static $mainParamColumn = 'popup_id';

    public static $rules = [
        'popup_id'   => 'required|integer',
        'field'      => 'required|max:32',
        'label'      => 'required|max:32',
        'type'       => 'required|max:16',
        'required'   => 'required|integer',
        'min_length' => 'min:0',
        'max_length' => 'min:0',
        'sequence'   => 'min:0',
    ];

    protected function beforeValidate() {
        $this->min_length > 0 or $this->min_length = null;
        $this->max_length > 0 or $this->max_length = null;
        return parent::beforeValidate();
    }

    public static function getValidTypes(){
        return static::_getArrayAttributes(__FUNCTION__);
    }

}