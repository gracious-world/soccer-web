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

        mgData: 'game/mg.utilData',
        mgConfig: 'game/mg.config',
        mgRender: 'game/mg.render',
        mgRankGames: 'game/mg.rankGames'
    },
    shim: {
        'bootstrap': ['jquery'],
        'cycle2': ['jquery'],
        'cycle2Center': ['cycle2'],
        'easing': ['jquery'],
        'jqueryMousewheel': ['jquery'],
        'jqueryJscroll': ['jquery'],
        'jqueryJscrollpane': ['jqueryJscroll', 'jqueryMousewheel'],
        'mgData': ['jquery', 'easing', 'jqueryJscrollpane', 'uiScript', 'customExtend', 'mgConfig'],
        'mgRender': ['jquery', 'mgData'],
        'mgRankGames': ['jquery', 'mgData', 'mgRender']
    },
    waitSeconds: 60,
    buildVersion: '', //buildVersion
});

require(['jquery', 'domReady', 'mgRankGames'], function($, domReady) {
    domReady(function() {
        //获取2016欧洲杯冠军玩法 数据
        Mango.UtilData.getData(gameChampionDataUrl, function(respone) {
            for(var o in respone.data){
                Mango.Render.render(Mango.Config.containers.champion.find('#championBorther'), respone.data[o], Mango.Config.scriptTmpl['champion'], true);
                Mango.RankGames.renderTableRowColor();
            }
        }, {}, 'GET');

        //获取 2016欧洲杯冠亚军玩法 数据
        Mango.UtilData.getData(gameChampionshipDataUrl, function(respone) {
            for(var o in respone.data){
                Mango.Render.render(Mango.Config.containers.champion.find('#champtionshipBorder'), respone.data[o], Mango.Config.scriptTmpl['championship'], true);
                Mango.RankGames.renderTableRowColor();
            }
        }, {}, 'GET');

        Mango.RankGames.init({
            billSubmitUrl: bettingUrl,
            gamesFilter: Mango.Config.containers.gamesFilter,
            gamesContainer: Mango.Config.containers.games,
            submitContainer: Mango.Config.containers.betting,
        });

    });
});