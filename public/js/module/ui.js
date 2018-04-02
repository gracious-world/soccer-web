//js效果集合

(function(host, name, undefined) {
    var Main = function() {};
    host[name] = Main
})(this, "UIscript");



(function(host, name, $, undefined) {
    var BEFOREINIT = "beforeInit",
    AFTERINIT = "afterInit";
    var utilConstructor = function(Sub, cases, config) {
        var config = config || {};
        if (Sub.superClass && Sub.superClass.defConfig) {
            Sub.defConfig = $.extend({},
            Sub.superClass.defConfig, Sub.defConfig)
        }
        config = cases.defConfig = $.extend({},
        Sub.defConfig, config);
        if (config['expands']) {
            $.extend(cases, config['expands'])
        }
        if (Sub.superClass) {
            Sub.superClass.call(cases, config)
        }
        if ($.isFunction(Sub.prototype.init)) {
            var isSubSuper = cases.constructor === Sub;
            isSubSuper && $.isFunction(config[BEFOREINIT]) && config[BEFOREINIT].call(cases, config);
            Sub.prototype.init.call(cases, config);
            isSubSuper && $.isFunction(config[AFTERINIT]) && config[AFTERINIT].call(cases, config);
            if (isSubSuper) {
                for (var i = 0,
                len = cases._inits.length; i < len; i++) {
                    cases._inits[i].call(cases, cases.defConfig)
                }
            }
        }
    };
    var Class = function(pros, superClass) {
        var Main = function(config) {
            var me = this;
            me._inits = [];
            utilConstructor(Main, me, config)
        };
        if (arguments.length < 2) {
            Main.prototype = pros
        } else {
            var Cons = function() {};
            Cons.prototype = superClass.prototype;
            Main.prototype = new Cons();
            $.extend(Main.prototype, pros);
            Main.superClass = superClass
        }
        Main.prototype.constructor = Main;
        return Main
    };
    host[name] = Class
})(UIscript, "Class", jQuery);



(function(host, name, undefined) {
    var stopEvent = function() {
        this._isStop = true
    };
    var pros = {
        init: function(config) {
            this._events = {}
        },
        addEvent: function(name, fn) {
            if (!fn || Object.prototype.toString.call(fn) !== "[object Function]") {
                throw "Event.addEvent\u7B2C\u4E8C\u4E2A\u53C2\u6570\u5FC5\u987B\u662F\u51FD\u6570";
            }
            var me = this,
            _evs = me._events;
            _evs[name] = _evs[name] || [];
            _evs[name].push(fn)
        },
        removeEvent: function(name, fn) {
            var me = this,
            _evs = me._events;
            if (!_evs[name]) {
                return
            }
            if (!fn) {
                delete _evs[name];
                return
            }
            var fns = _evs[name],
            i = fns.length;
            while (i) {
                i--;
                if (fns[i] === fn) {
                    fns.splice(i, 1)
                }
            }
            if (!fns.length) {
                delete _evs[name]
            }
        },
        fireEvent: function(name) {
            var me = this,
            _evs = me._events;
            if (!_evs[name]) {
                return
            }
            var fns = _evs[name];
            if (fns._isStop) {
                delete fns._isStop;
                return
            }
            var i = 0,
            len = fns.length,
            ev = {
                type: name,
                data: me,
                stopEvent: stopEvent
            },
            args = [ev].concat(Array.prototype.slice.call(arguments, 1));
            for (var i = 0; i < len; i++) {
                if (ev._isStop || fns[i].apply(me, args) === false) {
                    ev._isStop = false;
                    return
                }
            }
        },
        stopEvent: function(name) {
            var me = this,
            _evs = me._events;
            if (!_evs[name]) {
                return
            }
            _evs[name]._isStop = true
        }
    };
    var Main = host.Class(pros);
    host[name] = Main
})(UIscript, "Event");



(function(host, name, $, undefined) {
    var Main = function() {};
    Main.win = $(window);
    Main.doc = $(document);
    Main.isIE = !!document.all;
    Main.isIE6 = window.ActiveXObject && navigator.userAgent.toLowerCase().match(/msie ([\d.]+)/)[1] == 6.0 ? true: false;
    Main.toViewCenter = function(el) {
        Main.toViewCenter.fn(el);
        Main.win.bind('resize',
        function() {
            Main.toViewCenter.fn(el)
        })
    };
    Main.toViewCenter.fn = function(el) {
        var el = $(el),
        w = el.width(),
        h = el.height(),
        allw = Main.win.width(),
        allh = Main.win.height(),
        scrollWidth = Main.isIE6 ? Main.win.scrollLeft() : 0,
        scrollHeight = Main.isIE6 ? Main.win.scrollTop() : 0;
        el.css({
            left: allw / 2 - w / 2 + scrollWidth,
            top: allh / 2 - h / 2 + scrollHeight
        })
    }
    Main.startFixed = function(el, time) {
        var el = $(el),
        fn,
        time = time || 500,
        top = parseInt(el.css('top')),
        sTop = Main.win.scrollTop(),
        _sTop = sTop,
        left = parseInt(el.css('left')),
        sLeft = Main.win.scrollLeft(),
        _sLeft = sLeft;
        fn = function() {
            var h = el.height(),
            w = el.width(),
            allw = Main.win.width(),
            allh = Main.win.height();
            _sTop = Main.win.scrollTop();
            _sLeft = Main.win.scrollLeft();
            el.stop();
            el.animate({
                top: allh / 2 - h / 2 + _sTop
            },
            50);
            sTop = _sTop;
            el.animate({
                left: allw / 2 - w / 2 + _sLeft
            },
            50);
            sLeft = _sLeft
        };
        return new host['Timer']({
            time: time,
            fn: fn
        });
    };
    Main.getRandom = function(n, m){
        return Math.floor(Math.random()*(m - n + 1) + n);
    };
    Main.getByteLen = function(str) {
        return str.replace(/[^\x00-\xff]/g, 'xx').length
    };
    Main.getParam = function(name) {
        var reg = new RegExp("(^|\\?|&)" + name + "=([^&]*)(\\s|&|$)", "i");
        if (reg.test(location.href)) {
            return unescape(RegExp.$2.replace(/\+/g, " "))
        } else {
            return null
        }
    };
    Main.template = function(tpl, data) {
        var me = this,
        o = data,
        p, reg;
        for (p in o) {
            if (o.hasOwnProperty(p)) {
                reg = RegExp('<#=' + p + '#>', 'g');
                tpl = tpl.replace(reg, o[p])
            }
        }
        return tpl
    };
    Main.formatMoney = function(num, digit) {
        var num = Number(num),
            digit = (digit == undefined || digit < 0) ? 2 : digit,
            re = /(-?\d+)(\d{3})/;
        if (Number.prototype.toFixed) {
            num = (num).toFixed(digit)
        } else {
            num = Math.round(num * Math.pow(10,digit)) / Math.pow(10,digit)
        }
        num = '' + num;
        while (re.test(num)) {
            num = num.replace(re, "$1,$2")
        }
        return num
    };
    host[name] = Main
})(UIscript, "util", jQuery);




