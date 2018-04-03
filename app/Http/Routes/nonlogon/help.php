<?php

/**
 * 帮助中心
 */
Route::group(['prefix' => 'help'], function () {
    $resource = 'help';
    $controller = 'HelpController@';
    Route::get('/{category_id?}', ['as' => $resource . '.index', 'uses' => $controller . 'helpIndex']);
});

