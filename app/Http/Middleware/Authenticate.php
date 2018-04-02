<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;
use Session;
use Route;
use Config;
use Illuminate\Support\Facades\Redirect;
use Auth;

class Authenticate
{
    /**
     * The Guard implementation.
     *
     * @var Guard
     */
    protected $auth;

    /**
     * Create a new middleware instance.
     *
     * @param  Guard  $auth
     * @return void
     */
    public function __construct(Guard $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
//         if (!Session::get('user_id')){
//             if (isset($_SERVER[ 'HTTP_X_REQUESTED_WITH' ]) && $_SERVER[ 'HTTP_X_REQUESTED_WITH' ] == 'XMLHttpRequest'){
//                 $iErrno   = Config::get('global_error.ERRNO_LOGIN_EXPIRED');
//                 $oMessage = new Message(['system']);
//                 $oMessage->output(0,'loginTimeout',$iErrno,[]);
//                 exit;
//             }
//             else{
// //                if (!in_array(Route::currentRouteName(), Config::get('no_restore_routes'))){
//                     Session::put('__returnUrl', $request->getRequestUri());
// //                }
//                 return Redirect::route('signin');
//             }
//         }

//         return $next($request);
        if (!Session::get('user_id')) {
            if ($request->ajax()) {
                return response('Unauthorized.', 401);
            } else {
                return redirect()->route('signin');
            }
        }

        return $next($request);
    }
}
