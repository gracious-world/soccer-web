<?php

# 账变管理
$sPrefix = 'user-transactions';
Route::group(['prefix' => $sPrefix], function () use ($sPrefix) {
    $controller = 'UserTransactionController@';
    Route::get(       '/', ['as' => $sPrefix . '.index', 'uses' => $controller . 'index']);
     Route::get('/{id?}/mydeposit', ['as' => $sPrefix . '.mydeposit', 'uses' => $controller . 'myDeposit']);
     Route::get('/{id?}/mywithdraw', ['as' => $sPrefix . '.mywithdraw', 'uses' => $controller . 'myWithdraw']);
    Route::get('transfer', ['as' => $sPrefix . '.transfer', 'uses' => $controller . 'transfer']);
    Route::get('mybonus/{id?}', ['as' => $sPrefix . '.mybonus', 'uses' => $controller . 'myBonus']);
    // Route::any('create', ['as' => $sPrefix . '.create', 'uses' => $controller . 'create']);
    // Route::get('{id}/view', ['as' => $sPrefix . '.view', 'uses' => $controller . 'view']);
    // Route::any('{id}/edit', ['as' => $sPrefix . '.edit', 'uses' => $controller . 'edit']);
    // Route::delete('{id}', ['as' => $sPrefix . '.destroy', 'uses' => $controller . 'destroy']);
    // Route::get('/mini-window', ['as' => $sPrefix . '.mini-window', 'uses' => $controller . 'miniWindow']);
    Route::get('jc-index', ['as' => $sPrefix. '.jc-index', 'uses' => 'UserJcTransactionController@index']);
});