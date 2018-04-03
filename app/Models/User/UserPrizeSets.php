<?php
namespace App\Models\User;
use App\Models\BaseModel;
use Cache;

class UserPrizeSets extends BaseModel {

    public static $resourceName = 'Prize Group';
    protected static $cacheLevel = self::CACHE_LEVEL_FIRST;

    protected $table = 'user_prize_sets';

    protected $fillable = [
        'user_id',
        'gt_id',
        'method_id',
        'method_identity',
        'method_combination',
        'name',
        'water'
    ];

    const TYPE_NO_BONUS = 0;
    const TYPE_HAVE_BONUS = 1;

    public static $prizeTypes = [
        self::TYPE_NO_BONUS => 'no_bonus',
        self::TYPE_HAVE_BONUS => 'have_bonus'
    ];

}
