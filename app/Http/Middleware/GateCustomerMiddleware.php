<?php

namespace App\Http\Middleware;

use Closure;
use Config;
use App\Models\Basic\BusinessPartner;
use App\Models\Basic\GameType;

class GateCustomerMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $sResponseCode = 0;
        // 商户标识错误
        if (!$request->input('bp_identity') || !$oBusinessPartner = BusinessPartner::complexWhere(['identity' => $request->input('bp_identity'), 'status' => BusinessPartner::STATUS_ACTIVATED])->first()) {
            $sResponseCode = -100;
        }
        if (!$this->verifyToken($request, $oBusinessPartner->key)) {
            $sResponseCode = -101;
        }
        if ($sResponseCode < 0) {
            $sMsg = __(Config::get('custom-code.' . $sResponseCode));
            return response()->json(['coding' => $sResponseCode, 'msg' => $sMsg]);
        }
        return $next($request);
    }

    private function verifyToken($request, $token) {
        $aParams = $request->except('_token');
        ksort($aParams);
        $sLocalToken = md5(http_build_query($aParams) . $token);
        return $sLocalToken == $request->input('_token');
    }
}
