<?php

namespace App\Http\Middleware;

use Closure;
use Config;
use App\Models\Basic\BusinessPartner;
use App\Models\Basic\GameType;

class ExtraAuthMiddleware
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
        $sResponseCode = 1;
        if ($request->isMethod('POST')) {
            // 商户标识错误
//            if (!$request->input('identity') || !$oBusinessPartner = BusinessPartner::getActivateBusinessParnter($request->input('identity'))) {
//                $sResponseCode = -100;
//            }
//            if ($sResponseCode > 0 && !$this->verifyToken($request, $oBusinessPartner->key)) {
//                $sResponseCode = -101;
//            }
//            if ($sResponseCode < 0) {
//                $sMsg = __(Config::get('custom-code.' . $sResponseCode));
//                return response()->json(['coding' => $sResponseCode, 'msg' => $sMsg]);
//            }
        }
        return $next($request);
    }

    private function verifyToken($request, $key) {
        $aParams = $request->except('sign', 'skin', '_token');
        ksort($aParams);
        $sLocalSign = md5(http_build_query($aParams) . $key);
        // pr($sLocalSign);
        // pr($request->input('sign'));
        // exit;
        return $sLocalSign == $request->input('sign');
    }
}
