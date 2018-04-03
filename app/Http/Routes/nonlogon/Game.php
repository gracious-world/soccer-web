<?php
/**
 * 游戏路由.
 * User: damon
 * Date: 2/9/16
 * Time: 1:04 PM
 */
$sPrefix = 'games';
Route::group(['prefix' => $sPrefix], function () use ($sPrefix) {
    $controller = 'UserGameController@';
    Route::get(                   '/', ['as' => $sPrefix . '.index',  'uses' => $controller . 'index']);
    Route::get(             'danguan', ['as' => $sPrefix . '.single', 'uses' => $controller . 'singleGames']);
    Route::get(         'focus-games', ['as' => $sPrefix . '.focus',  'uses' => $controller . 'focusGames']);
    Route::get(          'rank-games', ['as' => $sPrefix . '.rank',   'uses' => $controller . 'rankGames']);
    Route::get( 'result/{game_type?}', ['as' => $sPrefix . '.result', 'uses' => $controller . 'result']);
    Route::get(  'trend/{game_type?}', ['as' => $sPrefix . '.trend',  'uses' => $controller . 'oddTrend']);
    Route::get( 'game-data/{game_type}/{bet_type}/{way_type?}/{is_focus?}', ['as' => $sPrefix . '.game-data',   'uses' => $controller . 'gameData']);
    Route::get(                      'game-config/{game_type}/{way_type?}', ['as' => $sPrefix . '.game-config', 'uses' => $controller . 'gameConfig']);
});
