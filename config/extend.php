<?php

/*
|--------------------------------------------------------------------------
| 拓展配置文件
|--------------------------------------------------------------------------
|
*/
//to fix cdn cache
$version = '?' . '107';

return array(

    /**
     * 网站静态资源文件别名配置
     */
      'webAssets' => array(
            'cssAliases' => array(  //  样式文件别名配置
                  // 框架样式
                  'ui'       => 'css/bootstrap.css',
                  //header页面
                  'main'       => 'css/main.css',
                  //游戏页面
                  'gameBase'     => 'imager/base.css',
                  'gameUi'       => 'imager/game/game.css',
                  'gameUi1200'       => 'imager/game/game1200.css',
                  'result'       => 'imager/game/result.css',
                  'uicss'        => 'imager/uicenter/uicss.css',

                  'focus'        => 'imager/focus/focus.css',
                  'focus-public' => 'imager/focus/public.css',
                  'focus-global' => 'imager/focus/global_reset.css',
                  'global_reset' => 'imager/kemp/global_reset.css',
                  'europeanCup'  => 'imager/kemp/europeanCup.css',
                //soccer game
                'login' => 'imager/login/login.css' . $version,
                'global' => 'imager/global/global.css' . $version,
                'reg' => 'imager/reg/reg.css' . $version,
                'ucenter' => 'imager/ucenter/ucenter.css' . $version,
            ),

            'jsAliases'  => array(  //  脚本文件别名配置
                  //工具
                  'jquery'              => 'js/lib/jquery-1.9.1.min.js',
                  'doT'                 => 'js/lib/doT.js',
                  'domReady'            => 'js/lib/domReady.js',
                  'bootstrap'           => 'js/lib/bootstrap.min.js',
                  'jquery.cycle'        => 'js/lib/jquery.cycle2.min.js',
                  'jquery.cycle.center' => 'js/lib/jquery.cycle2.center.js',
                  'md5'                 => 'js/lib/md5.js',
                  'moment'              => 'js/lib/moment.min.js',
                  'easing'              => 'js/lib/jquery.easing.js',
                  'countdown'           => 'js/lib/jquery.countdown.js',
                  'utilData'            => 'js/game/mg.utilData.js',
                  'laypage'             => 'js/lib/laypage/laypage.js',
                  'marquee'             => 'js/lib/jquery.marquee.min.js',


                  //框架
                  'main'                => 'js/main.js',
                  'requirejs'           => 'js/requirejs.min.js',

                  //ui组件
                  'uiScript'            => 'js/module/ui.js',

                  //echarts.min.js 绘图
                  'echarts'      => 'js/lib/echarts.min.js',

                 //组件
                'base' => 'js-min/base.js' . $version,
                  'gagame.base' => 'js-min/gagame.base.js' . $version,
                'gagame.Mask' => 'js-min/gagame.Mask.js' . $version,
                'gagame.Tip' => 'js-min/gagame.Tip.js' . $version,
                'gagame.Message' => 'js-min/gagame.Message.js' . $version,


                //竞彩大客户平台js 配置文件路劲
                //工具类
                // 'jquery-1.11'              =>'assets/wc-js-min/jquery-1.11.0.min.js'.$version,
                'jquery-1.9.1' => 'js-min/jquery-1.9.1.min.js' . $version,
                'jquery.easing.1.3' => 'js-min/jquery.easing.1.3.js' . $version,
                'jquery.mousewheel' => 'js-min/jquery.mousewheel.min.js' . $version,
                'jquery.cycle2' => 'js-min/jquery.cycle2.min.js' . $version,
                'cycle2.scrollVert' => 'js-min/jquery.cycle2.scrollVert.min.js' . $version,
                'cycle2.carousel' => 'js-min/jquery.cycle2.carousel.min.js' . $version,
                'gagame.Timer' => 'js-min/gagame.Timer.js' . $version,
                'gagame.Tab' => 'js-min/gagame.Tab.js' . $version,
                'gagame.Select' => 'js-min/gagame.Select.js' . $version,

                //quick deposit
                'jquery.jscrollpane' => 'imager/game/k3-dice/jquery.jscrollpane.css' . $version,
                'gagame.DatePicker' => 'js-min/gagame.DatePicker.js' . $version,

                //login + register
                'validate'            => 'js-min/validate.js' . $version,
                'global'              => 'js-min/global.js' . $version,
            	'headerjs'              => 'js/header/js.js' . $version,
			),
            // 'jsAliases'  => array(  //  脚本文件别名配置
            //       //工具
            //       'jquery'              => 'js-min/jquery-1.9.1.min.js',
            //       'doT'                 => 'js-min/doT.js',
            //       'domReady'            => 'js-min/domReady.js',
            //       'bootstrap'           => 'js-min/bootstrap.min.js',
            //       'jquery.cycle'        => 'js-min/jquery.cycle2.min.js',
            //       'jquery.cycle.center' => 'js-min/jquery.cycle2.center.js',
            //       'md5'                 => 'js-min/md5.js',
            //       'moment'              => 'js-min/moment.min.js',
            //       'easing'              => 'js-min/jquery.easing.js',
            //       'countdown'           => 'js-min/jquery.countdown.js',

            //       //框架
            //       'main'                => 'js-min/main.js',
            //       'requirejs'           => 'js-min/requirejs.min.js',

            //       //ui组件
            //       'uiScript'            => 'js-min/ui.js'


            // ),
    ),
);