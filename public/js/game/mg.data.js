// 数据中心
Mango.Data = (function() {
    // 对象内全局私有变量
    var miniutes = 5 * 60 * 1000; // 默认5分钟
    var p = null;
    var defaultParams = {
        'game_type': 'football'
    };
    var gameParamCache = null;
    var needAutoRefresh = true;
    var timer = null;
    var mask = new UIscript.Mask();
    var message = new UIscript.MiniWindow();

    //植入对象
    var implant = {
        implant : false,
        callback : null
    }

    // 对象内私有方法
    // ajax获取数据
    var getData = function(url, callback, param, type) {
        if (!url) return false;

        $.ajax({
            url: url,
            type: 'GET',
            dataType: 'json',
            data: param,
            beforeSend:function(){
                mask.show();

            },
            success: function(d) {
                callback(d, param);
                message.hideTip();
            },
            complete: function() {
                message.showTip('正在加载数据');
                setTimeout(function() {
                    message.hideTip();
                    mask.hide();
                }, 1000);
            },
            error: function(d) {
                getData(url, callback, param, type);
            }
        });
        // callback(Mango.Config.containers['games'], Mango.Config.gameData, Mango.Config.scriptTmpl['games']);
    };
    // 初始化获取赛事配置信息
    var initGameConfig = function() {
        getData(p.gameConfigUrl, handleGameConfig, defaultParams);
    };
    var handleGameConfig = function(d, param) {
        if (d.coding < 0) {
            if (confirm('获取游戏数据失败，是否返回首页？')) {
                window.location.href = '/';
            }
        }
        Mango.Config.gameConfig = d.data;
        Mango.Config.gameConfig.defaultWays = Array.minus(Mango.Config.gameConfig.ways, Mango.Config.gameConfig.extra_ways);
        getGameData();
    };
    // 获取赛事数据
    var getGameData = function(param) {
        gameParamCache = param;
        getData(p.gameDataUrl, handleGameData, $.extend({}, defaultParams, gameParamCache || {}));
    };
    var handleGameData = function(d, param) {
        if (d.coding < 0) {
            if(implant.implant){
                implant.implant && implant.callback && implant.callback();
            }else if(confirm('获取游戏数据失败，是否返回首页？')) {
                window.location.href = '/';
            }
        }else{
            if (implant.implant) {
                implant.implant && implant.restore && implant.restore();
            }
        }

        var today = moment().format('YYYY-MM-DD');
        needAutoRefresh = !param || !param.bet_date || param.bet_date >= today;
        needAutoRefresh ? startTimer() : stopTimer();
        var tmpl = +isFocusGame ? 'gamesList' : ['gamesList', 'gamesHistoryList'][Number(param.bet_date < today)];
        p.callback(p.gamesContainer, d.data, tmpl, true);
    };
    var startTimer = function() {
        stopTimer();
        timer = setInterval(function() {
            getGameData(gameParamCache);
        }, miniutes);
    };
    var stopTimer = function() {
        if (timer) clearInterval(timer);
    };


    return {
        init: function(options) {
            p = $.extend(p, null, options || {});
            initGameConfig();
            // getGameData();
            // startTimer(); // 默认开启轮询
            return this;
        },
        getGameData: getGameData,
        set:function(type,obj){
            switch(type){
                case 'implant':
                 $.extend(implant, obj)
                break;
            }
        }
    };
})();
