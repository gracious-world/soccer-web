<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

use App\Models\Basic\BusinessPartner;
use App\Models\Basic\GameType;
use App\Models\Bet\Bill;
use App\Models\Bet\BillOdd;
use App\Models\Game\Game;
use App\Models\Game\Odd;
use App\Models\Game\Method;
use App\Models\Game\Way;
use App\Models\Game\WayOdd;

use Restable;
use DB;
use Config;
use Carbon;
use Auth;
use Session;
use SessionTool;

class UserGateController extends Controller
{
    /**
     * [
     *     'identity' => '',
     *     'user'     => [
     *         'user_id'        => '',
     *         'username'       => '',
     *         'name'           => '',
     *         'nickname'       => '',
     *         'email'          => '',
     *         'is_agent'       => '',
     *         'is_tester'      => '',
     *         'login_ip'       => '',
     *         'rigister_id'    => '',
     *         'signin_at'      => '',
     *         'blocked'        => '',
     *         'forefathers'    => '',
     *         'forefather_ids' => '',
     * ]
     *
     */
    public function login(Request $request) {
        if (!$request->input('username')) {
            return $this->renderData(-700);
        }
        $sUsername = $request->input('username');
        $sIdentity = $request->input('identity');
        $sPwd = strtolower($sUsername) . strtolower($sIdentity) . $sUsername;
        $sPwd = md5(md5(md5($sPwd)));
        SessionTool::deleteSession(false, $sUsername);
        SessionTool::saveSessionId(false, $sUsername, Session::getId());
        // pr($request->input('identity'));
        // pr($request->input('username'));
        // exit;
        if (Auth::attempt(['username' => $sUsername, 'password' => $sPwd])) {
            pr($request->session()->get('_token'));
            pr(SessionTool::getSessionData(false, $sUsername));
            return Session::getId();
            // return redirect()->intended('/');
        } else {
            return $this->renderData(-701);
        }
    }
    public function testRender() {
        return $this->renderData(-700);
    }

}