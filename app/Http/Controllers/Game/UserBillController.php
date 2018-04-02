<?php
namespace App\Http\Controllers;

use App\Models\BaseTask;
use App\Models\Basic\BusinessPartner;
use App\Models\Basic\GameType;
use App\Models\Game\Game;
use App\Models\Game\Method;
use App\Models\Game\Odd;
use App\Models\Game\WayOdd;
use App\Models\Game\Way;
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

class UserBillController  extends UserBaseController
{
    protected $resourceView = 'userCenter.bill';
    protected $modelName = 'App\Models\Bet\Bill';
    protected $accountLocker;
    protected $walletLocker;
    protected $User;
    protected $Account;
    protected $Wallet;

    // protected static $pagesize = 1;

    // protected function beforeRender() {
    //     $oGameType  = new GameType;
    //     $aGameTypes = $oGameType->getValueListArray(null, [], [], true);
    //     $aStatus    = Bill::$status;
    //     $aGateTypes = Method::$types;
    //     $iBetType   = $this->request->session()->get('iBetType', 0);
    //     $this->setVars(compact('aGameTypes', 'aStatus', 'aGateTypes', 'iBetType'));
    //     switch ($this->action) {
    //         case 'view':
    //         case 'ticketDetail':
    //             $oBill           = $this->viewVars['data'];
    //             $aBetContent     = json_decode($oBill->bet_content, true);
    //             $aBetOddsDisplay = json_decode($oBill->bet_odds_display, true);
    //             $oRelatedGames   = $oBill->games()->get();
    //             $aWayOddRevertMap = WayOdd::getWayOddsRevertMapByGameTypeFromDB($oBill->gt_id);
    //             $this->setVars(compact('oRelatedGames', 'aBetContent', 'aBetOddsDisplay', 'aWayOddRevertMap'));
    //             break;
    //     }
    //     $sPageName = 'bills';
    //     $this->setVars(compact('sPageName'));
    //     // pr($aBetOddsDisplay);exit;
    //     parent::beforeRender();
    // }

    // ---------------------------以下功能转义到UserProgramController中--------------------------
    // public function index() {
    //     $this->params = $this->request->except(['page', 'sort_up', 'sort_down']);
    //     $this->params['user_id'] = $this->request->session()->get('user_id');
    //     return parent::index();
    // }

    // public function ticketDetail($id) {
    //     if (!$data = Bill::find($id)) {
    //         return $this->goBack('error', __('_bill.no-bill'));
    //     }
    //     $this->setVars(compact('data'));
    //     // pr($oBill->bet_content);exit;
    //     return $this->render();
    // }



