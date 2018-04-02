<?php
namespace App\Http\Controllers;
use App\Models\Cms\CmsArticle;
use App\Models\Cms\CmsCategory;
use Illuminate\Support\Facades\Session;
use App\Models\Cms\CmsUserReaded;

class SystemNoticeController extends UserBaseController {

    protected $resourceView = 'centerUser.system_notice';
    protected $resourceHelpView = 'help';
    protected $modelName = 'App\Models\Cms\CmsArticle';
    // 普通文章
    public function index(){
        $this->params['category_id'] = CmsCategory::SYSTEM_NOTICE_ID;
        $this->params['status'] = CmsArticle::STATUS_AUDITED;
        return parent::index();
    }
    public function view($id)
    {
        $iUserId = Session::get('user_id');
        //是否未读
        $isReaded = CmsUserReaded::getReadedByUserId($id,$iUserId);
        if(!$isReaded){
          //插入已读，更新浏览数量
          CmsUserReaded::setReaded($id,$iUserId);
        }
        return parent::view($id);
    }

    //根据模板获取帮助中心类别列表
    public function aIds(){
         $aHelpTemlIds = CmsCategory::where('template', '=', 'help')->get(['id'])->toArray();
           foreach($aHelpTemlIds as $aId) {
                $aIds[] = $aId['id'];
           }
           return $aIds;
    }

    public function getUserNotices(){
      $aNotices = CmsArticle::getLatestRecords(CmsCategory::SYSTEM_NOTICE_ID);//CmsArticle::where('category_id','=',CmsCategory::SYSTEM_NOTICE_ID)->get();
      $aNewNotices = [];
      $aArticles = [];
      foreach($aNotices as $v){
        $aArticles[] = $v['id'];
      }
      $iUserId = session::get('user_id');
      //获取已读
      $aUserReaded = [];
      if(!empty($aArticles)){
        $oUserReadeds = CmsUserReaded::whereIn('article_id',$aArticles)->where('user_id','=',$iUserId)->get();
        foreach($oUserReadeds as $oUserReaded){
          $aUserReaded[] = $oUserReaded['article_id'];
        }
      }
      //is_readed
      foreach($aNotices as $oNotice){
          $oNotice->url = route('system-notices.view', $oNotice->id);
          $oNotice->is_readed = 0;
          if(!empty($aUserReaded) && in_array($oNotice->id,$aUserReaded)){
            $oNotice->is_readed = 1;
          }
          $aNewNotices[] = $oNotice->toArray();
      }
      echo json_encode($aNewNotices);
    }

}
