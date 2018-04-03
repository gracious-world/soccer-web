<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
// Frame
use Illuminate\Support\Str;
use Illuminate\Support\MessageBag;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;

// Model
use App\Models\Func\Functionality;
use App\Models\Func\FunctionalityParam;
use App\Models\Func\FunctionalityRelation;
use App\Models\Func\SearchConfig;
use App\Models\Func\Popup;
use App\Models\Func\PopupItem;
use App\Models\Func\SysModel;
use App\Models\Func\SysConfig;
// use App\Models\Payment\PaymentPlatform;
use App\Models\User\User;
use App\Models\Fund\Account;
use App\Models\Activity\ActivityUserBonus;
use App\Models\User\Role;
use App\Models\AppUser\UserUser;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Redirect;
use App\Models\Cms\CmsArticle;

// Facades
use Auth;
use Config;
use Route;
use Session;
use Tool;
use Input;
use Lang;
use Carbon;
use DB;
use Restable;
use JWTAuth;

// Custom
use FormHelper;
use String;
use App\Models\Payment\PaymentPlatform;
// Event
use App\Events\BaseCacheEvent;
use App\Models\AppUser\UserMessage;
use App\Models\AppUser\UserTransaction;

class UserBaseController extends Controller
{

    static $aRediredtFullUrlActions = ['encode', 'index', 'settings', 'agentPrizeGroupList', 'agentDistributionList', 'listSearchConfig', 'unVefiriedRecords', 'verifiedRecords', 'remitRecords'];

    protected $oUser;

    protected $Message;

    protected $request;

    /**
     * how long the data will be displayed
     * @var int
     */
    protected $dataDisplayDays = 0;

    /**
     * 是否是ajax方式
     * @var bool
     */
    protected $isAjax = false;

    /**
     * 需要加载的错误码定义文件
     * @var array
     */
    protected $errorFiles = ['system', 'bet', 'fund', 'account', 'lottery', 'issue', 'seriesway'];

    /**
     * 资源视图目录
     * @var String
     */
    protected $resourceView = '';

    /**
     * self view path
     * @var String
     */
    protected $customViewPath = '';

    /**
     * view path
     * @var String
     */
    protected $view = '';

    /**
     * views use custom view path
     * @var array
     */
    protected $customViews = [];

    /**
     * 资源模型名称
     * @var String
     */
    protected $modelName;

    /**
     * friendly model
     * @var String
     */
    protected $friendlyModelName;

    /**
     * 模型实例
     * @var Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * Resource , use for route
     */
    protected $resource;

    /**
     * 资源数据库表
     * @var String
     */
    protected $resourceTable = '';

    /**
     * 资源名称
     * @var String
     */
    protected $resourceName = '';

    /**
     * pagesize
     * @var int
     */
    protected static $pagesize = 15;

    /**
     * 须自动准备数据的视图名称
     * @var array
     */
    protected $composerViews = array(
        'view',
        'index',
        'create',
        'edit',
    );

    /**
     * Functionality Model
     * @var Functionality
     */
    protected $functionality = null;

    /**
     * 用于关联按钮的语言包键数组
     * @var array
     */
    protected $langKeysForButtons = [];
    /**
     * 视图使用的样式名
     * @var array
     */
    public $viewClasses = [
        'div' => 'form-group ',
        'label' => 'col-sm-3 control-label ',
        'input_div' => 'col-sm-5 ',
        'msg_div' => 'col-sm-4 ',
        'msg_label' => 'text-danger control-label ',
        'radio_div' => 'switch ',
        'select' => 'form-control ',
        'input' => 'form-control ',
        'radio' => 'boot-checkbox',
        'date' => 'input-group date form_date ',
    ];

    /**
     * 自定义验证消息
     * @var array
     */
    protected $validatorMessages = [];

    /**
     * 消息对象
     * @var Illuminate\Support\MessageBag
     */
    protected $messages = null;

    /**
     * Controller
     */
    protected $controller;

    /**
     * Action
     */
    protected $action;

    /**
     * var for views
     */
    protected $viewVars = [];

    /**
     * sysConfig model
     * @var sysConfig
     */
    protected $sysConfig;

    /**
     * search config
     * @var array
     */
    protected $searchConfig;

    /**
     * search fields
     * @var array
     */
    protected $searchItems = [];

    /**
     * param settings
     * @var array
     */
    protected $paramSettings;

    /**
     * use for redirect
     * @var String
     */
    protected $redictKey;

