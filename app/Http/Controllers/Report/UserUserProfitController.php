<?php
namespace App\Http\Controllers;
use Session;
use App\Models\Stat\UserProfit;
use Input;
use App\Models\User\User;

# 用户盈亏报表管理

class UserUserProfitController extends UserBaseController {

    protected $resourceView = 'userCenter.user_profit';
    protected $modelName = 'App\Models\Stat\UserProfit';
    public $resourceName = '';

    public function beforeRender() {
        parent::beforeRender();
        switch ($this->action) {
            case 'index':
                $aUserTypes = User::$userTypes;
                $this->setVars(compact('aUserTypes'));
                $this->setVars('reportName', 'profit');
                break;
            case 'commission':
                $this->setVars('reportName', 'commission');
                break;
            case 'myself':
                $this->setVars('reportName', 'myself');
                break;
        }
    }

    /**
     * [index 查询用户的盈亏报表]
     * @return [Response]          [description]
     */
    public function index() {
        // $aUserLinkGroups = UserRegisterLink::getUserLinksWithChannelGroup();
        $iUserId = Session::get('user_id');
        if (!$iUserId)
            return $this->goBack('error', __('_basic.no-rights'));
        if (Session::get('is_agent')) {
            $this->params['parent_user_id'] = $iUserId;
            if (!key_exists('date_from', $this->params) && !key_exists('date_to', $this->params)) {
                $this->params['date_from'] = $this->params['date_to'] = date('Y-m-d');
            }
//            if (!array_get($this->params, 'date_from') && !array_get($this->params, 'date_to')) {
//                $this->params['date_from'] = $this->params['date_to'] = date('Y-m-d');
//            }
            if ($this->dataDisplayDays){
                $dMinDate = date('Y-m-d',strtotime("-$this->dataDisplayDays days"));
                $this->params['date_from'] >= $dMinDate or $this->params['date_from'] = $dMinDate;
                if (isset($this->params['date']) && $this->params['date'] < $dMinDate){
                    $this->params['date'] = $dMinDate;
                }
                unset($dMinDate);
            }
//            if (array_get($this->params, 'date_from') && array_get($this->params, 'date_to') && array_get($this->params, 'date_from') > array_get($this->params, 'date_to')) {
//                return $this->goBack('error', '开始日期不能大于结束日期');
//            }
            $oAgentSumInfo = UserProfit::getAgentSumInfo($this->params['date_from'], $this->params['date_to'], $iUserId, array_get($this->params, 'username'));
            $this->setVars(compact('oAgentSumInfo'));
        } else {
            $this->params['user_id'] = $iUserId;
        }
        return parent::index();
    }

    public function myself() {
        $iUserId = Session::get('user_id');
        if (!$iUserId)
            return $this->goBack('error', __('_basic.no-rights'));
        $this->params['user_id'] = $iUserId;
//        pr($this->params);
        return parent::index();
    }

    public function indexQuery() {
        $this->compileParams();
        $aConditions = & $this->makeSearchConditions();
//        pr($aConditions);
        $oQuery = $aConditions ? $this->model->doWhere($aConditions) : $this->model;
        if (!array_get($this->params, 'username')) {
            $oQuery = $oQuery->orWhere(function($query) {
                $query = $query->orWhere('user_id', '=', Session::get('user_id'));
                !array_get($this->params, 'date_from') or $query = $query->where('date', '>=', array_get($this->params, 'date_from'));
                !array_get($this->params, 'date_to') or $query = $query->where('date', '<=', array_get($this->params, 'date_to'));
            });
        }

        // TODO 查询软删除的记录, 以后需要调整到Model层
        $bWithTrashed = trim(Input::get('_withTrashed', 0));
        // pr($bWithTrashed);exit;
        if ($bWithTrashed)
            $oQuery = $oQuery->withTrashed();
        if ($sGroupByColumn = Input::get('group_by')) {
            $oQuery = $this->model->doGroupBy($oQuery, [$sGroupByColumn]);
        }
        // 获取排序条件
        $aOrderSet = [];
        if ($sOorderColumn = Input::get('sort_up', Input::get('sort_down'))) {
            $sDirection = Input::get('sort_up') ? 'asc' : 'desc';
            $aOrderSet[$sOorderColumn] = $sDirection;
        }
        $oQuery = $this->model->doOrderBy($oQuery, $aOrderSet);
        return $oQuery;
    }

    public function commission() {
//        $this->params['date'] = date('Y-m-d');
        return $this->index();
    }

    // public function create($id = null)
    // {
    //     if ( ! $bIsAgent = Session::get('is_agent')) {
    //         return $this->goBack('error', __('_basic.no-rights', $this->langVars));
    //     }
    //     return parent::create($id);
    // }

    protected function compileParams(){
            if (!key_exists('date_from', $this->params) && !key_exists('date_to', $this->params)) {
                $this->params['date_from'] = $this->params['date_to'] = date('Y-m-d');
            }
//            if (!array_get($this->params, 'date_from') && !array_get($this->params, 'date_to')) {
//                $this->params['date_from'] = $this->params['date_to'] = date('Y-m-d');
//            }
            if ($this->dataDisplayDays){
                $dMinDate = date('Y-m-d',strtotime("-$this->dataDisplayDays days"));
                $this->params['date_from'] >= $dMinDate or $this->params['date_from'] = $dMinDate;
                if (isset($this->params['date']) && $this->params['date'] < $dMinDate){
                    $this->params['date'] = $dMinDate;
                }
                unset($dMinDate);
            }
    }

}