    // /**
    //  * 下单接口
    //  *'identity': 商户唯一标识,
    //  *     'game_type': 游戏类型football/basketball,
    //  *     'data' => [[
    //  *         'sn'                  => '商户平台的注单流水号',
    //  *         'gt_id'               => '游戏类型id',
    //  *         'user_id'             => '用户id',
    //  *         'username'            => '用户名',
    //  *         'is_tester'           => '是否测试用户',
    //  *         'forefather_user_ids' => '用户上级链id',
    //  *         'first_played_at'     => '该注单首场赛事开赛时间',
    //  *         'first_game_bn'       => '该注单首场赛事序号',
    //  *         'multiple'            => '倍数',
    //  *         'bet_num'             => '注数',
    //  *         'amount'              => '投注金额',
    //  *         'bet_content'         => '投注内容',
    //  *         'coefficient'         => '货币模式',
    //  *     ]],
    //  * }
    //  * bet_content: {
    //  *     "gate": ["3x4"],
    //  *     "games": {"20160204YX006": ["h_1.40", "d_2.15"], "20160204YX007": ["hh_1.40", "0100_6.15"], "20160204YX008": ["ch_1.40", "s0_4.15"]},
    //  *     "dan": ["20160204YX006"],
    //  *     "bets_num": 16,
    //  *     "amount": 32,
    //  *     "multiple": 1,
    //  *     "max_gate":8
    // * }
    //  */
    // public function betting($sGameType = 'football') {
    //     // pr($this->request->method());exit;
    //     $data          = $this->params;
    //     // pr($data);exit;
    //     $userId        = $this->request->session()->get('user_id');
    //     $userName      = $this->request->session()->get('username');
    //     // 没有投注内容
    //     if(!$data || empty($data)) {
    //         $sResponseCode = -204;
    //         return $this->renderData(['coding' => $sResponseCode, 'msg' => __(Config::get('custom-code.' . $sResponseCode)), 'data' => []]);
    //     }
    //     if (!is_numeric($data['amount']) || $data['amount'] <= 0) {
    //         $sResponseCode = -303;
    //         return $this->renderData(['coding' => $sResponseCode, 'msg' => __(Config::get('custom-code.' . $sResponseCode)), 'data' => []]);
    //     }
    //     // 非法商户
    //     if (! $this->Customer = BusinessPartner::getActivateBusinessParnter($this->request->session()->get('bp_identity'))) {
    //         $sResponseCode = -205;
    //         return $this->renderData(['coding' => $sResponseCode, 'msg' => __(Config::get('custom-code.' . $sResponseCode)), 'data' => []]);
    //     }
    //     if (!$this->Wallet = $this->lockWallet()) {
    //         $sResponseCode = Wallet::ERRNO_LOCK_FAILED;
    //         return $this->renderData(['coding' => $sResponseCode, 'msg' => __(Config::get('custom-code.' . $sResponseCode)), 'data' => []]);
    //     }
    //     // 合作商户荷包余额不足
    //     if ($this->Customer->wallet->available < $data['amount']) {
    //         $sResponseCode = -401;
    //         return $this->renderData(['coding' => $sResponseCode, 'msg' => __(Config::get('custom-code.' . $sResponseCode)), 'data' => []]);
    //     }
    //     // 非法赛事类型
    //     if (!$oGameType = GameType::where('en_name', $sGameType)->first()) {
    //         $sResponseCode = -206;
    //         return $this->renderData(['coding' => $sResponseCode, 'msg' => __(Config::get('custom-code.' . $sResponseCode)), 'data' => []]);
    //     }
    //     $aGameBns = array_keys($data['games']); // 场次序号
    //     //获取该注首场赛事
    //     $oGames = Game::getGamesByBns($aGameBns);
    //     $oFirstGame = $oGames->first();
    //     // 投注的赛事中有已截止销售的赛事
    //     if ($oFirstGame->sale_stop_at < Carbon::now()->toDateTimeString()) {
    //         $sResponseCode = -207;
    //         return $this->renderData(['coding' => $sResponseCode, 'msg' => __(Config::get('custom-code.' . $sResponseCode)), 'data' => []]);
    //     }
    //     $bIsWeekend = Carbon::now()->isWeekend();
    //     $iAllowBetEndTime = intval($bIsWeekend);
    //     $iCurHour = Carbon::now()->hour;
    //     $sSaleStopAt = ($bIsWeekend ? Carbon::today()->hour(24) : Carbon::today()->hour(25));
    //     // pr($iCurHour);
    //     // pr($iAllowBetEndTime);
    //     // pr($oFirstGame->sale_stop_at);
    //     // pr($sSaleStopAt);
    //     // pr(min($sSaleStopAt, $oFirstGame->sale_stop_at));
    //     // exit;
    //     // 所选第一场赛事不在可投注时间范围内
    //     if (($iCurHour <= 9 && $iCurHour >= $iAllowBetEndTime) || Carbon::now() > min($sSaleStopAt, $oFirstGame->sale_stop_at)) {
    //         $sResponseCode = -207;
    //         return $this->renderData(['coding' => $sResponseCode, 'msg' => __(Config::get('custom-code.' . $sResponseCode)), 'data' => []]);
    //     }
    //     //判断用户当前金额是否足够下单
    //     $this->User = UserUser::find($userId);
    //     //锁定用户账户
    //     if (!$this->Account = $this->lockAccount()) {
    //         $sResponseCode = -302;
    //         return $this->renderData(['coding' => $sResponseCode, 'msg' => __(Config::get('custom-code.' . $sResponseCode)), 'data' => []]);
    //     }
    //     // pr($this->Account->toArray());exit;
    //     if($this->Account->available < $data['amount']) {
    //         $sResponseCode = -301;
    //         return $this->renderData(['coding' => $sResponseCode, 'msg' => __(Config::get('custom-code.' . $sResponseCode)), 'data' => []]);
    //     }
    //     $iGameTypeId = $oGameType->id;
    //     if(!isset($data['dan'])) {
    //         $data['dan'] = [];
    //     }
    //     // pr($data);exit;
    //     if(count($data['gate']) > 1) {
    //         $iWayTypeId = Method::TYPE_FREE;
    //         $aMethodIds = Method::whereIn('identity', $data['gate'])->lists('id')->toArray();
    //     }else{
    //         if ($oGate = Method::where('identity', '=', $data['gate'])->first()) {
    //             $iWayTypeId = $oGate->type;
    //             $aMethodIds = [$oGate->id];
    //         } else {
    //             $iWayTypeId = null;
    //             $aMethodIds = null;
    //         }
    //     }
    //     if (is_null($iWayTypeId) || !$aMethodIds) {
    //         $sResponseCode = -207;
    //         return $this->renderData(['coding' => $sResponseCode, 'msg' => __(Config::get('custom-code.' . $sResponseCode)), 'data' => []]);
    //     }
    //     $sMethodIds = implode(',', $aMethodIds);
    //     $aRankMethodIds = Method::getRankMethods($iGameTypeId);
    //     // pr($data['gate']);
    //     // pr($aMethodIds);exit;
    //     $sProgramSn = generateSerialNumber();
    //     $aProgramData = [
    //         'bp_id'               => $this->Customer->id,
    //         'bp_name'             => $this->Customer->name,
    //         'sn'                  => $sProgramSn,
    //         'gt_id'               => $iGameTypeId,
    //         'game_type'           => $sGameType,
    //         'user_id'             => $this->User->id,
    //         'username'            => $this->User->username,
    //         'account_id'          => $this->Account->id,
    //         'is_tester'           => $this->User->is_tester,
    //         'is_rank'             => count(array_intersect($aMethodIds, $aRankMethodIds)) > 0 ? 1 : 0,
    //         // 'forefather_user_ids' => $forefatherIds,
    //         'first_played_at'     => $oFirstGame->played_at,
    //         'first_game_bn'       => $oFirstGame->bn,
    //         'multiple'            => $data['multiple'],
    //         'bet_num'             => $data['bet_num'],
    //         'game_num'            => count($aGameBns),
    //         'amount'              => $data['amount'],
    //         'gate_type'           => $iWayTypeId,
    //         'gate'                => implode(',', $data['gate']),
    //         'method_id'           => $sMethodIds,
    //         'bet_content'         => json_encode($data),
    //         'coefficient'         => 1.000,
    //         'bought_at'           => Carbon::now()->toDateTimeString()
    //     ];
    //     $oProgram = new Program($aProgramData);
    //     // 注单数据验证
    //     if (!$oProgram->verifyBetData($iGameTypeId, $data) || !$oProgram->rebuildBetData($this->Customer, $aProgramData, $data, $aProgramGameExtraData, $aBetOddsForDisplay, $aFailedDesc)) {
    //         $sResponseCode = -204;
    //         return $this->renderData(['coding' => $sResponseCode, 'msg' => __(Config::get('custom-code.' . $sResponseCode)), 'data' => []]);
    //     }
    //     // $aProgramData['bet_odds_display'] = json_encode($aBetOddsForDisplay);
    //     // pr($aProgramData);
    //     // pr($data);
    //     // pr($aProgramGameExtraData);
    //     // pr($aBetOddsForDisplay);
    //     // exit;
    //     // pr($iWayTypeId);exit;
    //     DB::beginTransaction();

