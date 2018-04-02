<?php

/*
|--------------------------------------------------------------------------
| 拓展配置文件
|--------------------------------------------------------------------------
|
*/

return array(

    /**
     * 网站静态资源文件别名配置
     */
      'webAssets' => array(
            'cssAliases' => array(  //  样式文件别名配置
                  // 框架样式
                  'ui'       => 'css/bootstrap.css',
                  //游戏页面
                  'gameBase'     => 'imager/base.css',
                  'gameUi'       => 'imager/game/game.css',
                  'gameUi1200'       => 'imager/game/game1200.css',
                  'result'       => 'imager/game/result.css',
                  'uicss'        => 'imager/uicenter/uicss.css',

                  'focus'        => 'imager/focus/focus.css',
                  'focus-public' => 'imager/focus/public.css',
                  'focus-global' => 'imager/focus/global_reset.css',
            ),

            // 'jsAliases'  => array(  //  脚本文件别名配置
            //       //工具
            //       'jquery'              => 'js/lib/jquery-1.9.1.min.js',
            //       'doT'                 => 'js/lib/doT.js',
            //       'domReady'            => 'js/lib/domReady.js',
            //       'bootstrap'           => 'js/lib/bootstrap.min.js',
            //       'jquery.cycle'        => 'js/lib/jquery.cycle2.min.js',
            //       'jquery.cycle.center' => 'js/lib/jquery.cycle2.center.js',
            //       'md5'                 => 'js/lib/md5.js',
            //       'moment'              => 'js/lib/moment.min.js',
            //       'easing'              => 'js/lib/jquery.easing.js',
            //       'countdown'           => 'js/lib/jquery.countdown.js',

            //       //框架
            //       'main'                => 'js/main.js',
            //       'requirejs'           => 'js/requirejs.min.js',

            //       //ui组件
            //       'uiScript'            => 'js/module/ui.js'


            // ),
    ),
);