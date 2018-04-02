<?php
$sPrefix = 'user-activity-user-bonuses';
Route::group(['prefix' => 'user-activities'], function () use ($sPrefix) {
    $controller = 'UserActivityUserBonusController@';
    Route::get('/', ['as' => $sPrefix . '.index', 'uses' => $controller . 'index']);
//    Route::get('/unavailable', ['as' => $sPrefix . '.unavailable', 'uses' => $controller . 'unAvailableHB']);
    Route::get('/available', ['as' => $sPrefix . '.available', 'uses' => $controller . 'availableHB']);
    Route::get('/unavailable', ['as' => $sPrefix . '.unavailable', 'uses' => $controller . 'unavailableHB']);
    Route::get('/received', ['as' => $sPrefix . '.received', 'uses' => $controller . 'receivedHB']);
    Route::get('/expired', ['as' => $sPrefix . '.expired', 'uses' => $controller . 'expiredHB']);
    Route::post('/get-bonus', ['as' => $sPrefix . '.get-bonus', 'uses' => $controller . 'getPrize']);
    // Route::any(   'create', ['as' => $sPrefix . '.create',  'uses' => $controller . 'create']);
    // Route::get('{id}/view', ['as' => $sPrefix . '.view',    'uses' => $controller . 'view']);
    // Route::delete(  '{id}', ['as' => $sPrefix . '.destroy', 'uses' => $controller . 'destroy']);
});