    //     //插入注单数据
    //     $oProgram->fill($aProgramData);
    //     $aGates = $oProgram->getGates($data['gate']);
    //     // $aBills = $oProgram->createBills($data['games'], $aGates, $data['dan']);
    //     // pr(json_encode($aBills));exit;
    //     $bSucc = $oProgram->save(Program::$rules);
    //     $aBillBetGames = [];
    //     $aGameBns = array_keys($data['games']);
    //     $aGameBnMaps = Game::whereIn('bn', $aGameBns)->get()->pluck('bn', 'id')->toArray();
    //     if (!$bSucc || !$oProgram->createBills($iGameTypeId, $data['gate'], $data['games'], $aGates, $data['dan'], $aGameBnMaps)) {
    //         DB::rollback();
    //         $sResponseCode = -209;
    //         $aReturnData = [
    //             'coding' => $sResponseCode,
    //             'msg'    => __(Config::get('custom-code.' . $sResponseCode)),
    //             'data'   => ['error' => $oProgram->getValidationErrorString()]
    //         ];
    //         return $this->renderData($aReturnData);
    //     }
    //     // pr($aBillBetGames);exit;
    //     if (!$bSucc = $this->createTransactions($oProgram, $iReturn)) {
    //         DB::rollback();
    //         $aReturnData = ['coding' => $iReturn, 'msg' => '系统繁忙', 'data' => []];
    //         return $this->renderData($aReturnData);
    //     }

