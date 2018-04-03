<?php

# 充值管理
Route::group(['prefix' => 'deposit'], function () {
    $resource = 'user-recharges';
    $controller = 'UserDepositController@';
    Route::get('/', ['as' => $resource . '.index', 'uses' => $controller . 'index']);
    Route::any('quick/{id}', ['as' => $resource . '.quick', 'uses' => $controller . 'quick']);
//    Route::post( 'confirm', ['as' => $resource . '.confirm', 'uses' => $controller . 'confirm']);
});
