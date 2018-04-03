<?php

// 静态路由

// 游戏
//Route::any('game', ['as' => 'game', function() {
//        return View::make('gameCenter.game');
//    }]);

//游戏记录
Route::any('bet', ['as' => 'bet', function() {
        return View::make('gameCenter.boughts');
    }]);
Route::any('betlist', ['as' => 'bet', function() {
        return View::make('gameCenter.betlist');
    }]);
Route::any('order', ['as' => 'bet', function() {
        return View::make('gameCenter.order');
    }]);

// 用户明细
Route::group(['prefix' => 'userDetails'], function() {
    // Route::get('index', function() {
    //     return view('userCenter.user.account');
    // });
    Route::get('account', ['as' => 'userDetails.account', 'uses' => function() {
        return View::make('userCenter.userDetails.account')->with('resource', 'account');
    }]);
    Route::get('recharge',['as' => 'userDetails.recharge', 'uses' =>  function() {
        return View::make('userCenter.userDetails.recharge')->with('resource', 'recharge');
    }]);
    Route::get('withdrawal',['as' => 'userDetails.withdrawal', 'uses' =>  function() {
        return View::make('userCenter.userDetails.withdrawal')->with('resource', 'withdrawal');
    }]);
    Route::get('transfers',['as' => 'userDetails.transfers', 'uses' =>  function() {
        return View::make('userCenter.userDetails.transfers')->with('resource', 'transfers');
    }]);
    Route::get('winning',['as' => 'userDetails.winning', 'uses' =>  function() {
        return View::make('userCenter.userDetails.winning')->with('resource', 'winning');
    }]);
});

//充值
//Route::group(['prefix' => 'account'], function() {
//    Route::get('index', ['as' => 'account.index', 'uses' => function() {
//        return View::make('userCenter.account.index');
//    }]);
//    Route::get('index-2', ['as' => 'account.index-2', 'uses' => function() {
//        return View::make('userCenter.account.index-2');
//    }]);
//});
//
////提现
//Route::group(['prefix' => 'withdrawal'], function() {
//    Route::get('index', ['as' => 'withdrawal.index', 'uses' => function() {
//        return View::make('userCenter.withdrawal.index');
//    }]);
//     Route::get('index-2', ['as' => 'withdrawal.index-2', 'uses' => function() {
//        return View::make('userCenter.withdrawal.index-2');
//    }]);
//});


//用户中心
Route::group(['prefix' => 'user'], function() {
//    Route::get('index', ['as' => 'user.index', 'uses' => function() {
//        return View::make('userCenter.user.index');
//    }]);
    Route::get('safe', ['as' => 'user.safe', 'uses' => function() {
        return View::make('userCenter.user.safe');
    }]);
    Route::get('truename', ['as' => 'user.truename', 'uses' => function() {
        return View::make('userCenter.user.truename');
    }]);
    Route::get('banklist', ['as' => 'user.banklist', 'uses' => function() {
        return View::make('userCenter.user.banklist');
    }]);
//    Route::get('bank', ['as' => 'user.bank', 'uses' => function() {
//        return View::make('userCenter.user.bank');
//    }]);
    Route::get('bank-2', ['as' => 'user.bank-2', 'uses' => function() {
        return View::make('userCenter.user.bank-2');
    }]);
});



//信息
//Route::group(['prefix' => 'message'], function() {
//    Route::get('receiver', ['as' => 'message.receiver', 'uses' => function() {
//        return View::make('userCenter.message.receiver');
//    }]);
//    Route::get('sendlist', ['as' => 'message.sendlist', 'uses' => function() {
//        return View::make('userCenter.message.sendlist');
//    }]);
//    Route::get('send', ['as' => 'message.send', 'uses' => function() {
//        return View::make('userCenter.message.send');
//    }]);
//});



//代理
Route::group(['prefix' => 'agent'], function() {
    // Route::get('receiver', ['as' => 'agent.receiver', 'uses' => function() {
    //     return View::make('userCenter.agent.receiver');
    // }]);
    // Route::get('sendlist', ['as' => 'agent.sendlist', 'uses' => function() {
    //     return View::make('userCenter.agent.sendlist');
    // }]);
    // Route::get('send', ['as' => 'agent.send', 'uses' => function() {
    //     return View::make('userCenter.agent.send');
    // }]);
});











