<?php

# 提现管理
Route::group(['prefix' => 'withdrawal'], function () {
    $resource = 'user-withdrawal';
    $controller = 'UserWithdrawalController@';
    Route::get( '/index', ['as' => $resource . '.index', 'uses' => $controller . 'index']);
    Route::any('withdraw/{step?}', ['as' => $resource . '.withdraw', 'uses' => $controller . 'withdraw']);
});