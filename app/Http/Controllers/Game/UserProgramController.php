<?php
namespace App\Http\Controllers;

use App\Models\BaseTask;
use App\Models\Basic\BusinessPartner;
use App\Models\Basic\GameType;
use App\Models\Func\SysConfig;
use App\Models\Game\Game;
use App\Models\Game\Method;
use App\Models\Game\Odd;
use App\Models\Game\WayOdd;
use App\Models\Game\Way;
use App\Models\Game\CustomSaleStopTime;
use App\Models\Bet\Program;
use App\Models\Bet\Bill;
use App\Models\Fund\Account;
use App\Models\Fund\Transaction;
use App\Models\Fund\TransactionType;
use App\Models\AppUser\UserUser;
use App\Models\Customer\Wallet;
use App\Models\Customer\PointJournal;
use App\Models\Customer\PointType;

use App\Jobs\PrintTicketJob;

use Session;
use DB;
use Tool;
use Carbon;
use Config;
use Illuminate\Support\Facades\Input;
use App\Models\User\User;

class UserProgramController extends UserBaseController {
    protected $resourceView = 'userCenter.program';
    protected $modelName = 'App\Models\Bet\Program';
    protected $accountLocker;
    protected $walletLocker;
    protected $User;
    protected $Account;
    protected $Wallet;

    // protected static $pagesize = 1;

    protected function beforeRender() {
        $oGameTypes = GameType::filterStatus(false)->get();
        $aGameTypes = $oGameTypes->pluck('cn_name', 'id');
        $aStatus = Program::$status;
        $aGateTypes = Method::$types;
        $iBetType = $this->request->session()->get('iBetType', 0);
        $aEuroGameMaps = Config::get('custom-euro-champion-team');
        $aEuroGameMaps = array_flip($aEuroGameMaps);
        $this->setVars(compact('aGameTypes', 'aStatus', 'aGateTypes', 'iBetType'));
        switch ($this->action) {
            case 'view':
            case 'ticketDetail':
                $oProgram = $this->viewVars['data'];
                $aBetContent = json_decode($oProgram->bet_content, true);
                $aBetOddsDisplay = json_decode($oProgram->bet_odds_display, true);
                $oRelatedGames = $oProgram->games()->get();
                $aOriginalGameBnMaps = $oRelatedGames->pluck('ds_g_bn', 'bn')->toArray();
                // pr($aOriginalGameBnMaps);
                $aGameBnMaps = [];
                foreach ($aOriginalGameBnMaps as $key => $value) {
                    $iStart = strpos($value, '(');
                    $iEnd = strpos($value, ')');
                    // pr(substr($value, $iStart + 1, $iEnd - $iStart - 1));exit;
                    // pr($oProgram->is_rank);exit;
                    if (intval($oProgram->is_rank)) {
                        $sTeamIdentity = substr($value, $iStart + 1, $iEnd - $iStart - 1);
                        $aTeamIdentities = str_split($sTeamIdentity, 2);
                        $aTeamNames = [];
                        foreach ($aTeamIdentities as $sTeam) {
                            $aTeamNames[] = $aEuroGameMaps[$sTeam];
                        }
                        $aGameBnMaps[$key] = implode('/', $aTeamNames);
                    } else {
                        $aGameBnMaps[$key] = substr($value, 0, $iStart);
                    }
                }
                // pr($aGameBnMaps);exit;
                $aBillStatus = Program::$status;
                $aWayOddRevertMap = WayOdd::getWayOddsRevertMapByGameTypeFromDB($oProgram->gt_id);
                $iGameTypeId = $oProgram->gt_id;
                $this->setVars(compact('iGameTypeId', 'oRelatedGames', 'aGameBnMaps', 'aBillStatus', 'aBetContent', 'aBetOddsDisplay', 'aWayOddRevertMap'));
                break;
        }
        $sPageName = 'programs';
        $this->setVars(compact('sPageName'));
        // pr($aGameBnMaps);exit;
        parent::beforeRender();
    }

    /**
     * 下级投注
     * @return Response
     */
    public function index() {
        $this->params = Input::except(['page', 'sort_up', 'sort_down']);
        $iUserId = Session::get('user_id');
        $oUser = UserUser::find($iUserId);
        if (isset($this->params['username'])) $oSubUser = User::getUserByUsername($this->params['username']);
        if (isset($oSubUser) && $oSubUser && $oUser->isChild($oSubUser->id, false)) {
            $aUserIds = [$oSubUser->id];
        } else {
            $aUserIds = User::getAllUsersBelongsToAgent($iUserId, true);
        }
        // pr($aUserIds);exit;
        if (count($aUserIds) > 0) $this->params['user_id'] = implode(',', $aUserIds);
        else $this->params['user_id'] = $iUserId;
        if(Session::get('is_agent')) $this->view="userCenter.teamManager.checkChildBet";
        return parent::index();
    }


