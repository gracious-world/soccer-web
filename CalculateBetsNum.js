//total_num 10
//3 matches 3*1 type (multi gate)
/**
 * @author sad
 * @type {{"20180407YX107": string[], "20180407YX108": string[], "20180407YX109": string[]}}
 */

gamesOddJson = {
    '20180407YX107': ["ah_22.00,-1,1.00,", "0103_30.00,-1,1.00,"],
    '20180407YX108': ["0301_20.00,-1,1.00,", "0004_80.00,-1,1.00,", "dd_4.40,-1,1.00,", "s3_3.70,-1,1.00,", "0005_250.00,-1,1.00,"],
    '20180407YX109': ["ch_3.05"]
};


gamesArr = ["20180407YX107", "20180407YX108", "20180407YX109"];

//自由过关
gatesJson = {'3*1': ["3"]};

//这两场做胆
danArr = [
    "20180407YX108",
    "20180407YX107"
];


var calculateBetsNum = function (gamesOddJson, gamesArr, gatesJson, danArr) {
    var result = {},
        filtedGamesArr = [],
        betsNum = 0,
        gamesNum = gamesArr.length,
        danNum = danArr.length,
        gatesArr = [],
        danArrStr = danArr.join('^'),
        newArrayDan = [];

    if (!gamesArr.length) return 0;

    for (var n in gatesJson) {
        $.merge(gatesArr, gatesJson[n]);
    }
    // 取数组差集
    if (danNum > 0) {
        for (var i = 0, len = gamesArr.length; i < len; i++) {
            for (var j = 0; j < danNum; j++) {
                if (gamesArr[i] == danArr[j]) {
                    gamesArr.splice(i, 1);
                    i--;
                }
            }
        }
    }

    // debugger;
    for (var i = gatesArr.length - 1; i >= 0; i--) {
        var n = gatesArr[i] - danNum; // 剔除胆码场次后，从剩余场次中选择 (串关场次数 - 胆码场次数) 的场次n
        if (gamesArr.length == n) {
            filtedGamesArr.push(gamesArr);
        } else {
            filtedGamesArr = filtedGamesArr.concat(combine(gamesArr, n));
        }
    }

    var splittedGamesArr = [];
    for (var i = filtedGamesArr.length - 1; i >= 0; i--) {
        var item = filtedGamesArr[i];
        splittedGamesArr.push(item.concat(danArr));
    }
    ;
    // console.log(splittedGamesArr);

    for (var i = splittedGamesArr.length - 1; i >= 0; i--) {
        var bet = splittedGamesArr[i];
        var count = 1;
        for (var j = bet.length - 1; j >= 0; j--) {
            count *= gamesOddJson[bet[j]].length;
        }
        ;
        betsNum += count;
    }
    ;

    return betsNum;
};

var r = calculateBetsNum(gamesOddJson, gamesArr, gatesJson, danArr);
//r=10


