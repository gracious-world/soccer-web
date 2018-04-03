<?php
namespace App\Models\User;

class UserUser extends User {

    protected static $cacheUseParentClass = true;
    protected $isAdmin = false;

    public static $customMessages = [
        'username.required'               => '请填写用户名',
        'username.alpha_num'              => '用户名只能由大小写字母和数字组成',
        'username.between'                => '用户名长度有误，请输入 :min - :max 位字符',
        'username.unique'                 => '用户名已被注册',
        'username.custom_first_character' => '首字符必须是英文字母',
        'nickname.required'               => '请填写昵称',
        'nickname.between'                => '用户昵称长度有误，请输入 :min - :max 位字符',
        'password.custom_password'        => '密码由字母和数字组成, 且需同时包含字母和数字, 不允许连续三位相同',
//        'password.confirmed'              => '密码两次输入不一致',
        'fund_password.custom_password'   => '资金密码由字母和数字组成, 且需同时包含字母和数字, 不允许连续三位相同',
        'fund_password.confirmed'         => '资金密码两次输入不一致',
        // 'email.required'                  => '请填写邮箱地址',
    ];

    /**
     * 生成用户唯一标识
     * @return string
     */
    protected function getUserFlagAttribute()
    {
        $iUserId = $this->id;
        // $sRange = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $sRange = 'GqNbzewIF6kfx5mYaAnBEUvMuJyH8o9D7XcWt0hiQKOgRLdlSPpsC2jZ143rTV'; // 使用乱序字串
        if($iUserId == 0)
        {
            return $sRange[0];
        }
        $iLength = strlen($sRange);
        $sStr = ''; // 最终生成的字串
        while ($iUserId > 0)
        {
            $sStr = $sRange[$iUserId % $iLength]. $sStr;
            $iUserId = floor($iUserId / $iLength);
        }
        return $sStr;
    }

    /**
     * [getRegistPrizeGroup 获取注册用户的奖金组信息]
     * @param  [String] $sPrizeGroup [链接开户特征码]
     * @param  &      $aPrizeGroup [奖金组数组的引用]
     * @param  &      $oPrizeGroup [奖金组对象的引用]
     * @return [type]              [description]
     */
    public static function getRegistPrizeGroup($sPrizeGroup = null, & $aPrizeGroup, & $oPrizeGroup, & $aPrizeSetQuota) {
        // pr($sPrizeGroup);exit;
        // 如果不是链接开户的注册，提供默认奖金组供注册用
        if (!$sPrizeGroup) {
            $aLotteries = & Lottery::getTitleList();
            $oExpirenceAgent = User::getExpirenceAgent();
            if (!$oExpirenceAgent) {
                return false;
            }
            $iPrizeGroup = $oExpirenceAgent->prize_group;
            // $aPrizeGroup = [];
            foreach ($aLotteries as $key => $value) {
                $aPrizeGroup[] = arrayToObject(['lottery_id' => $key, 'prize_group' => $iPrizeGroup]);
            }
            // 模拟oPrizeGroup对象
            $oPrizeGroup = $oExpirenceAgent;
            $oPrizeGroup->is_top = 0;
            $oPrizeGroup->is_agent = 0;
            $oPrizeGroup->user_id = $oExpirenceAgent->id;
        } else {
            $oPrizeGroup = UserRegisterLink::getRegisterLinkByPrizeKeyword($sPrizeGroup);
            // TODO 此处注册失败的具体条件后续可以改进
            if (!$oPrizeGroup) {
                return false;
            }
            $aPrizeSetQuota = objectToArray( json_decode($oPrizeGroup->agent_prize_set_quota));
            // 总代开户链接只能使用一次
            if ($oPrizeGroup->is_top && $oPrizeGroup->created_count) {
                return false;
                // return Redirect::back()->withInput()->with('error', '该链接已被使用。');
            }
            $aPrizeGroup = json_decode($oPrizeGroup->prize_group_sets);
            
        }
        return true;
    }


