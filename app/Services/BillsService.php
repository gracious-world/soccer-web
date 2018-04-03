<?php
/**
 * 赛事相关接口
 * User: damon
 * Date: 2/4/16
 * Time: 3:20 PM
 */

namespace App\Services;

use App\Services\BaseService;

class BillsService extends BaseService
{
    protected $logFile = 'games';
    /**
     * 获取赛事数据
     * @param StringTool $startTime 开始时间
     * @param StringTool $endTime 结束时间
     * @param StringTool $isHot 是否热门
     * @param StringTool $isSingle 是否单关
     * @return array []
     */
    public function addBills($data){
        return $this->_doApi('data-api','bills',['game_type'=>'football'],$data);
    }

    public function getBills($sn){
        return $this->_doApi('data-api','bill-results',['game_type'=>'football'],['sn'=>$sn]);
    }
}
