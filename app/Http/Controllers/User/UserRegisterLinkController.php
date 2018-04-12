<?php
namespace App\Http\Controllers;
use App\Models\User\UserAccountQuota;
use Illuminate\Support\Facades\Session;
use App\Models\User\UserUser;
use App\Models\User\UserRegisterLink;
use App\Models\User\User;
use Config;
use DB;
use App\Models\User\UserPercentSet;
use App\Models\Lotteries\PercentWay;

# 链接开户管理
class UserRegisterLinkController extends UserBaseController {

    protected $resourceView = 'userCenter.link';
    protected $modelName = 'App\Models\User\UserRegisterLink';
    public $resourceName = '';

    const QQ_NUM_MIN = 50000;
    const QQ_NUM_MAX = 99999999999;

    public function beforeRender() {
        parent::beforeRender();
        // $aChannels = UserRegisterLink::$aChannels;
        // $oLottery     = new Lottery;
        // $aCondition   = null; //Session::get('is_tester') ? null : ['open' => ['=', 1]];
        // $aLotteries   = $oLottery->getValueListArray(Lottery::$titleColumn, $aCondition, [Lottery::$titleColumn => 'asc'], true);
        // $this->setVars(compact('aLotteries'));
        switch ($this->action) {
            case 'view':
//                $aSeriesLotteries = Series::getLotteriesGroupBySeriesGroupType();
//                $this->setVars('aListColumnMaps', UserRegisterLink::$listColumnMaps);
                // $aPrizeGroupWaters       = PrizeGroup::getPrizeGroupWaterMap();
//                $this->setVars(compact('aSeriesLotteries'));
                //获取当前链接返点
                $sPercentSets = $this->viewVars['data']->percent_sets;
                $aPercentSets = json_decode($sPercentSets,true);
                if(!empty($sPercentSets)){
                foreach($aPercentSets as $percentSet){
                    switch ($percentSet['percent_identity'] ) {
                        case 'single':
                            $fUserSinglePercentValue = $percentSet['percent_value'];
                            break;
                        case 'multi':
                            $fUserMultiPercentValue = $percentSet['percent_value'];
                            break;
                        default:
                            # code...
                            break;
                    }
                }
                $this->setVars(compact('fUserSinglePercentValue', 'fUserMultiPercentValue'));
                }
            // pr($this->viewVars['data']->toArray());
            // exit;
            case 'index':
                $aUserTypes = User::$userTypes;
                $this->setVars(compact('aUserTypes'));
                $iUserId = Session::get('user_id');
                $totalUserCount = RegisterLink::getTotalUserCountByUserId($iUserId);
                $this->setVars('totalUserCount', $totalUserCount);
                break;
            case 'create': // 新增时, 需要提供奖金组范围, 当前奖金组信息等数据
               break;
        }
    }

    public function index() {
        // $aUserLinkGroups = UserRegisterLink::getUserLinksWithChannelGroup();
        $iUserId = Session::get('user_id');
        if (!$iUserId)
            return $this->goBack('error', __('_basic.no-rights'));
        $this->params['user_id'] = $iUserId;
        $this->params['is_top'] = 0;
        // TODO 是否只显示未删除的链接
        // $this->params['status']   = 0;

        return parent::index();
    }

