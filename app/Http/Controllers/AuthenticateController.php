<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

use App\Models\User\Role;
use App\Models\User\User;

class AuthenticateController extends Controller
{
    public function authenticate(Request $request)
    {
        // grab credentials from the request
        // $credentials = $request->only('username', 'identity');
        $sUsername = $request->input('username');
        $sIdentity = $request->input('bp_identity');
        $sPwd = strtolower($sUsername) . strtolower($sIdentity) . $sUsername;
        $sPwd = md5(md5(md5($sPwd)));
        $credentials = ['username' => $sUsername, 'password' => $sPwd];
        $user = User::where('username', $sUsername)->where('bp_identity', $sIdentity)->first();
        $roles = $this->_getUserRole($user);
        // pr($user->toArray());exit;
        try {
            // attempt to verify the credentials and create a token for the user
            if (! $token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'invalid_credentials'], 401);
            }
        } catch (JWTException $e) {
            // something went wrong whilst attempting to encode the token
            return response()->json(['error' => 'could_not_create_token'], 500);
        }
        $request->session()->put('token', $token);
        $request->session()->put('bp_identity', $user->bp_identity);
        $request->session()->put('bp_name', $user->bp_name);
        $request->session()->put('user_id', $user->id);
        $request->session()->put('username', $user->username);
        $request->session()->put('nickname', $user->nickname);
        $request->session()->put('language', $user->language);
        $request->session()->put('account_id', $user->account_id);
        $request->session()->put('forefather_ids', $user->forefather_ids);
        $request->session()->put('is_agent', $user->is_agent);
        $request->session()->put('prize_group', $user->prize_group);
        $request->session()->put('is_tester', $user->is_tester);
        $request->session()->put('is_top_agent', $user->is_agent && empty($user->parent_id));
        $request->session()->put('is_player', !$user->is_agent);
        $request->session()->put('CurUserRole', $roles);
        $request->session()->put('portraitCode', $user->portrait_code);
        // all good so return the token
        return response()->json(compact('token'));
    }

    protected function _getUserRole($oUser) {
        $roles = $oUser->getRoleIds();

        $aDefaultRoles[] = Role::EVERY_USER;

        if ($oUser->is_agent) {
            $aDefaultRoles[] = Role::AGENT;
            if (empty($oUser->parent_id)) {
                $aDefaultRoles[] = Role::TOP_AGENT;
            }
        } else {
            $aDefaultRoles[] = Role::PLAYER;
        }
        $roles = array_merge($roles, $aDefaultRoles);
        $roles = array_unique($roles);
        $roles = array_map(function($value) {
            return (int) $value;
        }, $roles);

        return $roles;
    }
}