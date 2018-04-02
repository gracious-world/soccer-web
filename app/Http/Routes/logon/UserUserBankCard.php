<?php
# 银行卡管理
$sPrefix = 'bank-cards';
Route::group(['prefix' => $sPrefix], function () use ($sPrefix) {
    $controller = 'UserUserBankCardController@';
    Route::get(                      '/', ['as' => $sPrefix . '.index',       'uses' => $controller . 'index']);
    Route::any(       '{step}/bind-card', ['as' => $sPrefix . '.bind-card',   'uses' => $controller . 'bindCard']);
    Route::any('{step}/{id}/modify-card', ['as' => $sPrefix . '.modify-card', 'uses' => $controller . 'modifyCard']);
    Route::any(           '{id}/destroy', ['as' => $sPrefix . '.destroy',     'uses' => $controller . 'customDestroy']);
    Route::any(     '/card-lock', ['as' => $sPrefix . '.card-lock',   'uses' => $controller . 'cardLock']);
});

