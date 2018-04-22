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

        //游戏类
        gameAll:'gameAll'

    },
    shim: {
        'bootstrap': ['jquery'],
        'cycle2': ['jquery'],
        'cycle2Center': ['cycle2'],
        'easing':['jquery'],
        'jqueryMousewheel': ['jquery'],
        'jqueryJscroll' : ['jquery'],
        'jqueryJscrollpane': ['jqueryJscroll','jqueryMousewheel'],

        'gameAll' :['jquery','easing','jqueryJscrollpane','uiScript']

    },
    waitSeconds: 60,
    buildVersion: '' //buildVersion
});

require(['domReady', 'gameAll'], function($, domReady) {
    domReady(function() {
        Mango.Data.init({
            gameConfigUrl: gameConfigUrl,
            gameDataUrl: gameDataUrl,
            gamesContainer: Mango.Config.containers.games,
            isFocusGame: isFocusGame,
            callback: function (container, data, tmpl, needClear, prependDom) {
                Mango.Render.render(container.find('.gamesList'), data, Mango.Config.scriptTmpl[tmpl], needClear, prependDom);
                //渲染已经选择过的投注
                $('.gameOption a').each(function(index,el){
                    var selector = $(el).attr('index');
                    $('.gamesList em[index="'+selector+'"],.gamesList td[index="'+selector+'"]').addClass('active');
                });
            }
        });
        Mango.Game.init({
            billSubmitUrl: bettingUrl,
            gamesFilter: Mango.Config.containers.gamesFilter,
            gamesContainer: Mango.Config.containers.games,
            submitContainer: Mango.Config.containers.betting
        });
    });
});