    public function ticketDetail($id) {
        if (!$data = Program::find($id)) {
            return $this->goBack('error', __('_program.no-program'));
        }
        $this->setVars(compact('data'));
        // pr($oBill->bet_content);exit;
        return $this->render();
    }


    public function bills($iProgramId) {
        $this->params = $this->request->except(['page', 'perpage', 'sort_up', 'sort_down']);
        if (!$oProgram = Program::find($iProgramId)) {
            return $this->renderData(-801);
        }
        if ($oProgram->user_id != $this->request->session()->get('user_id')) {
            return $this->renderData(-702);
        }
        $sOrderColumn = $this->request->input('sort_up', $this->request->input('sort_down', 'id'));
        $sOrderDirection = $this->request->input('sort_up') ? 'asc' : 'desc';
        $iPerPage = $this->request->input('perpage', 15);
        $iPage = $this->request->input('page', 1);
        Bill::$withAppends = true;
        $iCount = $oProgram->bills()->count();
        $iTotalPages = $iCount % $iPerPage == 0 ? intval($iCount / $iPerPage) : intval($iCount / $iPerPage) + 1;
        $aBills = $oProgram->bills()->skip(($iPage - 1) * $iPerPage)->take($iPerPage)->get()->toArray();

        return $this->renderData(['coding' => 1, 'msg' => '成功', 'data' => ['bills' => $aBills, 'total' => $iCount, 'per_page' => $iPerPage, 'cur_page' => intval($iPage), 'page_num' => $iTotalPages]]);
    }