//日期控件
(function(host, name, Event, $, undefined){
    var defConfig = {
        //绑定的input控件
        input:null,
        //初始化使用的日期
        //年月日之间使用非数字区分
        date:new Date(),
        //追加的class
        cls:'',
        //选区开始年份
        startYear:1980,
        //选区结束年份
        endYear:(new Date()).getFullYear(),
        //endYear:2020,

        effectShow:function(){
            this.dom.show();
        },
        effectHide:function(){
            this.dom.hide();
        },
        //是否锁定Input输入字符类型
        isLockInputType:true,
        //是否显示小时和分钟和秒
        isShowTime:false,
        setDisabled:function(){
            var me = this,tds = me.getContent().find('td'),it;
            /**
            tds.each(function(){
                it = $(this),num = Number(it.text());
                if(!it.hasClass('day-thisMonth') || (num < 4) || (num > 10)){
                    it.addClass('day-disabled');
                }
            });
            **/
        }
    },
    //星期的html
    DAYSTRING = '<tr><th>日</th><th>一</th><th>二</th><th>三</th><th>四</th><th>五</th><th>六</th></tr>',
    //公共实例
    instance,
    //实例dom的引用
    caseHas = [];

    var pros = {
        init:function(cfg){
            var me = this,_dateArr,_case,_input,str_h,str_s,inputCheckFn;

            _input = $(cfg.input);
            if(_input.size() > 0){
                if(_case = me.checkCases(_input.get(0))){
                    //console.log(_case.effectShow);
                    me.removeCase(_case);
                    _case.dom.remove();
                }
                caseHas.push(me);
            }
            me.input = _input;
            me.randomNum = (''+Math.random()).replace('0.', '');

            me.effectShow = cfg.effectShow;
            me.effectHide = cfg.effectHide;

            me.dom = $('<div onselectstart="return false;" class="j-ui-datepicker '+ cfg.cls +'" style="display:none;"><div class="control"><span class="pre">&lt;&lt;</span><span class="year"></span><span class="month"></span><span class="next">&gt;&gt;</span></div><div class="content"></div><div class="content-time" style="display:none;">时间：<input class="input time-input time-h" type="text" value="00" /> : <input class="input time-input time-s" type="text" value="00" /> : <input class="input time-input time-sec" type="text" value="00" />&nbsp;&nbsp;<a href="#" class="btn btn-small button-confirm" type="button" value="确定" >确定</a></div>').appendTo($('body'));

            me.addEvent('afterRender', cfg.setDisabled);


            if(me.input.size() > 0 && $.trim(me.input.val()) != ''){
                me.setDate(me.input.val());
            }else{
                me.setDate(cfg.date);
            }

            if(me.defConfig.isLockInputType){
                if(me.input.size() > 0){
                    me.input.keydown(function(e){
                        if(e.keyCode == 8 || e.keyCode == 46){
                            return true;
                        }else{
                            return false;
                        }
                    });
                }
            }



            //下拉框初始化请放在setDate之后，它将使用到初始化的date
            me.initSelectYear(cfg.startYear, cfg.endYear);
            me.initSelectMonth();
            me.initSimulateSelect();

            me.getContent().click(function(e){
                var el = $(e.target),year = me.getDate().getFullYear(),mon = me.getDate().getMonth(),day = me.getDate().getDate(),cfg = me.defConfig,_date;

                if(e.target.tagName.toLowerCase() == 'td' && !el.hasClass('day-disabled')){
                    if( el.hasClass('day-curr') ){
                        me.buttonConfirm();
                    }else{
                        mon = el.hasClass('day-preMonth') ? mon - 1 : mon;
                        if(mon < 0){
                            mon = 11;
                            year -= 1;
                        }
                        mon = el.hasClass('day-nextMonth') ? mon + 1 : mon;
                        if(mon > 11){
                            mon = 0;
                            year += 1;
                        }
                        day = Number(el.text());

                        me.confirmDate(year, mon, day);
                    }
                }
            });

            me.dom.find('.content-time .button-confirm').click(function(e){
                var highDt = me.dom.find('.day-curr');
                if(highDt.size() > 0 && !highDt.hasClass('day-disabled')){
                    me.buttonConfirm();
                }
                e.preventDefault();
            });

            host.util.doc.bind('mousedown.datapcker-' + me.randomNum, function(e){
                if(!$.contains(me.dom.get(0), e.target)){
                    me.hide();
                }
            });

            me.getPreMonth().click(function(e){
                me.preMonth();
            });
            me.getNextMonth().click(function(e){
                me.nextMonth();
            });

            if(cfg.isShowTime){
                inputs = me.dom.find('.content-time .time-input');
                inputCheckFn = function(e){
                    var me = this,v = $.trim(me.value),a = inputs.get(0),b = inputs.get(1);
                    if(me == a){
                        me.value = v = v.replace(/^(\d{2}).*/g, '$1').replace(/\D/, '');
                        v = Number(v) > 23 ? 23 : v;
                    }else{
                        me.value = v = v.replace(/^(\d{2}).*/g, '$1').replace(/\D/, '');
                        v = Number(v) > 59 ? 59 : v;
                    }
                    me.value = v;
                };
                inputs.keyup(inputCheckFn).blur(function(){
                    var me = this,v = $.trim(me.value);
                    inputCheckFn.call(me);
                    if(v == ''){
                        me.value = '00';
                    }else{
                        me.value = Number(v) < 10 ? '0'+Number(v) : v;
                    }

                });
            }

        },
        confirmDate:function(y, m, d, isButtonConfirm){
            var me = this,cfg = me.defConfig,_date,inputs = me.dom.find('.content-time input');
            if(cfg.isShowTime){
                _date = new Date(y, m, d);
                _date.setHours(inputs.get(0).value, inputs.get(1).value, inputs.get(2).value, 0);
                me.setDate(_date);
                if(isButtonConfirm){
                    me.setInputVal(me.formatDateTime(y, m + 1, d, _date.getHours(), _date.getMinutes(), _date.getSeconds()));
                    me.hide();
                }
            }else{
                me.setDate(new Date(y, m, d));
                me.hide();
                me.setInputVal(me.formatDate(y, m + 1, d));
            }
        },
        buttonConfirm:function(){
            var me = this,dt = me.getPanelDate();
            me.confirmDate(dt.y, dt.m, dt.d, true);
        },
        getPanelDate:function(){
            var me = this,
                y = Number(me.getControlYear().find('select').val()),
                m = Number(me.getControlMonth().find('select').val()) - 1,
                d = Number(me.dom.find('.day-curr').text()),
                inputs = me.dom.find('.content-time input'),
                h = Number(inputs.get(0).value),
                s = Number(inputs.get(1).value),
                sec = Number(inputs.get(2).value);
            //console.log({y:y,m:m,d:d,h:h,s:s});
            return {y:y,m:m,d:d,h:h,s:s,sec:sec};
        },
        formatDate:function(y, m, d){
            m = Number(m) < 10 ? '0' + Number(m) : m;
            d = Number(d) < 10 ? '0' + Number(d) : d;
            return y + '-' + m + '-' + d;
        },
        formatDateTime:function(y, m, d, h, s, sec){
            m = Number(m) < 10 ? '0' + Number(m) : m;
            d = Number(d) < 10 ? '0' + Number(d) : d;
            h = Number(h) < 10 ? '0' + Number(h) : h;
            s = Number(s) < 10 ? '0' + Number(s) : s;
            sec = Number(sec) < 10 ? '0' + Number(sec) : sec;
            return y + '-' + m + '-' + d + '  ' + h + ':' + s + ':' + sec;
        },
        removeCase:function(cs){
            var i = 0,len = caseHas.length;
            for(;i < len;i++){
                if(caseHas[i] == cs){
                    caseHas.splice(i, 1);
                    break;
                }
            }
        },
        checkCases:function(input){
            var i = 0,len = caseHas.length;
            for(;i < len;i++){
                if(caseHas[i]['input'] && input == caseHas[i]['input'].get(0)){
                    return caseHas[i];
                }
            }
            return false;
        },
        initSelectYear:function(startYear, endYear){
            var me = this,strArr = [],sel = '',currYear = me.getDate().getFullYear();
            strArr.push('<select class="control-year">');
            for(;startYear <= endYear; endYear--){
                sel = endYear == currYear ? ' selected="selected" ' : '';
                strArr.push('<option'+ sel +' value="'+ endYear +'">'+ endYear +'</option>');
            }
            strArr.push('</select>');
            me.getControlYear().html(strArr.join(''));
            me.dom.find('.control-year').change(function(){
                var y = Number(this.value),m = me.getDate().getMonth(),d = me.getDate().getDate();
                me.setDate(new Date(y, m, d));
            });
        },
        initSelectMonth:function(){
            var me = this,strArr = [],i = 1,month = 12,sel = '',currMonth = me.getDate().getMonth();
            strArr.push('<select class="control-month">');
            for(;i <= month; i++){
                sel = i == (currMonth + 1) ? ' selected="selected" ' : '';
                strArr.push('<option'+ sel +' value="'+ i +'">'+ i +'</option>');
            }
            strArr.push('</select>');
            me.getControlMonth().html(strArr.join(''));
            me.dom.find('.control-month').change(function(){
                var y = me.getDate().getFullYear(),m = Number(this.value) - 1,d = 1;
                me.setDate(new Date(y, m, d));
                //console.log(m);
            });
        },
        //生成模拟下拉框
        initSimulateSelect:function(){
            var me = this,yearDom = me.dom.find('.control-year'),monthDom = me.dom.find('.control-month');
            if(!host.Select){
                return;
            }
            yearDom.hide();
            monthDom.hide();
            me.simSelectYear = new host.Select({realDom:yearDom, cls:'select-year w-1'});
            me.simSelectMonth = new host.Select({realDom:monthDom, cls:'select-monty w-1'});

            me.simSelectYear.addEvent('change', function(e, value, text){
                var y = Number(value),m = me.getDate().getMonth(),d = me.getDate().getDate();
                me.setDate(new Date(y, m, d));
            });
            me.simSelectMonth.addEvent('change', function(e, value, text){
                var y = me.getDate().getFullYear(),m = Number(value) - 1,d = 1;
                me.setDate(new Date(y, m, d));
            });

        },
        getDate:function(){
            return this._date;
        },
        setDate:function(date){
            var me = this,darr = [],y,m,d,h = 0,s = 0,sec = 0,_date,cfg = me.defConfig;
            if(typeof date == 'string'){
                darr = me.getDateArr(date);
                y = darr[0];
                m = darr[1] - 1;
                d = darr[2];
                if(darr.length > 4){
                    h = darr[3];
                    s = darr[4];
                    sec = darr[5];
                    sec = typeof sec == 'undefined' ? '00' : sec;
                }
            }else if(typeof date == 'undefined'){
                _date = new Date();
                y = _date.getFullYear();
                m = _date.getMonth();
                d = _date.getDate();
                h = _date.getHours();
                s = _date.getMinutes();
                sec = _data.getSeconds();
            }else{
                y = date.getFullYear();
                m = date.getMonth();
                d = date.getDate();
                h = date.getHours();
                s = date.getMinutes();
                sec = date.getSeconds();
            }
            me._date = new Date(y, m, d);
            me._date.setHours(h, s, sec, 0);
            me.render(y, m, d, h, s, sec);
        },
        setInputVal:function(v){
            var me = this;
            if(me.input.size() > 0){
                me.input.val(v);
            }
            me.fireEvent('afterSetValue');
        },
        //获取上个月的天数
        getPreMonthDays:function(y, m){
            var y = (m == 0) ? (y - 1) : y,
                date = new Date(y, m, 0);
            //alert(y + '/' + date.getMonth() + '/' + date.getDate());
            return date.getDate();
        },
        //将字符串日期格式化成日期数组
        getDateArr:function(dateStr){
            return $.trim(dateStr).replace(/\D/g, '-').replace(/\-+/g, '-').split('-');
        },
        show:function(){
            var me = this,pos;
            if(me.input && me.input.size() > 0){
                pos = me.input.offset();
                me.dom.css({left:pos.left,top:pos.top + me.input.outerHeight()});
            }
            me.effectShow();
        },
        hide:function(){
            var me = this;
            me.effectHide();
            host.util.doc.unbind('mousedown.datapcker-' + me.randomNum);
            me.dom.remove();
        },
        getPreMonth:function(){
            var me = this;
            return me._domPre || (me._domPre = me.dom.find('.pre'));
        },
        getNextMonth:function(){
            var me = this;
            return me._domNext || (me._domNext = me.dom.find('.next'));
        },
        getControlYear:function(){
            var me = this;
            return me._domYear || (me._domYear = me.dom.find('.year'));
        },
        getControlMonth:function(){
            var me = this;
            return me._domMonth || (me._domMonth = me.dom.find('.month'));
        },
        getContent:function(){
            var me = this;
            return me._content || (me._content = me.dom.find('.content'));
        },
        getMaxDay:function(y, m){
            var date = new Date(y, m+1, 0);
            return date.getDate();
        },
        getWeek:function(y, m, d){
            return (new Date(y, m, d)).getDay();
        },
        isToday:function(date, y, m, d){
            return date.getFullYear() == y && date.getMonth() == m && date.getDate() == d;
        },
        preMonth:function(){
            var me = this,date = me.getDate(),y = date.getFullYear(),m = date.getMonth(),d = date.getDate();
            m -= 1;
            if(m < 0){
                m = 11;
                y -= 1;
            }
            me.setDate(new Date(y, m, 1));
        },
        nextMonth:function(){
            var me = this,date = me.getDate(),y = date.getFullYear(),m = date.getMonth(),d = date.getDate();
            m += 1;
            if(m > 11){
                m = 0;
                y += 1;
            }
            me.setDate(new Date(y, m, 1));
        },
        render:function(y, m, d, h, s, sec){
            var me = this,cfg = me.defConfig,date = new Date(),maxDay,week,i = 0,j = 1,k = 1,l = 0,n = 1,rownum,str = [],isFirst = true,
                preMonthDays,
                preYear,
                preMonth,
                nextYear,
                nextMonth,
                now = new Date(),
                //今天
                todayCls = 'day-today',
                //目标日期
                dayCurrCls = 'day-curr',
                //小时和分钟
                contentTime,
                inputs;


            if(!y){
                y = me._date.getFullYear();
                m = me._date.getMonth();
                d = me._date.getDate();
                h = me._date.getHours();
                s = me._date.getMinutes();
                sec = me._date.getSeconds();
            }
            //本月最大天数
            maxDay = me.getMaxDay(y, m);
            week = me.getWeek(y, m, 0);
            rownum = Math.ceil((week + 1 + maxDay)/7);
            //上月最大天数
            preMonthDays = me.getPreMonthDays(y, m);


            str.push('<table width="100%" class="tb">');
            str.push('<tbody>');
            str.push(DAYSTRING);
            for(;i < rownum;i++){
                str.push('<tr>');
                k = 1;
                if(isFirst){
                    preMonth = (m - 1) < 0 ? 11 : m - 1;
                    preYear = (m - 1) < 0 ? y - 1 : y;
                    for(;l <= week;l++){
                        str.push('<td class="day-preMonth" data-year="'+ preYear +'" data-month="'+ preMonth +'">'+ (preMonthDays - week + l) +'</td>');
                    }
                    isFirst = false;
                    k += week+1;
                }
                for(;j <= maxDay;j++){
                    if(k%(7 + 1) == 0){
                        break;
                    }
                    todayCls = me.isToday(now, y, m, j) ? 'day-today' : '';
                    daycurrCls = d == j ? ' day-curr ' : '';
                    str.push('<td class="day-thisMonth '+ todayCls + daycurrCls +'" data-year="'+ y +'" data-month="'+ m +'">'+ j +'</td>');
                    k++;
                }

                if(i == rownum - 1){
                    nextMonth = (m + 1) > 11 ? 0 : m + 1;
                    nextYear = (m + 1) > 11 ? y + 1 : y;
                    for(;n < rownum*7 - week - maxDay;n++){
                        str.push('<td class="day-nextMonth" data-year="'+ nextYear +'" data-month="'+ nextMonth +'">'+ n +'</td>');
                    }
                }


                str.push('</tr>');
            }
            str.push('</tbody>');
            str.push('</table>');

            me.getContent().html(str.join(''));



            me.fireEvent('afterRender');

            if(cfg.isShowTime){
                contentTime = me.dom.find('.content-time');
                contentTime.show();
                inputs = contentTime.find('input');
                inputs.get(0).value = Number(h) < 10 ? '0' + Number(h) : h;
                inputs.get(1).value = Number(s) < 10 ? '0' + Number(s) : s;
                inputs.get(2).value = Number(sec) < 10 ? '0' + Number(sec) : sec;
            }

            me.dom.find('.control-year option').each(function(){
                if(Number(this.value) == y){
                    this.selected = true;
                    if(me.simSelectYear){
                        me.simSelectYear.setValue(y, true);
                    }
                }else{
                    this.selected = false;
                }
            });
            me.dom.find('.control-month option').each(function(){
                if(Number(this.value) == (m+1)){
                    this.selected = true;
                    if(me.simSelectMonth){
                        me.simSelectMonth.setValue(m+1, true);
                    }
                }else{
                    this.selected = false;
                }
            });


        }

    };

    var Main = host.Class(pros, Event);
        Main.defConfig = defConfig;
    host[name] = Main;

    host[name].getInstance = function(){
        return instance || (instance = new host[name]());
    };

})(UIscript, "DatePicker", UIscript.Event, jQuery);


