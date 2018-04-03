// contents of main.js:
require.config({
    paths: {
        // 插件库
        jquery: 'lib/jquery-1.9.1.min',
        jqueryMousewheel: 'lib/jquery.mousewheel.min',
        jqueryJscroll: 'lib/jquery.jscroll',
        jqueryJscrollpane: 'lib/jquery.jscrollpane',

        domReady: 'lib/domReady',
        bootstrap: 'lib/bootstrap.min',
        cycle2: 'lib/jquery.cycle2.min',
        cycle2Center: 'lib/jquery.cycle2.center',
        easing: 'lib/jquery.easing',
        // moment: 'lib/moment',
        customExtend: 'lib/custom.extend',
        uiScript: 'module/ui',

        countdown:'lib/jquery.countdown',

        //游戏类

        mgData: 'game/mg.data',
        mgConfig: 'game/mg.config',
        mgRender: 'game/mg.render',
        mgGameFocus: 'game/mg.gameFocus'

    },
    shim: {
        'bootstrap': ['jquery'],
        'cycle2': ['jquery'],
        'cycle2Center': ['cycle2'],
        'easing':['jquery'],
        'jqueryMousewheel': ['jquery'],
        'jqueryJscroll' : ['jquery'],
        'jqueryJscrollpane': ['jqueryJscroll','jqueryMousewheel'],

        // 'uiScript':['jquery',],, 'uiScript'
        'mgData': ['jquery','easing','uiScript','jqueryJscrollpane', 'customExtend', 'mgConfig'],
        'mgRender': ['jquery', 'mgData'],
        'mgGameFocus': ['jquery', 'mgData', 'mgRender'],
        'countdown':['jquery','mgGameFocus']

    },
    waitSeconds: 60,
    buildVersion: '', //buildVersion
});

require(['jquery', 'domReady', 'mgGameFocus','countdown'], function($, domReady) {
    domReady(function() {
        Mango.Data.set('implant',{
            implant:true,
            callback:function(){
                $('.j-ui-mask').hide();
                $('.focusleft').hide();
                $('.emptyBox').show();
            },
            restore:function(){
                $('.j-ui-mask').hide();
                $('.focusleft').show();
                $('.emptyBox').hide();
            }
        });
        Mango.Data.init({
            gameConfigUrl: gameConfigUrl,
            gameDataUrl: gameDataUrl,
            gamesContainer: Mango.Config.containers.games,
            isFocusGame: isFocusGame,
            callback: function (container, data, tmpl, needClear, prependDom) {
                Mango.Render.render(container.find('.gamesList'), data, Mango.Config.scriptTmpl[tmpl], needClear, prependDom);
                Mango.Render.render(container.find('.gamesMenu'), data, Mango.Config.scriptTmpl['gamesMenu'], needClear, prependDom);
                Mango.Game.initCountDown();


            }
        });
        Mango.Game.init({
            billSubmitUrl: bettingUrl,
            // gamesFilter: Mango.Config.containers.gamesFilter,
            gamesContainer: Mango.Config.containers.games,
            // submitContainer: Mango.Config.containers.betting,
        });
    });
});
