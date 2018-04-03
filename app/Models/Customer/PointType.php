<?php
namespace App\Models\Customer;

use Illuminate\Support\Str;

use App\Models\BaseModel;
use App\Models\Fund\FundFlow;

use Cache;

# 商户荷包账变类型
class PointType extends BaseModel {

    const TYPE_LOAD         = 1;
    const TYPE_BET          = 2;
    const TYPE_DROP         = 3;
    const TYPE_WITHDRAW     = 4;
    const TYPE_AWARD_PRIZE  = 5;
    const TYPE_CANCEL_PRIZE = 6;

    protected static $cacheLevel = self::CACHE_LEVEL_FIRST;
    protected $table             = 'point_types';
    protected $softDelete        = false;
    protected $fillable          = [
        'id',
        'fund_flow_id',
        'name',
        'cn_name',
        'balance',
        'available',
        'frozen',
        'withdrawable',
        'prohibit_amount',
        'credit',
        'debit',
        'project_linked',
        'reverse_type',
    ];
    public static $resourceName = 'PointType';
    public static $titleColumn  = 'name';
    public static $ignoreColumnsInEdit = [
        'balance',
        'available',
        'frozen',
    ];

    public static $types = [
        self::TYPE_LOAD         => 'load',
        self::TYPE_BET          => 'bet',
        self::TYPE_DROP         => 'drop',
        self::TYPE_WITHDRAW     => 'withdraw',
        self::TYPE_AWARD_PRIZE  => 'award-prize',
        self::TYPE_CANCEL_PRIZE => 'cancel-prize',
    ];

    /**
     * the columns for list page
     * @var array
     */
    public static $columnForList       = [
        'id',
        'fund_flow_id',
        'name',
        'cn_name',
        'balance',
        'available',
        'frozen',
        'withdrawable',
        'prohibit_amount',
        'credit',
        'debit',
        'project_linked',
        'reverse_type',
    ];

    /**
     * 下拉列表框字段配置
     * @var array
     */
    public static $htmlSelectColumns = [
        'fund_flow_id' => 'aFundFlows',
        'balance'      => 'aFundActions',
        'available'    => 'aFundActions',
        'frozen'       => 'aFundActions',
        'reverse_type' => 'aPointTypes'
    ];

    public $orderColumns = [
        'credit' => 'desc'
    ];

    public static $treeable = false;

    /**
     * the main param for index page
     * @var StringTool
     */
    public static $mainParamColumn = 'name';
    public static $rules = [
        'name'            => 'required|max:30',
        'cn_name'         => 'required|max:30',
        'fund_flow_id'    => 'required|integer',
        'balance'         => 'required|integer',
        'available'       => 'required|integer',
        'frozen'          => 'required|integer',
        'withdrawable'    => 'required|integer',
        'prohibit_amount' => 'required|integer',
        'credit'          => 'boolean',
        'debit'           => 'boolean',
        'project_linked'  => 'boolean',
        'reverse_type'    => 'integer',
    ];

    protected function getFriendlyDescriptionAttribute() {
        return __('_pointtype.' . strtolower(Str::slug($this->attributes['description'])));
    }

    protected function beforeValidate() {
        if (!$this->fund_flow_id || !$oFundFlow = FundFlow::find($this->fund_flow_id)) {
            return false;
        }
        $this->balance   = $oFundFlow->balance;
        $this->available = $oFundFlow->available;
        $this->frozen    = $oFundFlow->frozen;
        $this->credit or $this->credit = 0;
        $this->debit or $this->debit   = 0;
        return parent::beforeValidate();
    }

    public static function getAllPointType() {
        $aColumns          = ['id', 'description', 'cn_name'];
        $aTransactionTypes = static::all($aColumns);
        return $aTransactionTypes;
    }

    private static function makeCacheKeyOfAll() {
        return 'basic-point-types';
    }

    public static function getAllPointTypeMap() {
        $bReadDb   = true;
        $bPutCache = false;
        if (static::$cacheLevel != self::CACHE_LEVEL_NONE) {
            Cache::setDefaultDriver(static::$cacheDrivers[static::$cacheLevel]);
            $sCacheKey = static::makeCacheKeyOfAll();
            if ($aAllTypes = Cache::get($sCacheKey)) {
                $bReadDb = false;
            } else {
                $bPutCache = true;
            }
        }
        if ($bReadDb) {
            $aAllTypes = static::getPointTypeArray();
        }

        if ($bPutCache) {
            Cache::forever($sCacheKey, $aAllTypes);
        }
        return $aAllTypes;
    }

    public static function getPointTypeArray() {
        $data              = [];
        $aTransactionTypes = static::getAllPointType();
        foreach ($aTransactionTypes as $oTransactionType) {
            $data[$oTransactionType->id] = $oTransactionType->toArray();
        }
        return $data;
    }



}
