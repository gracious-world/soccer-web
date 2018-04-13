<?php

# 盈亏报表
Route::group(['prefix' => 'team-profits'], function () {
    $resource = 'team-profits';
    $controller = 'UserTeamProfitController@';
    Route::get(         '/', ['as' => $resource . '.index',      'uses' => $controller . 'index']);
    Route::get('commission', ['as' => $resource . '.commission', 'uses' => $controller . 'commission']);
    Route::get('self', ['as' => $resource . '.self', 'uses' => $controller . 'self']);
    Route::get('info', ['as' => $resource . '.info', 'uses' => $controller . 'info']);
    Route::get('member-profits', ['as' => $resource . '.member-profits', 'uses' => $controller . 'memberProfits']);
});