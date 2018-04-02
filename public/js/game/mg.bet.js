// 投注中心，核心类
Mango.Bet = (function() {
    var defConfig = {

    };

    var bindEvent = function(container) {
        $(container).click(function(e) { // 绑定投注篮的dom事件, 包括胆码设置，串关方式选择后出发注单计算
            var target = e.target;
            var param = {};
            switch ($(target).attr('eventName')) {
                case '':
                    param = {

                    };
                    break;
            }
        });
        $(container).find('.oddUnit').live('click', function() { // 绑定赔率单元的点击事件，点击后触发删除事件

        });
    };
    var caculateOrderCount = function(gamesOddJson, gamesArr, gatesArr, danArr) {
            var result = {},
                filtedGamesArr = [],
                betsNum = 0,
                gamesNum = gamesArr.length,
                danNum = danArr.length,
                _gatesArr =[];

            for(var i = 0 ; i<gatesArr.length ; i++){
                _gatesArr.push(gatesArr[i].slice(0,1))
            }

            // 取数组差集
            var gamesWithoutDan = gamesArr.filter(function(v) {
                return !(danArr.indexOf(v) > -1)
            }).concat(danArr.filter(function(v) {
                return !(gamesArr.indexOf(v) > -1)
            }));
            // debugger;
            for (var i = _gatesArr.length - 1; i >= 0; i--) {
                var n = _gatesArr[i] - danNum; // 剔除胆码场次后，从剩余场次中选择 (串关场次数 - 胆码场次数) 的场次n
                if (gamesWithoutDan.length == n) {
                    filtedGamesArr.push(gamesWithoutDan);
                } else {
                    filtedGamesArr = filtedGamesArr.concat(combine(gamesWithoutDan, n));
                }
            }

            var splittedGamesArr = [];
            for (var i = filtedGamesArr.length - 1; i >= 0; i--) {
                var item = filtedGamesArr[i];
                splittedGamesArr.push(item.concat(danArr));
            };
            // console.log(splittedGamesArr);
            for (var i = splittedGamesArr.length - 1; i >= 0; i--) {
                var bet = splittedGamesArr[i];
                var count = 1;
                for (var j = bet.length - 1; j >= 0; j--) {
                    count *= gamesOddJson[bet[j]]['oddNum'];
                };
                betsNum += count;
            };
            //console.log(betsNum);
            return betsNum;
    };
    var combine = function(arr, num) {
        var r = [];
        (function f(t, a, n) {
            if (n == 0) return r.push(t);
            for (var i = 0, l = a.length; i <= l - n; i++) {
                f(t.concat(a[i]), a.slice(i + 1), n - 1);
            }
        })([], arr, num);
        return r;
    };
    return {
        init: {
            caculateOrderCount:caculateOrderCount
        }
    }
})();
