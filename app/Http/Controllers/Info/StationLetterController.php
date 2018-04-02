<?php
namespace App\Http\Controllers;
// use Symfony\Component\HttpFoundation\Request;
// use Symfony\Component\HttpFoundation\StreamedResponse;

use App\Models\AppUser\UserMessage;
use Illuminate\Support\Facades\Session;
use App\Models\Message\MsgType;
use App\Models\User\UserUser;
use App\Models\Message\MsgUser;
use App\Models\Message\MsgMessage;
# 站内信

class StationLetterController extends UserBaseController {

    protected $resourceView = 'userCenter.stationLetter';
    protected $modelName = 'App\Models\AppUser\UserMessage';

    protected function beforeRender() {
        parent::beforeRender();
        $aMsgTypes = MsgType::getMsgTypesByGroup(0);
        $this->setVars(compact('aMsgTypes'));
        if (in_array($this->action, ['index', 'getSentMessages'])) {
            $oUser = UserUser::find(Session::get('user_id'));
            $aParent = null;
            $aChildren = null;
            if (! Session::get('is_top_agent') && $oUser->getDirectParent()) /**/{
                $aParent = $oUser->getDirectParent()->toArray();
                $aParent['username'] = '直属上级';
            }
            if (! Session::get('is_player') && $oUser->getUsersBelongsToAgent()) {
                $aChildren = $oUser->getUsersBelongsToAgent()->toArray();

            }
            $sJsonParent = json_encode([$aParent]);
            $sJsonChildren = json_encode($aChildren);
            $this->setVars(compact('sJsonParent', 'sJsonChildren', 'aParent'));
        }
    }

    /**
     * [index 用户的站内信列表]
     * @return [Response] [description]
     */
    public function index() {
        $this->params['receiver_id'] = Session::get('user_id');
        return parent::index();
    }

    /**
     * [viewMessage 查看站内信详情, 相当于自定义view, 用户阅读后标记已读/未读状态, 并根据是否保持属性判断该条信息是否阅后即焚]
     * @param  [Integer] $id [站内信记录id]
     * @return [Response]    [description]
     */
    public function viewMessage($id) {
        $this->model = $this->model->find($id);
        if (!is_object($this->model)) {
            return $this->goBackToIndex('error', __('_basic.missing', $this->langVars));
        }
        // pr($this->model->msg_id);
        // pr(MsgMessage::find($this->model->msg_id)->content);
        // exit;
        // 只记录用户第一次阅读的时间
        if (!$this->model->readed_at && !$this->model->deleted_at && $this->model->sender_id != Session::get('user_id')) {
            $this->model->readed_at = Carbon::now()->toDateTimeString();
            $this->model->is_readed = 1;
            if (!$this->model->is_keep) {
                $this->model->deleted_at = Carbon::now()->toDateTimeString();
                $this->model->is_deleted = 1;
            }
        }

        $this->model->save([
            'readed_at' => MsgUser::$rules['readed_at'],
            'is_readed' => MsgUser::$rules['is_readed']
        ]);

        $oMsgMessage = MsgMessage::find($this->model->msg_id);
        if (is_null($oMsgMessage)) {
            return $this->goBackToIndex('error', __('_basic.missing', $this->langVars));
        }
        $this->model->msg_content = $oMsgMessage->content;
        $data = $this->model;
        $this->setVars(compact('data'));
        return $this->render();
    }

    /**
     * 用户删除站内信
     * @param type $id  站内信id
     * @return type
     */
    public function deleteMessage($id) {
        $this->model = $this->model->find($id);
        if (!is_object($this->model)) {
            return $this->goBackToIndex('error', __('_basic.missing', $this->langVars));
        }
        if ($this->model->receiver_id != Session::get('user_id')) {
            return $this->goBackToIndex('error', __('_usermessage.not-allowed'));
        }
        $this->model->deleted_at = date('Y-m-d H:i:m');
        $this->model->is_deleted = 1;
        $bSucc = $this->model->save();
        if ($bSucc) {
            return $this->goBackToIndex('error', __('_usermessage.delete-success'));
        } else {
            return $this->goBackToIndex('error', __('_usermessage.delete-error'));
        }
    }

    /**
     * [getUserUnreadNum 获取用户未读信息的数量]
     * @return [Integer] [用户未读信息的数量]
     */
    public function getUserUnreadNum() {
        // TODO 测试html5的EventSource对象长连接
        // $response = new Symfony\Component\HttpFoundation\StreamedResponse(function() {
        //     $iOldNum = 0;
        //     while (true) {
        //         $iNewNum = UserMessage::getUserUnreadMessagesNum();
        //         if ($iNewNum != $iOldNum) {
        //             Log::info('test-event-source-' . time() . ': ' . $iNewNum);
        //             echo '{data: ' . ($iNewNum) . '}\n\n';
        //             ob_flush();
        //             flush();
        //         }
        //         sleep(30);
        //     }
        //     $iOldNum = $iNewNum;
        // });
        // $response->headers->set('Content-Type', 'text/event-stream');
        // $response->headers->set('Cache-Control', 'no-cache');
        // return $response;

        return UserMessage::getUserUnreadMessagesNum();
    }

