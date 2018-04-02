<?php

namespace App\Models\Func;

use App\Models\BaseModel;

/**
 * 操作确认窗口配置
 *
 * @author system
 */

class Popup extends BaseModel {

    protected $table = 'popups';

    protected static $cacheUseParentClass = false;

    protected static $cacheLevel = self::CACHE_LEVEL_FIRST;

    protected static $cacheMinutes = 0;

    protected $fillable = [
        'id',
        'name',
        'need_form',
        'method',
        'created_at',
        'updated_at',
    ];

    const METHOD_GET    = 1;
    const METHOD_POST   = 2;
    const METHOD_PUT    = 3;
    const METHOD_DELETE = 4;
    public static $validMethods = [
        self::METHOD_GET    => 'get',
        self::METHOD_POST   => 'post',
        self::METHOD_PUT    => 'put',
        self::METHOD_DELETE => 'delete',
    ];
    public static $sequencable = false;

    public static $enabledBatchAction = false;

    protected $validatorMessages = [];

    protected $isAdmin = true;

    public static $resourceName = 'Popup';

    protected $softDelete = false;

    protected $defaultColumns = [ '*' ];

    protected $hidden = [];

    protected $visible = [];

    public static $treeable = '';

    public static $foreFatherIDColumn = '';

    public static $foreFatherColumn = '';

    public static $columnForList = [
        'id',
        'name',
        'need_form',
        'created_at',
        'updated_at',
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
        'method' => 'aValidMethods',
    ];

    public static $htmlTextAreaColumns = [];

    public static $htmlNumberColumns = [];

    public static $htmlOriginalNumberColumns = [];

    public static $amountAccuracy = 0;

    public static $originalColumns;

    public $orderColumns = [];

    public static $titleColumn = 'name';

    public static $mainParamColumn = 'name';

    public static $rules = [
        'name'      => 'required|max:32',
        'need_form' => 'required|integer|boolean',
        'method'    => 'required|integer|in:1,2,3',
    ];

    protected function beforeValidate() {
        return parent::beforeValidate();
    }

    public static function getValidMethods(){
        return static::_getArrayAttributes(__FUNCTION__);
    }
}