    /**
     * 下单接口
     *'identity': 商户唯一标识,
     *     'game_type': 游戏类型football/basketball,
     *     'data' => [[
     *         'sn'                  => '商户平台的注单流水号',
     *         'gt_id'               => '游戏类型id',
     *         'user_id'             => '用户id',
     *         'username'            => '用户名',
     *         'is_tester'           => '是否测试用户',
     *         'forefather_user_ids' => '用户上级链id',
     *         'first_played_at'     => '该注单首场赛事开赛时间',
     *         'first_game_bn'       => '该注单首场赛事序号',
     *         'multiple'            => '倍数',
     *         'bet_num'             => '注数',
     *         'amount'              => '投注金额',
     *         'bet_content'         => '投注内容',
     *         'coefficient'         => '货币模式',
     *     ]],
     * }
     * bet_content: {
     *     "gate": ["3x4"],
     *     "games": {"20160204YX006": ["h_1.40", "d_2.15"], "20160204YX007": ["hh_1.40", "0100_6.15"], "20160204YX008": ["ch_1.40", "s0_4.15"]},
     *     "dan": ["20160204YX006"],
     *     "bets_num": 16,
     *     "amount": 32,
     *     "multiple": 1,
     *     "max_gate":8
     * }
     */
    public function betting($sGameType = 'football') {
        //1月27日0点--2月3日9点为休市时间,停止投注
        //aDate = ['start_time'=>'2017-01-26 23:59:59', 'end_time'=>'2017-02-03 09:00:00', 'current_time'=> '2017-01-27 22:59:59'];

        //从SysConfig表中读取竞彩游戏禁止投注时间
        $oData = SysConfig::getDataSourceInfo('forbid_bet_time');
        list($sStart, $sEnd, $sDescription) = explode('#', $oData->data_source);

        $aDate = ['start_time' => $sStart, 'end_time' => $sEnd, 'current_time' => date('Y-m-d H:i:s')];
        if ($aDate['current_time'] >= $aDate['start_time'] && $aDate['end_time'] >= $aDate['current_time']) {
            return $this->renderData(['coding' => '0', 'msg' => $sDescription, 'data' => []]);
        }
        // pr($this->request->method());exit;
        $data = $this->params;
        // pr($data);exit;
        $userId = $this->request->session()->get('user_id');
        $userName = $this->request->session()->get('username');
        if (!$userId || !($this->User = UserUser::find($userId))) {
            $sResponseCode = -700;
            return $this->renderData(['coding' => $sResponseCode, 'msg' => __(Config::get('custom-code.' . $sResponseCode)), 'data' => []]);
        }
        // 没有投注内容
        if (!$data || empty($data)) {
            $sResponseCode = -204;
            return $this->renderData(['coding' => $sResponseCode, 'msg' => __(Config::get('custom-code.' . $sResponseCode)), 'data' => []]);
        }
        if (!is_numeric($data['amount']) || $data['amount'] <= 0) {
            $sResponseCode = -303;
            return $this->renderData(['coding' => $sResponseCode, 'msg' => __(Config::get('custom-code.' . $sResponseCode)), 'data' => []]);
        }
        // 非法商户
//        if (!$this->Customer = BusinessPartner::getActivateBusinessParnter($this->request->session()->get('bp_identity'))) {
//            $sResponseCode = -205;
//            return $this->renderData(['coding' => $sResponseCode, 'msg' => __(Config::get('custom-code.' . $sResponseCode)), 'data' => []]);
//        }
//        if (!$this->Customer->status_bet) {
//            $sResponseCode = -212;
//            return $this->renderData(['coding' => $sResponseCode, 'msg' => __(Config::get('custom-code.' . $sResponseCode)), 'data' => []]);
//        }
//        if ($this->Customer->is_mortgage) {
//            if (!$this->Wallet = $this->lockWallet()) {
//                $sResponseCode = Wallet::ERRNO_LOCK_FAILED;
//                return $this->renderData(['coding' => $sResponseCode, 'msg' => __(Config::get('custom-code.' . $sResponseCode)), 'data' => []]);
//            }
//            // 合作商户荷包余额不足
//            if ($this->Customer->wallet->available < $data['amount']) {
//                $sResponseCode = -401;
//                return $this->renderData(['coding' => $sResponseCode, 'msg' => __(Config::get('custom-code.' . $sResponseCode)), 'data' => []]);
//            }
//        }
        // 非法赛事类型
        if (!$oGameType = GameType::where('en_name', $sGameType)->first()) {
            $sResponseCode = -206;
            return $this->renderData(['coding' => $sResponseCode, 'msg' => __(Config::get('custom-code.' . $sResponseCode)), 'data' => []]);
        }

        //同赛事赔率单元不能重复(需要前端配合修改为一致的规则)
        foreach ($data['games'] as $key => $odd) {
            $data['games'][$key] = array_unique($odd);
        }

        $aGameBns = array_keys($data['games']); // 场次序号
        //获取该注首场赛事
        $oGames = Game::getGamesByBns($aGameBns);
        $oFirstGame = $oGames->first();
        if (!$this->checkBetTimeLegal($oFirstGame)) {
            $sResponseCode = -207;
            return $this->renderData(['coding' => $sResponseCode, 'msg' => __(Config::get('custom-code.' . $sResponseCode)), 'data' => []]);
        }
        //判断用户当前金额是否足够下单
        $iGameTypeId = $oGameType->id;
        //锁定用户账户
        if (!$this->Account = $this->lockAccount()) {
            $sResponseCode = -302;
            return $this->renderData(['coding' => $sResponseCode, 'msg' => __(Config::get('custom-code.' . $sResponseCode)), 'data' => []]);
        }
        // pr($this->Account->toArray());exit;
        if ($this->Account->available < $data['amount']) {
                $sResponseCode = -301;
                return $this->renderData(['coding' => $sResponseCode, 'msg' => __(Config::get('custom-code.' . $sResponseCode)), 'data' => []]);
//            $iLessAmount = $data['amount'] - $this->Account->available;
//            $aAutoChargeData = [
//                'game_id' => $iGameTypeId,
//                'username' => $userName,
//                'amount' => $iLessAmount,
//                'coefficient' => 1,
//            ];
//            if (!($this->Customer->auto_transfer && $this->Account->autoCharge($this->Customer, $aAutoChargeData, $aResponse))) {
//                $sResponseCode = -301;
//                return $this->renderData(['coding' => $sResponseCode, 'msg' => __(Config::get('custom-code.' . $sResponseCode)), 'data' => []]);
//            }
        }
        if (!isset($data['dan'])) {
            $data['dan'] = [];
        }
        // pr($data);exit;
        $iGateType = null;
        $aMethodIds = null;
        if (count($data['gate']) > 1) {
            $iGateType = Method::TYPE_FREE;
            $aMethodIds = Method::whereIn('identity', $data['gate'])->where('gt_id', $iGameTypeId)->lists('id')->toArray();
        } else {
            if ($oGate = Method::where('identity', '=', $data['gate'])->where('gt_id', $iGameTypeId)->first()) {
                $iGateType = $oGate->type;
                $aMethodIds = [$oGate->id];
                if (in_array($oGate->id, Method::$aRankMethodIds)) {
                    $aRankGateTypes = array_flip(Method::$aRankMethodIds);
                    $iGateType = $aRankGateTypes[$oGate->id];
                }
            }
        }
        if (is_null($iGateType) || !$aMethodIds) {
            $sResponseCode = -207;
            return $this->renderData(['coding' => $sResponseCode, 'msg' => __(Config::get('custom-code.' . $sResponseCode)), 'data' => []]);
        }
        $sMethodIds = implode(',', $aMethodIds);
        $aRankMethods = Method::getRankMethods($iGameTypeId);
        $aRankMethods = array_flip($aRankMethods);
        // pr($data['gate']);
        // pr($aMethodIds);
        // pr($aRankMethods);
        // pr(array_intersect($aMethodIds, $aRankMethods));
        // exit;
        $sProgramSn = generateSerialNumber();
        $aProgramData = [
//            'bp_id' => $this->Customer->id,
//            'bp_name' => $this->Customer->name,
            'sn' => $sProgramSn,
            'gt_id' => $iGameTypeId,
            'game_type' => $sGameType,
            'user_id' => $this->User->id,
            'username' => $this->User->username,
            'account_id' => $this->Account->id,
            'is_tester' => $this->User->is_tester,
            'is_rank' => count(array_intersect($aMethodIds, $aRankMethods)) > 0 ? 1 : 0,
            // 'forefather_user_ids' => $forefatherIds,
            'first_played_at' => $oFirstGame->played_at,
            'first_game_bn' => $oFirstGame->bn,
            'multiple' => $data['multiple'],
            'bet_num' => $data['bet_num'],
            'game_num' => count($aGameBns),
            'amount' => $data['amount'],
            'gate_type' => $iGateType,
            'gate' => implode(',', $data['gate']),
            'method_id' => $sMethodIds,
            'bet_content' => json_encode($data),
            'coefficient' => 1.000,
            'bought_at' => Carbon::now()->toDateTimeString()
        ];
        // pr($aProgramData);exit;
        $oProgram = new Program($aProgramData);
        // 注单数据验证
        if (!$oProgram->verifyBetData($iGameTypeId, $data) || !$oProgram->rebuildBetData($aProgramData, $data, $aProgramGameExtraData, $aBetOddsForDisplay, $aFailedDesc)) {
            $sResponseCode = -204;
            return $this->renderData(['coding' => $sResponseCode, 'msg' => __(Config::get('custom-code.' . $sResponseCode)), 'data' => []]);
        }
        // $aProgramData['bet_odds_display'] = json_encode($aBetOddsForDisplay);
        // pr($aProgramData);
        // pr($data);
        // pr($aProgramGameExtraData);
        // pr($aBetOddsForDisplay);
        // exit;
        // pr($iWayTypeId);exit;
        DB::beginTransaction();

        //插入注单数据
        $oProgram->fill($aProgramData);
        $aGates = $oProgram->getGates($data['gate']);
        // $aBills = $oProgram->createBills($data['games'], $aGates, $data['dan']);
        // pr(json_encode($aBills));exit;
        $bSucc = $oProgram->save(Program::$rules);
        $aBillBetGames = [];
        $aGameBns = array_keys($data['games']);
        $aGameBnMaps = Game::whereIn('bn', $aGameBns)->get()->pluck('bn', 'id')->toArray();
        // empty($this->accountLocker) or Account::unLock($this->User->account_id, $this->accountLocker, false);
        // empty($this->walletLocker) or Wallet::unLock($this->Customer->wallet_id, $this->walletLocker, false);
        if (!$bSucc || !$oProgram->createBills($iGameTypeId, $data['gate'], $data['games'], $aGates, $data['dan'], $aGameBnMaps)) {
            DB::rollback();
            $sResponseCode = -209;
            $aReturnData = [
                'coding' => $sResponseCode,
                'msg' => __(Config::get('custom-code.' . $sResponseCode)),
                'data' => ['error' => $oProgram->getValidationErrorString()]
            ];
            return $this->renderData($aReturnData);
        }
        // pr($aBillBetGames);exit;
        if (!$bSucc = $this->createTransactions($oProgram, $iReturn)) {
            DB::rollback();
            $aReturnData = ['coding' => $iReturn, 'msg' => '系统繁忙', 'data' => []];
            return $this->renderData($aReturnData);
        }

        // pr($oProgram->toArray());
        // pr($aProgramGameExtraData);exit;
        $oProgram->games()->attach($aProgramGameExtraData);
        DB::commit();
        $iDelaySeconds = Config::get('custom-sysconfig.ticket-checking-seconds', 5 * 60);
        // TODO for test
        // $iDelaySeconds = 0;
        BaseTask::addTask('PrintTicket', ['program_id' => $oProgram->id], 'ticket', $iDelaySeconds);
        $aReturnData = [
            'coding' => 1,
            'msg' => '投注成功',
            'data' => []
        ];
        return $this->renderData($aReturnData);
    }

