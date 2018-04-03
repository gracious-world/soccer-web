<?php
use App\Models\Basic\BusinessPartner;
use App\Models\Admin\AdminUser;
use App\Models\Admin\Role;
use App\Models\Func\Functionality;
use App\Models\Func\FunctionalityRelation;

use App\Models\User\User;
use App\Models\Game\Game;
use App\Models\Game\Method;
use App\Models\Game\Way;
use App\Models\Game\WayOdd;
use App\Models\Game\ChangedOdd;
use App\Models\Bet\Program;
use App\Models\Bet\Bill;
use App\Models\BaseTask;
use App\Models\Fund\Account;

use App\Events\CalculateBillEvent;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\DispatchesJobs;

use App\Jobs\PrintTicketJob;
use App\Models\User\UserLogin;

// use DispatchesJobs;
// use Cache;
// use Config;

Route::get('testCase', ['as' => 'test-case', 'uses' => function () {
    // $data = (base64_decode('eyJlcnJubyI6MCwiZXJyb3IiOm51bGwsImRhdGEiOm51bGwsInNpZ24iOiIzOGIyMzZiM2E5Mzc1ODdjYTQyZGQ4MWYxM2Q2OGE5NyJ9'));
    // $data = (json_decode($data, true));
    // pr($data['errno']);
    // exit;
    // $oAccount = Account::where('username', 'toptop02')->first();
    // $oCustomer = BusinessPartner::getActivateBusinessParnter('JPG');
    // $oAccount->autoCharge($oCustomer, $aAutoChargeData, $aResponse);
    // pr($aResponse);
    // exit;
    // $oGame = Game::find(4642); // 4642
    // pr($oGame->calculateWonResults(false));
    // exit;
    // pr(Carbon::now()->toDateString());
    // $result = Game::getSaleStopAt('2016-06-15 03:00:00', 3);
    // pr($result);
    // exit;

    // $sPlayedAt = strtotime('2016-06-16 03:00:00');
    // $date = date('Y-m-d', $sPlayedAt);
    // $time = date('H:i:s', $sPlayedAt);
    // pr($date);
    // pr($time);
    // exit;
    $aData = [
        'identity' => 'JMG',
        'username' => 'toptop'
    ];
    ksort($aData);
    $sign = md5(http_build_query($aData) . '22EbOsD3GmnLEw');
    pr($sign);
    exit;

    $oProgram = new Program;
    pr(Method::getFreeGateTypes(1));
    pr(Method::getFreeGateTypes(1));
    pr($oProgram->getGates(["gate" => ["chp"]]));
    pr(Method::getGateNameAndIdMap(1));

    exit;

    $data = '{"max_gate":1,"gate":["chp"],"games":{"EURO2016CHPAT":["chp_45.00"],"EURO2016CHPCH":["chp_40.00"]},"dan":[],"bet_num":"2","multiple":"10","amount":"40.00"}';
    $data = json_decode($data, true);
    $data = $oProgram->verifyMaxGate(2, $data);
    pr($data);
    exit;

    $bet_content = '{"20160524YX003":["h_2.9300"],"20160524YX004":["h_1.9700"]}';
    $bet_odds_display = '{"20160524YX003":["\u80dc[2.93]"],"20160524YX004":["\u80dc[1.97]"]}';
    $aBetOdds = json_decode($bet_content, true);
    $aBetOddsDisplay = json_decode($bet_odds_display, true);
    foreach ($aBetOdds as $sGameBn => $aBetOdd) {
        list($sOddIdentity, $iOdd) = explode('_', $aBetOdd[0]);
        $aBetOddsDisplay[$sGameBn] = [$sOddIdentity => $aBetOddsDisplay[$sGameBn][0]];
    }
    pr(json_encode($aBetOddsDisplay));exit;

    // $oProgram = Program::find(3);
    // $aData = json_decode($oProgram->bet_odds_display, true);
    // // $aContent = json_decode($oProgram->bet_content, true);
    // pr($aData);
    // // pr($aContent['games']);
    // $aResult = $oProgram->compileBillOddDataSingle($aData, true);
    // pr(json_encode($aResult));
    // exit;


    // $data = '{"max_gate":8,"gate":["8x1"],"games":{"20160401YX005":["h_1.9500","ch_3.9000"],"20160401YX010":["h_1.7800","ch_3.6500"],"20160401YX024":["h_1.8400","ch_3.6000"],"20160401YX030":["h_1.9300","ch_3.8000"],"20160401YX032":["h_3.5000","ch_1.6100"],"20160402YX005":["h_2.2200","ch_4.5500"],"20160402YX008":["h_1.7500","ch_3.4000"],"20160402YX064":["h_1.4300","a_6.1000","d_3.8500"],"20160402YX070":["h_2.2400","ch_5.1000"],"20160402YX083":["d_4.6000"],"20160403YX005":["h_2.7500","ch_1.4500"],"20160403YX010":["h_1.8800","cd_3.5000"],"20160403YX027":["h_2.0200","cd_3.8500"]},"bet_num":"299904","multiple":"1","amount":"599808","dan":[]}';
    // $data = json_decode($data, true);
    // $oBill = new Bill;
    // $aGates = $oBill->getGates($data['gate']);
    // $iCount = $oBill->caculateOrderCount($data['games'], $aGates, $data['dan']);
    // pr($iCount);exit;
    // // BaseTask::addTask('PrintTicket', ['program_id' => 62], 'ticket');
    // pr(preg_match('/^[0-9]+(.[0-9]{1,2})?$/', '2.1'));exit;
    // $oBill = Bill::find(512);
    // pr($oBill->program->toArray());exit;
    // $data = $oBill->games()->get();
    // pr($data[0]->toArray());exit;

    // $oGame = Game::find(4172);
    // pr($oGame->bills()->get()->toArray());
    // exit;

    $oProgram = Program::find(55);
    $data = $oProgram->bills; //()->get()->sum('prize');
    pr($data->toArray());exit;

    $aWayOddMap = WayOdd::getWayOddsByGameType(1);
    $aOddMaxGates = array_map(function($item) {
        return $item['max_connect_games'];
    }, $aWayOddMap['way_odd_map']);
    pr($aOddMaxGates);exit;

    $aData = [
        'amount' => 1000.8,
        'identity' => 'JMG',
        'username' => 'toptop',
    ];
    ksort($aData);
    $sign = md5(http_build_query($aData) . '22EbOsD3GmnLEw');
    $aData['sign'] = $sign;
    $return = Curl::to('http://jc.dca.com/data-gate/transfer-in')->withData($aData)->post();
    pr($return);
    exit;
    // pr(Carbon::createFromFormat('Y-m-dH:i:s', '2016-05-1713:45:44')->toDateTimeString());
    pr(Carbon::createFromTimestampUTC(1463463944)->toDateTimeString());
    pr(Carbon::createFromTimestamp(1463463944)->toDateTimeString());
    exit;
    // $oBusinessPartner = BusinessPartner::getActivateBusinessParnter('JPG');
    // pr($oBusinessPartner->toArray());exit;
    // pr(preg_match('/^[a-zA-Z][a-zA-Z0-9]+$/', 'jyzdavy1'));exit;
    $aParams = ['identity' => 'JPG', 'username' => 'toptop'];
    // $aParams = ['identity' => 'EDF', 'created_at_from' => '2016-05-12 15:10:47', 'created_at_to' => '2016-05-13 15:10:47'];
    ksort($aParams);
    pr($aParams);
    pr(http_build_query($aParams));
    pr(md5(http_build_query($aParams) . '22EbOsD3GmnLEw'));// &sign=3582f38d1a27bea7184bfe65405c3c32
    // pr(md5('created_at_from=2016-05-12 15:10:47&created_at_to=2016-05-13 15:10:47&identity=EDFc615e352198a'));
    exit;
    // pr(BusinessPartner::getActivateBusinessParnter('JYZ'));exit;
    $oGame = Game::find(2937);
    $aOddParams = [
        'gt_id' => 1,
        'g_id'  => $oGame->id,
        'w_id'  => ['in', [1,2]],
    ];
    $aChangedOdds = [];
    $oChangedOdds = ChangedOdd::complexWhere($aOddParams)->orderBy('created_at', 'asc')->get();
    foreach ($oChangedOdds as $key => $oChangedOdd) {
        if (!isset($aChangedOdds[$oChangedOdd->wo_identity])) $aChangedOdds[$oChangedOdd->wo_identity] = [];
        $aChangedOdds[$oChangedOdd->wo_identity][] = $oChangedOdd->odd;
    }
    pr($aChangedOdds);exit;
    // pr(array_slice($aChangedOdds['a'], 0 ,1)[0]);exit;
    pr($oGame->real_sale_stop_time);
    pr($oGame->changed_odds->pluck('odd', 'wo_identity')->toArray());
    exit;
    pr(WayOdd::getWayOddsRevertMapByGameTypeFromDB(1));exit;
    $oGames = Game::find(2571);
    pr($oGames->game_result_display);exit;
    $data = '{"max_gate":1,"gate":["1x1"],"games":{"20160413YX008":["h_3.02"],"20160413YX009":["d_4.00"],"20160414YX002":["h_2.65"]},"dan":[],"bet_num":3,"multiple":"10","amount":60}';
    $data = json_decode($data, true);
    $oBill = new Bill;
    pr($oBill->verifyBillData($data));
    exit;
    // if (!$data1 = Cache::tags('test1')->get('key123')) {
    //     pr(1);
    //     $data1 = ['a' => [1,2,3], 'b' => [4,5,6]];
    //     Cache::tags('test1')->put('key123', $data1, 5);
    // }
    // if (!$data2 = Cache::tags('test2')->get('key123')) {
    //     pr(2);
    //     $data2 = ['a', 'b'];
    //     Cache::tags('test2')->put('key123', $data2, 5);
    // }
    // pr($data1);
    // pr($data2);
    // exit;
    $data = '{"max_gate":8,"gate":["3x1"],"games":{"20160404YX004":["ch_1.78"],"20160404YX005":["ch_1.50"],"20160404YX007":["cd_3.25"]},"dan":[],"bet_num":1,"multiple":"10","amount":20}';

    $data = json_decode($data, true);
    pr($data);exit;
    $url = "http://su.japple518.com/bills/betting/football";
    $response = Curl::to($url)->withData($data)->post();
    pr($response);
    exit;
    DB::enableQueryLog();
    $count = UserLogin::getLoginUserCount('2015-09-08 00:00:00');
    $queries = DB::getQueryLog();
    $last_query = end($queries);
    pr($last_query);
    pr($count);exit;

    $oUser = User::find(139);
    $sPwd = strtolower($oUser->username) . strtolower($oUser->bp_identity) . $oUser->username;
    $sPwd = md5(md5(md5($sPwd)));
    pr($sPwd);
    $credentials = ['username' => $oUser->username, 'bp_identity' => $oUser->bp_identity];
    $bSucc1 = Auth::attempt($credentials);
    $bSucc2 = Hash::check($sPwd, $oUser->password);
    pr(intval($bSucc1));
    pr(intval($bSucc2));

    exit;

    // $oUsers = User::all();
    // foreach ($oUsers as $oUser) {
    //     $sPwd = (strtolower($oUser->bp_identity) . $oUser->username);
    //     $data = ['password' => $sPwd, 'password_confirmation' => $sPwd];
    //     $oUser->resetPassword($data);
    // }
    // exit;

    // pr(Config::get('custom-sysconfig'));exit;
    pr(Carbon::today()->hour(25)->hour);exit;
    $sStart = Carbon::today()->addHours(9);
    $sWorkdayEnd = (Carbon::today()->addDay());
    $sWeekendEnd = (Carbon::today()->addDay()->addHour());
    $oGame = Game::find(2008);
    pr($oGame->real_sale_stop_at);
    pr($oGame->sale_stop_at);
    pr($sStart);
    pr($sWorkdayEnd);
    pr('<<<' . boolval($oGame->sale_stop_at < $sStart));
    pr('>>>' . boolval($oGame->sale_stop_at > $sWorkdayEnd));
    exit;
    pr($oGame->get(['bn', 'h_cn']))->toArray();exit;
    // event(new CalculateBillEvent($oGame));
    // // $oBills = $oGame->bills()->where('bills.status', Bill::STATUS_TICKET_PRINTED)->get();
    // // pr($oBills->toArray());
    // exit;

    $oBills = Bill::where('status', 0)->get();
    // $oBill = Bill::find(19);
    pr(Carbon::now()->toDateTimeString());
    foreach ($oBills as $oBill) {
        // $job = (new PrintTicketJob($oBill))->onQueue('jcdc-ticket')->delay(60);
        // app('Illuminate\Contracts\Bus\Dispatcher')->dispatch($job);
        BaseTask::addTask('PrintTicket', ['bill_id' => $oBill->id], 'ticket');
    }
    exit;
    $oBill = Bill::find(19);
    // $oBill->update(['status' => 2]);exit;
    // pr(Carbon::now()->toDateTimeString());
    // pr($oBill->toArray());
    $bSucc = BaseTask::addTask('PrintTicket', ['data' => $oBill], 'ticket', 60);
    // pr($bSucc);
    exit;
    pr(substr('a\b\c', strrpos('a\b\c', '\\') +1));exit;
    $oBill = Bill::find(7);
    $bSucc = $oBill->update(['status' => Bill::STATUS_TICKET_PRINTED]);
    pr($bSucc);
    exit;
    $aOddNames = WayOdd::where('gt_id', 1)->get()->pluck('name', 'identity')->toArray();
    pr($aOddNames);
    exit;
    $oGame = Game::find(1);
    $aResult = $oGame->calculateWonResults(false);
    pr($aResult);exit;

    $oWay = Way::find(1);
    $oOdds = $oWay->way_odds->pluck('name', 'identity')->toArray();
    pr($oOdds);exit;
    pr(Config::get('custom-sysconfig.default-log-path'));
    exit;
    pr(Method::whereIn('identity', $data['gate'])->pluck('id'));exit;
    $oGames = Game::getGamesByPlayedAt(1, '2016-02-23');
    // $queries = DB::getQueryLog();
    // $last_query = end($queries);
    // pr($last_query);
    // exit;
    pr($oGames->toArray());exit;

    $key = md5('App\Models\Admin\User') . '_64';
    $cache = Cache::get($key);
    pr($cache);exit;
    for ($i=0; $i < 10; $i++) {
        // echo uniqid(rand(1, 100000));
        $year_code = array('A','B','C','D','E','F','G','H','I','J');
        $order_sn = $year_code[intval(date('Y'))-2016].
        strtoupper(dechex(date('m'))).date('d').
        substr(time(),-5).substr(microtime(),2,5).sprintf('%02d',rand(0,99));
        echo $order_sn; // date('ymd').substr(time(),-5).substr(microtime(),2,5);
        echo "</br>";
    }
    // $uid = uniqid(rand(1, 100000));
    // pr($uid);
    // $uid = sha1($uid);
    // pr($uid);
    exit;
    $aData = FunctionalityRelation::all();
    pr($aData->toArray());exit;

    // $sRouter = 'App\Http\Controllers\HomeController@getHome';
    $sRouter = 'App\\Http\\Controllers\\FunctionalityController@index';
    // $sRouter = 'App\\Http\\Controllers\\UserController@index';
    $oRouter = Route::getRoutes()->getByAction($sRouter);
    // $sRouter = 'home';
    // $oRouter = Route::getRoutes()->getByName($sRouter);

    pr($oRouter->getName());exit;
    // $aRoutes = Route::getRoutes();
    // pr($aRoutes);exit;
    // pr(Request::ip());
    // pr('\n');
    // pr(Request::ips());
    // pr($_SERVER['REMOTE_ADDR']);
    // pr(app()->setLocale('zh-CN'));
    // pr(app()->getLocale());
    // return Route::currentRouteAction();

    // $aRights = & Role::getRights(1);
    // pr($aRights);exit;
    // $aSystemFuncs = Functionality::whereIn('realm',[Functionality::REALM_SYSTEM, Functionality::REALM_ADMIN])->lists('id');
    // pr(count($aSystemFuncs->toArray()));exit;
    // $oUser = User::find(51);
    // $aRoles = $oUser->getUserRoles();
    // return $aRoles;
    // return view('welcome');

}]);