<?php
namespace App\Models\User;
use App\Models\BaseModel;
/**
 * 开启配额模型
 *
 */
class UserPrizeSetQuota extends BaseModel {

    protected $table                     = 'user_prize_set_quota';
    public static $resourceName          = 'UserPrizeSetQuota';
    protected $softDelete                = false;
    public static $htmlSelectColumns     = [
    ];
    protected $fillable                  = [
        'user_id',
        'user_forefather_ids',
        'username',
        'prize_group',
        'total_quota',
        'left_quota',
    ];
    public static $ignoreColumnsInEdit   = [
        'left_quota',
//        'user_id',
    ];
    public static $readonlyColumnsInEdit = [
        'user_id',
        'username',
    ];
    public static $rules                 = [
        'user_id'     => 'required|integer|min:1',
        'username'    => 'required|alpha_num|between:6,16',
        'prize_group' => 'required|numeric|between:1950,1960',
        'total_quota' => 'required|numeric',
        'left_quota'  => 'required|numeric',
    ];
    public static $columnForList         = [
        'username',
        'prize_group',
        'total_quota',
        'left_quota',
    ];

    protected function beforeValidate() {
        if ($this->user_id) {
            $oUser                     = User::find($this->user_id);
            $this->user_forefather_ids = $oUser->forefather_ids;
        }
        if ($this->total_quota && !$this->left_quota) {
            $this->left_quota = $this->total_quota;
        }
        return parent::beforeValidate();
    }

    /**
     * 获取指定用户的奖金组配额
     * @param type $userId  用户id
     */
    public static function getUserAllPrizeSetQuota($userId) {
        $oQuery  = static::where('user_id', '=', $userId)
            ->orderBy('prize_group', 'asc');
        $aModels = $oQuery->get();
        $data    = [];
        foreach ($aModels as $oModel) {
            $data[$oModel->prize_group] = $oModel->left_quota;
        }
        return $data;
    }

    /**
     * 获取指定用户的指定奖金组配额
     * @param int $userId  用户id
     */
    public static function getUserThePrizeGroupQuota($userId, $iPrizeGroup) {
        return static::where('user_id', '=', $userId)->where('prize_group', '=', $iPrizeGroup)->first();
    }

    /**
     * 更新用户奖金组配额数据
     * @param int $userId      用户id
     * @param array $aPrizeSet   奖金组配额数组信息，如：[1956=>10,1955=>5]
     */
    public static function updateUserPrizeSetQuota($userId, $aPrizeSet, $operator = 'minus') {
        if (count($aPrizeSet) == 0) {
            return true;
        }
        $bSucc = false;
        foreach ($aPrizeSet as $iPrizeGroup => $iCount) {
            $oUserPrizeSetQuota = static::getUserThePrizeGroupQuota($userId, $iPrizeGroup);
            if (empty($oUserPrizeSetQuota)) {
                continue;
            }
            if ($operator == 'minus') {
                $oUserPrizeSetQuota->left_quota -= $iCount;
            } else if ($operator == 'plus') {
                $oUserPrizeSetQuota->left_quota += $iCount;
            }
            if (!$bSucc = $oUserPrizeSetQuota->save()) {
                break;
            }
        }
//        pr($bSucc);
//        exit;
        return $bSucc;
    }

    /**
     * 新增用户奖金组配额数据
     * @param type $userId      用户id
     * @param type $aPrizeSet   奖金组配额数组信息，如：[1956=>10,1955=>5]
     */
    public static function insertUserPrizeSetQuota($oUser, $aPrizeSet) {
        if (!is_array($aPrizeSet)) {
            return true;
        }
        $bSucc = true;
        foreach ($aPrizeSet as $iPrizeGroup => $iCount) {
            $oUserPrizeSetQuota = static::getUserThePrizeGroupQuota($oUser->id, $iPrizeGroup);
            if (!is_object($oUserPrizeSetQuota)) {
                $oUserPrizeSetQuota              = new UserPrizeSetQuota;
                $oUserPrizeSetQuota->total_quota = intval($iCount);
                $oUserPrizeSetQuota->left_quota  = intval($iCount);
            } else {
                $oUserPrizeSetQuota->left_quota  = $oUserPrizeSetQuota->left_quota + $iCount;
                $oUserPrizeSetQuota->total_quota = $oUserPrizeSetQuota->total_quota + $iCount;
            }
            $oUserPrizeSetQuota->user_id             = $oUser->id;
            $oUserPrizeSetQuota->user_forefather_ids = $oUser->forefather_ids;
            $oUserPrizeSetQuota->username            = $oUser->username;
            $oUserPrizeSetQuota->prize_group         = $iPrizeGroup;
            $bSucc                                   = $oUserPrizeSetQuota->save();
            if (!$bSucc) {
                break;
            }
        }
        return $bSucc;
    }

    /**
     *
     *  核查奖金组配额是否符合要求
     * @param array $aPrizeSetQuota        奖金组配额数组
     * @param int $iParenId                      上级id
     * @return boolean  配额符合要求，返回true；不符合要求，返回false
     *
     */
    public static function checkQuota($aPrizeSetQuota, $iParenId) {
        $aParentPrizeSetQuota = static::getUserAllPrizeSetQuota($iParenId);
        foreach ($aPrizeSetQuota as $iPrizeGroup => $iQuota) {
            if ($iQuota == 0) {
                continue;
            }
            $iParentQuota = array_get($aParentPrizeSetQuota, $iPrizeGroup);
            if ($iParentQuota === null) {
                return false;
            }
            if ($iParentQuota < $iQuota) {
                return false;
            }
        }
        return true;
    }

}
