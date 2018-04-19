//total_num 10
//3 matches 3*1 type (multi gate)
/**
 * @author sad
 * @type {{"20180407YX107": string[], "20180407YX108": string[], "20180407YX109": string[]}}
 */
//game with odd
//作胆

/**
 *
 * 比赛投注信息
 *
 * @type {{"20180407YX107": string[], "20180407YX108": string[], "20180407YX109": string[]}}
 */

gamesOddJson = {
    '20180407YX107': ["ah_22.00,-1,1.00,", "0103_30.00,-1,1.00,"],
    '20180407YX108': ["0301_20.00,-1,1.00,", "0004_80.00,-1,1.00,", "dd_4.40,-1,1.00,", "s3_3.70,-1,1.00,", "0005_250.00,-1,1.00,"],
    '20180407YX109': ["ch_3.05"]
};

/**
 * 比赛编码
 * @type {string[]}
 */
//game bn
gamesArr = ["20180407YX107", "20180407YX108", "20180407YX109"];

/**
 * 串关玩法
 * 3*1 3串1
 * 2*1 2串1
 * @type {{"3*1": string[], "2*1": string[]}}
 */
//自由过关
//gate way method
gatesJson = {'3*1': ["3"],'2*1':['2']};

//这两场做胆
danArr = [
     "20180407YX108",
     "20180407YX107"
 ];
// danArr=[];

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

    //gate way method odd
    //gatesArr = gatesJson
    for (var n in gatesJson) {
        $.merge(gatesArr, gatesJson[n]);
    }
    console.log(gatesArr);
    // 取数组差集
    // the substraction between danNum
    if (danNum > 0) {
        for (var i = 0, len = gamesArr.length; i < len; i++) {
            //remove dan
            for (var j = 0; j < danNum; j++) {
                if (gamesArr[i] == danArr[j]) {
                    //remove dan match
                    gamesArr.splice(i, 1);
                    i--;
                }
            }
        }
    }
    console.log(gamesArr);

    // debugger;
    //gatesArr = gatesJson
    for (var i = gatesArr.length - 1; i >= 0; i--) {
        var n = gatesArr[i] - danNum; // 剔除胆码场次后，从剩余场次中选择 (串关场次数 - 胆码场次数) 的场次n
        //gamesArr already remove dan match
        if (gamesArr.length == n) {
            filtedGamesArr.push(gamesArr);
        } else {
            //
            filtedGamesArr = filtedGamesArr.concat(combine(gamesArr, n));
        }
    }

    console.log(filtedGamesArr);

    //splitted Games from DAN
    var splittedGamesArr = [];
    for (var i = filtedGamesArr.length - 1; i >= 0; i--) {
        var item = filtedGamesArr[i];
        splittedGamesArr.push(item.concat(danArr));
    };
    console.log(splittedGamesArr);
    // console.log(splittedGamesArr);
    for (var i = splittedGamesArr.length - 1; i >= 0; i--) {
        var bet = splittedGamesArr[i];
        var count = 1;
        //bet[j] = match bn
        for (var j = bet.length - 1; j >= 0; j--) {
            //gamesOddJson = games with odd way (ha,hf,score...)
            count *= gamesOddJson[bet[j]].length;
        }
        ;
        betsNum += count;
    }
    ;
    console.log(betsNum);
    return betsNum;
};

var r = calculateBetsNum(gamesOddJson, gamesArr, gatesJson, danArr);


//排列组合算法
//arr=[1,2,3,4],num=1,r=[[1],[2],[3],[4]]
//arr=[1,2,3,4],num=2,r=[[1,2],[1,3],[1,4],[2,3],[2,4],[3,4]]
var combine = function(arr, num) {
    var r = [];
    (function f(out, arr, num) {
        if (num == 0) {
            console.log(out);
            return r.push(out);
        }
        for (var i = 0, l = arr.length; i <= l - num; i++) {
            console.log(out);
            //递归算法
            f(out.concat(arr[i]), arr.slice(i + 1), num - 1);
        }
    })([], arr, num);
    console.log(r);
    return r;
};

