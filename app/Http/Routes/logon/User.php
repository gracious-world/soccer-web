<?php

# User管理
$sPrefix = 'users';
Route::group(['prefix' => $sPrefix], function () use ($sPrefix) {
    $controller = 'UserUserController@';
    Route::any(      '/', ['as' => $sPrefix . '.index',   'uses' => $controller . 'index']);
    Route::get('account', ['as' => $sPrefix . '.account', 'uses' => $controller . 'getLoginUserMonetaryInfo']);
    Route::get(                          '/', ['as' => $sPrefix . '.index',                    'uses' => $controller . 'index'])->before('agent');
    Route::get(            '{pid}/sub-users', ['as' => $sPrefix . '.sub-users',                'uses' => $controller . 'subUsers'])->before('agent');
//    Route::get(            'sub-users', ['as' => $resource . '.sub-users',                'uses' => $controller . 'subUsers'])->before('agent');
    Route::any('change-password', ['as' => $sPrefix . '.change-password',      'uses' => $controller . 'changePassword']);
    Route::any('change-fund-password', ['as' => $sPrefix . '.change-fund-password',      'uses' => $controller . 'changeFundPassword']);
    Route::any(   'safe-reset-fund-password/{isWin?}', ['as' => $sPrefix . '.safe-reset-fund-password', 'uses' => $controller . 'safeChangeFundPassword']);
    Route::any(            'accurate-create', ['as' => $sPrefix . '.accurate-create',          'uses' => $controller . 'accurateCreate']);
    Route::any(                       'user', ['as' => $sPrefix . '.user',                     'uses' => $controller . 'user']);
    Route::any(                  'true-name', ['as' => $sPrefix . '.true-name',                'uses' => $controller . 'trueName']);
    Route::any(                   'personal', ['as' => $sPrefix . '.personal',                 'uses' => $controller . 'personal']);
    Route::any(                   'portrait', ['as' => $sPrefix . '.portrait',                 'uses' => $controller . 'portrait']);
    Route::any(                   'bind-email', ['as' => $sPrefix . '.bind-email',             'uses' => $controller . 'bindEmail']);
    //TODO check
//    Route::any(                   'activate-email', ['as' => $resource . '.activate-email',     'uses' => $controller . 'activateEmail', 'before'=>'maxAccess:1,10']);
    Route::any(                   'activate-email', ['as' => $sPrefix . '.activate-email',     'uses' => $controller . 'activateEmail']);
    Route::get(         'user-monetary-info', ['as' => $sPrefix . '.user-monetary-info',       'uses' => $controller . 'getLoginUserMonetaryInfo'])->before('ajax');
    Route::any('{step}/safe-question', ['as' => $sPrefix . '.safe-question', 'uses' => $controller . 'safeQuestion']);

    Route::any(         'open-account', ['as' => $sPrefix . '.open-account',       'uses' => $controller . 'openAccount']);
//    Route::get(         'openAccountSuccess', ['as' => $resource . '.open-account-successs',       'uses' => $controller . 'openAccountSuccess']);
    Route::any(         'spread-code', ['as' => $sPrefix . '.spread-code',       'uses' => $controller . 'spreadCode']);
    Route::any(         'check-child', ['as' => $sPrefix . '.check-child',       'uses' => $controller . 'checkChild']);
    Route::any(         'bind-qq', ['as' => $sPrefix . '.bind-qq',       'uses' => $controller . 'bindQq']);
    Route::any(         'bind-wx', ['as' => $sPrefix . '.bind-wx',       'uses' => $controller . 'bindWx']);
    Route::any(         'bind-cellphone', ['as' => $sPrefix . '.bind-cellphone', 'uses' => $controller . 'bindCellphone']);
    //绑定手机

    //发送验证码
    Route::any('user-send-code-to-phone', ['as' => $sPrefix . '.user-send-code-to-phone', 'uses' => $controller . 'userSendCodeTophone']);
    //绑定成功
    Route::any('bind-cellphone-success', ['as' => $sPrefix . '.bind-cellphone-success', 'uses' => $controller . 'bindcellphoneSuccess']);


    Route::any('bind-sfz', ['as' => $sPrefix . '.bind-sfz', 'uses' => $controller . 'bindSFZ']);

});

