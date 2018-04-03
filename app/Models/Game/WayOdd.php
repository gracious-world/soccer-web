<?php

namespace App\Models\Game;

use App\Models\BaseModel;
use Cache;

class WayOdd extends BaseModel {
    static $cacheLevel = self::CACHE_LEVEL_FIRST;
    protected static $cacheMinutes = 0;

    protected $table = 'way_odds';
    public static $resourceName = 'WayOdd';

    public static $titleColumn = 'name';

    public static $sequencable = true;


    public static $columnForList = [
        'gt_id',
        'w_id',
        // 'w_identity',
        'name',
        'identity',
        'max_connect_games',
        'default_odd',
        'sub_group',
        'sequence'
    ];
    public static $mainParamColumn = 'cn_name';

    public static $rules = [
        'gt_id'             => 'required|integer',
        'w_id'              => 'required|integer',
        'w_identity'        => 'alpha_dash|max:15',
        'name'              => 'max:15',
        'identity'          => 'alpha_dash|max:15',
        'max_connect_games' => 'integer|max:8',
        'default_odd'       => 'numeric',
        'sub_group'         => 'max:5',
        'sequence'          => 'integer',
    ];
    protected $fillable = [
        'gt_id',
        'w_id',
        'w_identity',
        'name',
        'identity',
        'max_connect_games',
        'default_odd',
        'sub_group',
        'sequence',
    ];

    public static $htmlSelectColumns = [
        'gt_id' => 'aGameTypes',
        'w_id'  => 'aWays',
    ];

    public static $htmlNumberColumns = [
        'default_odd' => 2,
    ];

    public function way() {
        return $this->belongsTo('App\Models\Game\Way', 'w_id');
    }

    protected function afterSave($oSavedModel) {
        $this->deleteCache($this->id);
        $this->deleteCache('gt-' . $this->gt_id);
        $key = generateComplexDataCacheKey('game-config-for-betting');
        Cache::tags($key)->flush();

        return true;
    }

    protected function afterDelete($oDeletedModel) {
        $this->deleteCache($this->id);
        $this->deleteCache('gt-' . $this->gt_id);
        $key = generateComplexDataCacheKey('game-config-for-betting');
        Cache::tags($key)->flush();
        return true;
    }

    protected function afterUpdate() {
        $this->deleteCache($this->id);
        $this->deleteCache('gt-' . $this->gt_id);
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

    public static function getWayOddNameMap($iGameTypeId) {
        return self::getWayOddNameOrConnectNumByGameType($iGameTypeId, 1);
    }

    public static function getWayOddConnectMap($iGameTypeId) {
        return self::getWayOddNameOrConnectNumByGameType($iGameTypeId, 0);
    }

    public static function getWayOddNameOrConnectNumByGameType($iGameTypeId, $iType = 0) {
        $aWayOddMap = self::getWayOddsByGameType($iGameTypeId);
        $sKey = ['max_connect_games', 'name'][$iType];
        $aOddMaxGates = array_map(function($item) use ($sKey) {
            return $item[$sKey];
        }, $aWayOddMap['way_odd_map']);
        return $aOddMaxGates;
    }

    public static function getWayOddsByGameType($iGameTypeId) {
        if (static::$cacheLevel == self::CACHE_LEVEL_NONE) {
            return self::getWayOddsByGameTypeFromDB($iGameTypeId);
        }
        Cache::setDefaultDriver(static::$cacheDrivers[static::$cacheLevel]);
        $key = static::generateCacheKey('gt-' . $iGameTypeId);
        if (!$data = Cache::get($key)) {
            $data = self::getWayOddsByGameTypeFromDB($iGameTypeId);
            if (static::$cacheMinutes){
                Cache::put($key, $data, static::$cacheMinutes);
            } else {
                Cache::forever($key, $data);
            }
        }
        return $data;
    }

    public static function getWayOddsByGameTypeFromDB($iGameTypeId) {
        $oWayOdds = self::where('gt_id', $iGameTypeId)->orderBy('sequence','asc')->get();
        if (!$oWayOdds->count()) {
            return false;
        }
        $aWayOddMap = [];
        $aWayOdds = [];
        foreach ($oWayOdds as $oWayOdd) {
            $aWayOddMap[$oWayOdd->identity] = ['name' => $oWayOdd->name, 'max_connect_games' => $oWayOdd->max_connect_games];
            $aWayOdds[$oWayOdd->w_identity][$oWayOdd->sub_group][] = $oWayOdd->identity;
        }
        return ['way_odd_map' => $aWayOddMap, 'way_odd' => $aWayOdds];
    }

    public static function getWayOddsRevertMapByGameTypeFromDB($iGameTypeId) {
        return self::where('gt_id', $iGameTypeId)->orderBy('sequence','asc')->get()->pluck('w_identity', 'identity')->toArray();
    }
}
