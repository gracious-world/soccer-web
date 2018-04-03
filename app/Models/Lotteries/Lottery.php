<?php
namespace App\Models\Lotteries;
use App\Models\BaseModel;
use Illuminate\Support\Facades\Cache;
use App\Models\Series\Series;
/**
 * 彩票模型
 */
class Lottery extends BaseModel {

    static $cacheLevel             = self::CACHE_LEVEL_FIRST;

    /**
     * 数字排列类型
     */
    const LOTTERY_TYPE_DIGITAL           = 1;

    /**
     * 乐透类型
     */
    const LOTTERY_TYPE_LOTTO             = 2;

    /**
     * 体育类型
     */
    const LOTTERY_TYPE_SPORT             = 3;

    /**
     * 真人娱乐类型
     */
    const LOTTERY_TYPE_LIVE_CASINO       = 4;

    /**
     * 单区乐透类型
     */
    const LOTTERY_TYPE_LOTTO_SINGLE      = 1;

    /**
     * 双区乐透类型
     */
    const LOTTERY_TYPE_LOTTO_DOUBLE      = 2;
    const WINNING_SPLIT_FOR_DOUBLE_LOTTO = '+';

    /**
     * 针对正式用户可用
     */
    const STATUS_AVAILABLE_FOR_NORMAL_USER = 2;

    /**
     * 针对测试用户可用
     */
    const STATUS_AVAILABLE_FOR_TESTER      = 1;

    /**
     * 不可用
     */
    const STATUS_NOT_AVAILABLE             = 0;

    /**
     * 测试状态（此状态下系统不接受自动录号）
     */
    const STATUS_TESTING = 4;

    /**
     * 永久关闭
     */
    const STATUS_CLOSED_FOREVER = 8;

    /**
     * 所有用户可用
     */
    const STATUS_AVAILABLE      = 3;
    const ERRNO_LOTTERY_MISSING = -900;
    const ERRNO_LOTTERY_CLOSED  = -901;

    /**
     * all types
     * @var array
     */
    public static $validTypes = [
        self::LOTTERY_TYPE_DIGITAL => 'Digital',
        self::LOTTERY_TYPE_LOTTO   => 'Lotto',
        self::LOTTERY_TYPE_SPORT    => 'Sport',
        self::LOTTERY_TYPE_LIVE_CASINO    => 'Live-Casino',
    ];

    /**
     * all lotto types
     * @var array
     */
    public static $validLottoTypes = [
        self::LOTTERY_TYPE_LOTTO_SINGLE => 'Single',
        self::LOTTERY_TYPE_LOTTO_DOUBLE => 'Double',
    ];
    public static $validStatuses = [
        self::STATUS_NOT_AVAILABLE        => 'Closed',
        self::STATUS_AVAILABLE_FOR_TESTER => 'For Tester',
//        self::STATUS_AVAILABLE_FOR_NORMAL_USER => 'Available',
        self::STATUS_AVAILABLE            => 'Available',
        self::STATUS_TESTING            => 'Testing',
        self::STATUS_CLOSED_FOREVER       => 'Closed Forever'
    ];
    public static $resourceName  = 'Lottery';
    protected $table                 = 'lotteries';

    /**
     * 下拉列表框字段配置
     * @var array
     */
    public static $htmlSelectColumns = [
        'series_id'  => 'aSeries',
        'type'       => 'aValidTypes',
        'lotto_type' => 'aValidLottoTypes',
        'status'     => 'aValidStatus',
    ];
    public static $sequencable       = true;
    public static $listColumnMaps = [
        'name' => 'friendly_name'
    ];

    /**
     * order by config
     * @var array
     */
    public $orderColumns = [
        'sequence' => 'asc'
    ];

    /**
     * the main param for index page
     * @var string
     */
    public static $mainParamColumn = 'type';

    public static $customMessages = [];
    public static $titleColumn    = 'name';