    /**
     * 获取用户站内信信息
     */
    public function getUserMessages() {
        $iUserId = Session::get('user_id');
        $aUserMessages = UserMessage::getLatestRecords($iUserId);
        $aNewUserMsgs = [];
        foreach($aUserMessages as $oUserMsg){
            $oUserMsg->url = route('station-letters.view', $oUserMsg->id);
            $aNewUserMsgs[] = $oUserMsg->toArray();
        }
        echo json_encode($aNewUserMsgs);
    }

    public function getSentMessages() {
        $iUserId = Session::get('user_id');
        $this->params['sender_id'] = $iUserId;
        $this->params['type_id']   = MsgType::PRIVATE_MSG_TYPE;
        return parent::index();
    }

    public function sendMessage() {
        // echo json_encode([1 => 'ab']);exit;
        $iUserId = Session::get('user_id');
        $oUser = UserUser::find($iUserId);
        // pr(Request::method());exit;
        // if (Request::method() == 'POST') {
            $this->params = trimArray(Input::except('user_type', '_token'));
            // echo json_encode($this->params);exit;
            $iUserType = Input::get('user_type');
            $aReceivers = $this->getReceivers($iUserType);
            if (!count($aReceivers)) {
                return Response::json(['success' => 0, 'msg' => '没有收信人！']);
            }
            $this->model = App::make('MsgMessage');
            $this->model->sender_id = $iUserId;
            $this->model->sender = Session::get('username');
            $this->model->type_id = MsgType::PRIVATE_MSG_TYPE;
            $bSucc = $this->saveData();
            if (!$bSucc) {
                // $this->langVars['reason'] = & $this->model->getValidationErrorString();
                return Response::json(['success' => 0, 'msg' => '发送消息失败！']);
            }
            $aParams    = $this->generateSyncParams( $aReceivers );
            // pr($aParams);exit;
            if (!$aParams) return Response::json(['success' => 0, 'msg' => '发送消息失败！']);
            return $this->saveMsgToUsers($aParams);
        // } else {
        //     $aParent = null;
        //     $aChildren = null;
        //     if (! Session::get('is_top_agent')) {
        //         $aParent = $oUser->getDirectParent()->toArray();
        //     }
        //     if (! Session::get('is_player')) {
        //         $aChildren = $oUser->getUsersBelongsToAgent()->toArray();

        //     }
        //     $sJsonParent = json_encode([$aParent]);
        //     $sJsonChildren = json_encode($aChildren);
        //     $this->setVars(compact('sJsonParent', 'sJsonChildren', 'aParent'));
        //     // pr($sJsonParent);
        //     // pr($sJsonChildren);
        //     // exit;
        //     return $this->render();
        // }
    }

    protected function saveMsgToUsers($aParams)
    {
        foreach ($aParams as $key => $value) {
            $this->model->users()->attach($key, $value);
            MsgUser::deleteListCache($key);
        }
        // return Redirect::route('station-letters.outbox');
        return Response::json(['success' => 1, 'msg' => '发送消息成功！']);
    }

    private function getReceivers($iUserType) {
        $iUserId = Session::get('user_id');
        $oUser = UserUser::find($iUserId);
        switch ($iUserType) {
            case 2:
                $oReceivers = $oUser->getUsersBelongsToAgent();
                break;
            case 1:
                $oReceivers = [$oUser->getDirectParent()];
            case 3:
                $oReceiver = UserUser::find($this->params['receiver']);
                $oReceivers = $oReceiver ? [UserUser::find($this->params['receiver'])] : [];
                break;
            default:
                $oReceivers = [];
                break;
        }
        return $oReceivers;
    }

    protected function generateSyncParams( $oUsers )
    {
        $aParams   = [];
        $sender_id = Session::get('user_id');
        $sender    = Session::get('username');
        $bIsKeep   = 1;
        foreach ($oUsers as $key => $oUser) {
            $aParams[$oUser->id] = [
                'receiver'  => $oUser->username,
                'msg_title' => $this->params['title'],
                'sender_id' => $sender_id,
                'sender'    => $sender,
                'type_id'   => MsgType::PRIVATE_MSG_TYPE,
                'is_keep'   => (int)$bIsKeep
            ];
        }
        return $aParams;
    }
}
