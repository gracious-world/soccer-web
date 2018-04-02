<?php
namespace App\Http\Controllers;

use App\Models\BaseTask;
use App\Models\BaseModel;
use App\Models\Basic\BusinessPartner;
use App\Models\Basic\GameType;
use App\Models\Game\Game;
use App\Models\Game\Method;
use App\Models\Game\Odd;
use App\Models\Game\ChangedOdd;
use App\Models\Game\WayOdd;
use App\Models\Game\Way;
use App\Models\Game\CustomSaleStopTime;
use App\Models\Bet\Bill;
// use App\Models\Fund\Account;
// use App\Models\Fund\Transaction;
// use App\Models\Fund\TransactionType;
use App\Models\AppUser\UserUser;
use App\Models\Func\SysConfig;

use App\Services\ServiceFactory;


use Session;
use DB;
use Tool;
use Carbon;
use Cache;
use Input;
/**
 * Created by PhpStorm.
 * User: damon
 * Date: 2/9/16
 * Time: 1:06 PM
 */
class UserGameController  extends UserBaseController
{
    protected $resourceView = 'gameCenter';
    protected $modelName = 'App\Models\Game\Game';
    protected $cacheMinutes = 60;

    protected function beforeRender() {
        if (!isset($this->viewVars['sGameType']))
            $sGameType = isset($this->params['game_type']) ? $this->params['game_type'] : 'football';
        else
            $sGameType = $this->viewVars['sGameType'];
        $oGameType = GameType::where('en_name', '=', $sGameType)->first();
        $sWayType = isset($this->params['way_type']) ? $this->params['way_type'] : '';
        $aBetDates = getDatesArrBeforeNow();
        $aWayTypeCss = [
            'had' => 'mixGg spf onlyBidCounts showDgTips ',
            'crs' => 'bf ',
            'ttg' => 'zjq showDgTips ',
            'hafu'=> 'bqc showDgTips ',
        ];
        switch ($this->action) {
            case 'index':
                $iBetType = 0;
                $bNeedShowDesc = true;
                break;
            case 'singleGames':
            case 'focusGames':
                $iBetType = 1;
                $bNeedShowDesc = false;
                break;
            default:
                $iBetType = 0;
                $bNeedShowDesc = true;
                break;
        }
        $sPageName = $this->action == 'index' ? 'games' : $this->action;
        $aCustomSaleStopTimes = CustomSaleStopTime::getLatestSaleStopRules($oGameType->id);
        $this->setVars(compact('aBetDates', 'sGameType', 'sWayType', 'aWayTypeCss', 'iBetType', 'bNeedShowDesc', 'sPageName', 'aCustomSaleStopTimes'));
        // $this->request->session()->put('iBetType', $iBetType);
        // pr($this->action);exit;

        //从SysConfig表中读取竞彩游戏禁止投注时间
        $oData = SysConfig::getDataSourceInfo('forbid_bet_time');
        list($sStart, $sEnd, $sDescription) = explode('#', $oData->data_source);

        //1月27日0点--2月3日9点为休市时间,停止投注
        $aDate = ['start_time'=>$sStart, 'end_time'=>$sEnd, 'current_time'=> date('Y-m-d H:i:s'), 'description'=>$sDescription];
        //$aDate = ['start_time'=>'2017-01-26 23:59:59', 'end_time'=>'2017-02-03 09:00:00', 'current_time'=> '2017-01-27 22:59:59'];
        $this->setVars(compact('aDate'));


        parent::beforeRender();
    }

    public function index() {
        $this->setVars('bIsFocus', 0);
        $this->view = $this->resourceView . '.game';
        return $this->render();
    }

    /**
     * [singleGames 单关赛事]
     * @return [Response]
     */
    public function singleGames() {
        return $this->index();
    }

    /**
     * [focusGames 焦点赛事]
     * @return [Response]
     */
    public function focusGames() {
        $this->setVars('bIsFocus', 1);
        return $this->render();
    }

    /**
     * [rankGames 排位赛事, 如欧洲杯]
     * @return [Response]
     */
    public function rankGames() {

        return $this->render();
    }

