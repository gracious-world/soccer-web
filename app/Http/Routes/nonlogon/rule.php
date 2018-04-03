<?php

/**
 * 帮助中心
 */
Route::get('rule', ['as' => 'rule', function() {
        return view('regulation.football');
    }]);
