<?php
$sPrefix = 'bills';
Route::group(['prefix' => $sPrefix], function () use ($sPrefix) {
    $controller = 'UserBillController@';
    // Route::get(                    '/', ['as' => $sPrefix . '.index',         'uses' => $controller . 'index']);
    // Route::get(            '{id}/view', ['as' => $sPrefix . '.view',          'uses' => $controller . 'view']);
    // Route::post('betting/{game_type?}', ['as' => $sPrefix . '.betting',       'uses' => $controller . 'betting']);
    // Route::get(   '{id}/ticket-detail', ['as' => $sPrefix . '.ticket-detail', 'uses' => $controller . 'ticketDetail']);
});