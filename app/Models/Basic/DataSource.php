<?php

namespace App\Models\Basic;

use App\Models\BaseModel;

class DataSource extends BaseModel {
    static $cacheLevel = self::CACHE_LEVEL_NONE;
    protected static $cacheMinutes = 0;

    protected $table = 'data_sources';
    public static $resourceName = 'DataSource';

    public static $columnForList = [
        // 'gt_id',
        'game_type',
        'identity',
        'name',
        'odd_url',
        'result_url',
        'status',
        'remark',
    ];
    public static $mainParamColumn = 'identity';

    public static $rules = [
        'gt_id'        => 'integer',
        'game_type'    => 'max:50',
        'identity'     => 'required|max:50',
        'name'         => 'max:50',
        'odd_url'      => 'url|max:200',
        'result_url'   => 'url|max:200',
        'status'       => 'boolean',
        'remark'       => 'max:200',
    ];
    protected $fillable = [
        'gt_id',
        'game_type',
        'identity',
        'name',
        'odd_url',
        'result_url',
        'status',
        'remark',
    ];

    public static $htmlSelectColumns = [
        'gt_id' => 'aGameTypes',
        // 'status'=> 'aStatus',
    ];

}
