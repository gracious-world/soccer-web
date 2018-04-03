<?php
namespace App\Http\Controllers;

use App\Models\Game\LotteryNotice;
use Carbon;

class UserLotteryNoticeController  extends UserBaseController
{
    protected $resourceView = 'userCenter.notice';
    protected $modelName = 'App\Models\Game\LotteryNotice';

    protected function beforeRender() {

    }

    public function latest() {
        return LotteryNotice::complexWhere(['updated_at' => ['>=', Carbon::now()->toDateString()]])->get()->toJson();
    }



}