    public function series() {
        return $this->belongsTo('Series');
    }

    protected function beforeValidate() {
        $this->lotto_type or $this->lotto_type = null;
        return parent::beforeValidate();
    }

//    public static function getAllLotteryNameArray($aColumns = null)
//    {
//        $aColumns or $aColumns = ['id', 'name'];
//        $aLotteries = Lottery::all($aColumns);
//        $data = [];
//        foreach ($aLotteries as $key => $value) {
//            $data[$value->id] = $value->name;
//        }
//        return $data;
//    }
    protected static function compileLotteryListCacheKey($bOpen = null) {
        $sKey = static::getCachePrefix(true) . 'list';
        if (!is_null($bOpen)) {
            $sKey .= $bOpen ? '-open' : '-close';
        }
        return $sKey;
    }

    protected static function & getLotteryListByStatus($iStatus = null) {
        $bReadDb   = true;
        $bPutCache = false;
        if (static::$cacheLevel != self::CACHE_LEVEL_NONE) {
            Cache::setDefaultDriver(static::$cacheDrivers[static::$cacheLevel]);
            $sCacheKey  = static::compileLotteryListCacheKey($iStatus);
            if ($oLotteries = Cache::get($sCacheKey)) {
                $bReadDb = false;
            } else {
                $bPutCache = true;
            }
        }
//        $bReadDb = $bPutCache = true;
        if ($bReadDb) {
            if (!is_null($iStatus)) {
                $aStatus    = self::_getStatusArray($iStatus);
//                file_put_contents('/tmp/kkkkkk', var_export($aStatus, true));
                $oLotteries = Lottery::whereIn('status', $aStatus)->orderBy('sequence')->get();
            } else {
                $oLotteries = Lottery::orderBy('sequence')->get();
            }
        }
        if ($bPutCache) {
            Cache::forever($sCacheKey, $oLotteries);
        }
        return $oLotteries;
    }

    protected static function _getStatusArray($iNeedStatus) {
        $aStatus = [];
        foreach (static::$validStatuses as $iStatus => $sTmp) {
            if (($iStatus & $iNeedStatus) == $iNeedStatus) {
                $aStatus[] = $iStatus;
            }
        }
        return $aStatus;
    }

    /**
     * [getAllLotteries 获取所有彩种信息]
     * @param  [Boolean] $bOpen  [open属性]
     * @param  [Array] $aColumns [要获取的数据列名]
     * @return [Array]           [结果数组]
     */
    public static function getAllLotteries($iStatus = null, $aColumns = null) {
//        $aColumns or $aColumns = ['id', 'series_id', 'name'];
//        if (! is_null($bOpen)) {
//            $aLotteries = Lottery::where('open', '=', $bOpen)->orderBy('sequence')->get($aColumns);
//        } else {
//            $aLotteries = Lottery::orderBy('sequence')->get($aColumns);
//        }
        $oLotteries = static::getLotteryListByStatus($iStatus);
        $data       = [];
        foreach ($oLotteries as $key => $oLottery) {
            if ($aColumns) {
                foreach ($aColumns as $sColumn) {
                    $aTmpData[$sColumn] = $oLottery->$sColumn;
                }
            } else {
                $aTmpData = $oLottery->getAttributes(); // ['id' => $value->id, 'series_id' => $value->series_id, 'name' => $value->name];
            }
            $aTmpData['name'] = $oLottery->friendly_name;
            $data[]           = $aTmpData;
        }
        return $data;
    }

