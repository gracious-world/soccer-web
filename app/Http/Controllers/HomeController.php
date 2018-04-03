<?php
namespace App\Http\Controllers;
use Session;
use App\Models\Fund\TransactionType;
use App\Models\AppUser\UserTransaction;
use App\Models\User\User;
use App\Models\Activity\ActivityUserBonus;
use View;

# 首页
class HomeController extends UserBaseController {

    protected $modelName = 'App\Models\AppUser\UserMessage';

    public function beforeRender() {
        parent::beforeRender();
    }

    /**
     * [getIndex 首页，输出首页需要渲染的参数]
     * @return [type] [description]
     */
    public function getIndex() {
         $this->beforeRender();
        if (!is_object($this->oUser)) {
            $this->goBack('error', '_user.missing-user');
        }
        $this->view = 'index';
        return $this->render();
    }

}
