/*ajax拉数据的util*/
if (!window.Mango) {
    Mango = {};
}
Mango.betRecord = (function() {
    return (function() {
        return {
            dataUrl: "",
            loadFirst: true,
            perShowNum:15,
            ticketDetailCon:$("#ticketDetailCon"),
            events:function(){
                var me = this;
                $('#J-date-start').focus(function() {
                    (new UIscript.DatePicker({
                        input: '#J-date-start',
                        isShowTime: true,
                        startYear: 2013
                    })).show();
                });

                $(document).on("click",".game-detail",function(){
                    me.ticketDetailCon.toggle();
                });
            },
            init: function(p) {
                var me = this;
                me.dataUrl = p.url;
                me.gameBnMaps = p.gameBnMaps;
                me.gateTypeDisplay = p.gateTypeDisplay;
                //注册分页
                me.pullData({
                    page: 1
                }, function(res) {
                    laypage({
                        cont: 'pagination',
                        pages: Math.ceil(res.data.total / me.perShowNum),
                        total:res.data.total,
                        perShowNum:me.perShowNum,
                        curr: res.data.cur_page,
                        jump: function(obj) {
                            if (me.loadFirst) {
                                me.loadFirst = false;
                                return;
                            }
                            me.pullData({
                                page: obj.curr
                            });
                        }
                    });
                });

                this.events();
            },
            setUrl:function(url){
                this.dataUrl = url;
            },
            pullData: function(param, callback) {
                var me = this;
                Mango.UtilData.getData(me.dataUrl, function(respone) {
                    var data = me.compileData(respone.data.bills);
                    me.render({data:data,gateTypeDisplay:me.gateTypeDisplay}, "#ticketDetailWrap");
                    callback && callback(respone);
                }, param || {}, 'GET');
            },
            compileData: function(data) {
                for (var i = 0, len = data.length; i < len; i++) {
                    var bill = data[i],
                        betOddDisplay = $.parseJSON(bill.bet_odds_display),
                        billOddDisplay = [];
                    for (var n in betOddDisplay) {
                        for (var m in betOddDisplay[n]) {
                            billOddDisplay.push(this.gameBnMaps[n] + '-' + betOddDisplay[n][m]);
                        }
                    }
                    data[i].bet_detail = billOddDisplay.join(' X ');
                    // data[i].detailObj = $.parseJSON(data[i].bet_odds_display);
                    // data[i].bet_detail = [];
                    // for (var o in data[i].detailObj) {
                    //     for (var oo in data[i].detailObj[o]) {
                    //         data[i].bet_detail.push(gameBnMaps[] + "[" + data[i].detailObj[o][oo] + "]");
                    //     }
                    // }

                }
                return data;
            },
            compile2week:function(str){
                var week = new Date(str.replace('-','/')).getDay() + '';
                return this.week[week];
            },
            week:{
                "0":"周日",
                "1":"周一",
                "2":"周二",
                "3":"周三",
                "4":"周四",
                "5":"周五",
                "6":"周六"
            },
            render: function(data, domSelector) {
                var tmpl = $('#ticketDetailTmpl').html(),
                    doTtmpl = doT.template(tmpl);
                var content = doTtmpl(data);
                $(domSelector).html(content);
            }
        };
    })();

})();