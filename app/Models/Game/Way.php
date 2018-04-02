<?php

namespace App\Models\Game;

use App\Models\BaseModel;
use Cache;

class Way extends BaseModel {
    static $cacheLevel = self::CACHE_LEVEL_FIRST;
    protected static $cacheMinutes = 60;

    protected $table = 'ways';
    public static $resourceName = 'Way';

    public static $titleColumn = 'name';

    const WAY_HAD  = 1;
    const WAY_HHAD = 2;
    const WAY_HAFU = 3;
    const WAY_TTG  = 4;
    const WAY_CRS  = 5;

    public static $aExtraWays = [["hafu", "ttg", "crs"], ['wnm']];

    public static $columnForList = [
        'gt_id',
        'name',
        'identity',
        'max_connect_games',
    ];
    public static $mainParamColumn = 'name';

    public static $rules = [
        'gt_id'             => 'required|integer',
        'name'              => 'max:15',
        'identity'          => 'alpha_dash|max:15',
        'max_connect_games' => 'integer|max:8',
    ];
    protected $fillable = [
        'gt_id',
        'name',
        'identity',
        'max_connect_games',
    ];

    public static $htmlSelectColumns = [
        'gt_id' => 'aGameTypes',
    ];

    public static $singleableWays = ['crs', 'hafu', 'ttg'];

    public function way_odds() {
        return $this->hasMany('App\Models\Game\WayOdd', 'w_id');
    }

    protected function afterSave($oSavedModel) {
        $this->deleteCache($this->id);
        if ($aWays = Cache::get('way-cache-keys')) {
            foreach ($aWays as $key => $value) {
                $this->deleteCache($value);
            }
        }
        $key = generateComplexDataCacheKey('game-config-for-betting');
        Cache::tags($key)->flush();
        return true;
    }

    protected function afterDelete($oDeletedModel) {
        if ($aWays = Cache::get('way-cache-keys')) {
            foreach ($aWays as $key => $value) {
                $this->deleteCache($value);
            }
        }
        $key = generateComplexDataCacheKey('game-config-for-betting');
        Cache::tags($key)->flush();
        return true;
    }

    protected function afterUpdate() {
        $this->deleteCache($this->id);
        if ($aWays = Cache::get('way-cache-keys')) {
            foreach ($aWays as $key => $value) {
                $this->deleteCache($value);
            }
        }
        $key = generateComplexDataCacheKey('game-config-for-betting');
        Cache::tags($key)->flush();
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

    public static function manageCacheKeys($key) {
        if (! $keys = Cache::get('way-cache-keys')) {
            $keys = [$key];
        } else {
            $keys[] = $key;
        }
        Cache::forever('way-cache-keys', $keys);
    }

    public static function getWayIdIdentityMap($iGameType = null) {
        $aWayArr = self::getWaysArrByGameType($iGameType);
        $aWays = [];
        foreach ($aWayArr as $key => $value) {
            $aWays[$value['id']] = $key;
        }
        return $aWays;
    }

    public static function getWaysArrByGameType($iGameType = null) {
        if (static::$cacheLevel == self::CACHE_LEVEL_NONE) {
            $aWays = self::getWaysArrByGameTypeFromDB();
            return $aWays[$iGameType];
        }
        Cache::setDefaultDriver(static::$cacheDrivers[static::$cacheLevel]);
        $key = generateComplexDataCacheKey('all-ways');
        if (!$aWays = Cache::get($key)) {
            $aWays = self::getWaysArrByGameTypeFromDB();
            if (static::$cacheMinutes){
                Cache::put($key, $aWays, static::$cacheMinutes);
            } else {
                Cache::forever($key, $aWays);
            }
            self::manageCacheKeys('all-ways');
        }
        // pr($aWays);exit;
        return $iGameType ? $aWays[$iGameType] : $aWays;
    }

    public static function getWayIdsArrByGameType($iGameType, $sWayType = '') {
        if (static::$cacheLevel == self::CACHE_LEVEL_NONE) {
            return self::getWayIdsArrByGameTypeFromDB($iGameType, $sWayType);
        }
        Cache::setDefaultDriver(static::$cacheDrivers[static::$cacheLevel]);
        $sKeyData = 'way-ids-' . $iGameType . $sWayType;
        $key = static::generateCacheKey($sKeyData);
        if (!$aWays = Cache::get($key)) {
            $aWays = self::getWayIdsArrByGameTypeFromDB($iGameType, $sWayType);
            if (static::$cacheMinutes){
                Cache::put($key, $aWays, static::$cacheMinutes);
            } else {
                Cache::forever($key, $aWays);
            }
            self::manageCacheKeys($sKeyData);
        }
        return $aWays;
    }

    public static function getWaysArrByGameTypeFromDB() {
        $aWays = [];
        foreach (self::all() as $oWay) {
            if (!isset($aWays[$oWay->gt_id])) $aWays[$oWay->gt_id] = [];
            $aWays[$oWay->gt_id][$oWay->identity] = $oWay->getAttributes();
        }
        // if ($iGameType) return $aWays[$iGameType];
        return $aWays;
    }

    public static function getWayIdsArrByGameTypeFromDB($iGameType, $sWayType = '') {
        return self::where('identity', 'like', '%' . $sWayType)->lists('id')->toArray();
    }
}
