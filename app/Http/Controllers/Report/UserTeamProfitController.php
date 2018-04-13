<?php
namespace App\Http\Controllers;
use Session;
use App\Models\Stat\TeamProfit;
use App\Models\User\User;
use Illuminate\Support\Facades\Route;
use App\Models\User\UserUser;
use Carbon;
use App\Models\Fund\Dividend;
use Date;
use DB;

/**
 * 团队盈亏报表
 */
class UserTeamProfitController extends UserBaseController {
    protected $resourceView = 'userCenter.team_profit';
    protected $modelName = 'App\Models\Stat\UserProfit';
    public $resourceName = '';
    protected $customViews = [
        'index', 'self',
    ];
    private $useParentIndexQuery = true;


    public function beforeRender() {
        parent::beforeRender();
        switch ($this->action) {
            case 'index':
                $aUserTypes = User::$userTypes;
                $this->setVars(compact('aUserTypes'));
                $this->setVars('reportName', 'team-profit');
                break;
            case 'self':
                $this->setVars('reportName', 'self');
                break;
            case 'commission':
                $this->setVars('reportName', 'team-commission');
                break;
        }
    }

    /**
     * [index 查询用户的盈亏报表]
     * @return [Response]          [description]
     */
    public function index() {
        $iUserId = Session::get('user_id');
        $sUsername = Session::get('username');
        if (!$iUserId) {
            return $this->goBack('error', __('_basic.no-rights'));
        }
        $this->params['parent_user_id'] = $iUserId;
        if (!key_exists('date_from', $this->params) && !key_exists('date_to', $this->params)) {
            $this->params['date_from'] = $this->params['date_to'] = date('Y-m-d');
        }
//        if (!array_get($this->params, 'date_from') && !array_get($this->params, 'date_to')) {
//            $this->params['date_from'] = $this->params['date_to'] = date('Y-m-d');
//        }
        if ($this->dataDisplayDays){
            $dMinDate = date('Y-m-d',strtotime("-$this->dataDisplayDays days"));
            $this->params['date_from'] >= $dMinDate or $this->params['date_from'] = $dMinDate;
            unset($dMinDate);
        }

//        if (array_get($this->params, 'date_from') && array_get($this->params, 'date_to') && array_get($this->params, 'date_from') > array_get($this->params, 'date_to')) {
//            return $this->goBack('error', '开始日期不能大于结束日期');
//        }
        $sUsernameFromParam = array_get($this->params, 'username');
        if (!$sUsernameFromParam) {
            $sUsernameFromParam = $sUsername;
        }

        $bParent = $sUsernameFromParam == $sUsername;
        $oAgentSumInfo = TeamProfit::getAgentSumInfo($this->params['date_from'], $this->params['date_to'], $bParent ? null : $iUserId, $sUsernameFromParam);
        $this->setVars(compact('oAgentSumInfo'));

        return parent::index();
    }

    public function commission() {
        return $this->index();
    }

    public function self(){
    $iUserId = Session::get('user_id');
    $this->params['user_id'] = $iUserId;
    return parent::index();
}

}
