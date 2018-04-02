<?php

# User管理
$sPrefix = 'message';
Route::group(['prefix' => $sPrefix], function () use ($sPrefix) {
    $controller = 'UserMessageController@';
    Route::get('/receiver', ['as' => $sPrefix . '.receiver','uses' => $controller . 'receiver']);
    Route::get('/sendlist', ['as' => $sPrefix . '.sendlist','uses' => $controller . 'sendlist']);
    Route::get('/send', ['as' => $sPrefix . '.send','uses' => $controller . 'send']);
    Route::get('{id}/view', ['as' => $sPrefix . '.view','uses' => $controller . 'viewMessage']);
    Route::get('get-user-messages', ['as' => $sPrefix . '.get-user-messages', 'uses' => $controller . 'getUserMessages']);
    Route::post('send-message', ['as' => $sPrefix . '.send-message','uses' => $controller . 'sendMessage']);
});

