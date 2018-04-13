<?php
namespace App\Http\Controllers;
use Session;
use App\Models\User\User;
# 用户盈亏报表管理

class UserDividendController extends UserBaseController {

    protected $resourceView = 'userCenter.report.dividend';
    protected $modelName = 'App\Models\Fund\Dividend';
    public $resourceName = '';

    public function beforeRender() {
        parent::beforeRender();
        switch ($this->action) {
            case 'index':
                $aUserTypes = User::$userTypes;
//                $this->setVars(compact('aUserTypes'));
                $this->setVars('reportName', 'dividend');
                break;
        }
    }

    public function index() {
        $this->params['user_id'] = Session::get('user_id');
        !$this->dataDisplayDays or $this->params['created_at'] = date('Y-m-d',strtotime("-$this->dataDisplayDays days"));
        return parent::index();
    }

}
