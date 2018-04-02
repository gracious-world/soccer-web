<?php
$sPrefix = 'notices';
Route::group(['prefix' => $sPrefix], function () use ($sPrefix) {
    $controller = 'UserLotteryNoticeController@';
    Route::get(      '/', ['as' => $sPrefix . '.index',    'uses' => $controller . 'index']);
    Route::get( 'latest', ['as' => $sPrefix . '.latest',   'uses' => $controller . 'latest']);
});