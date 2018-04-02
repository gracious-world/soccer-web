<?php
/**
 * 注册和登录
 */
Route::group(['prefix' => 'auth'], function () {
    $Authority = 'AuthorityController@';
    # 退出
    Route::get(                   'logout', ['as' => 'logout', 'uses' => $Authority . 'logout']);
    Route::match(['get', 'post'], 'signin', ['as' => 'signin', 'uses' => $Authority . 'signin']);
    Route::match(['get', 'post'], 'signup', ['as' => 'signup', 'uses' => $Authority . 'signup']);
//    Route::match(['get', 'post'], 'signin', ['as' => 'signin', 'uses' => $Authority . 'signin'])->middleware('extra-auth');
});