    public function oddTrend($sGameType = 'football') {
        $sBetDate = Carbon::today()->toDateString();
        $oGameType = GameType::where('en_name', $sGameType)->first();
        $iGameTypeId = $oGameType->id;
        $aParam = [
            'gt_id'         => $iGameTypeId,
            'is_rank'       => 0,
            'sale_start_at' => ['>=', $sBetDate],
            'status'        => Game::STATUS_SALE_ON,
        ];
        $sCacheKey = generateComplexDataCacheKey('game-odd-trend-' . $iGameTypeId . '-' . $sBetDate);
        // Cache::forget($sCacheKey);
        if (!$datas = Cache::get($sCacheKey)) {
            $datas = [];
            $oGamesData = Game::complexWhere($aParam)->get();
            $aWays = Way::where('gt_id', $iGameTypeId)->get()->pluck('identity', 'id')->toArray();
            // $oGamesData = Game::complexWhere($aParam)->get();
            $wCN = ['周日','周一','周二','周三','周四','周五','周六'];
            foreach ($oGamesData as $oGame) {
                $iDayOfWeek = Carbon::parse($oGame->sale_start_at)->dayOfWeek;
                $sSaleStartAt = strtotime($oGame->sale_start_at);
                $saleStartDate = date('Y-m-d', $sSaleStartAt);
                $aGameData = [
                    'week'               => $wCN[$iDayOfWeek],
                    'num'                => substr($oGame->bn, -3),
                    'played_at'          => explode(' ', $oGame->played_at)[1],
                    'sale_stop_at'       => $oGame->sale_stop_time,
                    // 'real_sale_stop_at'  => $oGame->real_sale_stop_time,
                    'l_cn'               => $oGame->l_cn,
                    'h_cn'               => $oGame->h_cn,
                    'a_cn'               => $oGame->a_cn,
                    'l_cn_abbr'          => $oGame->l_cn_abbr,
                    'h_cn_abbr'          => $oGame->h_cn_abbr,
                    'a_cn_abbr'          => $oGame->a_cn_abbr,
                    'h_score'            => $oGame->h_score,
                    'a_score'            => $oGame->a_score,
                    'l_background_color' => '#' . $oGame->l_background_color,
                    'fixed'              => $oGame->fixed_display,

                ];
                // $aChangedOdds = $oGame->changed_odds->pluck('odd', 'wo_identity')->toArray();
                $aOddParams = [
                    'gt_id' => $iGameTypeId,
                    'g_id'  => $oGame->id,
                    'w_id'  => ['in', [WAY::WAY_HAD, WAY::WAY_HHAD]],
                ];
                $aChangedOdds = [];
                $oChangedOdds = ChangedOdd::complexWhere($aOddParams)->orderBy('created_at', 'asc')->get();
                foreach ($oChangedOdds as $key => $oChangedOdd) {
                    if (!isset($aChangedOdds[$oChangedOdd->wo_identity])) $aChangedOdds[$oChangedOdd->wo_identity] = [];
                    $aChangedOdds[$oChangedOdd->wo_identity][$oChangedOdd->created_at->toDateTimeString()] = $oChangedOdd->odd;
                }
                $aGameData['odd_trend'] = $aChangedOdds;
                $datas[$saleStartDate][] = $aGameData;
            }
            Cache::put($sCacheKey, $datas, $this->cacheMinutes);
        }
        // pr($datas);exit;
        $this->setVars(compact('datas'));
        return $this->render();
    }

