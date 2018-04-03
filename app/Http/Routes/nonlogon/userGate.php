<?php
# 用户登录/注册接口

Route::group(['prefix' => 'user-gate'], function () {// , 'middleware' => 'gate-customer'
    $sController = 'UserGateController@';
    // Route::any(     'login', ['as' => 'user-gate.login',       'uses' => $sController . 'login']);
    Route::any('login', ['as' => 'user-gate.login', 'uses' => 'AuthenticateController@authenticate']);
    Route::get('test-render', ['as' => 'user-gate.test-render', 'uses' => $sController . 'testRender']);
    // Route::match(['get', 'post'], 'bill-results', ['as' => 'data.pull-bills', 'uses' => $sController . 'getBills']);
});