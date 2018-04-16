<?php
/**
 * 注册和登录
 */
Route::group(['prefix' => 'auth'], function () {
    $Authority = 'AuthorityController@';
    # 退出
    Route::get(                   'logout', ['as' => 'logout', 'uses' => $Authority . 'logout']);
    Route::match(['get', 'post'], 'signin', ['as' => 'signin', 'uses' => $Authority . 'signin']);
    Route::match(['get', 'post'], 'signup', ['as' => 'signup', 'uses' => $Authority . 'signup']);
//    Route::match(['get', 'post'], 'signin', ['as' => 'signin', 'uses' => $Authority . 'signin'])->middleware('extra-auth');

     //短信验证码
    Route::any('send-sms-message', array('before'=>'csrf','as' => 'send-sms-message','uses' => $Authority . 'sendSmsMessage'));
    //短信验证码
    Route::any('send-sms-password', array('before'=>'csrf','as' => 'send-sms-password','uses' => $Authority . 'sendSmsPassword'));
    # 忘记密码
    Route::any('find-password', array('as' => 'find-password', 'uses' => $Authority . 'findPassword'));

    //找回密码方式
    Route::any('findWay', array('as' => 'findway', 'uses' => $Authority . 'findWay'));
    //安全验证 - 通过安全问题方式
    Route::any('verificationQuestion', array('as' => 'verificationquestion', 'uses' => $Authority . 'verificationQuestion'));
    //安全验证 - 通过邮件方式
    Route::any('verificationEmail', array('as' => 'verificationemail', 'uses' => $Authority . 'verificationEmail'));
    //安全验证 - 通过邮件方式
    Route::any('verify-email', array('as' => 'verify-email', 'uses' => $Authority . 'verifyEmail'));

    //安全验证 - 通过手机方式
    Route::any('verificationPhone', array('as' => 'verificationphone', 'uses' => $Authority . 'verificationPhone'));

    //安全验证 - 重设密码
    Route::any('passwordReset', array('as' => 'passwordreset', 'uses' => $Authority . 'passwordReset'));

    //注册成功页面
    Route::any('signupSuccess', array('as' => 'signupsuccess', 'uses' => $Authority . 'signupSuccess'));

});