    /**
     * generate select widget
     * @return int or false   -1: path not writeable
     */
    public static function generateWidget() {
        $sCacheDataPath = Config::get('widget.data_path');
        if (!is_writeable($sCacheDataPath)) {
            return [
                'code'    => -1,
                'message' => __('_basic.file-write-fail-path', ['path' => $sCacheDataPath]),
            ];
        }
        $sFile = $sCacheDataPath . '/' . 'lotteries.blade.php';
        if (file_exists($sFile) && !is_writeable($sFile)) {
            return [
                'code'    => -1,
                'message' => __('_basic.file-write-fail-file', ['file' => $sFile]),
            ];
        }
        $aLotterys = static::getAllLotteryNameArray();
//        pr(json_encode($aLotterys));
        $iCode     = @file_put_contents($sFile, 'var lotteries = ' . json_encode($aLotterys));
        $sLangKey  = '_basic.' . ($iCode ? 'file-writed' : 'file-write-fail');
        return [
            'code'    => $iCode,
            'message' => __($sLangKey, ['resource' => $sFile]),
        ];
    }

    /**
     * 返回可用的数字数组
     *
     * @param string $sString
     * @param int $iLotteryType
     * @param int $iLottoType
     * @return array
     */
    public function & getValidNums($sString, $iLotteryType = self::LOTTERY_TYPE_DIGITAL, $iLottoType = self::LOTTERY_TYPE_LOTTO_SINGLE) {
        $data = [];
        if ($iLotteryType == self::LOTTERY_TYPE_LOTTO && $iLottoType != self::LOTTERY_TYPE_LOTTO_SINGLE) {
//            echo "$iLotteryType   New...\n";
            $aStringOfAreas = explode('|', $sString);
            $data           = [];
            foreach ($aStringOfAreas as $iArea => $sStr) {
                $data[$iArea] = & $this->getValidNums($sStr, self::LOTTERY_TYPE_LOTTO, self::LOTTERY_TYPE_LOTTO_SINGLE);
            }
//            return $data;
        } else {
            $a = explode(',', $sString);
            foreach ($a as $part) {
                $aPart = explode('-', $part);
                if (count($aPart) == 1) {
                    $data[] = $this->formatBall($aPart[0], $iLotteryType, $iLottoType);
                } else {
                    for ($i = $aPart[0]; $i <= $aPart[1]; $i++) {
                        $data[] = $this->formatBall($i, $iLotteryType, $iLottoType);
                    }
                }
            }
        }
        return $data;
    }

    /**
     * 格式化数字
     *
     * @param int $iNum
     * @param int $iLotteryType
     * @param int $iLottoType
     * @return string
     */
    public function formatBall($iNum, $iLotteryType = self::LOTTERY_TYPE_DIGITAL, $iLottoType = self::LOTTERY_TYPE_LOTTO_SINGLE) {
        switch ($iLotteryType) {
            case self::LOTTERY_TYPE_DIGITAL:
                return $iNum + 0;
                break;
            case self::LOTTERY_TYPE_LOTTO:
                switch ($iLottoType) {
                    case self::LOTTERY_TYPE_LOTTO_SINGLE:
                    case self::LOTTERY_TYPE_LOTTO_DOUBLE:
                    case self::LOTTERY_TYPE_LOTTO_MIXED:
                        return str_pad($iNum, 2, '0', STR_PAD_LEFT);
                        break;
                }
        }
    }

    protected function getFriendlyNameAttribute() {
        return __('_lotteries.' . strtolower($this->name), [], 1);
    }

    /**
     * 返回数据列表
     * @param boolean $bOrderByTitle
     * @return array &  键为ID，值为$$titleColumn
     */
    public static function & getTitleList($bOrderByTitle = false) {
        $aColumns     = [ 'id', 'name'];
        $sOrderColumn = $bOrderByTitle ? 'name' : 'sequence';
        $oModels      = static::orderBy($sOrderColumn, 'asc')->get($aColumns);
        $data         = [];
        foreach ($oModels as $oModel) {
            $data[$oModel->id] = $oModel->friendly_name;
        }
        return $data;
    }

