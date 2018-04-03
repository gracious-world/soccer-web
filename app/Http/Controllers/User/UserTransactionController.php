<?php

namespace App\Http\Controllers;

use App\Models\Basic\GameType;
use App\Models\Fund\TransactionType;
use App\Models\Fund\Transaction;
use App\Models\Game\Method;

use Coefficient;
use DB;
use App\Models\AppUser\UserTransaction;
use Illuminate\Support\Facades\Session;
use App\Models\Series\SeriesWay;
use App\Models\User\Role;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\App;

class UserTransactionController extends UserBaseController {

    protected $resourceView = 'userCenter.transaction';
    protected $modelName = 'App\Models\AppUser\UserTransaction';
    private static $aTransactionTypeMyDeposit    = [1, 18];
    private static $aTransactionTypeMyBonus      = [13, 14];
    private static $aTransactionTypeMyAdditional = [20, 21, 23];
    private static $aTransactionTypeMyWithdraw   = [2, 19];
    private static $aTransactionTypeMyTransfer   = [3, 4];
    private static $aTransactionTypeDiscountCard = [33, 34];

    protected function beforeRender() {
        parent::beforeRender();

        $aCoefficients = Coefficient::$coefficients;
        $aSeriesWays = & SeriesWay::getTitleList();
        switch ($this->action) {
            case 'index':
                $this->setVars('reportName', 'transaction');
                break;
            case 'myBonus': // 返点
                $this->action = 'index';
                $this->setVars('reportName', 'bonus');
                $this->setVars('transactionType', self::$aTransactionTypeMyBonus);
                break;
            case 'myAdditional': // 加奖
                $this->action = 'index';
                $this->setVars('reportName', 'additional');
                $this->setVars('transactionType', self::$aTransactionTypeMyAdditional);
                break;
            case 'myDeposit':
                $this->action = 'index';
                $this->setVars('reportName', 'deposit');
                $this->setVars('transactionType', self::$aTransactionTypeMyDeposit);
                break;
            case 'myWithdraw':
                $this->action = 'index';
                $this->setVars('reportName', 'withdraw');
                $this->setVars('transactionType', self::$aTransactionTypeMyWithdraw);
                break;
            case 'myTransfer':
                $this->action = 'index';
                $this->setVars('reportName', 'transfer');
                $this->setVars('transactionType', self::$aTransactionTypeMyTransfer);
                break;
            case 'view':
                // $bHasSumRow = 1;
                // $aNeedSumColumns = ['amount', 'transaction_charge', 'transaction_amount'];
                // $aSum = $this->getColumnSum($aNeedSumColumns);
//                $aSum = $this->getSumData(['amount'], true);
                break;
            case 'discountCard':
                $this->action = 'index';
                $this->setVars('reportName', 'discountCard');
                $this->setVars('transactionType', self::$aTransactionTypeDiscountCard);
            break;
        }
        $aTransactionTypes = TransactionType::getAllTransactionTypes();
        if (Session::get('is_top_agent')){
            $iMainRole = Role::TOP_AGENT;
        }
        else{
            $iMainRole = Session::get('is_agent') ? Role::AGENT : Role::PLAYER;
        }
        $aHideTransactionTypes = Config::get('hide_trans_types.' . $iMainRole);
        $aSelectorData = $this->generateSelectorData();
        // pr($aTransactionTypes);exit;
        $this->setVars(compact('aCoefficients', 'aSeriesWays', 'aTransactionTypes', 'aHideTransactionTypes', 'aSelectorData'));
    }

    /**
     * [index 自定义资金列表查询, 代理用户需要可以查询其子用户的记录]
     * @return [Response] [description]
     */
    public function index($iUserId = null) {
        $this->params = trimArray(Input::except('page', 'sort_up', 'sort_down'));
        if ($iCount = count($this->params))
            $this->generateSearchParams($this->params);
        if ($iUserId) {
            $this->params['user_id'] = $iUserId;
            $sJumpUsername = User::find($iUserId)->username;
            $this->setVars('sJumpUsername', $sJumpUsername);
        } else {
            $iLoginUserId = Session::get('user_id');
            // 如果是代理并且有username参数，则精准查找该代理下用户名为输入参数的子用户的账变列表
            // 否则，查询该代理的账变列表
            if (Session::get('is_agent') && isset($this->params['username']) && $this->params['username']) {
                // $oUser = User::find($iLoginUserId);
                // $aUsers   = $oUser->getUsersBelongsToAgent();
                // $aUserIds = array_map(function ($item){
                //     return $item['id'];
                // } , $aUsers->toArray());
                // $this->params['user_id'] = implode(',' , $aUserIds);
                //
                $oUser = UserUser::getObjectByParams(['username' => $this->params['username']]);
                $oAgent = UserUser::find($iLoginUserId);
                // $queries = DB::getQueryLog();
                // $last_query = end($queries);
                // pr($last_query);exit;
                if (is_object($oUser) && !$oAgent->isChild($oUser->id, false) && $oUser->id != Session::get('user_id')) {
                    $aReplace = ['username' => $this->params['username']];
                    return $this->goBack('error', __('_basic.not-your-user', $aReplace));
                }
            } else {
                $this->params['user_id'] = $iLoginUserId;
            }
            // $this->params['user_id'] = Session::get('user_id');
        }
        // $this->view = 'centerUser.bet.accountDetails';
//         pr($this->params);exit;
        return parent::index();
    }