/*
 * scroll-bar 模拟滚动条插件
 *
 */
(function (factory) {
    if ( typeof define === 'function' && define.amd ) {
        // AMD. Register as an anonymous module.
        define(['jquery'], factory);
    } else if (typeof exports === 'object') {
        // Node/CommonJS style for Browserify
        module.exports = factory;
    } else {
        // Browser globals
        factory(jQuery);
    }
}(function ($) {

    var toFix  = ['wheel', 'mousewheel', 'DOMMouseScroll', 'MozMousePixelScroll'],
        toBind = ( 'onwheel' in document || document.documentMode >= 9 ) ?
                    ['wheel'] : ['mousewheel', 'DomMouseScroll', 'MozMousePixelScroll'],
        slice  = Array.prototype.slice,
        nullLowestDeltaTimeout, lowestDelta;

    if ( $.event.fixHooks ) {
        for ( var i = toFix.length; i; ) {
            $.event.fixHooks[ toFix[--i] ] = $.event.mouseHooks;
        }
    }

    var special = $.event.special.mousewheel = {
        version: '3.1.11',

        setup: function() {
            if ( this.addEventListener ) {
                for ( var i = toBind.length; i; ) {
                    this.addEventListener( toBind[--i], handler, false );
                }
            } else {
                this.onmousewheel = handler;
            }
            // Store the line height and page height for this particular element
            $.data(this, 'mousewheel-line-height', special.getLineHeight(this));
            $.data(this, 'mousewheel-page-height', special.getPageHeight(this));
        },

        teardown: function() {
            if ( this.removeEventListener ) {
                for ( var i = toBind.length; i; ) {
                    this.removeEventListener( toBind[--i], handler, false );
                }
            } else {
                this.onmousewheel = null;
            }
            // Clean up the data we added to the element
            $.removeData(this, 'mousewheel-line-height');
            $.removeData(this, 'mousewheel-page-height');
        },

        getLineHeight: function(elem) {
            var $parent = $(elem)['offsetParent' in $.fn ? 'offsetParent' : 'parent']();
            if (!$parent.length) {
                $parent = $('body');
            }
            return parseInt($parent.css('fontSize'), 10);
        },

        getPageHeight: function(elem) {
            return $(elem).height();
        },

        settings: {
            adjustOldDeltas: true, // see shouldAdjustOldDeltas() below
            normalizeOffset: true  // calls getBoundingClientRect for each event
        }
    };

    $.fn.extend({
        mousewheel: function(fn) {
            return fn ? this.bind('mousewheel', fn) : this.trigger('mousewheel');
        },

        unmousewheel: function(fn) {
            return this.unbind('mousewheel', fn);
        }
    });


    function handler(event) {
        var orgEvent   = event || window.event,
            args       = slice.call(arguments, 1),
            delta      = 0,
            deltaX     = 0,
            deltaY     = 0,
            absDelta   = 0,
            offsetX    = 0,
            offsetY    = 0;
        event = $.event.fix(orgEvent);
        event.type = 'mousewheel';

        // Old school scrollwheel delta
        if ( 'detail'      in orgEvent ) { deltaY = orgEvent.detail * -1;      }
        if ( 'wheelDelta'  in orgEvent ) { deltaY = orgEvent.wheelDelta;       }
        if ( 'wheelDeltaY' in orgEvent ) { deltaY = orgEvent.wheelDeltaY;      }
        if ( 'wheelDeltaX' in orgEvent ) { deltaX = orgEvent.wheelDeltaX * -1; }

        // Firefox < 17 horizontal scrolling related to DOMMouseScroll event
        if ( 'axis' in orgEvent && orgEvent.axis === orgEvent.HORIZONTAL_AXIS ) {
            deltaX = deltaY * -1;
            deltaY = 0;
        }

        // Set delta to be deltaY or deltaX if deltaY is 0 for backwards compatabilitiy
        delta = deltaY === 0 ? deltaX : deltaY;

        // New school wheel delta (wheel event)
        if ( 'deltaY' in orgEvent ) {
            deltaY = orgEvent.deltaY * -1;
            delta  = deltaY;
        }
        if ( 'deltaX' in orgEvent ) {
            deltaX = orgEvent.deltaX;
            if ( deltaY === 0 ) { delta  = deltaX * -1; }
        }

        // No change actually happened, no reason to go any further
        if ( deltaY === 0 && deltaX === 0 ) { return; }

        // Need to convert lines and pages to pixels if we aren't already in pixels
        // There are three delta modes:
        //   * deltaMode 0 is by pixels, nothing to do
        //   * deltaMode 1 is by lines
        //   * deltaMode 2 is by pages
        if ( orgEvent.deltaMode === 1 ) {
            var lineHeight = $.data(this, 'mousewheel-line-height');
            delta  *= lineHeight;
            deltaY *= lineHeight;
            deltaX *= lineHeight;
        } else if ( orgEvent.deltaMode === 2 ) {
            var pageHeight = $.data(this, 'mousewheel-page-height');
            delta  *= pageHeight;
            deltaY *= pageHeight;
            deltaX *= pageHeight;
        }

        // Store lowest absolute delta to normalize the delta values
        absDelta = Math.max( Math.abs(deltaY), Math.abs(deltaX) );

        if ( !lowestDelta || absDelta < lowestDelta ) {
            lowestDelta = absDelta;

            // Adjust older deltas if necessary
            if ( shouldAdjustOldDeltas(orgEvent, absDelta) ) {
                lowestDelta /= 40;
            }
        }

        // Adjust older deltas if necessary
        if ( shouldAdjustOldDeltas(orgEvent, absDelta) ) {
            // Divide all the things by 40!
            delta  /= 40;
            deltaX /= 40;
            deltaY /= 40;
        }

        // Get a whole, normalized value for the deltas
        delta  = Math[ delta  >= 1 ? 'floor' : 'ceil' ](delta  / lowestDelta);
        deltaX = Math[ deltaX >= 1 ? 'floor' : 'ceil' ](deltaX / lowestDelta);
        deltaY = Math[ deltaY >= 1 ? 'floor' : 'ceil' ](deltaY / lowestDelta);

        // Normalise offsetX and offsetY properties
        if ( special.settings.normalizeOffset && this.getBoundingClientRect ) {
            var boundingRect = this.getBoundingClientRect();
            offsetX = event.clientX - boundingRect.left;
            offsetY = event.clientY - boundingRect.top;
        }

        // Add information to the event object
        event.deltaX = deltaX;
        event.deltaY = deltaY;
        event.deltaFactor = lowestDelta;
        event.offsetX = offsetX;
        event.offsetY = offsetY;
        // Go ahead and set deltaMode to 0 since we converted to pixels
        // Although this is a little odd since we overwrite the deltaX/Y
        // properties with normalized deltas.
        event.deltaMode = 0;

        // Add event and delta to the front of the arguments
        args.unshift(event, delta, deltaX, deltaY);

        // Clearout lowestDelta after sometime to better
        // handle multiple device types that give different
        // a different lowestDelta
        // Ex: trackpad = 3 and mouse wheel = 120
        if (nullLowestDeltaTimeout) { clearTimeout(nullLowestDeltaTimeout); }
        nullLowestDeltaTimeout = setTimeout(nullLowestDelta, 200);

        return ($.event.dispatch || $.event.handle).apply(this, args);
    }

    function nullLowestDelta() {
        lowestDelta = null;
    }

    function shouldAdjustOldDeltas(orgEvent, absDelta) {
        // If this is an older event and the delta is divisable by 120,
        // then we are assuming that the browser is treating this as an
        // older mouse wheel event and that we should divide the deltas
        // by 40 to try and get a more usable deltaFactor.
        // Side note, this actually impacts the reported scroll distance
        // in older browsers and can cause scrolling to be slower than native.
        // Turn this off by setting $.event.special.mousewheel.settings.adjustOldDeltas to false.
        return special.settings.adjustOldDeltas && orgEvent.type === 'mousewheel' && absDelta % 120 === 0;
    }
}));


