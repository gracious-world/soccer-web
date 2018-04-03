<?php

namespace App\Models\Basic;

use App\Models\BaseModel;

use Cache;

class GameType extends BaseModel {
    static $cacheLevel = self::CACHE_LEVEL_FIRST;
    protected static $cacheMinutes = 0;

    protected $table = 'game_types';
    public static $resourceName = 'GameType';

    public static $columnForList = [
        'id',
        'en_name',
        'cn_name',
        'status',
    ];
    public static $mainParamColumn = 'cn_name';

    public static $rules = [
        'en_name' => 'required|max:50|unique:game_types,en_name,',
        'cn_name' => 'max:50',
        'status'  => 'integer|in:0,1,2',
    ];
    protected $fillable = [
        'en_name',
        'cn_name',
        'status',
    ];

    public static $titleColumn = 'en_name';

    const STATUS_CLOSED  = 0;
    const STATUS_TESTING = 1;
    const STATUS_OPEN    = 2;
    public static $status = [
        self::STATUS_CLOSED  => 'Closed',
        self::STATUS_TESTING => 'Testing',
        self::STATUS_OPEN    => 'Open',
    ];

    public static $htmlSelectColumns = [
        'status' => 'aStatus',
    ];

    protected function afterSave($oSavedModel) {
        $this->deleteCache($this->id);
        $this->deleteCache($this->en_name);
        return true;
    }

    protected function afterDelete($oDeletedModel) {
        $this->deleteCache($this->id);
        $this->deleteCache($this->en_name);
        return true;
    }

    protected function afterUpdate() {
        $this->deleteCache($this->id);
        $this->deleteCache($this->en_name);
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

    public function scopeFilterStatus($query, $bNeedTest = false) {
        return $bNeedTest ? $query->where('status', '<>', self::STATUS_CLOSED) : $query->where('status', self::STATUS_OPEN);
    }

    public static function getAvailableGameType($en_name) {
        if (static::$cacheLevel == self::CACHE_LEVEL_NONE) {
            return self::getAvailableGameTypeFromDB($en_name);
        }
        Cache::setDefaultDriver(static::$cacheDrivers[static::$cacheLevel]);
        $key = static::generateCacheKey($en_name);
        if ($aAttributes = Cache::get($key)) {
            $obj = new static;
            $obj = $obj->newFromBuilder($aAttributes);
        } else {
            $obj = self::getAvailableGameTypeFromDB($en_name);
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
    public static function getAvailableGameTypeFromDB($en_name) {
        return self::filterStatus()->where('en_name', $en_name)->first();
    }
}