    /**
     * save the all input data: get,post
     * @var array
     */
    protected $params = [];

    /**
     * Widgets
     * @var array
     */
    protected $widgets = [];

    /**
     * Breadcrumb
     * @var array
     */
    protected $breadcrumbs = [];

    /**
     * for lang transfer
     * @var array
     */
    protected $langVars = [];

    /**
     * for lang transfer, short title
     * @var array
     */
    protected $langShortVars = [];

    /**
     * default lang file
     */
    protected $defaultLangPack;

    /**
     * if is admininistrator
     */
    protected $admin;

    /**
     * Client IP
     * @var String
     */
    protected $clientIP;

    /**
     * Proxy IP
     * @var String
     */
    protected $proxyIP;

    /**
     * Need Right Check
     * @var bool
     */
    protected $needRightCheck = true;

    /**
     * 当前用户可访问的功能ID列表
     * @var array
     */
    protected $hasRights = null;

    /**
     * 不进行权限检查的控制器列表
     * @var array
     */
    protected $openControllers = ['HomeController', 'UserGameController', 'UserLotteryNoticeController'];

    protected $User;

    /**
     * 初始化
     * @return void
     */
    public function __construct(Request $request)
    {
//        var_dump(__FILE__,__LINE__);exit;
        $this->request = $request;
        // $this->User = $this->getAuthenticatedUser();
        // $request->session()->put('user_id', $this->User->id);

        // pr($request->session()->get('token'));exit;
        // CSRF 保护
        $this->isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest';
        // if (!$this->checkLogin()){
        //     return $this->doNotLogin();
        // }
        $this->admin = false;
        // init controller and action
        $this->initCA() or app()->abort(404);
        // 设置功能配置信息
        $this->setFunctionality();
        $this->getFunctionalityParams();

        // todo: new function
        if (!in_array($this->controller, $this->openControllers)) {
            $this->functionality or app()->abort(404);
            $this->checkRight() or app()->abort(403);
        }

        // 实例化 消息对象
        $this->messages = new MessageBag;

        $this->setReirectKey();
        if ($request->isMethod('GET') && in_array($this->action, static::$aRediredtFullUrlActions)) {
            $request->session()->put($this->redictKey, $request->fullUrl());
            // pr(Session::all());exit;
        }
        // 实例化资源模型
        $this->initModel();

        $this->resource = $this->getResourceName();

        // sysconfig
        $this->sysConfig = new SysConfig;

        // language
        $sLanguage = 'zh-CN';
        app()->setLocale($sLanguage);

        $this->clientIP = Tool::getClientIp();
        $this->proxyIP = Tool::getProxyIp();
//        Kint::dump(func_get_arg(0));
    }