    public function result($sGameType = 'football') {
        $sBetDate = $this->request->input('sale_start_at') ? $this->request->input('sale_start_at') : Carbon::yesterday()->toDateString();
        $oGameType = GameType::where('en_name', $sGameType)->first();
        $iGameTypeId = $oGameType->id;
        $aParam = [
            'gt_id'         => $iGameTypeId,
            'is_rank'       => 0,
            'sale_start_at' => $sBetDate,
            'status'        => ['>=', Game::STATUS_SALE_OFF],
        ];
        $sCacheTag = generateComplexDataCacheKey('game-result-' . $sGameType);
        $sCacheKey = generateComplexDataCacheKey('game-result-' . $sGameType . '-' . $sBetDate);
        // Cache::tags($sCacheTag)->flush();
        if (!$datas = Cache::tags($sCacheTag)->get($sCacheKey)) {
            $datas = [];
            $oGamesData = Game::complexWhere($aParam)->get();
            $aWays = Way::where('gt_id', $iGameTypeId)->get()->pluck('identity', 'id')->toArray();
            // $oGamesData = Game::complexWhere($aParam)->get();
            $wCN = ['周日','周一','周二','周三','周四','周五','周六'];
            foreach ($oGamesData as $oGame) {
                $iDayOfWeek = Carbon::parse($oGame->sale_start_at)->dayOfWeek;
                $aGameData = [
                    'week'               => $wCN[$iDayOfWeek],
                    'num'                => substr($oGame->bn, -3),
                    'played_at'          => explode(' ', $oGame->played_at)[1],
                    'l_cn'               => $oGame->l_cn,
                    'h_cn'               => $oGame->h_cn,
                    'a_cn'               => $oGame->a_cn,
                    'l_cn_abbr'          => $oGame->l_cn_abbr,
                    'h_cn_abbr'          => $oGame->h_cn_abbr,
                    'a_cn_abbr'          => $oGame->a_cn_abbr,
                    'h_score'            => $oGame->h_score,
                    'a_score'            => $oGame->a_score,
                    'l_background_color' => '#' . $oGame->l_background_color,
                    'fixed'              => $oGame->fixed_display,
                    'fixed_json'         => $oGame->fixed_json_display,
                ];
                $aGameData['result'] = $oGame->calculateWonResults(false);
                $aWayOddIdentities = $oGame->calculateWonResults();
                $aGameData['odds'] = [];
                $aOddResults = Odd::complexWhere(['g_id' => $oGame->id, 'wo_identity' => ['in', $aWayOddIdentities]])->get()->pluck('odd', 'w_id')->toArray();
                foreach ($aOddResults as $key => $value) {
                    $sWayIdentity = $aWays[$key];
                    $aGameData['odds'][$sWayIdentity] = number_format($value, 2);
                }

                $datas[] = $aGameData;
            }
            Cache::tags($sCacheTag)->put($sCacheKey, $datas, $this->cacheMinutes);
        }
        // pr($datas);exit;
        $this->setVars(compact('datas'));
        return $this->render();
    }