    /**
     * 直客注册
     * @author lucda
     * @date 2016-11-19
     * @param $aData
     * @param $iParentId
     * @param $iRegisterLinkId
     * @param bool $bConfirmPasswd
     * @param $oUser
     * @param $iErrno
     * @param $sErrmsg
     * @return bool
     */
    public static function createUserDirect(& $aData, $iParentId, $iRegisterLinkId, $bConfirmPasswd = false, & $oUser, & $iErrno, & $sErrmsg) {
        $sPrizeGroup = 1700;//默认的奖金组  所有直客注册都是 这个奖金组

        if (!$data = static::compileUserDataDirect($aData, $sPrizeGroup, $iParentId, $iRegisterLinkId, $iErrNo)){
            return false;
        }

        $oUser = new static($data);
        
        if (!$oUser->compilePasswordString(self::PASSWD_TYPE_LOGIN,$bConfirmPasswd)){
            $iErrno = self::REGISTER_ERROR_PASSWD_WRONG;
            return false;
        }

//        if (!$oUser->compilePasswordString(self::PASSWD_TYPE_FUND,true)){
//            $iErrno = self::REGISTER_ERROR_PASSWD_WRONG;
//            return false;
//        }
        if (!$oUser->save()) {
            $iErrNo = self::REGISTER_ERROR_USER_SAVE_ERROR;
            $sErrmsg = $oUser->getValidationErrorString();
            return false;
        }
        if (!$oUser->createAccount()) {
            $iErrno = User::REGISTER_ERROR_CREATE_ACCOUNT_FAILED;
            return false;
        }
        //初始化用户奖金组
        if (!$bSucc = UserPrizeSet::initUserPrizeGroup($oUser, $sPrizeGroup)) {
            $iErrno = User::REGISTER_ERROR_CREATE_PRIZE_GROUP_SET;
            return false;
        }

        return true;
    }

    /**
     * 直接开户使用
     * @author lucda
     * @date    2016-11-19
     * @param $aData
     * @param $sPrizeGroup
     * @param $iParentId
     * @param null $iRegisterLinkId
     * @param $iErrno
     * @return array
     */
    public static function & compileUserDataDirect($aData, $sPrizeGroup, $iParentId, $iRegisterLinkId = null, & $iErrno) {
        $aDataComiled = [
            'username'              => $aData['username'],
            'password'              => $aData['password'],
//            'fund_password'         => isset($aData['fund_password']) ? $aData['fund_password'] : null,
//            'fund_password_confirmation' => isset($aData['fund_password_confirmation']) ? $aData['fund_password'] : null,
            'nickname'              => isset($aData['nickname']) ? $aData['nickname'] : $aData['username'],
            'is_agent'              => $aData['is_agent'],
            'email'                 => isset($aData['email']) ? $aData['email'] : null,
            'qq'                    => isset($aData['qq']) ? $aData['qq'] : null,
            'mobile'                => isset($aData['mobile']) ? $aData['mobile'] : null,
            'skype'                 => isset($aData['skype']) ? $aData['skype'] : null,
            'name'                  => isset($aData['name']) ? $aData['name'] : null,
            'shenfenzheng'          => isset($aData['shenfenzheng']) ? $aData['shenfenzheng'] : null,
            'weixin'                => isset($aData['weixin']) ? $aData['weixin'] : null,
            'prize_group'           => $sPrizeGroup,
            'register_ip'           => Tool::getClientIp(),
            'register_at'           => ($sCurTime = Carbon::now()->toDateTimeString()),
            'activated_at'          => $sCurTime,
            'is_tester'             => $aData['is_tester'],
            'parent'                => '',
            'forefather_ids'        => '',
            'is_from_link'          => intval($iRegisterLinkId > 0),
        ];
        if ($iParentId) {
            $oAgent = static::find($iParentId);
            $aDataComiled['parent_id'] = $oAgent->id;
            $aDataComiled['forefather_ids'] = $oAgent->forefather_ids ? $oAgent->forefather_ids . ',' . $oAgent->id : $oAgent->id;
            $aDataComiled['parent'] = $oAgent->username;
            $aDataComiled['forefathers'] = $oAgent->forefathers ? $oAgent->forefathers . ',' . $oAgent->username : $oAgent->username;
            $aDataComiled['is_tester'] = $oAgent->is_tester;
        }
        return $aDataComiled;
    }




}