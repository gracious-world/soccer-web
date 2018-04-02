<?php

# 站内信
Route::group(['prefix' => 'letters'], function () {
    $resource = 'station-letters';
    $controller = 'StationLetterController@';
    Route::get(                '/', ['as' => $resource . '.index',             'uses' => $controller . 'index']);
    Route::get(        '{id}/view', ['as' => $resource . '.view',              'uses' => $controller . 'viewMessage']);
    Route::get('get-user-messages', ['as' => $resource . '.get-user-messages', 'uses' => $controller . 'getUserMessages']);
    Route::get(     '/delete/{id}', ['as' => $resource . '.delete-message',    'uses' => $controller . 'deleteMessage']);
    Route::get(           'outbox', ['as' => $resource . '.outbox',            'uses' => $controller . 'getSentMessages']);
    Route::any(     'send-message', ['as' => $resource . '.send-message',      'uses' => $controller . 'sendMessage'])->before('ajax');
    // Route::get('get-user-unread-num', ['as' => $resource . '.get-user-unread-num', 'uses' => $controller . 'getUserUnreadNum']);
});

/*零时路由*/
// Route::any('letters/outbox', ['as' => 'outbox', function () {

//     return View::make('centerUser.stationLetter.outbox');

// }]);
// Route::any('letters/sendmessage', ['as' => 'sendmessage', function () {

//     return View::make('centerUser.stationLetter.sendMessage');

// }]);
