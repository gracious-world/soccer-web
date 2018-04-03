<?php

namespace App\Models\Func;

use App\Models\BaseModel;

class SysModelColumn extends BaseModel {
    protected $table = 'sys_model_columns';
    protected $softDelete = false;
    protected $fillable = [
        'name',
        'table_name',
    ];

    public static $resourceName = 'SysModelColumn';

    /**
     * the columns for list page
     * @var array
     */
    public static $columnForList = [
        'name',
        'sys_model_name',
        'table_name',
        'column_default',
        'is_nullable',
        'data_type',
        'max_length',
        'charset_name',
        'column_type',
        'column_comment',
        'note',
    ];

    /**
     * order by config
     * @var array
     */
    public $orderColumns = [
        'name' => 'asc'
    ];

    public static $treeable = false;

    /**
     * the main param for index page
     * @var StringTool
     */
    public static $mainParamColumn = 'sys_model_id';
    public static $titleColumn = 'name';

    public static $rules = [
//        'name'   => 'required|max:64',
//        'table_name'   => 'required|max:64',
    ];

}