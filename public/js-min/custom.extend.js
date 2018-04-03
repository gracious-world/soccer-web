/**
 * [doCombinations 组合]
 * @param  [Array] arr [数组]
 * @param  [Number] arr [数字]
 */
function doCombinations(arr, num) {
    var r = [];
    (function f(t, a, n) {
        if (n == 0) return r.push(t);
        for (var i = 0, l = a.length; i <= l - n; i++) {
            f(t.concat(a[i]), a.slice(i + 1), n - 1);
        }
    })([], arr, num);
    return r;
}

// 计算注数
var calculateBetsNum = function(gamesOddJson, gamesArr, gatesJson, danArr) {
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
    };
    // console.log(splittedGamesArr);

    for (var i = splittedGamesArr.length - 1; i >= 0; i--) {
        var bet = splittedGamesArr[i];
        var count = 1;
        for (var j = bet.length - 1; j >= 0; j--) {
            count *= gamesOddJson[bet[j]].length;
        };
        betsNum += count;
    };

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

// 计算Json对象的size
if (!Object.keys) {
    Object.keys = function(obj) {
        var keys = [],
            k;
        for (k in obj) {
            if (Object.prototype.hasOwnProperty.call(obj, k)) {
                keys.push(k);
            }
        }
        return keys;
    };
}

Array.prototype.max = function() {
    return Math.max.apply(null, this);
};

Array.prototype.min = function() {
    return Math.min.apply(null, this);
};

Array.prototype.contains = function(obj) {
    var i = this.length;
    while (i--) {
        if (this[i] === obj) {
            return true;
        }
    }
    return false;
};
/**
 * each是一个集合迭代函数，它接受一个函数作为参数和一组可选的参数
 * 这个迭代函数依次将集合的每一个元素和可选参数用函数进行计算，并将计算得的结果集返回
 *
 *
 *     var a = [1,2,3,4].each(function(x){return x > 2 ? x : null});
 *     var b = [1,2,3,4].each(function(x){return x < 0 ? x : null});
 *     alert(a);
 *     alert(b);
 *
 *
 * @param {Function} fn 进行迭代判定的函数
 * @param more ... 零个或多个可选的用户自定义参数
 * @returns {Array} 结果集，如果没有结果，返回空集
 */
Array.prototype.each = function(fn) {
    fn = fn || Function.K;
    var a = [];
    var args = Array.prototype.slice.call(arguments, 1);
    for (var i = 0; i < this.length; i++) {
        var res = fn.apply(this, [this[i], i].concat(args));
        if (res != null) a.push(res);
    }
    return a;
};

/**
 * 得到一个数组不重复的元素集合<br/>
 * 唯一化一个数组
 * @returns {Array} 由不重复元素构成的数组
 */
Array.prototype.uniquelize = function() {
    var ra = new Array();
    for (var i = 0; i < this.length; i++) {
        if (!ra.contains(this[i])) {
            ra.push(this[i]);
        }
    }
    return ra;
};

/**
* 求两个集合的补集
{%example
<script>
     var a = [1,2,3,4];
     var b = [3,4,5,6];
     alert(Array.complement(a,b));
</script>
%}
* @param {Array} a 集合A
* @param {Array} b 集合B
* @returns {Array} 两个集合的补集
*/
Array.complement = function(a, b) {
    return Array.minus(Array.union(a, b), Array.intersect(a, b));
};

/**
* 求两个集合的交集
{%example
<script>
     var a = [1,2,3,4];
     var b = [3,4,5,6];
     alert(Array.intersect(a,b));
</script>
%}
* @param {Array} a 集合A
* @param {Array} b 集合B
* @returns {Array} 两个集合的交集
*/
Array.intersect = function(a, b) {
    return a.uniquelize().each(function(o) {
        return b.contains(o) ? o : null
    });
};

/**
* 求两个集合的差集
{%example
<script>
     var a = [1,2,3,4];
     var b = [3,4,5,6];
     alert(Array.minus(a,b));
</script>
%}
* @param {Array} a 集合A
* @param {Array} b 集合B
* @returns {Array} 两个集合的差集
*/
Array.minus = function(a, b) {
    return a.uniquelize().each(function(o) {
        return b.contains(o) ? null : o
    });
};

/**
* 求两个集合的并集
{%example
<script>
     var a = [1,2,3,4];
     var b = [3,4,5,6];
     alert(Array.union(a,b));
</script>
%}
* @param {Array} a 集合A
* @param {Array} b 集合B
* @returns {Array} 两个集合的并集
*/
Array.union = function(a, b) {
    return a.concat(b).uniquelize();
};

sortNumber = function (a,b)
{
    return a - b
};

/*The bind function is an addition to ECMA-262, 5th edition; as such it may not be present in all browsers. You can partially work around this by inserting the following code at the beginning of your scripts, allowing use of much of the functionality of bind() in implementations that do not natively support it.
 */
if (!Function.prototype.bind) {
  Function.prototype.bind = function(oThis) {
    if (typeof this !== 'function') {
      // closest thing possible to the ECMAScript 5
      // internal IsCallable function
      throw new TypeError('Function.prototype.bind - what is trying to be bound is not callable');
    }

    var aArgs   = Array.prototype.slice.call(arguments, 1),
        fToBind = this,
        fNOP    = function() {},
        fBound  = function() {
          return fToBind.apply(this instanceof fNOP
                 ? this
                 : oThis,
                 aArgs.concat(Array.prototype.slice.call(arguments)));
        };

    if (this.prototype) {
      // Function.prototype doesn't have a prototype property
      fNOP.prototype = this.prototype;
    }
    fBound.prototype = new fNOP();

    return fBound;
  };
}

