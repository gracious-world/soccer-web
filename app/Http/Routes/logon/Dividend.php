<?php

# 盈亏报表
Route::group(['prefix' => 'dividends'], function () {
    $resource = 'dividends';
    $controller = 'UserDividendController@';
    Route::get(         '/', ['as' => $resource . '.index',      'uses' => $controller . 'index']);

    Route::get(         '/old', ['as' => $resource . '.old',      'uses' => $controller . 'old']);//之前的分红报表,暂留,可删除
});