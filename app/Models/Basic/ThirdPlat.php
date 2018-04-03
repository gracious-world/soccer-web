<?php
namespace App\Models\Basic;
use App\Models\BaseModel;

class ThirdPlat extends BaseModel {

    const ENABLED               = 0;

    protected $table      = 'third_plats';
    protected $softDelete = false;
    public $timestamps    = false; // 取消自动维护新增/编辑时间
    protected $fillable   = [
        'id',
        'identity',
        'name',
        'plat_identity',
        'key',
        'iframe_url',
        'data_url',
    ];
    public static $resourceName  = 'ThirdPlat';
//    public static $treeable = true;
//    public static $sequencable = true;
//    public static $mainParamColumn = 'parent_id';
    public static $titleColumn   = 'identity';

    public static $columnForList = [
        'id',
        'identity',
        'name',
        'plat_identity',
        'key',
        'iframe_url',
        'data_url',
    ];
    public static $rules = [
        'identity'      => 'required|max:50',
        'name'          => 'required|max:50',
        'plat_identity' => 'max:50',
        'key'           => 'max:32',
        'iframe_url'    => 'max:200',
        'data_url'      => 'max:200',
    ];

    public static function getThirdPlatBySeriesId($iSeriesId) {
        $oSeries = Series::find($iSeriesId);
        return static::find($oSeries->plat_id);
    }

}