    public function create($id = null) {
            if (!$bIsAgent = Session::get('is_agent')) {
                return $this->goBack('error', __('_basic.no-rights'));
            }

            $iUserId = Session::get('user_id');
            $oUser = UserUser::find($iUserId);
            //TODO change
             // 总代不能开玩家
//             if (Session::get('is_top_agent') && array_get($this->params, 'is_agent') == 0) {
//                 return $this->goBack('error', __('_basic.no-rights'));
//             }
//            // 只允许二代开玩家
//            if ($oUser->user_level <= 2 && array_get($this->params, 'is_agent') == 0) {
//                return $this->goBack('error', __('_basic.no-rights'));
//            }
            $oUserRegisterLink = new UserRegisterLink;

            $sAvailableDomain = $_SERVER['HTTP_HOST'];
			//zero end
            // pr($sAvailableDomain);exit;
            $this->params['user_id'] = $iUserId;
            $this->params['username'] = Session::get('username');
            $this->params['is_tester'] = Session::get('is_tester');
            $iSpreadCode = User::generateSpreadCode();
            $oUserAccountQuota = UserAccountQuota::getUserAccountQuota($iUserId);
            $quota = $oUserAccountQuota->left_quota;
            $this->setVars(compact('quota'));

//            $this->params['keyword'] = md5($this->params['username'] . time() . Tool::randomStr(5));
            $this->params['keyword'] = $iSpreadCode;
            $this->params['url'] = $sAvailableDomain . Config::get('var.default_signup_dir_name') . '?prize=' . $this->params['keyword']; // $_SERVER['SERVER_NAME']
//            $this->params['prize_group_sets'] = $sPrizeGroupSetsJson;
            if (strpos($this->params['url'], 'http') !== 0) {
                $this->params['url'] = ((isset($_SERVER["https"]) && $_SERVER["https"]) ? 'https://' : 'http://') . $this->params['url'];
            }

            $bSucc = true;
            $aAgentQQs = [999999];
            foreach ($aAgentQQs as $key => $value) {
                if (!preg_match("/^\d*$/", $value) || $value < static::QQ_NUM_MIN) { // || $value > static::QQ_NUM_MAX
                    $bSucc = false;
                    break;
                }
            }

            if (!$bSucc) {
                return $this->goBack('error', __('_registerlink.qq-number-error', ['min' => static::QQ_NUM_MIN, 'max' => static::QQ_NUM_MAX]));
            }

            $this->params['agent_qqs'] = 999999;
            $this->params['is_top'] = 0;
            $this->params['valid_days'] = "0";
            if (intval($this->params['valid_days'])) {
                // TIP 添加链接从添加时间的后一天开始计算, 到过期时间的23：59：59 即过期日期的后一天的00:00:00
                // 编辑时, 从编辑当天向后续期
                $this->params['expired_at'] = Carbon::today()->addDays(intval($this->params['valid_days']) + 1)->toDateTimeString();
            }

        // pr($this->params);exit;
         //设置返点
            if(!empty($this->params['fb_single']) && !empty($this->params['fb_all'])){
                //获取当前用户返点
                $fUserSinglePercentValue = UserPercentSet::getPercentValueByUser($iUserId,UserPercentSet::$iFootBallLotteryId,PercentWay::$jcWays['single']);
                $fUserMultiPercentValue = UserPercentSet::getPercentValueByUser($iUserId,UserPercentSet::$iFootBallLotteryId,PercentWay::$jcWays['multi']);
                if($this->params['fb_single'] > $fUserSinglePercentValue){
                   return $this->goBack('error', __('_user.top-agent-single-percent-error', ['max' => $fUserSinglePercentValue]));
                }
                if($this->params['fb_all'] > $fUserMultiPercentValue){
                   return $this->goBack('error', __('_user.top-agent-multi-percent-error', ['max' => $fUserMultiPercentValue]));
                }
                $aPercentSet = [
                    [
                        'percent_identity' => 'single',
                        'percent_value' => $this->params['fb_single']
                    ],
                    [
                        'percent_identity' => 'multi',
                        'percent_value' => $this->params['fb_all']
                    ]
                ];
                $this->params['percent_sets'] = json_encode($aPercentSet);
            }

        $this->setVars(compact('fUserMultiPercentValue','fUserSinglePercentValue'));

//获取当前用户返点
                $fUserSinglePercentValue = UserPercentSet::getPercentValueByUser($iUserId,UserPercentSet::$iFootBallLotteryId,PercentWay::$jcWays['single']);
                $fUserMultiPercentValue = UserPercentSet::getPercentValueByUser($iUserId,UserPercentSet::$iFootBallLotteryId,PercentWay::$jcWays['multi']);
                $fUserSinglePercentValue *= 100;
                $fUserMultiPercentValue *= 100;
                $this->setVars(compact('fUserSinglePercentValue', 'fUserMultiPercentValue'));


            Session::put($this->redictKey, route('user-links.index'));
            $this->setVars(compact("iSpreadCode"));
            return parent::create();
    }

    /**
     * TODO fix
     * @param null $id
     * @return RedirectResponse|Response
     */
    private function createOwn($id = null) {
        DB::connection()->beginTransaction();
        $bSucc = $this->saveData($id);
        if ($bSucc = $this->saveData($id)) {
            DB::connection()->commit();
            $this->view = "userCenter.teamManager.spreadCode";
            return $this->render();
        }
        // pr($this->model->toArray());
        // pr('---------');
        // pr($this->model->validationErrors);exit;
        DB::connection()->rollback();
        $this->langVars['reason'] = & $this->model->getValidationErrorString();
//                pr($this->langVars);
//                exit;
        return $this->goBack('error', __('_basic.create-fail', $this->langVars));
    }

    /**
     * [closeLink 删除代理创建的开户链接]
     * @param  [Integer] $id [链接id]
     */
    public function closeLink($id) {
        $oLink = UserRegisterLink::getActiveLink($id);
        // pr($oLink->toArray());exit;
        if (!$oLink) {
            return $this->goBack('error', __('_basic.no-rights'));
        }
        // 只能关闭自己的链接
        if ($oLink->user_id != Session::get('user_id')) {
            return $this->goBack('error', __('_basic.no-rights'));
        }
        // $oLink->status = 1;
        $bSucc = $oLink->update(['status' => 1]);
        if ($bSucc) {
            return $this->goBack('success', __('_basic.closed', $this->langVars));
        } else {
            return $this->goBack('error', __('_basic.close-fail', $this->langVars));
        }
    }

}
