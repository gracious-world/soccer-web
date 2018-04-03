<?php
namespace App\Http\Controllers;

use Session;
use Input;
use Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
use App\Models\Message\MsgType;
use App\Models\AppUser\UserMessage;
use App\Models\Message\MsgUser;
use App\Models\Message\MsgMessage;
use App\Models\AppUser\UserUser;

# 站内信

class UserMessageController extends UserBaseController {

    protected $resourceView = 'userCenter.message';
    protected $modelName = 'App\Models\AppUser\UserMessage';

    protected function beforeRender() {
        parent::beforeRender();
        $aMsgTypes = MsgType::getMsgTypesByGroup(0);
        $this->setVars(compact('aMsgTypes'));
        $oUser = UserUser::find(Session::get('user_id'));
        $aParent = null;
        $aChildren = null;
        if (! Session::get('is_top_agent')) {
            $aParent = $oUser->getDirectParent()->toArray();
            $aParent['username'] = '直属上级';
        }
        if (! Session::get('is_player')) {
            $aChildren = $oUser->getUsersBelongsToAgent()->toArray();
        }
        $this->setVars(compact( 'aChildren', 'aParent'));
    }

    /**
     * 收件箱
     * @return [Response] [description]
     */
    public function receiver() {
        $this->params['receiver_id'] = Session::get('user_id');
        return parent::index();
    }

    /**
     * 发件箱
     * @return Response
     */
    public function sendlist(){
        $iUserId = Session::get('user_id');
        $this->params['sender_id'] = $iUserId;
        $this->params['type_id']   = MsgType::PRIVATE_MSG_TYPE;
        return parent::index();
    }

    /**
     * 发邮件页面
     * @return Response
     */
    public function send(){
        return $this->render();
    }

    /**
     * 获取用户站内信信息 ajax
     */
    public function getUserMessages() {
        $iUserId = Session::get('user_id');
        $aUserMessages = UserMessage::getLatestRecords($iUserId);
        $aNewUserMsgs = [];
        foreach($aUserMessages as $oUserMsg){
            $oUserMsg->url = route('message.view', $oUserMsg->id);
            $aNewUserMsgs[] = $oUserMsg->toArray();
        }
        echo json_encode($aNewUserMsgs);
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
        if (!$this->model->readed_at && !$this->model->deleted_at) {
            $this->model->readed_at = date('Y-m-d H:i:m');
            $this->model->is_readed = 1;
            if (!$this->model->is_keep) {
                $this->model->deleted_at = date('Y-m-d H:i:m');
                $this->model->is_deleted = 1;
            }
        }

        $this->model->save([
            'readed_at' => MsgUser::$rules['readed_at'],
            'is_readed' => MsgUser::$rules['is_readed']
        ]);

        $oMsgMessage = MsgMessage::find($this->model->msg_id);
        // pr($oMsgMessage->exists);exit;
        if (!$oMsgMessage->exists)
            return $this->goBackToIndex('error', __('_basic.missing', $this->langVars));
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
     * 发邮件操作
     * @return mixed
     */
    public function sendMessage() {
        $iUserId = Session::get('user_id');
        $this->params = trimArray(Input::except('user_type', '_token'));
        // echo json_encode($this->params);exit;
        $iUserType = Input::get('user_type');
        $aReceivers = $this->getReceivers($iUserType);
        if (!count($aReceivers)) {
            return Response::json(['success' => 0, 'msg' => '没有收信人！']);
        }
        $this->model = new MsgMessage();
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
    }

    protected function saveMsgToUsers($aParams)
    {

        foreach ($aParams as $key => $value) {
            $this->model->users()->attach($key, $value);
            MsgUser::deleteListCache($key);
        }
        // return Redirect::route('message.outbox');
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
                if(!isset($this->params['receiver'])){
                    $oReceivers = [];
                }else{
                    $oReceiver = UserUser::find($this->params['receiver']);
                    $oReceivers = $oReceiver ? [UserUser::find($this->params['receiver'])] : [];
                }
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