    //     // pr($oProgram->toArray());
    //     // pr($aProgramGameExtraData);exit;
    //     $oProgram->games()->attach($aProgramGameExtraData);
    //     DB::commit();
    //     $iDelaySeconds =  Config::get('custom-sysconfig.ticket-checking-seconds', 5*60);
    //     // TODO for test
    //     // $iDelaySeconds = 0;
    //     BaseTask::addTask('PrintTicket', ['program_id' => $oProgram->id], 'ticket', $iDelaySeconds);
    //     $aReturnData = [
    //         'coding' => 1,
    //         'msg' => '投注成功',
    //         'data' => []
    //     ];
    //     return $this->renderData($aReturnData);
    // }

    // private function createTransactions($oProgram, & $iReturn) {
    //     $bSucc = true;
    //     $oBills = $oProgram->bills;
    //     foreach ($oBills as $oBill) {
    //         // $oGames = Game::whereIn('bn', $aBillBetGames[$oBill->sn])->get();
    //         //扣减用户金额,记录账变
    //         $aAttributes = [
    //             'client_ip'   => Tool::getClientIp(),
    //             'proxy_ip'    => Tool::getProxyIp(),
    //             'coefficient' => 1.000,
    //             'bp_id'       => $oBill->bp_id,
    //             'bp_name'     => $oBill->bp_name,
    //             'gt_id'       => $oBill->gt_id,
    //             'method_id'   => $oBill->method_id,
    //             'way_id'      => $oBill->gate_type,
    //             'bill_id'     => $oBill->id,
    //             'bill_sn'     => $oBill->sn,
    //         ];
    //         $iReturn = PointJournal::addPointJournal($this->Customer, $this->User, $this->Wallet, PointType::TYPE_BET, $oBill->amount, $aAttributes);
    //         if($iReturn != PointJournal::ERRNO_CREATE_SUCCESSFUL) {
    //             $bSucc = false;
    //             break;
    //         }
    //         $iReturn = Transaction::addTransaction($this->User, $this->Account, TransactionType::TYPE_BET, $oBill->amount, $aAttributes);
    //         // pr($iReturn);exit;
    //         if($iReturn != Transaction::ERRNO_CREATE_SUCCESSFUL) {
    //             $bSucc = false;
    //             break;
    //         }
    //     }
    //     return $bSucc;
    // }

    // protected function lockAccount(){
    //     $oAccount = Account::lock($this->User->account_id, $this->accountLocker);
    //     if (empty($oAccount)) {
    //         $this->writeLog('lock-fail');
    //         return false;
    //     }
    //     return $oAccount;
    // }

    // protected function lockWallet() {
    //     $oWallet = Wallet::lock($this->Customer->wallet_id, $this->walletLocker);
    //     if (empty($oWallet)) {
    //         $this->writeLog('lock-fail');
    //         return false;
    //     }
    //     return $oWallet;
    // }


    // /**
    //  * 析构
    //  * 1 自动解锁
    //  * 2 自动删除交易线程
    //  */
    // function __destruct() {
    //     empty($this->accountLocker) or Account::unLock($this->User->account_id, $this->accountLocker, false);
    //     empty($this->walletLocker) or Wallet::unLock($this->Customer->wallet_id, $this->walletLocker, false);

    //     parent::__destruct();
    // }

}