    /**
     * 返回人性化的游戏列表，游戏名称为已翻译的
     * @param boolean $bOrderByTitle
     * @return array &  键为ID，值为$$titleColumn
     */
    public static function & getLotteryList() {
        $bReadDb = false;
        $sLocale = App::getLocale();
        if (static::$cacheLevel != self::CACHE_LEVEL_NONE) {
            Cache::setDefaultDriver(static::$cacheDrivers[static::$cacheLevel]);
            $key        = static::compileListCaheKey($sLocale);
            if (!$aLotteries = Cache::get($key)) {
                $bReadDb = true;
            }
        }
        if ($bReadDb) {
            $aLotteries = static::getTitleList();
            !$key or Cache::forever($key, $aLotteries);
        }

        return $aLotteries;
    }

    /**
     * 从数据库提取游戏列表
     * @param bool $bOrderByTitle   是否按名字排序
     * @return array
     */
    protected static function & _getLotteryList($bOrderByTitle = true) {
        $aColumns     = [ 'id', 'name'];
        $sOrderColumn = $bOrderByTitle ? 'name' : 'sequence';
        $oModels      = static::orderBy($sOrderColumn, 'asc')->get($aColumns);
        $data         = [];
        foreach ($oModels as $oModel) {
            $data[$oModel->id] = $oModel->name;
        }
        return $data;
    }

    public static function & getIdentifierList($bOrderByTitle = false) {
        $aColumns     = [ 'id', 'identifier'];
        $sOrderColumn = $bOrderByTitle ? 'name' : 'sequence';
        $oModels      = static::orderBy($sOrderColumn, 'asc')->get($aColumns);
        $data         = [];
        foreach ($oModels as $oModel) {
            $data[$oModel->id] = $oModel->identifier;
        }
        return $data;
    }

    /**
     * 更新游戏列表配置
     * @return int  1: 成功 0:失败 -1: 文件不可写
     */
    public static function updateLotteryConfigs() {
        $aLotteries = & static::getIdentifierList();
//        pr($aLotteries);
        $sString    = "<?php\nreturn " . var_export($aLotteries, true) . ";\n";
        $sPath      = app_path('config');
        $sFile      = $sPath . DIRECTORY_SEPARATOR . 'lotteries.php';
        if (!is_writeable($sFile)) {
            return -1;
        }
        return file_put_contents($sFile, $sString) ? 1 : 0;
    }

    public static function updateLotteryListCache() {
        if (static::$cacheLevel == self::CACHE_LEVEL_NONE)
            return true;
        Cache::setDefaultDriver(static::$cacheDrivers[static::$cacheLevel]);
        $sLanguageSource = SysConfig::readDataSource('sys_support_languages');
        // pr($sLanguageSource);
        $aLanguages      = SysConfig::getSource($sLanguageSource);
        $aLotteries      = & self::_getLotteryList();
        foreach ($aLanguages as $sLocale => $sLanguage) {
            $aLotteriesOfLocale = array_map(function($value) use ($sLocale) {
                return __('_lotteries.' . strtolower($value), [], 1, $sLocale);
            }, $aLotteries);
            $key = static::compileListCaheKey($sLocale);
            Cache::forever($key, $aLotteriesOfLocale);
        }
        return true;
    }

    protected static function compileListCaheKey($sLocate) {
        return 'lottery-list-' . $sLocate;
    }

    protected static function deleteOtherCache() {
        if (static::$cacheLevel == self::CACHE_LEVEL_NONE)
            return true;
        Cache::setDefaultDriver(static::$cacheDrivers[static::$cacheLevel]);
        $sKey = static::compileLotteryListCacheKey();
        !Cache::has($sKey) or Cache::forget($sKey);
        $sKey = static::compileLotteryListCacheKey(1);
        !Cache::has($sKey) or Cache::forget($sKey);
        $sKey = static::compileLotteryListCacheKey(0);
        !Cache::has($sKey) or Cache::forget($sKey);
    }

    protected function deleteCacheByIdentifier(){
        $sKey = static::compileCacheKeyByIdentifier($this->identifier) ;
        !Cache::has($sKey) or Cache::forget($sKey);
    }