    public function getAuthenticatedUser()
    {
        try {
            if (!$user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['user_not_found'], 404);
            }
        } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            return response()->json(['token_expired'], $e->getStatusCode());
        } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return response()->json(['token_invalid'], $e->getStatusCode());
        } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {
            return response()->json(['token_absent'], $e->getStatusCode());
        }
        // the token is valid and we have found the user via the sub claim
        return $user;
    }

    /**
     * 初始化模型实例及语言包等
     */
    protected function initModel()
    {
        if ($this->modelName) {
            $sModelName = $this->modelName;
            $this->resourceName = __('_model.' . $sModelName::$resourceName);
//            pr($this->resourceName);
            $this->model = app()->make($this->modelName);
            // pr($this->modelName);exit;
            $this->resourceTable = $this->model->getTable();
            $this->friendlyModelName = Str::slug($this->modelName);
            $this->langVars = ['resource' => __('_model.' . Str::slug($sModelName::$resourceName))];
            $this->langShortVars = ['resource' => null];
            $this->defaultLangPack = $sModelName::comaileLangPack();
//            pr($this->langVars);
        }
    }

    /**
     * 检查当前用户是否有权限访问当前功能
     * @return boolean
     */
    protected function checkRight()
    {
        if ($this->functionality) {
            if ($this->functionality->disabled) {
                return false;
            }
            $roleIds = Session::get('CurUserRole');
            if (!isset($enabled)) {
                $this->blockedFuncs = &$this->getBlockedFuncs();
//                pr($this->blockedFuncs);
//                pr($this->functionality->id);
//                pr(in_array($this->functionality->id, $this->blockedFuncs));
//                exit;
                if ($enabled = !in_array($this->functionality->id, $this->blockedFuncs)) {
                    $this->hasRights = &$this->getUserRights();
                    // pr($this->hasRights);
                    // pr($this->functionality->id);
                    // exit;
                    $enabled = in_array($this->functionality->id, $this->hasRights);
                }
            }
            if ($enabled && Session::get('user_id')) {
//                die($this->functionality->realm);
                if ($this->functionality->realm == Functionality::REALM_SYSTEM) {
                    $enabled = Session::get('IsAdmin');
                }
            }
        } else {
            $enabled = false;
        }
        return $enabled;
    }

    protected function getFunctionalityParams()
    {
        // DB::enableQueryLog();
        $this->paramSettings = FunctionalityParam::getParams($this->functionality->id);
        // $queries = DB::getQueryLog();
        // $last_query = end($queries);
        // pr($last_query);exit;
        // pr($this->paramSettings);
        // exit;
        // $this->params['token'] = $this->request->session()->get('token');
        // pr($this->params['token']);exit;
        $this->params = trimArray(Input::except('_token'));
        if ($this->functionality->need_search) {
            $this->getSearchConfig();
            // $this->_setSearchInfo();
        }
    }

    protected function & getBlockedFuncs()
    {
        $roleIds = Session::get('CurUserRole');
        // pr($roleIds);
        // exit;
        $aRights = &Role::getBlockedFuncsOfRoles($roleIds);
        return $aRights;
    }

    /**
     * 检查是否登录，需要在子类中覆盖
     * @return bool
     */
    protected function checkLogin()
    {
        return boolval(Session::get('user_id'));
    }

    /**
     * 如果未登录时执行的动作,需要在子类中覆盖
     * @return Redirect
     */
    protected function doNotLogin()
    {
        if ($this->isAjax) {
//            $this->halt(false, 'loginTimeout', Config::get('global_error.ERRNO_LOGIN_EXPIRED'));
        } else {
            return Redirect::route('signin');
        }
    }

    /**
     * 初始化controller　action属性
     * @return boolean
     */
    protected function initCA()
    {
        // pr(Route::currentRouteAction());exit;
        if (!$ca = Route::currentRouteAction()) {
            // pr($ca);exit;
            return false;
        }
        $aCAs = explode('@', $ca);
        $iPos = strripos($aCAs[0], '\\') - strlen($aCAs[0]) + 1;
        $sController = substr($aCAs[0], $iPos);
        // pr($aCAs);
        // pr($sController);
        // exit;
        list($this->controller, $this->action) = [$sController, $aCAs[1]];
        return true;
    }

    /**
     * set the redirect key, use for redirect after edit,create,delete action and so on.
     */
    protected function setReirectKey()
    {
        $this->redictKey = 'curPage-' . $this->modelName;
    }

    /**
     * 当负责响应的方法没有返回值，或返回值为 null 时，
     * 系统将判断 layout 属性是否为空，
     * 若不为空，则根据 layout 属性，返回一个视图响应。
     * @return void
     */
    protected function setupLayout()
    {
        if (!is_null($this->layout)) {
            $this->layout = View::make($this->layout);
        }
    }

    /**
     * [_generateTreeList 递归生成树形数据]
     * @param  [Array] $table     [待递归的数据]
     * @param  [String] $parent_id [父id]
     * @return [Array]            [格式为{..., children: [{..., leaf: true}]}]
     */
    protected function _generateTreeList($table, $parent_id)
    {
        $tree = array();
        foreach ($table as $row) {
            if ($row['parent_id'] == $parent_id) {
                $tmp = $this->_generateTreeList($table, $row['id']);
                if ($tmp) {
                    $row['children'] = $tmp;
                } else {
                    $row['leaf'] = true;
                }
                $tree[] = $row;
            }
        }
        return $tree;
    }

    /**
     * getUserRights 获取可访问的功能ID数组，必须在子类中覆盖
     *
     * @param  boolean $onlyMenu 是否只获取设置为菜单的功能权限
     * @return Array              根据$returnType得到的不同数组
     */
    protected function & getUserRights()
    {
        $roleIds = Session::get('CurUserRole');
        $aRights = &Role::getRightsOfRoles($roleIds);
        return $aRights;
    }

    /**
     * 获取指定角色ID范围所拥有的权限集合
     * @param array $aRoleIds
     * @return array
     */
    public function & getRights($aRoleIds = array())
    {
        $aRoles = Role::whereIn('id', $aRoleIds)->get(array('id', 'rights'));
        $aRights = [];
        foreach ($aRoles as $oRole) {
            $aRights = array_merge($aRights, explode(',', $oRole->rights));
        }
        $aRights = array_unique($aRights);
        return $aRights;
    }

    /**
     * get router name
     * @param String $route_action
     * @return String
     */
    protected function _getRouterName($route_action)
    {
        $router = Route::getRoutes()->getByAction($route_action);
        return $router ? $router->getName() : '';
    }

    /**
     * render view
     * @return Response
     */
    protected function render()
    {
        $this->beforeRender();
        if (!$this->view) {
            if (in_array($this->action, $this->customViews) && $this->customViewPath) {
                $this->resourceView = $this->customViewPath;
            }
            $this->view = $this->resourceView . '.' . $this->action;
        }
        $sLangKey = '_basic.';
        $sModelName = &$this->modelName;
        switch ($this->action) {
            case 'index':
                $sLangKey .= 'management';
                if ($sModelName) {
                    $this->setVars('aColumnForList', $sModelName::$columnForList);
                    $this->setVars('sModelSingle', __('_model.' . $this->friendlyModelName));
                    $this->setVars('bSequencable', $sModelName::$sequencable);
                    $this->setVars('bCheckboxForBatch', $sModelName::$enabledBatchAction);
                    if ($sModelName::$sequencable) {
                        $sSetOrderRoute = $this->resource . '.set-order';
                        $this->setvars(compact('sSetOrderRoute'));
                    }
                    $this->setVars('aListColumnMaps', $sModelName::$listColumnMaps);
                    $this->setVars('aNoOrderByColumns', $sModelName::$noOrderByColumns);
                    if ($sModelName::$totalColumns) {
                        $this->setVars('aTotalColumns', $sModelName::$totalColumns);
                    }
                }
                break;
        }
        // $token = JWTAuth::getToken()->__toString();
        // pr(($token->__toString()));exit;
        // $this->setVars('token', $token);
        return response()->view($this->view, $this->viewVars);
        // view($this->view, [], 200, ['Authorization' => 'Bearer' . $this->request->session()->get('token')])->with($this->viewVars);
        // View::make($this->view)->with($this->viewVars);
    }

    /**
     * 在渲染前执行，为视图准备变量
     */
    protected function beforeRender()
    {
        $this->setVars('sPageName',$this->modelName);
        //提交数据注册变量
        $this->setVars($this->params);
        //提交数据注册到aParams变量
        $this->setVars('aParams', $this->params);
        $iUserId = Session::get('user_id');
        if ($iUserId) {
            $oUser = UserUser::find($iUserId);
            $this->oUser = $oUser;
            $fAvailable = number_format(Account::getAvaliable($iUserId), 2, '.', ',');
            // $iUnreadMessagesNum = UserMessage::getUserUnreadMessagesNum();
            $sBpName = $this->request->session()->get('bp_name');
            $sUsername = $this->request->session()->get('username');
//            $bSimple = $oUser->customer->is_simple;
            $bSimple = 1;
            $this->setVars(compact('oUser', 'fAvailable', 'sBpName', 'sUsername', 'bSimple'));
        }
        // $this->setVars('bNeedShowDesc', 1);
        $this->setVars('iUserId', $iUserId);
        // $this->setVars('iDefaultPaymentPlatformId', PaymentPlatform::getDefaultPlatformId());
        $this->setVars('resource', $this->resource);

        $iDefaultPaymentPlatformId = PaymentPlatform::getDefaultPlatformId();
        $this->setVars('iDefaultPaymentPlatformId', $iDefaultPaymentPlatformId);
        $oDefaultPaymentPlatform = PaymentPlatform::find($iDefaultPaymentPlatformId);
        $sPaymentRoute = $oDefaultPaymentPlatform->type == 2 ? 'user-recharges.quick' : 'bank-deposits.netbank';
        $sPaymentRouteStr = route($sPaymentRoute,$iDefaultPaymentPlatformId);
        $this->setVars(compact('sPaymentRouteStr'));

        //current route
        $sCurrentRoute = Route::current()->getName();
        $this->setVars(compact("sCurrentRoute"));

        $aGameTypes = Config::get('jc-config.game_types');
        $this->setVars(compact('aGameTypes'));
         $aGateTypes = Config::get('jc-config.method_types');
        $this->setVars(compact('aGateTypes'));
    }

    /**
     * get search conditions array
     *
     * @return array
     */
    protected function & makeSearchConditions()
    {
        $aConditions = [];
        $iCount = count($this->params);
        foreach ($this->paramSettings as $sColumn => $aParam) {
            if (!isset($this->params[$sColumn])) {
                if ($aParam['limit_when_null'] && $iCount <= 1) {
                    $aFieldInfo[1] = null;
                } else {
                    continue;
                }
            }
            $mValue = isset($this->params[$sColumn]) ? $this->params[$sColumn] : null;
            if (!mb_strlen($mValue) && !$aParam['limit_when_null'])
                continue;
            if (!isset($this->searchItems[$sColumn])) {
                $aConditions[$sColumn] = ['=', $mValue];
                continue;
            }
            $aPattSearch = array('!\$model!', '!\$\$field!', '!\$field!');
            $aItemConfig = &$this->searchItems[$sColumn];
            $aPattReplace = array($aItemConfig['model'], $mValue, $aItemConfig['field']);
            $sMatchRule = preg_replace($aPattSearch, $aPattReplace, $aItemConfig['match_rule']);
            $aMatchRule = explode("\n", $sMatchRule);
            if (count($aMatchRule) > 1) {        // OR
                // todo : or
            } else {
//                pr($aMatchRule);
                $aFieldInfo = array_map('trim', explode(' = ', $aMatchRule[0]));
//                    pr($aFieldInfo);
//                    pr($mValue);
                $aTmp = explode(' ', $aFieldInfo[0]);
//                pr($aTmp);
//                pr(count($aTmp));
//                exit;
                $iOperator = (count($aTmp) > 1) ? $aTmp[1] : '=';
                if (!mb_strlen($mValue) && $aParam['limit_when_null']) {
                    $aFieldInfo[1] = null;
                }
                list($tmp, $sField) = explode('.', $aTmp[0]);
                $sField{0} == '$' or $sColumn = $sField;
                if (isset($aConditions[$sColumn])) {
                    // TODO 原来的方式from/to的值和search_items表中的记录的顺序强关联, 考虑修改为自动从小到大排序的[from, to]数组
                    $arr = [$aConditions[$sColumn][1], $aFieldInfo[1]];
                    sort($arr);
                    // $sFrom = $aConditions[$sColumn][1];
                    // $sTo = $aFieldInfo[1];
                    $aConditions[$sColumn] = ['between', $arr];
                } else {
                    $aConditions[$sColumn] = [$iOperator, $aFieldInfo[1]];
                }
            }
//            if ($sColumn == 'parent_id'){
//                die('test');
//            }
        }
        // pr($this->paramSettings);
        // pr($this->params);
        // pr($aConditions);exit;
        // if (count($this->params) > 1 && isset($aConditions['parent_id'])){
        //    unset($aConditions['parent_id']);
        // }
        return $aConditions;
    }

    /**
     * 资源列表页面
     * GET
     * @return Response
     */
    public function index()
    {
        $oQuery = $this->indexQuery();
        $sModelName = $this->modelName;
        $iPageSize = isset($this->params['pagesize']) && is_numeric($this->params['pagesize']) ? $this->params['pagesize'] : static::$pagesize;
        // DB::enableQueryLog();
        $datas = $oQuery->paginate($iPageSize);
        // $queries = DB::getQueryLog();
        // $last_query = end($queries);
        // pr($last_query);exit;
        // pr(($datas->toArray()));exit;
        $this->setVars(compact('datas'));
        if ($sMainParamName = $sModelName::$mainParamColumn) {
            if (isset($aConditions[$sMainParamName])) {
                $$sMainParamName = is_array($aConditions[$sMainParamName][1]) ? $aConditions[$sMainParamName][1][0] : $aConditions[$sMainParamName][1];
            } else {
                $$sMainParamName = null;
            }
            $this->setVars(compact($sMainParamName));
        }
        return $this->render();
    }

    public function view($id)
    {
        $this->model = $this->model->find($id);
        if (!is_object($this->model)) {
            return $this->goBackToIndex('error', __('_basic.missing', $this->langVars));
        }
        $data = $this->model;
        $sModelName = $this->modelName;
        if ($sModelName::$treeable) {
            if ($this->model->parent_id) {
                if (!array_key_exists('parent', $this->model->getAttributes())) {
                    $sParentTitle = $sModelName::find($this->model->parent_id)->{$sModelName::$titleColumn};
                } else {
                    $sParentTitle = $this->model->parent;
                }
            } else {
                $sParentTitle = '(' . __('_basic.top_level', [], 3) . ')';
            }
        }
        $this->setVars(compact('data', 'sParentTitle'));
        return $this->render();
    }

    public function indexQuery()
    {
        $aConditions = &$this->makeSearchConditions();
        $oQuery = $aConditions ? $this->model->doWhere($aConditions) : $this->model;
        // TODO 查询软删除的记录, 以后需要调整到Model层
        $bWithTrashed = trim(Input::get('_withTrashed', 0));
        // pr($bWithTrashed);exit;
        if ($bWithTrashed)
            $oQuery = $oQuery->withTrashed();
        if ($sGroupByColumn = Input::get('group_by')) {
            $oQuery = $this->model->doGroupBy($oQuery, [$sGroupByColumn]);
        }
        // 获取排序条件
        $aOrderSet = [];
        if ($sOorderColumn = Input::get('sort_up', Input::get('sort_down'))) {
            $sDirection = Input::get('sort_up') ? 'asc' : 'desc';
            $aOrderSet[$sOorderColumn] = $sDirection;
        }
        $oQuery = $this->model->doOrderBy($oQuery, $aOrderSet);
        return $oQuery;
    }

    /**
     * set view vars
     * @param String|array $sKey
     * @param mixed $mValue
     */
    function setVars($sKey, $mValue = null)
    {
        if (is_array($sKey)) {
            foreach ($sKey as $key => $value) {
                $this->setVars($key, $value);
            }
        } else {
            $this->viewVars[$sKey] = $mValue;
        }
    }

    /**
     * save data to database
     * auto redirect
     * @return bool
     */
    protected function saveData()
    {
        // 用表单数据填充模型实例
        $this->_fillModelDataFromInput();
        // 创建验证规则
        $aRules = &$this->_makeVadilateRules($this->model);
        // pr($aRules);
        // pr('------------');
        // pr($this->model->toArray());exit;
        // save
        return $this->model->save($aRules);
    }

    /**
     * 用表单数据填充模型实例
     */
    protected function _fillModelDataFromInput()
    {
//        $this->model = $id ? $this->model->find($id) : $this->model;
        $data = $this->params;
        $sModelName = $this->modelName;
        !empty($this->model->columnSettings) or $this->model->makeColumnConfigures();
        foreach ($this->model->columnSettings as $sColumn => $aSettings) {
            if ($sColumn == 'id')
                continue;
            if (!isset($aSettings['type']))
                continue;
            switch ($aSettings['type']) {
                case 'bool':
                case 'numeric':
                case 'integer':
                    !empty($data[$sColumn]) or $data[$sColumn] = 0;
                    break;
                case 'select':
                    if (isset($data[$sColumn]) && is_array($data[$sColumn])) {
                        sort($data[$sColumn]);
                        $data[$sColumn] = implode(',', $data[$sColumn]);
                    }
            }
        }
        // pr($data);
        //        exit;
        // pr(get_class($this->model));
        // pr('---------');

        $this->model = $this->model->fill($data);
        // pr('---------');
        // pr($this->model->toArray());exit;
        if ($sModelName::$treeable) {
            $this->model->parent_id or $this->model->parent_id = null;
            if ($sModelName::$foreFatherColumn) {
                $this->model->{$sModelName::$foreFatherColumn} = $this->model->setForeFather();
            }
        }
    }

    /**
     * 根据实际情况修改验证规则
     * @param model $oModel
     * @return array
     */
    protected function & _makeVadilateRules($oModel)
    {
        $sClassName = get_class($oModel);
        return $sClassName::$rules;
    }

    /**
     * 构造 unique 验证规则
     * @param  String $column 字段名称
     * @param  int $id 排除指定 ID
     * @return String
     */
    protected function unique($column = null, $id = null, $extraParam = null)
    {
        $rule = 'unique:' . $this->resourceTable;
        if (!is_null($column))
            $rule .= ',' . $column;
        if (!is_null($id))
            $rule .= ',' . $id . ',id';
        else
            $rule .= ',NULL,id';
        if (!is_null($extraParam) && is_array($extraParam)) {
            foreach ($extraParam as $key => $value) {
                $rule .= ',' . $key . ',' . $value;
            }
        }
        return $rule;
    }

    protected function getResourceName()
    {
        $sControllerName = str_replace('Controller', '', $this->controller);
        $aParts = explode('_', $sControllerName);
        $sName = $aParts[count($aParts) - 1];
        $sName = String::snake($sName);
        return String::plural(String::slug($sName, '-'));
    }


    /**
     * get search config
     */
    protected function getSearchConfig()
    {
        $iFunctionalityId = $this->functionality->id;
        if ($this->searchConfig = SearchConfig::getForm($iFunctionalityId, $this->admin)) {
            $this->searchItems = &$this->searchConfig->getItems();
        }
    }

    /**
     * add widget name
     * @param String $sWidget
     */
    protected function addWidget($sWidget)
    {
        $this->widgets[] = $sWidget;
    }

    /**
     * redict to index page
     * @param String $sMsgType in list: success, error, warning, info
     * @param String $sMessage
     * @return RedirectResponse
     */
    protected function goBackToIndex($sMsgType, $sMessage)
    {
//         pr($this->redictKey);
//         pr(Session::get($this->redictKey));
//         exit;
        $sToUrl = Session::get($this->redictKey) or $sToUrl = route('games.index');
        return Redirect::to($sToUrl)->with($sMsgType, $sMessage);
    }

    /**
     * go back
     * @param String $sMsgType in list: success, error, warning, info
     * @param String $sMessage
     * @return RedirectResponse
     */
    protected function goBack($sMsgType, $sMessage, $bWithModelErrors = false)
    {
//        $oRedirectResponse = Session::get($this->redictKey) ? Redirect::back() : Redirect::route('home');
        $oRedirectResponse = Redirect::back();
        $oRedirectResponse->withInput()->with($sMsgType, $sMessage);
        !$bWithModelErrors or $oRedirectResponse = $oRedirectResponse->withErrors($this->model->validationErrors);
        return $oRedirectResponse;
    }

    protected function setFunctionality()
    {
        $this->functionality = Functionality::getByCA($this->controller, $this->action, $this->admin);
    }

    public function setOrder()
    {
//        pr($this->params);
//        exit;
        if (Request::method() != 'POST') {
            return $this->goBack('error', __('_basic.method-error'));
        }
        if (!isset($this->params['sequence']) || !is_array($this->params['sequence'])) {
            return $this->goBack('error', __('_basic.data-error'));
        }
        $sModelName = $this->modelName;
        DB::connection()->beginTransaction();
        $bSucc = true;
        foreach ($this->params['sequence'] as $id => $sequence) {
            $oModel = $sModelName::find($id);
            if ($oModel->sequence == $sequence) {
                continue;
            }
            $oModel->sequence = $sequence;
            if (!$bSucc = $oModel->save(['sequence' => 'numeric'])) {
                break;
            }
        }
        if ($bSucc) {
            DB::connection()->commit();

            $sInfoType = 'success';
            $sLangKey = '_basic.ordered';
        } else {
            DB::connection()->rollback();
            $sInfoType = 'error';
            $sLangKey = '_basic.order-fail';
        }
        return $this->goBack($sInfoType, __($sLangKey));
    }

    /**
     * 输出信息并终止运行
     * @param String $msg
     */
    protected function halt($bSuccess, $sType, $iErrno, & $aSuccessedBets = null, & $aFailedBets = null, & $aData = null, $sLinkUrl = null)
    {
        is_object($this->Message) or $this->Message = new Message($this->errorFiles);
        $this->Message->output($bSuccess, $sType, $iErrno, $aData, $aSuccessedBets, $aFailedBets, $sLinkUrl);
        exit;
    }

    /**
     * 输出Json数据
     * @param array $msg
     */
    protected function jsonEcho($msg)
    {
        header('Content-Type: application/json');
        echo json_encode($msg);
        exit();
    }

    protected function writeLog($msg)
    {
        !is_array($msg) or $msg = var_export($msg, true);
        file_put_contents('/tmp/bet', $msg . "\n", FILE_APPEND);
    }

    /**
     * [createUserManageLog 生成管理员对用户进行操作的日志]
     * @param  [Integer] $iUserId [用户id]
     * @return [Boolean]          [成功/失败状态]
     */
    protected function createUserManageLog($iUserId, $sComment = null)
    {
        $iFunctionalityId = $this->functionality->id;
        $sFunctionality = $this->functionality->title;
        return UserManageLog::createLog($iUserId, $iFunctionalityId, $sFunctionality, $sComment);
    }

    public function __destruct()
    {
        if (SysConfig::check('sys_use_sql_log', true)) {
            $sLogPath = Config::get('log.root') . DIRECTORY_SEPARATOR . 'sql' . DIRECTORY_SEPARATOR . date('Ymd');
//            pr($sLogPath);
            if (!file_exists($sLogPath)) {
                @mkdir($sLogPath, 0777, true);
                @chmod($sLogPath, 0777);
            }
            $sLogFile = $sLogPath . DIRECTORY_SEPARATOR . date('H') . '.sql';
            if (!$queries = DB::getQueryLog()) {
                return;
            }
//            $me       = DB::connection();
//            pr($queries);
            foreach ($queries as $aQueryInfo) {
//                $sql       = $aQueryInfo['query'];
                $sql = '';
                $aSqlParts = explode('?', $aQueryInfo['query']);
                foreach ($aSqlParts as $i => $sPart) {
                    $sql .= $aSqlParts[$i];
                    if (isset($aQueryInfo['bindings'][$i])) {
                        $bindings = $aQueryInfo['bindings'][$i];
                        !(is_string($bindings) && strlen($bindings) > 0 && $bindings{0} != "'") or $bindings = "'" . $bindings . "'";
                        $sql .= $bindings;
                    }
                }
                $aLogs[] = $sql;
                $aLogs[] = number_format($aQueryInfo['time'], 3) . 'ms';
//                pr($sql);
            }

            @file_put_contents($sLogFile, date('Y-m-d H:i:s') . "\n", FILE_APPEND);
            @file_put_contents($sLogFile, 'controller: ' . $this->controller . ' action: ' . $this->action . "\n", FILE_APPEND);
            @file_put_contents($sLogFile, implode("\n", $aLogs) . "\n\n", FILE_APPEND);
        }
    }

    public function destroy($id = null)
    {
        if (!$this->filterRights($id))
            App::abort(404);
        return parent::destroy($id);
    }

    /**
     * [filterRights 过滤访问权限，只有属于该用户或总代的记录可以被访问]
     * @param  [Integer] $id [数据记录的id]
     * @return [Integer]     [是否有权限, 0:否, 1:是]
     */
    private function filterRights($id)
    {
        $bSucc = true;
        // 只需过滤view, edit, destroy三种视图
        if (in_array($this->action, ['view', 'edit', 'destroy'])) {
            $sModelName = $this->modelName;
            $sTable = $this->model->getTable();
            $originalColumns = Schema::getColumnListing($sTable);
            if (in_array('user_id', $originalColumns)) {
                $iUserId = Session::get('user_id');
                $rUserId = $sModelName::find($id)->user_id;
                $sForefatherIds = User::find($rUserId)->forefather_ids;
                $aForefatherIds = explode(',', $sForefatherIds);
                $bSucc = ($iUserId == $rUserId or in_array($iUserId, $aForefatherIds));
            }
        }
        // pr((int)$bSucc);exit;
        return $bSucc;
    }

    /**
     * [getSumData 获取统计值]
     * @param  [Array]  $aSumColumns [待统计的列]
     * @param  [boolean] $bPerPage   [是否按页统计，该功能采用视图中操作每页数据的方式实现，以前的逻辑暂时注释掉]
     * @return [Array]               [统计数据]
     */
    public function getSumData($aSumColumns, $bPerPage = false)
    {
        // TODO 和BaseController中的查询有所重复，后续改进
        $aConditions = &$this->makeSearchConditions();
        $oQuery = $this->model->doWhere($aConditions);
        $aRawColumns = [];
        foreach ($aSumColumns as $key => $value) {
            $aRawColumns[] = DB::raw('SUM(' . $value . ') as ' . $value . '_sum');
        }
        $aSum = [];
        $aSum = $oQuery->get($aRawColumns)->toArray();
        if (count($aSum))
            $aSum = $aSum[0];
        return $aSum;
    }

    /**
     * 将需要缓存的url信息保存到session中
     */
    protected function saveUrlToSession()
    {
        Session::forget('request_full_url');
        Session::push('request_full_url', request()->url());
    }

    /**
     * 返回先前请求的url信息
     * @param String $sRoute 路由信息
     * @return String
     */
    protected function getUrlFromSession($sDefaultUrl = 'home')
    {
        if (Session::has('request_full_url')) {
            $sUrl = Session::get('request_full_url')[0];
        } else {
            $sUrl = Route($sDefaultUrl);
        }
        return $sUrl;
    }

}