(function(host, name, Event, $, undefined) {


    var defConfig = {

    };

    var pros = {

        /*
         *初始化插件
         */
        init: function(setting) {
            //滚动条设置
            var _setting = setting || {};
            var _name = (_setting.name) ? _setting.name : '#J-balls-order-wrap';

            //滚动条对象
            this.scrollBar = {

                //插件支持外部传递DOM
                //默认的支持DOM均为class=scrollbar的DOM
                eDom: $(_name),

                //滚动条的方向[默认为垂直]
                //@param [apeak:垂直 | level:水平]
                direction: _setting.direction || 'apeak',

                //滚动条的宽度&&高度
                //[横向滚动时为高度] || [垂直滚动时为宽度]
                size: _setting.size || '5px'
            };

            //鼠标移动标记
            this.allowMove = false;

            //鼠标滚动标记
            this.allowWheel = true;

            //处理DOM
            this._disposalDom();
        },

        //正数
        toPositive: function(n) {
            return n < 0 ? -n : n;
        },

        //取整
        toInt: function(n) {
            return isNaN(parseInt(n)) ? 0 : parseInt(n);
        },

        /*
         *绑定事件
         */
        _addEvent: function() {
            var dom = this.scrollBar.eDom;
            var that = this;
            var scrollBar = dom.find('.scroll_Bar');

            //绑定滚动条事件
            dom.find('.scroll_Bar')
                .bind('mousedown', function(event) {
                    var e = event || window.event;
                    var t = this;

                    //取消默认事件并阻止传播
                    that.halt(e);

                    //记录初始坐标
                    if (that.scrollBar.direction == 'apeak') {
                        that.noncePosition = e.clientY;
                        that.scrollPosition = parseInt(scrollBar.css('top'));
                    } else {
                        that.noncePosition = e.clientX;
                        that.scrollPosition = parseInt(scrollBar.css('left'));
                    }

                    $(t).addClass('scroll_Bar_hover');

                    //处理鼠标点击
                    that._scrollBarClick(e);

                    $(document).bind('mousemove', function(e) {

                        //取消默认事件并阻止传播
                        that.halt(e);

                        //处理鼠标移动
                        that._scrollBarMove(e);
                    }).one('mouseup', function(e) {
                        //解绑document的mousemove
                        //限制事件的存活周期
                        $(document).unbind('mousemove');
                        //恢复颜色
                        $(t).removeClass('scroll_Bar_hover');

                        //更改触发移动标记
                        that.allowMove = null;

                        //清空坐标信息
                        that.noncePosition = null;

                        //触发当前TOP记录
                        that.scrollPosition = null
                    })
                })

            //绑定鼠标滚轮
            // dom.bind('mousewheel', function(event) {
            //  var e = event || window.event;

            //  that._mouseWheel(e);
            // })

            // dom.bind('DOMMouseScroll', function(event) {
            //  var e = event || window.event;

            //  that._mouseWheel(e);
            // })

            dom.mousewheel(function(e){

                that._mouseWheel(e);
            });

            //绑定用户点击滚动条区域事件
            dom.find('.scroll_area').bind('click', function(event) {
                var e = event || window.event;
                var position = that.mouseCoords(e);

                that.controlBar(position, this);
            });

        },

        /**
         * [mouseCoords description]
         * @param  {[event]} ev [window.event对象]
         */
        mouseCoords: function(ev) {
            if (ev.pageX || ev.pageY) {
                return {
                    x: ev.pageX,
                    y: ev.pageY
                };
            };
            return {
                x: ev.clientX + document.body.scrollLeft - document.body.clientLeft,
                y: ev.clientY + document.body.scrollTop - document.body.clientTop
            };
        },

        /*
         *阻止event对象的浏览器默认时间
         *并且阻止传播
         */
        halt: function(e) {
            e.preventDefault();
            e.stopPropagation();
        },

        /*
         *处理模拟滚动条的容器DOM
         */
        _disposalDom: function() {
            var dom = this.scrollBar.eDom;
            var domInner = dom.html();
            var warpInner;

            //属性检查
            if (dom.css('overflow') != 'hidden') {
                dom.css({
                    'overflow': 'hidden'
                });
            };
            if (dom.css('position') != 'relative') {
                dom.css({
                    'position': 'relative'
                });
            }

            //向容器中添加定位层
            //将原对象的内容进行包裹处理
            //dom.wrapInner('<div class="scroll_warp"></div>');

            dom.append('<div class="scroll_warp">'+ domInner +'</div>');

            warpInner = dom.find('.scroll_warp');
            warpInner.css('position', 'absolute');

            //处理滚动条
            this._disposalBar();

            //console.log(warpInner.css('overflow'));

        },

        /**
         *点击定位滚动条位置功能
         * @param  {[obj]} e [当前鼠标位置 x:垂直 y:水平]
         * @param  {[dom]} dom [需要进行定位的DOM]
         */
        controlBar: function(e, dom) {
            var direction = (this.scrollBar.direction == 'apeak') ? 'top' : 'left',
                position = (this.scrollBar.direction == 'apeak') ? e.y : e.x,
                parentDom = (this.scrollBar.direction == 'apeak') ? $(dom).offset().top : $(dom).offset().left,
                domHeight = $(dom).height() / 100,
                num = parseInt((position - parentDom) / domHeight);

            this.scorllPosition(num + '%'); //点击定位滚动条位置
        },

        //隐藏滚动条
        scrollhide: function() {
            var dom = this.scrollBar.eDom;
            var scroll = dom.find('.scroll_warps');

            if (scroll.css('visibility') == 'visible') {
                scroll.css('visibility', 'hidden');
                this.allowWheel = false;
            }
        },

        //显示滚动条
        scrollshow: function() {
            var dom = this.scrollBar.eDom;
            var scroll = dom.find('.scroll_warps');
            var scrollBar = dom.find('.scroll_Bar');
            var direction = (this.scrollBar.direction == 'apeak') ? 'top' : 'left';

            if (scroll.css('visibility') == 'hidden') {
                scroll.css('visibility', 'visible');
                this.allowWheel = true;
                scrollBar.css(direction, '0px');
            }
        },
        /*
         *处理模拟滚动条的容器DOM
         */
        _checkHeight: function() {
            var dom = this.scrollBar.eDom;
            var warps = dom.find('.scroll_warp');
            var scroll = dom.find('.scroll_warps');
            var scrollBar = dom.find('.scroll_Bar');

            //warps.css('height', this.getCustomWarpsHeight());

            //如果内容层底部超过底部限制
            if ((warps.outerHeight() - dom.outerHeight()) < this.toPositive(parseInt(warps.css('top')))) {
                warps.css('top', -(warps.outerHeight() - dom.outerHeight()));
                scrollBar.css('top', dom.outerHeight() - scrollBar.outerHeight());
            }

            if (this.scrollBar.direction == 'apeak' && warps.outerHeight() <= dom.outerHeight()) { //如果内容高度小于
                //console.log(warps.css('height'));
                this.scrollhide();
                warps.css('top', '0px');
                return true;
            } else if (this.scrollBar.direction == 'level' && warps.outerWidth() <= dom.outerWidth()) { //如果宽度高度小于
                this.scrollhide();
                warps.css('left', '0px');
                return true;
            }

            this.scrollshow();
            return false;
        },

        /*
         *处理滚动条相关
         */
        _disposalBar: function() {
            var dom = this.scrollBar.eDom;
            var size = this.scrollBar.size;
            var scrollBar = $('<div class="scroll_warps"><div class="scroll_area"><div class="scroll_Bar"></div></div></div>');
            var warps, area, bar;

            //给容器添加滚动条
            dom.append(scrollBar);
            warps = dom.find('.scroll_warps');
            area = dom.find('.scroll_area');
            bar = dom.find('.scroll_Bar');


            //根据方向给滚动条附加相应样式
            if (this.scrollBar.direction == 'apeak') {
                warps.css({
                    'position': 'absolute',
                    'width': size,
                    'height': dom.outerHeight(),
                    'right': '5px',
                    'top': '0px',
                    'visibility': 'visible'
                });
                area.css({
                    'position': 'relative',
                    'width': '100%',
                    'height': dom.outerHeight()
                });
                bar.css({
                    'position': 'absolute',
                    'width': size,
                    'min-height': '20px',
                    'top': '0px',
                    'cursor': 'pointer'
                });
            } else {
                warps.css({
                    'position': 'absolute',
                    'width': dom.outerWidth(),
                    'height': size,
                    'bottom': '5px',
                    'left': '0px',
                    'visibility': 'visible'
                });
                area.css({
                    'position': 'relative',
                    'height': '100%',
                    'width': dom.outerHeight()
                });
                bar.css({
                    'position': 'absolute',
                    'height': size,
                    'min-width': '20px',
                    'left': '0px',
                    'cursor': 'pointer'
                });
            };

            //处理滚动条[高度][宽度]
            this.resizeHeight();

            //绑定滚动条触发事件
            this._addEvent();
        },

        /*
         *处理滚动条相关
         */
        scorllPosition: function(setting) {
            var dom = this.scrollBar.eDom;
            var eScrollSize = dom.find('.scroll_Bar');
            var eScrollwrap = dom.find('.scroll_warps');
            var allowSize = eScrollwrap.height() - eScrollSize.height();
            var scorllNum;

            //如果是数值
            if (typeof(setting) == 'number') {
                scorllNum = setting;
                //百分比形式
            } else if (typeof(setting) == 'string') {
                //如果是百分比形式
                //或者为纯数字的STRING类型
                if (setting.indexOf('%') != -1 || !! setting.match(/^\d+$/g)) {
                    scorllNum = setting;
                    //如果是单独查找DOM形式
                } else {
                    //如果当前dom有多个
                    if ($(setting).size() > 1) {
                        scorllNum = $($(setting)[0]).position().top;
                    } else {
                        scorllNum = $(setting).position().top;
                    };
                };
                //dom查找形式
            } else if (typeof(setting) == 'object' && setting instanceof Array) {
                var domName = dom.find(setting[0]),
                    domSub = setting[1];

                //如果不符合条件
                if (domName.size() == 0 || typeof(domSub) != 'number') {
                    return
                }
                scorllNum = $(domName[domSub]).position().top;
            };
            //判断参数
            if (typeof(scorllNum) == 'number') {
                scorllNum = scorllNum / dom.outerHeight() * eScrollwrap.outerHeight();
            } else {
                scorllNum = parseInt(scorllNum) / 100 * eScrollwrap.outerHeight() - eScrollSize.outerHeight() / 2;
            };
            //如果超出总高度则为置底
            if (scorllNum > allowSize) {
                scorllNum = allowSize;
            } else if (scorllNum < 0) {
                scorllNum = 0;
            }
            //调整滚动条高度
            this._processScroll(eScrollSize, scorllNum);
        },

        /**
         * [resizeHeight description]
         * @param  {[obj]} setting.scorllNum [需要初始化滚动条的位置]
         */
        resizeHeight: function() {
            var dom = this.scrollBar.eDom;
            var eWarpSize = dom.find('.scroll_warp');
            var scrollBar = dom.find('.scroll_Bar');
            var scrollArea = dom.find('.scroll_warps');
            var domSize;

            if (this._checkHeight()) {
                return;
            };

            //判断是否显示滚动条
            if (this.scrollBar.direction == 'apeak') { //计算出滚动条的[高度]
                domSize = dom.height();
                scrollBar.css('height', domSize / eWarpSize.outerHeight() * 100 + '%');
                scrollArea.css('height', dom.outerHeight())
            } else { //计算出滚动条的[宽度]
                domSize = dom.width();
                scrollBar.css('width', domSize / eWarpSize.outerWidth() * 100 + '%');
                scrollArea.css('width', dom.outerHeight())
            };
            this._innerPosition(); //判断内容层DOM定位
        },

        /*
         *高度变化内容层的定位
         *解决内容层底部高于容器底部
         */
        _innerPosition: function() {
            var dom = this.scrollBar.eDom;
            var eWarpSize = dom.find('.scroll_warp');
            var eScrollSize = dom.find('.scroll_Bar');

            if (this.scrollBar.direction == 'apeak') {
                if (eWarpSize.outerHeight() - dom.outerHeight() <= this.toPositive(parseInt(eWarpSize.css('top')))) {
                    eWarpSize.css('top', -(eWarpSize.outerHeight() - dom.outerHeight()));
                    eScrollSize.css('top', dom.outerHeight() - eScrollSize.outerHeight());
                };
            } else {
                if (eWarpSize.outerWidth() - dom.outerWidth() <= this.toPositive(parseInt(eWarpSize.css('top')))) {
                    eWarpSize.css('left', -(eWarpSize.outerWidth() - dom.outerWidth()));
                    eScrollSize.css('left', dom.outerWidth() - eScrollSize.outerWidth());
                };
            };
        },

        /*
         *处理鼠标点击
         */
        _scrollBarClick: function(e) {
            this.allowMove = true;
        },

        /*
         *设定滚动条位置
         */
        _processScroll: function(dom, num) {
            var direction = (this.scrollBar.direction == 'apeak') ? 'top' : 'left';

            dom.css(direction, num);
            this._eWarpMove(); //同步内容层的移动
        },

        /*
         *处理鼠标移动
         */
        _scrollBarMove: function(e, num) {
            var dom = this.scrollBar.eDom;
            var eWarpSize = dom.find('.scroll_warp');
            var eScrollSize = dom.find('.scroll_Bar');
            var warpSize, scrollSize, size, scrollSize;

            if (!this.allowMove) return; //判断是否点击触发
            if (this._confine(e)) return; //判断是否超出边线
            scrollSize = (this.scrollBar.direction == 'apeak') ? e.clientY - this.noncePosition : e.clientX - this.noncePosition; //计算当前鼠标的偏移量
            this._processScroll(eScrollSize, this.scrollPosition + scrollSize); //设置滚动条位置
        },

        /*
         *判断是否超出边界
         */
        _confine: function(e, type) {
            var dom = this.scrollBar.eDom;
            var eWarpSize = dom.find('.scroll_warp');
            var eScrollSize = dom.find('.scroll_Bar');
            var scrollSize = (this.scrollBar.direction == 'apeak') ? e.clientY - this.noncePosition : e.clientX - this.noncePosition;
            var direction = (this.scrollBar.direction == 'apeak') ? 'top' : 'left';

            if (type == 'mousewheel') {
                scrollSize = parseInt(eScrollSize.css(direction));
            };

            if (this.scrollBar.direction == 'apeak') {
                //顶部超出
                if (this.scrollPosition + scrollSize <= 0) {
                    eWarpSize.css('top', '0px');
                    eScrollSize.css('top', '0px');
                    return true;
                };
                //底部超出
                if (this.scrollPosition + scrollSize > dom.outerHeight() - eScrollSize.outerHeight()) {
                    eWarpSize.css('top', -(eWarpSize.outerHeight() - dom.outerHeight()));
                    eScrollSize.css('top', dom.outerHeight() - eScrollSize.outerHeight());
                    return true;
                };
            } else {
                //左部超出
                if (this.scrollPosition + scrollSize <= 0) {
                    eWarpSize.css('left', '0px');
                    eScrollSize.css('left', '0px');
                    return true;
                };
                //右部超出
                if (this.scrollPosition + scrollSize > dom.outerWidth() - eScrollSize.outerWidth()) {
                    eWarpSize.css('left', -(eWarpSize.outerWidth() - dom.outerWidth()));
                    eScrollSize.css('left', dom.outerWidth() - eScrollSize.outerWidth());
                    return true;
                };
            };
            return false;
        },

        /*
         *处理鼠标移动
         */
        _eWarpMove: function() {
            var dom = this.scrollBar.eDom;
            var eWarpSize = dom.find('.scroll_warp');
            var eScrollSize = dom.find('.scroll_Bar');
            var size = (this.scrollBar.direction == 'apeak') ? (eWarpSize.outerHeight() - dom.outerHeight()) / (dom.outerHeight() - eScrollSize.outerHeight()) : (eWarpSize.outerWidth() - dom.outerWidth()) / (dom.outerWidth() - eScrollSize.outerWidth());
            var direction = (this.scrollBar.direction == 'apeak') ? 'top' : 'left';
            var num = this.toInt(eScrollSize.css(direction));

            eWarpSize.css(direction, -(size * num)); //移动内容层
        },

        /*
         *处理鼠标滚轮滚动
         */
        _mouseWheel: function(e) {
            var delta = (this.scrollBar.direction == 'apeak') ? e.deltaY * 10 : e.deltaX * 10; //e.detail ? e.detail / -3 * 10 : e.wheelDelta / 120 * 10;
            var dom = this.scrollBar.eDom;
            var eScrollSize = dom.find('.scroll_Bar');
            var direction = (this.scrollBar.direction == 'apeak') ? 'top' : 'left';

            //判断是否点击触发
            if (!this.allowWheel) {
                return;
            } else {
                //阻止默认事件
                this.halt(e);
            };
            this.scrollPosition = -delta;
            //判断是否超出边线
            if (this._confine(e, 'mousewheel')) return;
            //滚动条移动
            eScrollSize.css(direction, parseInt(eScrollSize.css(direction)) + (-delta));
            //同步内容层的移动
            this._eWarpMove();
        }
    };

    var Main = host.Class(pros, Event);
    Main.defConfig = defConfig;
    host[name] = Main;
})(UIscript, "Scroll", UIscript.Event, jQuery);