    protected function afterSave($oSavedModel) {
        parent::afterSave($oSavedModel);
        $this->updateLotteryListCache();
        $this->deleteCacheByIdentifier();
        $this->deleteOtherCache();
        return true;
    }

    protected function afterDelete($oDeletedModel) {
        parent::afterDelete($oDeletedModel);
        $this->updateLotteryListCache();
        $this->deleteOtherCache();
        return true;
    }

    /**
     * 根据代码返回游戏对象
     * @param string $sIdentifier
     * @return Lottery | false
     */
    public static function getByIdentifier($sIdentifier) {
        $bReadDb = false;
        if (static::$cacheLevel != self::CACHE_LEVEL_NONE) {
            Cache::setDefaultDriver(static::$cacheDrivers[static::$cacheLevel]);
            $key         = static::compileCacheKeyByIdentifier($sIdentifier);
            if ($aAttributes = Cache::get($key)) {
                $obj = new static;
                $obj = $obj->newFromBuilder($aAttributes);
            } else {
                $bReadDb = true;
            }
        }
        if ($bReadDb) {
            $obj = static::where('identifier', '=', $sIdentifier)->first();
            if (!is_object($obj)) {
                return false;
            }
            !$key or Cache::forever($key, $obj->getAttributes());
        }

        return $obj;
    }

    protected static function compileCacheKeyByIdentifier($sIdentifier) {
        return 'lottery-identifier-' . strtolower($sIdentifier);
    }

    /**
     * [getAllLotteriesGroupBySeries 根据彩系组织彩种]
     * @param  [Integer] $iOpen     [open属性]
     * @param  [boolean] $bNeedLink [是否需要判断彩系的link_to属性]
     * @return [Array]           [彩种数据]
     */
    public static function getAllLotteriesGroupBySeries($iStatus = null, $bNeedLink = true, $aLotteryColumns = null) {
        $aLotteries      = static::getAllLotteries($iStatus, $aLotteryColumns);
        $aLinkTo         = Series::getAllSeriesWithLinkTo();
        $aLotteriesArray = [];
        foreach ($aLotteries as $key => $aLottery) {
            if ($bNeedLink && $aLinkTo[$aLottery['series_id']]) {
                $aLottery['series_id'] = $aLinkTo[$aLottery['series_id']];
            }
            if (!isset($aLotteriesArray[$aLottery['series_id']])) {
                $aLotteriesArray[$aLottery['series_id']] = [];
            }
            $aLotteriesArray[$aLottery['series_id']][] = $aLottery;
        }
        return $aLotteriesArray;
    }

    /**
     * [getAllLotteryIdsGroupBySeries 生成彩种--彩系的映射数组, 彩系以linkTo属性为准]
     * @return [Array] [彩种--彩系的映射数组]
     */
    public static function getAllLotteryIdsGroupBySeries() {
        $aLotteries      = static::getAllLotteries();
        $aLinkTo         = Series::getAllSeriesWithLinkTo();
        $aLotteriesArray = [];
        foreach ($aLotteries as $key => $aLottery) {
            if ($aLinkTo[$aLottery['series_id']]) {
                $aLottery['series_id'] = $aLinkTo[$aLottery['series_id']];
            }
            $aLotteriesArray[$aLottery['id']] = $aLottery['series_id'];
        }
        return $aLotteriesArray;
    }

    protected static function getValidTypes() {
        return self::_getArrayAttributes(__FUNCTION__);
    }

    protected static function getValidStatuses() {
        return self::_getArrayAttributes(__FUNCTION__);
    }

    protected static function getValidLottoTypes() {
        return self::_getArrayAttributes(__FUNCTION__);
    }

    public static function getGroupPrizeLottery(){
        $aConditions = [
            'type' => ['in',[static::LOTTERY_TYPE_DIGITAL,static::LOTTERY_TYPE_LOTTO]]
        ];
        return static::doWhere($aConditions)->get();
    }

}
