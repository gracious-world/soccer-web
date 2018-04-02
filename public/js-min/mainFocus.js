// contents of main.js:
require.config({
    paths: {
        // 插件库
        jquery: 'jquery-1.9.1.min',
        jqueryMousewheel: 'jquery.mousewheel.min',
        jqueryJscroll: 'jquery.jscroll',
        jqueryJscrollpane: 'jquery.jscrollpane',

        domReady: 'domReady',
        bootstrap: 'bootstrap.min',
        cycle2: 'jquery.cycle2.min',
        cycle2Center: 'jquery.cycle2.center',
        easing: 'jquery.easing',
        // moment: 'moment',
        customExtend: 'custom.extend',
        uiScript: 'ui',

        countdown:'jquery.countdown',

        //游戏类
        gameFocus: 'gameFocus'

    },
    shim: {
        'bootstrap': ['jquery'],
        'cycle2': ['jquery'],
        'cycle2Center': ['cycle2'],
        'easing':['jquery'],
        'jqueryMousewheel': ['jquery'],
        'jqueryJscroll' : ['jquery'],
        'jqueryJscrollpane': ['jqueryJscroll','jqueryMousewheel'],

        'gameFocus':['jquery','easing','jqueryJscrollpane','uiScript','customExtend'],

        'countdown':['jquery','gameFocus']
    },
    waitSeconds: 60,
    buildVersion: '', //buildVersion
});

require(['jquery', 'domReady', 'gameFocus','countdown'], function($, domReady) {
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