//模拟下拉框组件
(function(host, name, Event, $, undefined){
    var defConfig = {
        //最外层添加的class样式
        cls:'',
        valueKey: 'value',
        textKey: 'text',
        //是否同时能输入
        isInput:false,
        // 是否能作为计数器使用
        isCounter: false,
        // 计数器的最小值
        min: 1,
        // 计数器的最大值
        max: 99999,
        // 加减的步长
        step: 1,
        //对应的真实select
        realDom:'',
        //面板展开时的z-index值
        zIndex:100,
        //是否开启鼠标条
        isScroll:true,
        //滚动容器显示高度
        scrollHeight:310,
        //滚轮每次滚动面变移动距离
        scrollDis:31,
        //模拟select模板
        tpl:'<div class="choose-model"><div class="choose-list"><div class="choose-list-cont" style="-webkit-overflow-scrolling: touch;"><#=loopItems#></div></div><span class="choose-scroll" onselectstart="return false;"></span><span class="info"><input data-realvalue="<#=value#>" class="choose-input choose-input-disabled" disabled="disabled" type="text" value="<#=text#>" /></span><i></i></div>',
        //单行元素模板
        itemTpl:'<a data-value="<#=value#>" href="#"><#=text#></a>'
    };

    var pros = {
        init:function(cfg){
            var me = this;
            me.opts = cfg;
            me.realDom = $(cfg.realDom);
            me.realDom.hide();
            me.dom = null;
            me.listDom = null;
            me.buildSelect();
            // 计数器相关
            if( cfg.isCounter ){
                me.$ctrlDecrease = $('<span class="select-counter-action counter-decrease" data-counter-action="decrease"></span>');
                me.$ctrlIncrease = $('<span class="select-counter-action counter-increase" data-counter-action="increase"></span>');
                me.dom.before(me.$ctrlDecrease).after(me.$ctrlIncrease);
                me.$ctrl = me.$ctrlDecrease.add(me.$ctrlIncrease);
                me.setMinValue(cfg.min);
                me.setMaxValue(cfg.max);
                // me.checkCtrl();
                me.counterEvent(cfg);
            }
            me.initEvent();
        },
        show:function(){
            this.dom.show();
        },
        hide:function(){
            this.dom.hide();
        },
        showScroll:function(){
            var me = this;
            if(me.isScrollAble){
                this.getScrollDom().show();
            }
        },
        hideScroll:function(){
            var me = this,
                dom = me.getScrollDom();
            dom.hide();
            if(me.isScrollAble){
                me.scrollSetTop(0);
                dom.css('top', 0);
            }
        },
        getScrollDom:function(){
            var me = this;
            return me.scrollDom || (me.scrollDom = me.dom.find('.choose-scroll'));
        },
        initScroll:function(){
            var me = this,cfg = me.defConfig;
            if(me.getListContDomHeight() > cfg.scrollHeight){
                me.isScrollAble = true;
                me.getListDom().css({'height':cfg.scrollHeight, 'overflow':'hidden'});
                me.reBuildScroll();
            }else{
                me.isScrollAble = false;
            }
        },
        //计算滚动条相关参数
        reBuildScroll:function(){
            var me = this,cfg = me.defConfig,
                outerHeight = cfg.scrollHeight,
                innerHeight = me.getListContDomHeight();

            //最高内容器高度与显示区域高度比例
            me.scrollBl = innerHeight/outerHeight;
            //滚动条应显示的高度
            me.getScrollDom().css('height', outerHeight/me.scrollBl);
            me.scrollMin = 0;
        },
        scrollSetTop:function(top){
            var me = this;
            me.getListDom().scrollTop(top * me.scrollBl);
        },
        getListDom:function(){
            var me = this;
            return me.listDom || (me.listDom = me.dom.find('.choose-list'));
        },
        getListContDom:function(){
            var me = this;
            return me.listContDom || (me.listContDom = me.dom.find('.choose-list-cont'));
        },
        getListContDomHeight:function(){
            var me = this,h = 0;
            me.getListDom().css({'visibility':'hidden'}).show();
            h = me.getListContDom().height();
            me.getListDom().css({'visibility':'visible'}).hide();
            return h;
        },
        buildSelect:function(){
            var me = this,cfg = me.defConfig,tpl = cfg.tpl,itemTpl = cfg.itemTpl,items = me.getRealDom().options,len = items.length,i = 0,
                itemStrArr = [],
                currValue = '',
                currText = '';
            for(;i < len;i++){
                itemStrArr[i] = itemTpl.replace(/<#=value#>/g, items[i].value).replace(/<#=text#>/g, items[i].text);
                if(i == me.getRealDom().selectedIndex){
                    currValue = items[i].value;
                    currText = items[i].text;
                }
            }
            tpl = tpl.replace(/<#=text#>/g, currText).replace(/<#=loopItems#>/g, itemStrArr.join(''));
            me.dom = $(tpl);
            me.dom.addClass(cfg.cls);
            me.dom.insertBefore(me.getRealDom());

            if(cfg.isScroll){
                me.initScroll();
            }

            if(cfg.isInput){
                me.getInput().removeAttr('disabled');
                me.getInput().removeClass('choose-input-disabled');
                me.inputEvent();
            }
            me.setValue(currValue);

            me.reSetListWidth();
        },
        reSetListWidth:function(){
            var me = this,width = 0;
            if(host.util.isIE6){
                width = me.dom.width() + 8;
                me.getListDom().width(width);
            }
        },
        //data [{value:,text:,checked:true}]
        reBuildSelect:function(data){
            var me = this,cfg = me.defConfig,sel = $(me.getRealDom()),strArr = [],strArrOption = [],itemTpl = me.defConfig.itemTpl,selectIndex;
            $.each(data, function(i){
                strArr[i] = '<option value="'+ this[cfg.valueKey] +'">'+ this[cfg.textKey] +'</option>';
                strArrOption[i] = itemTpl.replace(/<#=value#>/g, this[cfg.valueKey]).replace(/<#=text#>/g, this[cfg.textKey]);
                if(this['checked']){
                    selectIndex = i;
                }
            });
            sel.html(strArr.join(''));
            me.getListContDom().html(strArrOption.join(''));

            if(typeof selectIndex != 'undefined'){
                me.setValue(data[selectIndex][cfg.valueKey]);
            }

            if(me.defConfig.isScroll){
                me.initScroll();
            }
        },
        initEvent:function(){
            var me = this,scrollDis = me.defConfig.scrollDis;

            $(document).mousedown(function(e){
                var el = e.target;
                if(!$.contains(me.dom.get(0), el)){
                    me.getListDom().hide();
                    me.hideScroll();
                    me.dom.css('zIndex', '');
                    me.dom.find('.open').removeClass('open');
                }
            });
            $(window).blur(function(){
                me.getListDom().hide();
                me.dom.css('zIndex', '');
                me.dom.find('.open').removeClass('open');
                me.hideScroll();
            });


            me.dom.click(function(e){
                var el = e.target,attr = el.getAttribute('data-value');
                //如果是选项点击
                if(attr != null){
                    me.setValue(attr);
                }
                if($.trim(me.getListDom().css('display').toLowerCase()) != 'none'){
                    me.dom.css('zIndex', '');
                    me.getListDom().hide();
                    me.hideScroll();
                    me.dom.find('.open').removeClass('open');
                }else{
                    me.dom.css('zIndex', me.defConfig.zIndex);
                    me.getListDom().show();
                    me.showScroll();
                    me.dom.find('i').addClass('open');
                }

                e.preventDefault();
            });

            var deltaAll = 0;
            me.getListDom().mousewheel(function(e, delta){
                deltaAll += delta * 5;
                e.preventDefault();
                clearTimeout(me.timer);
                me.timer = setTimeout(function(){
                    var el = me.getListDom(),
                        top = el.scrollTop(),
                        delta = delta * 3,
                        sTop = parseInt(me.getScrollDom().css('top'));
                        newTop = top + deltaAll * scrollDis * -1,
                        scrollTop = 0,
                        contHeight = me.getListContDom().height(),
                        maxScroll = el.height() - me.getScrollDom().outerHeight() - 4;

                    deltaAll = 0;
                    el.stop().animate({scrollTop:newTop}, 500, 'easeOutQuad');
                    scrollTop = newTop/me.scrollBl;
                    scrollTop = scrollTop < 0 ? 0 : scrollTop;
                    scrollTop = scrollTop > maxScroll ? maxScroll : scrollTop;

                    me.scrollDom.stop().animate({top:scrollTop}, 400, 'easeOutQuad');
                }, 10);
            });


            var dragDom = me.getScrollDom(),donwX,donwY,isDraging = false,
                downEventFn = function(e){
                    isDraging = true;
                    donwY = e.clientY - parseInt(dragDom.css('top'));
                    if(dragDom.get(0).setCapture){
                        dragDom.get(0).setCapture();
                    }else if(window.captureEvents){
                        window.captureEvents(Event.MOUSEMOVE | Event.MOUSEUP);
                    }
                    $(document).bind('mousemove', moveEventFn);
                    $(document).bind('mouseup', upEventFn);
                },
                moveEventFn = function(e){
                    var top = e.clientY - donwY,h = 0;
                    e.preventDefault();
                    if(!isDraging){
                        return false;
                    }
                    h = me.getListDom().height() - dragDom.height() - 4;
                    top = top < 0 ? 0 : top;
                    top = top > h ? h : top;

                    dragDom.css('top',top);

                    //到达极限时为防止误差，直接设很大的数字(scroll的设置对超出范围没有影响)
                    top = top == h ? top * 100 : top;
                    me.scrollSetTop(top);
                },
                upEventFn = function(e){
                    if(dragDom.get(0).releaseCapture){
                        dragDom.get(0).releaseCapture();
                    }else if(window.captureEvents){
                        window.captureEvents(Event.MOUSEMOVE|Event.MOUSEUP);
                    }
                    isDraging = false;
                    $(document).unbind('mousemove', moveEventFn);
                    $(document).unbind('mouseup', upEventFn);
                };
            dragDom.mousedown(downEventFn);
            dragDom.click(function(e){
                e.preventDefault();
                e.stopPropagation();
            });

        },
        getInput:function(){
            var me = this;
            return me.input || (me.input = me.dom.find('.choose-input'));
        },
        //input校验函数
        inputEvent:function(){

        },
        getRealDom:function(){
            return this.realDom.get(0);
        },
        getItemList:function(){
            var me = this;
            return me.getListContDom().children();
        },
        //isStop 防止两个对象相互调用，相互触发形成死循环，例如：日期控件的面板渲染和Select的相互触发
        setValue:function(value, isStop){
            var me = this,dom = me.getRealDom(),index = dom.selectedIndex,options = dom.options,len = options.length,i = 0,text = '';
            for(;i < len;i++){
                if(value == options[i].value){
                    options[i].selected = true;
                    text = options[i].text;
                }else{
                    options[i].selected = false;
                }
            }
            value = '' + value;
            me.getInput().attr('data-realvalue', value);
            text = text == '' ? value : text;
            me.getInput().val(text);
            if(!isStop){
                me.fireEvent('change', value, text);
            }
            me.getItemList().removeClass('choose-item-current').parent().find('[data-value="'+ value +'"]').addClass('choose-item-current');
            if( me.opts.isCounter ){
                me.checkCtrl();
            }
        },
        getValue:function(){
            var me = this,dom = me.getRealDom(),index = dom.selectedIndex;
            if(me.defConfig.isInput){
                return me.getInput().attr('data-realvalue');
            }
            return dom.options[index].value;
        },
        getText:function(){
            var dom = this.getRealDom(),index = dom.selectedIndex;
            return dom.options[index].text;
        },
        setMinValue: function( num ){
            this.minValue = num;
            this.checkCtrl();
        },
        getMinValue: function(){
            return this.minValue;
        },
        setMaxValue: function( num ){
            this.maxValue = num;
            this.checkCtrl();
        },
        getMaxValue: function(){
            return this.maxValue;
        },
        setButtonStatus: function(button, status){
            if( !this.$ctrl ) return;
            var $ctrl = this.$ctrl.filter('[data-counter-action="' + button + '"]');
            if( status == 'disabled' ){
                $ctrl.addClass('disabled');
            }else{
                $ctrl.removeClass('disabled');
            }
        },
        checkCtrl: function() {
            var me = this, val = me.getValue();
            if (val <= me.getMinValue()) {
                me.setButtonStatus('decrease', 'disabled');
                val = me.getMinValue();
            } else {
                me.setButtonStatus('decrease');
            }
            if (val >= me.getMaxValue()) {
                me.setButtonStatus('increase', 'disabled');
                val = me.getMaxValue();
            } else {
                me.setButtonStatus('increase');
            }
            return val;
        },
        counterEvent: function() {
            var me = this, opts = me.opts;
            me.$ctrl.on('click', function(e) {
                if ($(this).hasClass('disabled')) return false;
                var val = parseInt(me.getValue()),
                    action = $(this).data('counter-action');
                if (action == 'increase') val += opts.step;
                else if (action == 'decrease') val -= opts.step;
                me.setValue(val);
            });
        }

    };

    var Main = host.Class(pros, Event);
        Main.defConfig = defConfig;
    host[name] = Main;
})(UIscript, "Select", UIscript.Event, jQuery);




//Mask页面遮罩静态类
//todo 考虑监听窗体缩放
(function(host, name, Event, $, undefined){
    var defConfig = {
        //参照物，将以该参照物的大小设置遮罩宽高
        target:'body',
        //
        opacity:.7,
        //
        'background-color':'#000',
        //
        'z-index':600,
        effectShow:function(){
            var me = this;
            me.dom.css('opacity', 0);
            me.dom.show();
            me.dom.animate({opacity:.7}, 300);
        },
        effectHide:function(){
            this.dom.hide();
        }
    },
    html = $('html'),
    doc = $(document),
    instance;

    var pros = {
        init:function(cfg){
            var me = this;
            me.dom = $('<div class="j-ui-mask" style="display:none;position:absolute;left:0;top:0;"></div>').appendTo('body').css({'opacity':cfg['opacity'],'background-color':cfg['background-color'],'z-index':cfg['z-index']});
            me.effectShow = cfg.effectShow;
            me.effectHide = cfg.effectHide;
        },
        show:function(){
            var me = this,w = doc.width(),h = doc.height();
            me.dom.css({width:w,height:h});
            this.effectShow();
        },
        hide:function(){
            this.effectHide();
            this.fireEvent('afterHide');
        },
        css:function(styles){
            this.dom.css(styles);
        }

    };



    var Main = host.Class(pros, Event);
        Main.defConfig = defConfig;

    //遮罩层无需多个实例，直接限制为唯一单例
    host[name] = function(){
        return host[name].getInstance();
    };

    host[name].getInstance = function(){
        return instance || (instance = new Main(defConfig));
    };
})(UIscript, "Mask", UIscript.Event, jQuery);



//模拟弹出窗口
//内部已经绑定了在IE6下模拟fixed效果
;(function(host, name, Event, $, undefined){
    var util = host.util,
    defConfig = {
        //点击按钮
        confirmButtonText : 'confirm',
        //取消按钮
        cancelButtonText : 'cancel',
        //关闭按钮
        closeButtonText : 'closeTip',
        //实例化时追加的最外层样式名
        cls:'',
        //窗体内主内容html字符串
        contentHtml:'',
        //在主体内容生成之后，执行afterSetConent函数时的延迟时间，以确保内容生成完成或者其内部的资源加载完成
        //也可以再每次调用setContent的时候，设置延迟参数值
        afterSetContentDelay:0,
        effectShow:function(){
            // var me = this;
            // util.toViewCenter(me.dom);
            // this.dom.show();
            var me = this,top;
            util.toViewCenter(me.dom);

            top = parseInt(me.dom.css('top'));
            me.dom.css({top:top, opacity:1});
            me.dom.show();
            me.dom.animate({top: top, opacity:1}, 300);
        },
        effectHide:function(){
            this.dom.hide();
        },
        zIndex:700,
        //是否使用fixed模式
        isFixed:true
    },
    doc = $(document),
    instance;

    var pros = {
        init:function(cfg){
            var me = this,position = cfg.isFixed ? 'fixed' : 'absolute';
            position = util.isIE6 ? 'absolute' : position;
            me.dom = $('<div class="j-ui-miniwindow pop '+ cfg.cls +'" style="z-index:'+ cfg.zIndex +';position:'+ position +';display:none;"><div class="pop-hd"><i class="pop-close closeBtn"></i><span class="pop-title">提示</span></div><div class="pop-bd"></div><div class="pop-control"><a style="display:none" href="javascript:void(0);" class="btn confirm">确 认</a><a style="display:none" href="javascript:void(0);" class="btn cancel">取 消</a><a href="javascript:void(0);" style="display:none" class="btn closeTip">关 闭</a></div></div>').appendTo('body');
            me.effectShow = cfg.effectShow;
            me.effectHide = cfg.effectHide;
            me.dom.on('click', '.closeBtn', function(){
                me.doNormalClose();
                me.hide();
            })
            me.dom.find('.' + me.defConfig.confirmButtonText).bind('click', function(){
                me.doConfirm();
            });
            me.dom.find('.' + me.defConfig.cancelButtonText).bind('click', function(){
                me.doCancel();
            });
            me.dom.find('.' + me.defConfig.closeButtonText).bind('click', function(){
                me.doClose();
            });
        },
        //默认关闭按钮事件
        doNormalClose: function(){
        },
        //执行确定事件
        doConfirm: function(){
            this.disabled=true;
        },
        //执行取消事件
        doCancel: function(){
        },
        //执行关闭事件
        doClose: function(){

        },
        //获取确认按钮对象
        getConfirmButtonDom: function(){
            var me= this;
            return me.getContainerDom().find('.' + me.defConfig.confirmButtonText);
        },
        //获取取消按钮对象
        getCancelButtonDom: function(){
            var me= this;
            return me.getContainerDom().find('.' + me.defConfig.cancelButtonText);
        },
        //获取关闭按钮对象
        getCloseButtonDom: function(){
            var me= this;
            return me.getContainerDom().find('.' + me.defConfig.closeButtonText);
        },
        //修改确认按钮名称
        setConfirmName: function(text){
            var me= this;
            me.getConfirmButtonDom().text(text);
        },
        //修改取消按钮名称
        setCancelName: function(text){
            var me= this;
            me.getCancelButtonDom().text(text);
        },
        //修改关闭按钮名称
        setCloseName: function(text){
            var me= this;
            me.getCloseButtonDom().text(text);
        },
        //显示确认按钮对象
        showConfirmButton: function(){
            var me= this;
            me.getConfirmButtonDom().show();
        },
        //显示取消按钮对象
        showCancelButton: function(){
            var me= this;
            me.getCancelButtonDom().show();
        },
        //显示关闭按钮对象
        showCloseButton: function(){
            var me= this;
            me.getCloseButtonDom().show();
        },
        //隐藏确认按钮对象
        hideConfirmButton: function(){
            var me= this;
            me.getConfirmButtonDom().hide();
        },
        //隐藏取消按钮对象
        hideCancelButton: function(){
            var me= this;
            me.getCancelButtonDom().hide();
        },
        //隐藏关闭按钮对象
        hideCloseButton: function(){
            var me= this;
            me.getCloseButtonDom().hide();
        },
        setTitle:function(html){
            this.getTitleDom().html(html);
        },
        setContent:function(html, delay){
            var me = this,cfg = me.defConfig,delayTime = 0;
            me.getContentDom().html(html);

            delayTime = !!cfg.afterSetContentDelay ? cfg.afterSetContentDelay : delayTime;
            delayTime = !!delay ? delay : delayTime;
            if(delayTime > 0){
                setTimeout(function(){
                    me.fireEvent('afterSetContent');
                },delayTime);
            }else{
                me.fireEvent('afterSetContent');
            }
        },
        show:function(){
            var me = this;
            me.fireEvent('beforeShow');
            this.effectShow();
            me.fireEvent('afterShow');
            if(util.isIE6 && me.defConfig.isFixed){
                me._IE6Fixed = util.startFixed(me.dom);
            }
        },
        hide:function(){
            var me = this;
            me.effectHide();
            me.fireEvent('afterHide');
            if(util.isIE6 && me.defConfig.isFixed){
                me._IE6Fixed.stop();
            }
        },
        getContainerDom: function(){
            var me = this;
            return me._containerDom || (me._containerDom = me.dom);
        },
        getCloseDom: function(){
            var me = this;
            return me._closeDom || (me._closeDom = me.dom.find('.pop-close'));
        },
        getTitleDom:function(){
            var me = this;
            return me._titleDom || (me._titleDom = me.dom.find('.pop-title'));
        },
        getContentDom:function(){
            var me = this;
            return me._ContentDom || (me._ContentDom = me.dom.find('.pop-bd'));
        },
        //显示一个简单的提示面板
        showTip:function(msg, callback){
            var me = this,htmlArr = [];
            htmlArr.push('<div class="ui-miniwindow-tip"><div class="inner">');
            htmlArr.push(msg);
            htmlArr.push('</div></div>');

            me.tipdom = $(htmlArr.join(''));
            me.tipdom.appendTo($('body')).show();
            me.tipdom.css({'marginTop':me.tipdom.height()/2*-1, 'marginLeft':me.tipdom.width()/2*-1});
        },
        hideTip:function(){
            var me = this;
            if(me.tipdom){
                me.tipdom.remove();
            }
        }
    };



    var Main = host.Class(pros, Event);
        Main.defConfig = defConfig;

    //可生成多个实例
    host[name] = Main;
    //也可以重复使用实例
    host[name].getInstance = function(){
        return instance || (instance = new Main(defConfig));
    };

})(UIscript, "MiniWindow", UIscript.Event, jQuery);




(function(host, name, Event,undefined){
    var defConfig = {
            cls:''
        },
    instance;

    var pros = {
        //初始化
        init: function(cfg){
           var me = this;
            me.win = new host.MiniWindow(cfg);
            me.mask = host.Mask.getInstance();
            //绑定隐藏完成事件
            me.reSet();
            me.win.addEvent('afterHide', function(){
                me.reSet();
            })
            //定时器缓存
            me.closeTime = 0;
        },
        doAction: function(data){
            var me = this,
                funName = 'rebulid' + data['type'],
                getHtml = 'getHtml' + data['type'],
                fn = function(){
                };

            if(me[funName] && $.isFunction(me[funName])){
                fn = me[funName];
            }
            data['tpl']  = typeof data['tpl'] == 'undefined' ? me[getHtml]() : '' + data['tpl'];
            //删除type数据
            //防止在渲染的时候进行递归调用
            delete data['type'];
            //调用子类方法
            fn.call(me, data);
        },
        formatHtml:function(tpl, order){
            var me = this,o = order,p,reg;
            for(p in o){
                if(o.hasOwnProperty(p)){
                    reg = RegExp('<#=' + p + '#>', 'g');
                    tpl = tpl.replace(reg, o[p]);
                }
            }
            return tpl;
        },
        //添加题目
        setTitle: function(html){
            var me = this, win = me.win;
            win.setTitle(html);
        },
        //添加内容
        setContent: function(html, delay){
            var me = this, win = me.win;
            win.setContent(html, delay);
        },
        //隐藏关闭按钮
        hideClose: function(){
            var me = this, win = me.win;
            win.getCloseDom().hide();
        },
        //隐藏标题栏
        hideTitle: function(){
            var me = this, win = me.win;
            win.getTitleDom().hide();
        },
        //弹窗外容器增加样式
        addCssName: function(cssName){
            var me =this,
                win = me.win;
            win.getContainerDom().addClass(cssName);
        },
        //弹窗外容器恢复初始样式
        restCssName: function(){
            var me =this,
                win = me.win,
                cssName = me.defConfig.cls;

            me.removeCssName();
            win.getContainerDom().addClass(cssName);
        },
        //清除弹窗外容器样式
        removeCssName: function(cssName){
            var me =this,
                win = me.win;
            if(cssName){
                win.getContainerDom().removeClass(cssName);
            }else{
                win.getContainerDom().removeClass();
            }
        },
        //弹窗显示 具体参数说明
        //弹窗类型(会根据弹窗类型自动获取模版) type
        //模版 tpl  数据 tplData
        //内容:content, 绑定函数: callback, 是否遮罩: mask
        //宽度:width, 长度:height, 自动关闭时间单位S:time
        //是否显示头部: hideTitle, 是否显示关闭按钮:hideClose
        //增加弹窗外容器样式名称(一次性) cssName
        //确认按钮 是否显示: confirmIsShow 名称: confirmText 事件: confirmFun
        //取消按钮 是否显示: cancelIsShow  名称: cancelText   事件: cancelFun
        //关闭按钮 是否显示: closeIsShow   名称: closeText    事件: closeFun
        //默认关闭按钮 normalCloseFun 右上角关闭按钮关闭时触发执行函数
        show: function(data){
            var me = this, win = me.win;
            if(typeof data == 'undefined'){
                win.show();
                return;
            }

            me.reSet();
            if(typeof data['data'] == 'undefined'){
                data['data'] = {};
            }
            data['data']['tplData'] = typeof data['data']['tplData'] == 'undefined' ? {} : data['data']['tplData'];

            if(!data){return}

            if(data['type']){
                me.doAction(data);
                return;
            }else{
                if(typeof data['tpl'] != 'undefined'){
                    data['content'] = me.formatHtml(data['tpl'], data['data']['tplData']);
                }
            }

            //取消自动关闭时间缓存
            if(me.closeTime){
                clearTimeout(me.closeTime);
                me.closeTime = null;
            }

            //加入题目 && 内容
            me.setTitle(data['title'] || '温馨提示');
            me.setContent(data['content'] || '');

            if(data['cssName']){
                me.addCssName(data['cssName']);
            }

            //按钮名称
            if(data['confirmText']){
                win.setConfirmName(data['confirmText']);
            }
            if(data['cancelText']){
                win.setCancelName(data['cancelText']);
            }
            if(data['closeText']){
                win.setCloseName(data['closeText']);
            }
            //按钮事件
            if(data['normalCloseFun']){
                win.doNormalClose = data['normalCloseFun'];
            }
            if(data['confirmFun']){
                win.doConfirm = data['confirmFun'];
            }
            if(data['cancelFun']){
                win.doCancel = data['cancelFun'];
            }
            if(data['closeFun']){
                win.doClose = data['closeFun'];
            }
            //按钮显示
            if(data['confirmIsShow']){
                win.showConfirmButton();
            }
            if(data['cancelIsShow']){
                win.showCancelButton();
            }
            if(data['closeIsShow']){
                win.showCloseButton();
            }
            //判断是否隐藏头部和关闭按钮
            if(data['hideTitle']){
                me.hideTitle();
            }
            if(data['hideClose']){
                me.hideClose();
            }
            //遮罩显示
            if(data['isShowMask']){
                me.mask.show();
            }

            win.show();

            //执行回调事件
            if(data['callback']){
                data['callback'].call(me);
            }

            //定时关闭
            if(data['time'] > 0){
                me.closeTime = setTimeout(function(){
                    me.hide();
                    clearTimeout(me.closeTime);
                    me.closeTime = null;
                }, data['time'] * 1000);
            }
        },
        getContainerDom : function(){
            var me = this;
            return me.win.getContainerDom();
        },
        //获取内容容器DOM
        getContentDom : function(){
            var me = this;
            return me.win.getContentDom();
        },
        //弹窗隐藏
        hide: function(){
            var me = this, win = me.win;
            win.hide();
            me.reSet();
        },
        //重置
        reSet: function(){
            var me = this, win = me.win;

            me.mask.hide();
            me.setTitle('提示');
            me.setContent('');
            //me.restCssName();
            win.hideConfirmButton();
            win.hideCancelButton();
            win.hideCloseButton();
            win.doConfirm = function(){};
            win.doCancel = function(){};
            win.doClose = function(){};
            win.doNormalClose = function(){};
            win.setConfirmName('确 认');
            win.setCancelName('取 消');
            win.setCloseName('关 闭');
        }
    }

    var Main = host.Class(pros, Event);
        Main.defConfig = defConfig;
        Main.getInstance = function(cfg){
            return instance || (instance = new Main(cfg));
        };
    host[name] = Main;

})(UIscript, "Message",  UIscript.Event);







//提示组件
(function(host, name, Event, $, undefined){
    var defConfig = {
        //外层容器class
        //四个箭头位置class，默认为向左箭头
        //j-ui-tip-t, j-ui-tip-r, j-ui-tip-b, j-ui-tip-l
        //j-ui-tip-info 为绿色提示性样式
        cls:'j-ui-tip-l',
        //提示元素定位相对目标
        target:'body',
        //显示的文本
        text:'',
        //显示效果
        effectShow:function(){
            this.dom.show();
        },
        effectHide:function(){
            this.dom.hide();
        }
    },
    cls = 'j-ui-tip',
    zIndex = 500,
    //公共实例
    instance;

    var pros = {
        init:function(cfg){
            var me = this;
            me.dom = $('<div class="'+ cls +' '+ cfg.cls +'" style="display:none;position:absolute;left:0;top:0;z-index:'+ (zIndex++) +';"><i class="sj sj-t"></i><i class="sj sj-r"></i><i class="sj sj-b"></i><i class="sj sj-l"></i><span class="ui-tip-text">'+ cfg.text +'</span></div>').appendTo($('body'));
            me.effectShow = cfg.effectShow;
            me.effectHide = cfg.effectHide;
        },
        getTextContainer:function(){
            var me = this;
            return me._textContainer || (me._textContainer = me.dom.find('.ui-tip-text'));
        },
        getDom:function(){
            return this.dom;
        },
        setText:function(text){
            var me = this;
            me.getTextContainer().html(text);
        },
        show:function(x, y, target){
            var me = this,targetPos = (target == undefined ? $(me.defConfig.target) : $(target)).offset();
            me.dom.css({'left':targetPos.left + x,'top':targetPos.top + y});
            me.effectShow();
        },
        hide:function(){
            this.effectHide();
        },
        remove:function(){
            this.getDom().remove();
        }

    };

    var Main = host.Class(pros, Event);
        Main.defConfig = defConfig;
    host[name] = Main;

    host[name].getInstance = function(){
        return instance || (instance = new host[name]({cls:'j-ui-tip-l j-ui-tip-info'}));
    };

})(UIscript, "Tip", UIscript.Event, jQuery);




















