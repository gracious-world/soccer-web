<?php
/**
 * Created by PhpStorm.
 * User: damon
 * Date: 9/15/15
 * Time: 4:26 PM
 */

# 第三方银行充值管理
Route::group(['prefix' => 'bank-deposit'], function () {
    $resource = 'bank-deposits';
    $controller = 'UserBankDepositController@';
    Route::any('netbank/{id}', ['as' => $resource . '.netbank', 'uses' => $controller . 'netbank']);
});