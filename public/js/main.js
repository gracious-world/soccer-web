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
        //游戏类

        mgData: 'game/mg.data',
        mgConfig: 'game/mg.config',
        mgRender: 'game/mg.render',
        mgGame: 'game/mg.game',
        gameAll: 'game/gameAll'
    },
    shim: {
        'bootstrap': ['jquery'],
        'cycle2': ['jquery'],
        'cycle2Center': ['cycle2'],
        'easing':['jquery'],
        'jqueryMousewheel': ['jquery'],
        'jqueryJscroll' : ['jquery'],
        'jqueryJscrollpane': ['jqueryJscroll','jqueryMousewheel'],
        'mgData': ['jquery','easing','jqueryJscrollpane','uiScript', 'customExtend', 'mgConfig'],
        'mgRender': ['jquery', 'mgData'],
        'mgGame': ['jquery', 'mgData', 'mgRender']
    },
    waitSeconds: 60,
    buildVersion: '' //buildVersion
});

require(['jquery', 'domReady', 'mgGame'], function($, domReady) {
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
