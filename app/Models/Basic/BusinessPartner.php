<?php

namespace App\Models\Basic;

use App\Models\BaseModel;

use Cache;

class BusinessPartner extends BaseModel {
    static $cacheLevel = self::CACHE_LEVEL_FIRST;
    protected static $cacheMinutes = 60;

    const STATUS_ACTIVATED = 1;
    const STATUS_INACTIVATED = 0;

    protected $table = 'business_partners';
    public static $resourceName = 'BusinessPartner';

    public static $columnForList = [
        'identity',
        'name',
        'status',
        'status_bet',
        'is_tester',
        'is_simple',
        'taxable',
        'post_url',
        'template',
        'remark',
    ];
    public static $mainParamColumn = 'identity';

    public static $rules = [
        'identity'      => 'required|max:50|unique:business_partners,identity,',
        'name'          => 'max:50',
        'key'           => 'max:20|unique:business_partners,key,',
        'status'        => 'boolean',
        'status_bet'    => 'boolean',
        'is_tester'     => 'boolean',
        'is_mortgage'   => 'boolean',
        'is_simple'     => 'boolean',
        'taxable'       => 'boolean',
        'auto_transfer' => 'boolean',
        'post_url'      => 'url|max:200',
        'template'      => 'integer',
        'remark'        => 'max:200',
    ];
    protected $fillable = [
        'identity',
        'name',
        'key',
        'status',
        'status_bet',
        'wallet_id',
        'is_tester',
        'is_mortgage',
        'is_simple',
        'taxable',
        'auto_transfer',
        'sales_push_url',
        'post_url',
        'template',
        'remark',
    ];

    protected function afterSave($oSavedModel) {
        $this->deleteCache($this->id);
        $this->deleteCache($this->identity);
        $this->deleteCache('all-customer-id-name-array');
        return true;
    }

    protected function afterDelete($oDeletedModel) {
        $this->deleteCache($this->id);
        $this->deleteCache($this->identity);
        $this->deleteCache('all-customer-id-name-array');
        return true;
    }

    protected function afterUpdate() {
        $this->deleteCache($this->id);
        $this->deleteCache($this->identity);
        $this->deleteCache('all-customer-id-name-array');
        return true;
    }

    public static function deleteCache($sKeyData) {
        if (static::$cacheLevel == self::CACHE_LEVEL_NONE) {
            return true;
        }
        $key = static::generateCacheKey($sKeyData);
        Cache::setDefaultDriver(static::$cacheDrivers[static::$cacheLevel]);
        !Cache::has($key) or Cache::forget($key);
    }

    // public static $ignoreColumnsInEdit = ['key'];

    public function wallet() {
        return $this->hasOne('App\\Models\\Customer\\Wallet', 'bp_id');
    }

    public function scopeActived($oQuery) {
        return $oQuery->where('status', self::STATUS_ACTIVATED);
    }

    public function beforeValidate() {
        $this->key or $this->key = bin2hex(openssl_random_pseudo_bytes(6));
        return parent::beforeValidate();
    }

    public static function getActivateBusinessParnter($sIdentity) {
        if (static::$cacheLevel == self::CACHE_LEVEL_NONE) {
            return self::getActivateBusinessParnterFromDB($sIdentity);
        }
        Cache::setDefaultDriver(static::$cacheDrivers[static::$cacheLevel]);
        $key = static::generateCacheKey($sIdentity);
        if ($aAttributes = Cache::get($key)) {
            $obj = new static;
            $obj = $obj->newFromBuilder($aAttributes);
        } else {
            $obj = self::getActivateBusinessParnterFromDB($sIdentity);
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
        return $obj;
    }

    public static function getActivateBusinessPartnerIdNameArray() {
        if (static::$cacheLevel == self::CACHE_LEVEL_NONE) {
            return self::actived()->get()->pluck('name', 'id')->toArray();
        }
        Cache::setDefaultDriver(static::$cacheDrivers[static::$cacheLevel]);
        $key = static::generateCacheKey('all-customer-id-name-array');
        if (!$data = Cache::get($key)) {
            $obj = self::actived()->get();
            if (!is_object($obj)) {
                return false;
            }
            $data = $obj->pluck('name', 'id')->toArray();
            if (static::$cacheMinutes){
                Cache::put($key, $data, static::$cacheMinutes);
            } else {
                Cache::forever($key, $data);
            }
        }
        return $data;
    }

    public static function getActivateBusinessParnterFromDB($sIdentity) {
        return self::where('identity', $sIdentity)->where('status', BusinessPartner::STATUS_ACTIVATED)->first();
    }
}
