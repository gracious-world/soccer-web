<?php
namespace App\Models\AppUser;
use Session;
use App\Models\Message\MsgUser;

class UserMessage extends MsgUser {

    protected static $cacheUseParentClass = true;
    
    protected $fillable = [];
    public static $columnForList = [
        'msg_title',
        'type_id',
        'updated_at',
    ];

    public static function getUserUnreadMessagesNum() {
        $iUserId = Session::get('user_id');
        $iNum = static::where('receiver_id', '=', $iUserId)->whereNull('readed_at')->count();
        return $iNum;
    }


}
