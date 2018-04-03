Mango = window.Mango || {};
// 配置中心
Mango.Config = (function() {
    return {
        gameType: 'football',
        containers: {
            gamesFilter: $('#wayFilter'),
            games: $('#gamesContainer'),
            betting: $('#submitContainer'),
            champion:$('.e_main')
            // 一一对应templates里的key, 格式 key: $('#id')
        },
        events: {
            // 定义模板dom对应的事件处理类
        },
        gameConfigUrl: '/game-config', // '../data/config.json',
        gameDataUrl: '/game-data', // '../data/game.json', //'http://jc-cu-user.com/data' // 取决于实际的数据接口地址
        gameBettingUrl: '/betting', // 投注提交地址
        oddParam: {},
        oddBlockClass: {
            h: 'blockWin',
            ch: 'blockWin',
            d: 'blockPing',
            cd: 'blockPing',
            a: 'blockFu',
            ca: 'blockFu',
            other: 'blockYel'
        },
        // gameData: jcdata,
        scriptTmpl: {
            // 混合投注/单关 页面相关模板
            gamesList: $('#gamesList'),
            gamesHistoryList: $('#gamesHistoryList'),
            selectedGames: $('#selectedGames'),
            selectedOdds: $('#selectedOdds'),
            gatesBox: $('#gatesBox'),
            gameForm: $('#gameForm'),
            // 焦点赛事 页面相关模板
            gamesMenu: $('#focusGamesMenu'),
            champion:$('#championTmpl'),
            championship:$('#championshipTmpl')
        },
        // singleGates: ['1x1'],
        // freeGates: ['2x1', '3x1', '4x1', '5x1', '6x1', '7x1', '8x1'],
        // mixGates: [
        //     ['3x3', '3x4'],
        //     ['4x4', '4x5', '4x6', '4x11'],
        //     ['5x5', '5x6', '5x10', '5x15', '5x20', '5x26'],
        //     ['6x6', '6x7', '6x15', '6x42', '6x50', '6x57'],
        //     ['7x7', '7x8', '7x21', '7x35', '7x120'],
        //     ['8x8', '8x9', '8x28', '8x56', '8x70', '8x247']
        // ],
        // maxGates: {
        //     'had': 8,
        //     'hhad': 8,
        //     'ttg': 6,
        //     'crs': 4,
        //     'hafu': 4
        // },

        gameConfig: null


    }
})();