    private function checkCustomerTurnoverLimitForRiskGame() {

    }

    // 判断所选赛事投注时间是否合法
    private function checkBetTimeLegal($oGame) {
        // 投注的赛事中有已截止销售的赛事
        if ($oGame->sale_stop_at < Carbon::now()->toDateTimeString()) return false;
        $aCustomSaleStopTimes = CustomSaleStopTime::getLatestSaleStopRules($oGame->gt_id);
        $iSaleStopBeforeMinutes = Config::get('custom-sysconfig.sale-stop-minutes', 5);
        $iAllowBetHour = 0;
        $oNow = Carbon::now();
        $sToday = $oNow->toDateString();
        $iCurHour = $oNow->hour;
        if (array_key_exists($sToday, $aCustomSaleStopTimes)) {
            $iAllowBetHour = $aCustomSaleStopTimes[$sToday];
        } else if (in_array(Carbon::now()->subHour()->dayOfWeek, [Carbon::SATURDAY, Carbon::SUNDAY])) {
            $iAllowBetHour = 1;
        }

        $sSaleStopAt = Carbon::today()->hour(24 + $iAllowBetHour)->subMinutes($iSaleStopBeforeMinutes)->toDateTimeString();

        // 所选第一场赛事不在可投注时间范围内
        if (($iCurHour < 9 && $iCurHour >= $iAllowBetHour) || $oNow->toDateTimeString() > min($sSaleStopAt, $oGame->sale_stop_at)) {
            return false;
        }
        return true;
    }

