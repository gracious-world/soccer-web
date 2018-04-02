<?php

namespace App\Models\Basic;

use App\Models\BaseModel;

class AuditType extends BaseModel {
    /**
     * The database table used by the model.
     *
     * @var StringTool
     */
    protected $table = 'audit_types';

    /**
     * 资源名称
     * @var StringTool
     */
    public static $resourceName = 'AuditType';

    public static $columnForList = [
        'name',
        'controller',
        'action',
        'description',
        'sequence',
    ];

    protected $fillable = [
        'name',
        'controller',
        'action',
        'description',
        'sequence',
    ];
    public static $rules = [
        'name'        => 'required|between:6,50',
        'controller'  => 'required|between:0,40',
        'action'      => 'required|between:0,40',
        'description' => 'between:0,255',
        'sequence'    => 'integer',
    ];
    public $orderColumns = [
        'updated_at' => 'asc'
    ];

    public static function getAuditTypes ()
    {
        $aAuditTypes = [];
        $aAllTypes = self::all(['id', 'name']);
        foreach ($aAllTypes as $key => $value) {
            $aAuditTypes[$value->id] = $value->name;
        }
        return $aAuditTypes;
    }

}