    public function myBonus() {
        if (Session::get('is_player')) {
            if (!isset($this->params['type_id'])) {
                $this->params['type_id'] = implode(',', self::$aTransactionTypeMyAdditional);
            } else {
                in_array($this->params['type_id'], self::$aTransactionTypeMyAdditional) or $this->params['type_id'] = implode(',', self::$aTransactionTypeMyAdditional);
            }
        }else{
            if (!isset($this->params['type_id'])) {
                $this->params['type_id'] = implode(',', self::$aTransactionTypeMyBonus);
            } else {
                in_array($this->params['type_id'], self::$aTransactionTypeMyBonus) or $this->params['type_id'] = implode(',', self::$aTransactionTypeMyBonus);
            }
        }

        $this->params['user_id'] = Session::get('user_id');
        return parent::index();
    }

    public function myAdditional() {
        if (!isset($this->params['type_id'])) {
            $this->params['type_id'] = implode(',', self::$aTransactionTypeMyAdditional);
        } else {
            in_array($this->params['type_id'], self::$aTransactionTypeMyAdditional) or $this->params['type_id'] = implode(',', self::$aTransactionTypeMyAdditional);
        }
        $this->params['user_id'] = Session::get('user_id');
        return parent::index();
    }

    /**
     * [generateSearchParams 生成自定义查询参数]
     * @param  [Array]     & $aParams [查询参数数组的引用]
     */
    private function generateSearchParams(& $aParams) {
        if (isset($aParams['number_type']) && isset($aParams['number_value'])) {
            $aParams[$aParams['number_type']] = $aParams['number_value'];
        }
        unset($aParams['way_group_id'], $aParams['number_type'], $aParams['number_value']);
    }

    /**
     * [generateSelectorData 页面公用下拉框的生成参数]
     * @return [Array] [参数数组]
     */
    private function generateSelectorData() {
        $aSelectColumn = [
            ['name' => 'lottery_id', 'emptyDesc' => '所有游戏', 'desc' => '游戏名称：'],
            ['name' => 'way_group_id', 'emptyDesc' => '所有玩法群', 'desc' => '玩法群：'],
            ['name' => 'way_id', 'emptyDesc' => '所有玩法', 'desc' => '玩法：'],
        ];

        $aSelectorData = [
            'aSelectColumn' => $aSelectColumn,
            'sFirstNameKey' => 'name',
            'sSecondNameKey' => 'title',
            'sThirdNameKey' => 'title',
            'sDataFile' => 'series-way-groups-way-group-ways',
            'sExtraDataFile' => 'lottery-series',
            'sSelectedFirst' => trim(Input::get('lottery_id')),
            'sSelectedSecond' => trim(Input::get('way_group_id')),
            'sSelectedThird' => trim(Input::get('way_id')),
        ];
        return $aSelectorData;
    }

    public function myDeposit($iUserId = null) {

        if (!isset($this->params['type_id'])) {
            $this->params['type_id'] = implode(',', self::$aTransactionTypeMyDeposit);
        } else {
            in_array($this->params['type_id'], self::$aTransactionTypeMyDeposit) or $this->params['type_id'] = implode(',', self::$aTransactionTypeMyDeposit);
        }
        $this->params['user_id'] = Session::get('user_id');
        return parent::index();
    }

    public function myWithdraw($iUserId = null) {
        if (!isset($this->params['type_id'])) {
            $this->params['type_id'] = implode(',', self::$aTransactionTypeMyWithdraw);
        } else {
            in_array($this->params['type_id'], self::$aTransactionTypeMyWithdraw) or $this->params['type_id'] = implode(',', self::$aTransactionTypeMyWithdraw);
        }
        $this->params['user_id'] = Session::get('user_id');
        return parent::index();
    }

    public function myTransfer($iUserId = null) {
        $aTransactionTransfer = implode(',', self::$aTransactionTypeMyTransfer);
        if (!isset($this->params['type_id'])) {
            $this->params['type_id'] = $aTransactionTransfer;
        } else {
            in_array($this->params['type_id'], self::$aTransactionTypeMyTransfer) or $this->params['type_id'] = $aTransactionTransfer;
        }
        $this->params['user_id'] = Session::get('user_id');
        return parent::index();
    }

    public function miniWindow() {
//        Input::merge(['pagesize' => 5]);
        $this->setVars('datas', UserTransaction::getLatestRecords(Session::get('user_id')));
        return $this->render();
    }

    public function discountCard(){
      if (!isset($this->params['type_id'])) {
          $this->params['type_id'] = implode(',', self::$aTransactionTypeDiscountCard);
      } else {
          in_array($this->params['type_id'], self::$aTransactionTypeDiscountCard) or $this->params['type_id'] = implode(',', self::$aTransactionTypeDiscountCard);
      }
      $this->params['user_id'] = Session::get('user_id');
      return parent::index();
    }

}
