<?php
/**
 * Created by PhpStorm.
 * User: damon
 * Date: 10/28/15
 * Time: 8:56 PM
 */

class SuccessNotifyController extends UserBaseController{

    protected $resourceView = 'centerUser.recharge';

    public function index(){
        $this->view = $this->resourceView . '.rechargeSuccess';
        return $this->render();
    }
}