    private function createTransactions($oProgram, & $iReturn) {
        $bSucc = true;
        $oBills = $oProgram->bills;
        foreach ($oBills as $oBill) {
            // $oGames = Game::whereIn('bn', $aBillBetGames[$oBill->sn])->get();
            //扣减用户金额,记录账变
            $aAttributes = [
                'client_ip' => Tool::getClientIp(),
                'proxy_ip' => Tool::getProxyIp(),
                'coefficient' => 1.000,
                'bp_id' => $oBill->bp_id ? $oBill->bp_id : 0,
                'bp_name' => $oBill->bp_name ? $oBill->bp_name : '',
                'gt_id' => $oBill->gt_id,
                'method_id' => $oBill->method_id,
                'way_id' => $oBill->gate_type,
                'bill_id' => $oBill->id,
                'bill_sn' => $oBill->sn,
            ];
//            if ($this->Customer->is_mortgage) {
//                $iReturn = PointJournal::addPointJournal($this->Customer, $this->User, $this->Wallet, PointType::TYPE_BET, $oBill->amount, $aAttributes);
//                if ($iReturn != PointJournal::ERRNO_CREATE_SUCCESSFUL) {
//                    $bSucc = false;
//                    break;
//                }
//            }
            $iReturn = Transaction::addTransaction($this->User, $this->Account, TransactionType::TYPE_BET, $oBill->amount, $aAttributes);
            // pr($iReturn);exit;
            if ($iReturn != Transaction::ERRNO_CREATE_SUCCESSFUL) {
                $bSucc = false;
                break;
            }
        }
        return $bSucc;
    }

    protected function lockAccount() {
        $oAccount = Account::lock($this->User->account_id, $this->accountLocker);
        if (empty($oAccount)) {
            $this->writeLog($this->User->account_id . ' lock failed at ' . Carbon::now()->toDateTimeString());
            return false;
        }
        return $oAccount;
    }

    protected function lockWallet() {
        $oWallet = Wallet::lock($this->Customer->wallet_id, $this->walletLocker);
        if (empty($oWallet)) {
            $this->writeLog($this->Customer->wallet_id . ' lock failed at ' . Carbon::now()->toDateTimeString());
            return false;
        }
        return $oWallet;
    }


    /**
     * 析构
     * 1 自动解锁
     * 2 自动删除交易线程
     */
    function __destruct() {
        empty($this->accountLocker) or Account::unLock($this->User->account_id, $this->accountLocker, false);
        empty($this->walletLocker) or Wallet::unLock($this->Customer->wallet_id, $this->walletLocker, false);

        parent::__destruct();
    }

}