// 投注赛事区
Mango.RankGames = (function() {
    var p = {};
    return {
        MAX: 99999,
        championBet: $('#championBet'), //champion选择的球队数
        championshipBet: $('#championshipBet'), //championship选择的组合数
        multipleChampion: $('#multipleChampion'), //champion倍数
        multipleChampionship: $('#multipleChampionship'), //championship倍数
        championBonce: $('#championBonce'), //champion投注金额
        championMaxBonce: $('#championMaxBonce'), //champion理论最高奖金
        championshipBonce: $('#championshipBonce'), //championship投注金额
        championshipMaxBonce: $('#championshipMaxBonce'), //championship理论最高奖金a
        championDetail: $('#championDetail'), //选择明细
        championshipDetail: $('#championshipDetail'), //选择明细
        championDetailWrap: $('#championDetailWrap'),
        championshipDetailWrap: $('#championshipDetailWrap'),
        championSelected: '.e_mainMod:eq(0) .e_options dl dd a.on',
        championshipSelected: '.e_mainMod:eq(1) .e_options dl dd a.on',
        emptyBetText: '<tr><td colspan="2"><div>请进行投注</div></td></tr>',
        init: function(options) {
            p = $.extend(p, null, options || {});
            this.events();
        },
        events: function() {
            var me = this;

            //切换tab操作
            $(document).on("click", ".contList li", function() {
                var index = $(this).index();
                $(this).addClass("on").siblings().removeClass("on");
                $(".e_mainMod").eq(index).show().siblings().hide();
            });

            this.bindWindowScrollEvent();

            this.multipleHandler(['#multipleChampion', '#championMinusTiple', '#championPlusTtiple'], this.handleChampionBonce.bind(this));
            this.multipleHandler(['#multipleChampionship', '#championshipMinusTiple', '#championshipPlusTtiple'], this.handleChampionshipBonce.bind(this));
            this.handleClickChampion();
            this.handleClickChampionship();
            //清空chamption
            $(document).on('click', '#clearChampionBtn', this.clearChampion.bind(this));
            //清空champtionship
            $(document).on('click', '#clearChampionshipBtn', this.clearChampionship.bind(this));
            //提交champion数据
            var mask = new UIscript.Mask(),
                message = new UIscript.MiniWindow();
            $(document).on('click', '#submitChampion', function() {
                if (+me.championBet.html() == 0) {
                    mask.show();
                    message.showTip("请先进行投注");
                    setTimeout(function() {
                        mask.hide();
                        message.hideTip();
                    }, 1000);
                    return false;
                }
                var gOrderData = {};
                $(me.championSelected).each(function(index, item) {
                    var data = $.parseJSON(item.getAttribute('data'));
                    if(!gOrderData[data.bn]){
                        gOrderData[data.bn] = [];
                    }
                    gOrderData[data.bn].push('chp_' + data.chp.chp[0]);
                });
                var bettingData = {
                    "max_gate": 1,
                    "gate": ['chp'],
                    "games": gOrderData,
                    "dan": [],
                    "bet_num": me.championBet.html(),
                    "multiple": me.multipleChampion.val(),
                    "amount": me.championBonce.html()
                };
                me.submitChampion(bettingData, function() {
                    me.multipleChampion.val(10);
                },'champion');
            });
            //提交championship数据
            $(document).on('click', '#submitChampionship', function() {
                if (+me.championshipBet.html() == 0) {
                    mask.show();
                    message.showTip("请先进行投注");
                    setTimeout(function() {
                        mask.hide();
                        message.hideTip();
                    }, 1000);
                    return false;
                }
                var gOrderData = {};
                $(me.championshipSelected).each(function(index, item) {
                    var data = $.parseJSON(item.getAttribute('data'));
                    if(!gOrderData[data.bn]){
                        gOrderData[data.bn] = [];
                    }
                    gOrderData[data.bn].push('fnl_' + data.fnl.fnl[0]);
                });
                var bettingData = {
                    "max_gate": 1,
                    "gate": ['fnl'],
                    "games": gOrderData,
                    "dan": [],
                    "bet_num": me.championshipBet.html(),
                    "multiple": me.multipleChampionship.val(),
                    "amount": me.championshipBonce.html()
                };
                me.submitChampion(bettingData, function() {
                    me.multipleChampionship.val(10);
                },'championship');
            });
        },
        //提交champion数据
        submitChampion: function(data, callback,type) {
            var msgWin = new UIscript.MiniWindow();
            var msgWin2 = new UIscript.Message();
            var me = this;

            $.ajax({
                url: p.billSubmitUrl,
                type: 'POST',
                dataType: 'json',
                data: data || {},
                beforeSend: function() {
                    msgWin.showTip("提交中...");
                },
                success: function(d) {
                    me.callbackSubmitSuccess(d, msgWin, msgWin2,type);
                    callback && callback();

                },
                complete: function(d) {
                    msgWin.hideTip();
                },
                error: function(d) {
                    me.callbackSubmitFailed(d, msgWin2)
                }
            });
        },
        callbackSubmitSuccess: function(d, loadingWin, tipWin,type) {
            var me = this;
            loadingWin.hideTip();
            var msgHtml = '<div class="bd text-center"><div class="pop-title"><i class="ico-error"></i><h4 class="pop-text">' + ((d && d.msg) ? d.msg : '') + '<br></h4></div></div>';
            if (+d.coding > 0) {
                msgHtml = '<div class="bd text-center"><div class="pop-title"><i class="ico-success"></i><h4 class="pop-text">' + d.msg + '<br></h4></div><p class="text-note" style="padding:5px 0;">您可以通过”投注记录“查询您的投注记录！</p></div>';
                //清空注单信息数据
                if(type === 'champion'){
                    me.clearChampion();
                }else{
                    me.clearChampionship();
                }

            }
            tipWin.show({
                isShowMask: true,
                confirmIsShow: true,
                confirmText: '关 闭',
                confirmFun: function() {
                    tipWin.hide();
                },
                content: msgHtml
            });
        },
        callbackSubmitFailed: function(d, tipWin) {
            tipWin.hide();
            var desc = '服务器信息错误,请在投注列表中查询';
            if (d.status == 401) desc = '您没有登录或没有投注权限';
            tipWin.show({
                isShowMask: true,
                confirmIsShow: true,
                confirmText: '关 闭',
                confirmFun: function() {
                    tipWin.hide();
                },
                content: '<div class="pop-waring"><i class="ico-waring"></i><h4 class="pop-text">' + desc + '</h4></div>'
            });
        },
        //清空champion区域
        clearChampion: function() {
            var selector = ".e_mainMod:eq(0) .e_options dl dd a";
            $(selector).removeClass('on');
            this.championDetail.html(this.emptyBetText);
            this.championBet.html('0');
            this.multipleChampion.val(10);
            this.championBonce.html('0.00');
            this.championMaxBonce.html('0.00');
            this.championDetailWrap.jScrollPane().data("jsp");
        },
        //清空champion区域
        clearChampionship: function() {
            var selector = ".e_mainMod:eq(1) .e_options dl dd a";
            $(selector).removeClass('on');
            this.championshipDetail.html(this.emptyBetText);
            this.championshipBet.html('0');
            this.multipleChampionship.val(10);
            this.championshipBonce.html('0.00');
            this.championshipMaxBonce.html('0.00');
            this.championshipDetailWrap.jScrollPane().data("jsp");
        },
        //点击champion表格
        handleClickChampion: function() {
            var me = this;
            var selector = ".e_mainMod:eq(0) .e_options dl dd a";

            $(document).on("click", selector, function() {
                if ($(this).hasClass("on") == true) {
                    $(this).removeClass("on");
                } else {
                    $(this).addClass("on");
                }
                me.championBet.html($(me.championSelected).size());
                me.handleChampionBonce.bind(me)();
                me.addSelected2championDetailArea.bind(me)();

            });
        },
        //添加选择项到明细区域 champion
        addSelected2championDetailArea: function() {
            var me = this;

            if ($(me.championSelected).size() === 0) {
                me.championDetail.html(me.emptyBetText);
            } else {
                me.championDetail.html('');
            }
            $(me.championSelected).each(function(index, item) {
                var odds = +$(item).find('span:eq(2)').html();
                var name = $(item).find('span:eq(0)').html();
                me.championDetail.append('<tr><th>' + name + '</th><td>' + odds + '</td></tr>');
            });
            me.championDetailWrap.jScrollPane().data("jsp");

        },
        //添加选择项到明细区域 championship
        addSelected2DetailArea: function() {
            var me = this;
            if ($(me.championshipSelected).size() === 0) {
                me.championshipDetail.html(me.emptyBetText);
            } else {
                me.championshipDetail.html('');
            }
            $(me.championshipSelected).each(function(index, item) {
                var odds = +$(item).find('span:eq(2)').html();
                var name = $(item).find('span:eq(0)').html();
                me.championshipDetail.append('<tr><th>' + name + '</th><td>' + odds + '</td></tr>');
            });
            me.championshipDetailWrap.jScrollPane().data("jsp");
        },
        //计算champion的最高理论奖金
        calculateChampionTheory: function() {
            var me = this;
            var max = 0;
            $(me.championSelected).each(function(index, item) {
                var odds = +$(item).find('span:eq(2)').html();
                if (odds > max) {
                    max = odds;
                }
            });
            var money = (max * 2 * (+me.multipleChampion.val())).toFixed(2);
            me.championMaxBonce.html(money);
        },
        //计算championship的最高理论奖金
        calculateChampionshipTheory: function() {
            var me = this;
            var max = 0;
            $(me.championshipSelected).each(function(index, item) {
                var odds = +$(item).find('span:eq(2)').html();
                if (odds > max) {
                    max = odds;
                }
            });
            var money = (max * 2 * (+me.multipleChampionship.val())).toFixed(2);
            me.championshipMaxBonce.html(money);
        },
        //点击championship表格
        handleClickChampionship: function() {
            var me = this;
            var selector = ".e_mainMod:eq(1) .e_options dl dd a";
            $(document).on("click", selector, function() {
                if ($(this).hasClass("on") == true) {
                    $(this).removeClass("on");
                } else {
                    $(this).addClass("on");
                }
                me.championshipBet.html($(me.championshipSelected).size());
                me.handleChampionshipBonce.bind(me)();
                me.addSelected2DetailArea.bind(me)();
            });
        },
        //处理champoin奖金
        handleChampionBonce: function() {
            var betNumber = this.championBet.html();
            var multiple = this.multipleChampion.val();
            var bonce = this.calculateBonce(+multiple, +betNumber);
            this.championBonce.html(bonce);
            this.calculateChampionTheory.bind(this)();
        },
        //处理champoinship奖金
        handleChampionshipBonce: function() {
            var betNumber = this.championshipBet.html();
            var multiple = this.multipleChampionship.val();
            var bonce = this.calculateBonce(+multiple, +betNumber);
            this.championshipBonce.html(bonce);
            this.calculateChampionshipTheory.bind(this)();
        },
        //计算奖金
        calculateBonce: function(multiple, betNumber) {
            return (2 * multiple * betNumber).toFixed(2);
        },
        /*增加栅格样式*/
        renderTableRowColor: function() {
            var num = $(".e_options dl dd").length;
            for (var i = 0; i < num; i++) {
                $(".e_options dl dd").eq(i * 4 - 1).addClass("gray");
                $(".e_options dl dd").eq(i * 4 - 2).addClass("gray");
            }
        },
        numberFilter: function(selector, callback) {
            var me = this;
            $(selector).on('keydown', function(event) {
                if (!(event.keyCode == 35) && !(event.keyCode == 36) && !(event.keyCode == 46) && !(event.keyCode == 8) && !(event.keyCode == 37) && !(event.keyCode == 39)) {
                    if (!((event.keyCode >= 48 && event.keyCode <= 57) || (event.keyCode >= 96 && event.keyCode <= 105))) {
                        return false;
                    }
                    if ($.trim($(this).val()).length > 6) {
                        return false;
                    }
                }

            }).on('keyup', function() {
                var v = parseInt($.trim(this.value));
                if (v > me.MAX) {
                    this.value = v = me.MAX;
                }
                this.value = Math.max(this.value, 1);
                callback && callback.constructor == Function ? callback() : null;
            });
        },
        /*倍数加减*/
        multipleHandler: function(selector, callback) {
            var me = this;
            this.numberFilter(selector[0], callback);
            //减号
            $(document).on("click", selector[1], function() {
                var input = $(this).siblings('input');
                if (+$.trim(input.get(0).value) <= 1) {
                    return 0;
                }
                input.val(+$.trim(input.get(0).value) - 1);

                callback && callback.constructor == Function ? callback() : null;
            });

            //加号
            $(document).on("click", selector[2], function() {
                var input = $(this).siblings('input');
                if (+$.trim(input.get(0).value) > me.MAX - 1) {
                    input.val(me.MAX);
                } else {
                    input.val(+$.trim(input.get(0).value) + 1);
                }

                callback && callback.constructor == Function ? callback() : null;
            });
        },
        //固定选区区域
        bindWindowScrollEvent: function() {
            var fixDetaild = $('.e_contRight');
            var fixColumn = $('.e_options dt');
            var fixGames = $('.contList');
            $(window).scroll(function(event) {
                //var scrollTop = 140;
                setTimeout(function() {
                    if ($(window).scrollTop() > 200) {
                        if ($(window).height() > 460) {
                            var marginLeft = $('.mid').width() / 2 - fixDetaild.width() - 10;
                            fixDetaild.css({
                                "position": 'fixed',
                                "top": '0px',
                                "left": "50%",
                                "margin-left": marginLeft + "px"
                            });
                        }else{
                            var marginLeft = $('.mid').width() / 2 - fixDetaild.width() - 10;
                            fixDetaild.css({
                                position: 'absolute',
                                top: 'auto',
                                "left": "50%",
                                "margin-left": marginLeft + "px"
                            });
                        }
                        fixColumn.css({
                            position: 'fixed',
                            top: '0'
                        });
                        var ml = $('.mid').width() / 2 - fixGames.width();

                    } else {
                        fixColumn.css({
                            position: 'static',
                            top: 'auto'
                        });
                        var marginLeft = $('.mid').width() / 2 - fixDetaild.width() - 10;
                        fixDetaild.css({
                            position: 'absolute',
                            top: 'auto',
                            "left": "50%",
                            "margin-left": marginLeft + "px"
                        });

                    }
                }, 5);
            });
        }
    };
})();