    /**
     * 获取比赛数据接口,供前台ajax使用
     */
    public function gameData($sGameType = 'football', $iBetType = 0, $sFilterWay = '') {
        // DB::enableQueryLog();
        // pr($this->params);exit;
        // $sGameType = isset($this->params['game_type']) ? $this->params['game_type'] : 'football';
        $oGameType = GameType::getAvailableGameType($sGameType);
        $iGameTypeId = $oGameType->id;
        $sWayType = isset($this->params['way_type']) ? $this->params['way_type'] : ($sFilterWay ? $sFilterWay : '');
        $bSingle = boolval($iBetType) && !in_array($sWayType, Way::$singleableWays);
        $betDate = isset($this->params['bet_date']) ? $this->params['bet_date'] : date('Y-m-d');
        $bHistory = !(!$betDate || $betDate == date('Y-m-d'));
        $mCacheTag = $bHistory ? 'game-result-history-' . $sGameType . '-' . $betDate : 'game-for-betting-' . $sGameType;
        $bIsFocus = array_key_exists('is_focus', $this->params) && $this->params['is_focus'];
        $sCacheKey = generateComplexDataCacheKey(implode('-', [$iGameTypeId, $betDate, $iBetType, $sWayType, $bIsFocus, Carbon::today()->toDateString()]));
        $bRankGame = 0;
        $aRankMethods = Method::getRankMethods($iGameTypeId);
        if (in_array($sWayType, $aRankMethods)) {
            $bRankGame = 1;
        }
        // $last_query = end($queries);
        // Cache::store('redis')->tags($mCacheTag)->flush();
        //获取比赛数据
        if ($aData = Cache::store('redis')->tags($mCacheTag)->get($sCacheKey)) {
            return $this->renderData(['coding' => 2, 'msg' => '成功', 'data' => $aData]);
        } else {
            // DB::enableQueryLog();
            $oGamesData = $bHistory ? Game::getGamesByPlayedAt($iGameTypeId, $betDate, $bSingle) : Game::getAvailableGames($iGameTypeId, $bSingle, $bRankGame, $bIsFocus);
            // $queries = DB::getQueryLog();
            // $last_query = end($queries);
            // pr($last_query);
        }
        // pr($oGamesData->toArray());exit;
        if($oGamesData->count() <= 0 ){
            return $this->renderData(['coding' => -1, 'msg' => '游戏数据为空', 'data' => []]);
        }
        $aGameIds = [];
        foreach($oGamesData as $game){
            $aGameIds[] = $game->id;
        }
        if (!$bHistory) {
            $aWayIds = $sWayType ? Way::getWayIdsArrByGameType($iGameTypeId, $sWayType) : [];

            // $oOdds = collect([]);
            //获取玩法数据
            // $aWays = Way::where('gt_id', $iGameTypeId)->get()->pluck('identity', 'id')->toArray();
            $aWays = Way::getWayIdIdentityMap($iGameTypeId);
            // pr($aWays);exit;
            $aGameOdds = [];
            $aGameSingles = [];
            foreach (array_chunk($aGameIds, 10) as $aGameId) {
                // 获取比赛赔率数据
                $oOdd = Odd::getOddsByGameIds($aGameId, $aWayIds, ['g_id', 'w_id', 'wo_identity', 'odd','euro_odd', 'last_odd', 'single', 'probability']);
                // 拼装比赛赔率数据
                foreach($oOdd as $odd) {
                    $iTrend = $odd->odd > $odd->last_odd ? 1 : ($odd->odd < $odd->last_odd ? 0 : -1); // 1: 升, 0: 降, -1: 平
                    $aGameOdds[$odd->g_id][$aWays[$odd->w_id]][$odd->wo_identity] = [$odd->odd_formatted, $iTrend,$odd->euro_odd, $odd->probability];
                    if (!isset($aGameSingles[$odd->g_id][$aWays[$odd->w_id]])) $aGameSingles[$odd->g_id][$aWays[$odd->w_id]] = $odd->single;
                }
            }
        }
        // $queries = DB::getQueryLog();
        // pr($queries);exit;
        // pr($aGameOdds);
        // pr($aGameSingles);
        // exit;
        //数据拼装
        $aData = [];
        $wCN = ['周日','周一','周二','周三','周四','周五','周六'];
        foreach($oGamesData as $game) {
            $sPlayedAt = strtotime($game->played_at);
            $date = date('Y-m-d', $sPlayedAt);
            $time = date('H:i:s', $sPlayedAt);
            $num = substr($game->bn, -3);
            $sSaleStartAt = strtotime($game->sale_start_at);
            $w = date('w', $sSaleStartAt);
            $saleStartDate = date('Y-m-d', $sSaleStartAt);

            $aTmpData = [
                'id'                 => $game->id,
                'bn'                 => $game->bn,
                'week'               => $wCN[$w],
                'num'                => $num,
                'date'               => $date,
                'time'               => $time,
                'sale_start_at'      => Carbon::today()->hour(9)->toDateTimeString(),
                'sale_stop_at'       => $game->sale_stop_at,
                // 'real_sale_stop_at'  => $game->real_sale_stop_time,
                'b_date'             => $saleStartDate,
                'status'             => $game->status,
                'single'             => $game->single,
                'hot'                => $game->hot,
                'l_id'               => $game->l_id,
                'l_cn'               => $game->l_cn,
                'h_id'               => $game->h_id,
                'h_cn'               => $game->h_cn,
                'a_id'               => $game->a_id,
                'a_cn'               => $game->a_cn,
                'show'               => $game->show,
                'fixed'              => $game->fixed,
                'fixed_json'         => $game->fixed_json,
                'l_cn_abbr'          => $game->l_cn_abbr,
                'h_cn_abbr'          => $game->h_cn_abbr,
                'a_cn_abbr'          => $game->a_cn_abbr,
                'half_h_score'       => $game->half_h_score,
                'half_a_score'       => $game->half_a_score,
                'h_score'            => $game->h_score,
                'a_score'            => $game->a_score,
                'l_background_color' => $game->l_background_color,
                'weather'            => $game->weather,
                'weather_city'       => $game->weather_city,
                'temperature'        => $game->temperature,
                'weather_pic'        => "",
                'match_info'         => [],
            ];
            if ($bHistory) {
                $aExtraData = $game->calculateWonResults();
            } else {
                if (!isset($aGameOdds[$game->id])) continue;
                $aExtraData = $aGameOdds[$game->id];
                $aExtraData['single_ways'] = $aGameSingles[$game->id];
            }
            $aData[$saleStartDate][] = array_merge($aTmpData, $aExtraData);
        }
        if (isset($sCacheKey)) {
            Cache::store('redis')->tags($mCacheTag)->put($sCacheKey, $aData, 120);
        }
        return $this->renderData(['coding' => 1, 'msg' => '成功', 'data' => $aData]);
    }

