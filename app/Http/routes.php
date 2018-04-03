<?php

/*
 * 加载必须要登录才可访问的路由
 */
Route::group(['middleware' => 'auth'], function () {
    // $controller = 'UserGameController@';
    // Route::get('/', ['as' => 'games.index', 'uses' => $controller . 'index']);
    # include
    loadRoutes(Config::get('route.logon'));
});

/*
 * 加载不需要登录即可访问的路由
 */
loadRoutes(Config::get('route.nonlogon'));


function loadRoutes($sPath){
    $controller = 'UserGameController@';
    Route::get('/', ['as' => 'games.index', 'uses' => $controller . 'index']);

    $aRouteFiles = glob($sPath . '*.php');
    foreach ($aRouteFiles as $sRouteFile) {
        include($sRouteFile);
    }
    unset($aRouteFiles);
}
