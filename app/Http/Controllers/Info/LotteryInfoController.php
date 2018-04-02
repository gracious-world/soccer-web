<?php

/**
 * 用于其他信息的控制器
 *
 * @author winter
 */
class InfoController extends UserBaseController {

    protected $modelName = '';
    
    public function getLotteryInfoForIndex($iLotteryId){
        $sOnSaleIssueInfo = Issue::getOnSaleIssueInfo($iLotteryId);
        list($sOnSaleIssue, $iEndTime, $iCycle) = explode(',',$sOnSaleIssueInfo);
//        $oIssue = Issue::getIssue($iLotteryId, $sOnSaleIssue);
        $aLastNumber = Issue::getLastWnNumber($iLotteryId);
        $oLottery = Lottery::find($iLotteryId);
//        $sName = $oLottery->friendly_name;
        $data = [
            'lottery' => $oLottery->friendly_name,
            'identifier' => $oLottery->identifier,
            'lastNumber' => $aLastNumber,
            'cycle' => $iCycle,
            'endTime' => $iEndTime,
            'time' => time(),
        ];
//        pr($data);
        echo json_encode($data);
        exit();
    }

}