    /**
     * 获取游戏玩法相关数据接口,供前台ajax使用
     */
    public function gameConfig($sGameType = 'football', $sFilterWay = '') {
        $key = generateComplexDataCacheKey('game-config-for-betting');
        $subKey = $key . '-' . $sGameType;
        // Cache::forget($key);
        if (!$returnData = Cache::tags($key)->get($subKey)) {
            $returnData = $this->getGameConfigFromDB($sGameType, $sFilterWay);
            Cache::tags($key)->put($subKey, $returnData, 60);
        }
        return $this->renderData(['coding' => 1, 'msg' => '成功', 'data' => $returnData]);
    }

    public function getGameConfigFromDB($sGameType = 'football', $sFilterWay = '') {
        $oGameType = GameType::where('en_name', $sGameType)->first();
        $iGameTypeId = $oGameType->id;
        $returnData = [];
        $oWays = Way::where('gt_id', $iGameTypeId)->get();
        $aWayOddData = WayOdd::getWayOddsByGameType($iGameTypeId);
        $aWayOddMap = $aWayOddData['way_odd_map'];
        $aWayOdds = $aWayOddData['way_odd'];
        $oMethods = Method::where('gt_id', $iGameTypeId)->get();
        $aWays = [];
        // $aWayOdds = [];
        // $aWayOddMap = [];
        $aWayMap = [];
        $aMaxGates = [];
        $aGateMap = [];
        foreach($oWays as $way){
            $aMaxGates[$way->identity] = $way->max_connect_games;
            $aWays[$way->id] = $way->identity;
            $aWayMap[$way->identity] = ['name' => $way->name,'max_connect_games' => $way->max_connect_games];
        }

        // foreach($oWayOdds as $odd) {
        //     $aWayOddMap[$odd->identity] = ['name' => $odd->name,'max_connect_games' => $odd->max_connect_games];
        //     $wIdentity = $aWays[$odd->w_id];
        //     $aWayOdds[$wIdentity][$odd->sub_group][] = $odd->identity;
        // }

        foreach($aWayOdds as $wayOddKey => $wayOdd) {
            $i = 0;
            foreach($wayOdd as $key => $v){
                $aWayOdds[$wayOddKey][$i] = $v;
                unset($aWayOdds[$wayOddKey][$key]);
                $i++;
            }
        }
        foreach($oMethods as $oMethod) {
            $methodType = Method::$types[$oMethod->type];
            $aGateMap[$methodType][] = ['identity' => $oMethod->identity, 'name' => $oMethod->name, 'combination' => $oMethod->combination];
        }
        $aExtraWays = Way::$aExtraWays;
        $returnData['ways'] = array_values($aWays);
        $returnData['extra_ways'] = $aExtraWays[$iGameTypeId - 1];
        $returnData['way_odds'] = $aWayOdds;
        $returnData['way_odd_map'] = $aWayOddMap;
        $returnData['way_map'] = $aWayMap;
        $returnData['max_gates'] = $aMaxGates;
        $returnData['gate_map'] = $aGateMap;
        return $returnData;
    }


}