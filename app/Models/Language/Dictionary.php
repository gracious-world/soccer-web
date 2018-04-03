<?php
namespace App\Models\Language;

use App\Models\BaseModel;
/*
 * 字典模型类
 * 作用：管理语言包词汇
 */

class Dictionary extends BaseModel {


    protected $table                  = 'dictionaries';
    public static $resourceName       = 'Dictionary';
    public static $titleColumn        = 'name';
    public static $enabledBatchAction = true;
    public static $mainParamColumn    = 'name';

    public static $columnForList = [
        'name',
        'models',
        'en_column',
        'zh_column',
    ];

    public static $rules = [
        'name'      => 'required|max:64',
        'models'    => 'max:512',
        'en_column' => 'max:64',
        'zh_column' => 'max:64',
    ];

    protected $fillable = [
        'name',
        'models',
        'en_column',
        'zh_column',
    ];

    public static function getDictionaryByName($name) {
        return self::where('name', '=', $name)->first();
    }
}

