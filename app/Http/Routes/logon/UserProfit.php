<?php

# 盈亏报表
Route::group(['prefix' => 'user-profits'], function () {
    $resource = 'user-profits';
    $controller = 'UserUserProfitController@';
    Route::get(         '/', ['as' => $resource . '.index',      'uses' => $controller . 'index']);
    Route::get(         '/myself', ['as' => $resource . '.myself',      'uses' => $controller . 'myself']);
    Route::get(         '/test', ['as' => $resource . '.test',      'uses' => $controller . 'test']);
    Route::any(         '/{id}/master-show-bills', ['as' => $resource . '.master-show-bills',      'uses' => $controller . 'masterShowBills']);
    Route::any(         '/master-show-bills', ['as' => $resource . '.master-show-bills',      'uses' => $controller . 'masterShowBills']);
    Route::get(         '/{game_type}/{id}/heros', ['as' => $resource . '.heros',      'uses' => $controller . 'heros']);
    Route::get('commission', ['as' => $resource . '.commission', 'uses' => $controller . 'commission']);

});