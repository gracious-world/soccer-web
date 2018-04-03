<?php

# 系统广播
Route::group(['prefix' => 'system-notice'], function () {
    $resource = 'system-notices';
    $controller = 'SystemNoticeController@';
    Route::get(           '/', ['as' => $resource . '.index',        'uses' => $controller . 'index']);
    Route::get(   '{id}/view', ['as' => $resource . '.view',         'uses' => $controller . 'view']);
    Route::get('user-notices', ['as' => $resource . '.user-notices', 'uses' => $controller . 'getUserNotices']);
});
