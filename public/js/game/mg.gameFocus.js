// 投注赛事区
Mango.Game = (function() {
    var p = {};
    var gBetsJson = {}, // 选择的赛事和赔率单元树形数据
        // gSingleGateAvailable = 1, // 单关是否可选
        gMaxGate = 8, // 最大允许串关数
        gGamesArr = [], // 选择的赛事编号数组
        gOrderData = [], // 订单数据
        gBetsNum = 0, // 注单数
        gGatesJson = {}, // 过关方式数据对象
        gGatesArr = [], // 过关方式
        gMaxDanNum = 0, // 最大可选胆码赛事数
        gDanArr = [], // 胆码信息
        leftMenuStatus = false, //左侧菜单是否处于运动状态，默认为false
        fixDom = $('.focusleft,.focusright'),
        initCountDownFlag = false, // 初始化倒计时事件标记
        intervalId = null;

    var initConfig = function() {
        p.gamesListBox = $(p.gamesContainer).find('.gamesList');
        p.gamesMenuBox = $(p.gamesContainer).find('.gamesMenu')

    };

    var initCountDown = function() {
        $('.countDown').each(function(index, item) {
            var time = $.trim(item.getAttribute('data-time'));
            var stmp = new Date(Date.parse(time.replace(/-/g, "/"))).getTime();
            $(item).countdown({
                timestamp: stmp,
                callback: function(days, hours, minutes, seconds) {

                }
            });
        });
    }

    var initGamesMenu = function() {
        p.gamesMenuBox.on('click', function(e) {
            if (leftMenuStatus) return;
            var targetDom = e.target || e.srcElement,
                acType = $(targetDom).attr('action_type');
            if (!acType) { // 允许点击事件往上冒泡一级
                targetDom = $(targetDom).parent();
                acType = $(targetDom).attr('action_type');
            }
            if (!acType) e.stopPropagation();
            switch (acType) {
                case 'gameSwitcher':
                    fireGameSwitchEvent(targetDom);
                    break;
            }
        });
    };
    var bindGamesBoxEvent = function() {
        p.gamesListBox.on('click keyup', function(e) {
            var targetDom = e.target || e.srcElement;
            var acType = $(targetDom).attr('action_type');
            var parentDom = targetDom;
            if (!acType) { // 允许点击事件往上冒泡一级
                parentDom = $(targetDom).parents('[action_type]');
                acType = parentDom.attr('action_type');
            }
            if (!acType) e.stopPropagation();
            switch (acType) {
                case 'tabSwitcher':
                    fireTabSwitchEvent(targetDom);
                    break;
                case 'chooseOdd':
                    fireOddChooseEvent(parentDom);
                    break;
                case 'betting':
                    compileBettingData($(targetDom).parents('.bettingBox'));
                    break;
                case 'multipleTuner':
                    fireMultipleTuneEvent(targetDom, parentDom || targetDom);
                    break;
            }
        });
    };
    var fireMultipleTuneEvent = function(targetDom, parentDom) {
        var multipleInput = $(parentDom).find('input[name=multiple]'),
            multiple = +$(multipleInput).val();
        if ($(targetDom).hasClass('minusMultiple')) {
            $(multipleInput).val(multiple > 1 ? multiple - 1 : 1);
        }
        if ($(targetDom).hasClass('plusMultiple')) {
            $(multipleInput).val(multiple < 99999999 ? multiple + 1 : 99999999);
        }

        var gameBoxDom = $(targetDom).parents('.wayBox'),
            index = gameBoxDom.attr('index'),
            bnWayArr = index.split('_'),
            bn = bnWayArr[0],
            way = bnWayArr[1];
        fireOrderDataRenderEvent(bn, gameBoxDom);gi
    };
    var fireTabSwitchEvent = function(targetDom) {
        $(targetDom).addClass('on').siblings('li').removeClass('on');
        var index = $(targetDom).index();
        $(targetDom).parent().siblings('.focusbox').find('.focuschild').hide().eq(index).show();
    };
    var fireGameSwitchEvent = function(targetDom) {
        var index = $(targetDom).index();
        var gameBox = p.gamesListBox.find('.gameBox').eq(index);
        leftMenuStatus = true;
        $('html,body').animate({
            scrollTop: $(gameBox).offset().top
        }, 500, function() {
            leftMenuStatus = false;
            scrollnav();
        });
        $(targetDom).addClass('on').siblings().removeClass('on');

    };
    var fireOddChooseEvent = function(targetDom) {
        var styleDom = $(targetDom).parent('li');
        var isSelect = false;
        if (styleDom.hasClass('on')) {
            isSelect = false;
            styleDom.removeClass('on');
        } else {
            isSelect = true;
            styleDom.addClass('on');
        }

        var gameBoxDom = $(targetDom).parents('.wayBox'),
            index = gameBoxDom.attr('index'),
            bnWayArr = index.split('_'),
            bn = bnWayArr[0],
            way = bnWayArr[1];
        fireOrderDataRenderEvent(bn, gameBoxDom);
    };
    // {"20160417YX009":{"h":"1.34"},"20160417YX010":{"h":"2.10"},"20160417YX013":{"ch":"1.45"},"20160417YX014":{"ch":"4.90"}}
    // {"20160417YX009":["h_1.34"],"20160417YX010":["h_2.10"],"20160417YX013":["ch_1.45"],"20160417YX014":["ch_4.90"]}
    var fireOrderDataRenderEvent = function(bn, gameBoxDom) {
        var bettingBox = gameBoxDom.find('.bettingBox'),
            multiple = $(bettingBox).find('input[name=multiple]').val(),
            compiledData = reCompileBetGamesData(bn, multiple, gameBoxDom),
            orderData = compiledData['gamesJson'],
            referPrizeArr = compiledData['referPrizeArr'],
            betsNum = calculateBetsNum(orderData, [bn], {
                '1x1': [1]
            }, []),
            amountText = +betsNum * multiple * 2,
            referPrize = 0,
            referProfit = 0;
        if (referPrizeArr.length == 1) {
            referPrize = (+referPrizeArr[0]).toFixed(2);
            referProfit = (+referPrizeArr[0] - amountText).toFixed(2);
        } else if (referPrizeArr.length == 0) {
            referPrize = 0;
            referProfit = 0;
        } else {
            var referPrizeMin = referPrizeArr.min(),
                referPrizeMax = referPrizeArr.max();
            referPrize = referPrizeMin + '~' + referPrizeMax;
            referProfit = (referPrizeMin - amountText).toFixed(2) + '~' + (referPrizeMax - amountText).toFixed(2);
        }
        $(bettingBox).attr('order_data', JSON.stringify(orderData))
            .find('input[name=bet_num]').val(betsNum).end()
            .find('.amountText').text(amountText).end()
            .find('.referPrizeText').text(referPrize).end()
            .find('.profitText').text(referProfit);
    };

    var reCompileBetGamesData = function(bn, multiple, gameBoxDom) {
        var gamesJson = {};
        gamesJson[bn] = [];
        var referPrizeArr = [];
        var  uniqueArrG=[];
        var uniqueArrR=[];
        $(gameBoxDom).find('li.on').each(function(e) {
            var oddData = $(this).find('div[action_type=chooseOdd]').attr('data');
            oddData = JSON.parse(oddData);
            if ($.isArray(oddData.name)) {
                for (var i = oddData.name.length - 1; i >= 0; i--) {
                    gamesJson[bn].push(oddData.name[i] + '_' + oddData.odd[i]);
                    referPrizeArr.push((oddData.odd[i] * multiple * 2).toFixed(2));
                }
            } else {
                gamesJson[bn].push(oddData.name + '_' + oddData.odd[0]);
                referPrizeArr.push((oddData.odd * multiple * 2).toFixed(2));
            }
        });

            
               $.each(gamesJson[bn], function(i, value){
                   if($.inArray(value, uniqueArrG) === -1){
                       uniqueArrG.push(value);
                   } 
               });
               
               $.each(referPrizeArr, function(i, value){
                   if($.inArray(value, uniqueArrR) === -1){
                       uniqueArrR.push(value);
                   } 
               });
               gamesJson[bn]=uniqueArrG ;
               referPrizeArr=uniqueArrR;
        return {
            'gamesJson': gamesJson,
            'referPrizeArr': referPrizeArr
        };
    };

    var submitBettingData = function(data, bettingBox) {
        var msgWin = new UIscript.MiniWindow();
        var msgWin2 = new UIscript.Message();
        // 提交订单动作
        $.ajax({
            url: p.billSubmitUrl,
            type: 'POST',
            dataType: 'json',
            data: data || {},
            beforeSend: function() {
                msgWin.showTip("提交中...");
            },
            success: function(d) {
                callbackSubmitSuccess(d, msgWin, msgWin2, bettingBox);
            },
            complete: function(d) {
                msgWin.hideTip();
            },
            error: function(d) {
                callbackSubmitFailed(d, msgWin2, bettingBox)
            }
        });
    };
    var callbackSubmitSuccess = function(d, loadingWin, tipWin, bettingBox) {
        loadingWin.hideTip();
        var msgHtml = '<div class="bd text-center"><div class="pop-title"><i class="ico-error"></i><h4 class="pop-text">' + ((d && d.msg) ? d.msg : '') + '<br></h4></div></div>';
        if (+d.coding > 0) {
            msgHtml = '<div class="bd text-center"><div class="pop-title"><i class="ico-success"></i><h4 class="pop-text">' + d.msg + '<br></h4></div><p class="text-note" style="padding:5px 0;">您可以通过”投注记录“查询您的投注记录！</p></div>';
            //清空注单信息数据
            fireClearBettingEvent(bettingBox);
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
    };
    var callbackSubmitFailed = function(d, tipWin) {
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
    };
    var compileBettingData = function(bettingBox) {
        var mask = new UIscript.Mask(),
            message = new UIscript.MiniWindow(),
            sNum = 1,
            tipText = '',
            returnFlag = true;
        var multiple = bettingBox.find('input[name=multiple]').val(),
            betsNum = bettingBox.find('input[name=bet_num]').val(),
            orderData = JSON.parse(bettingBox.attr('order_data')),
            wayType = bettingBox.attr('way_type'),
            amount = betsNum * multiple * 2;
        if (!orderData || orderData.length < 1) {
            tipText = '缺少投注数据';
            returnFlag = false;
        }
        if (!returnFlag) {
            mask.show();
            message.showTip(tipText);
            setTimeout(function() {
                mask.hide();
                message.hideTip();
            }, 1000);
            return false;
        }
        var bettingData = {
            "max_gate": Mango.Config.gameConfig.max_gates[wayType],
            "gate": ['1x1'],
            "games": orderData,
            "dan": [],
            "bet_num": betsNum,
            "multiple": multiple,
            "amount": amount
        };
        submitBettingData(bettingData, bettingBox);
    };
    var fireClearBettingEvent = function(bettingBox) {
        $(bettingBox).attr('order_data', '')
            .find('input[name=bet_num]').val(0).end()
            .find('.amountText').text(0).end()
            .find('.referPrizeText').text(0).end()
            .find('.profitText').text(0);
        $(p.gamesContainer).find('.wayBox').find('li.on').removeClass('on');
    };

    var scrollnav = function(switchTab) {
        var st = Math.max(document.body.scrollTop || document.documentElement.scrollTop);
        //左侧悬浮条选中效果
        if (switchTab) {
            $(".fevent").each(function(i) {
                var offset = $(this).offset().top - 50;
                if (st <= offset) {
                    return false;
                }
                $(".Flefteventlist").eq(i - $(".fevent").length).addClass("on").siblings().removeClass("on");
            });
        }

        if (st > 120) {
            if ($(window).height() > 365) {
                fixDom.animate({
                    top: 0
                }, 5);
            }

        } else {
            fixDom.animate({
                top: '455px'
            }, 5);
        }

    };

    var numberFilter = function(selector, callback) {
            var me = this;
            $(document).on('keydown',selector, function(event) {
                if (!(event.keyCode == 35) && !(event.keyCode == 36) && !(event.keyCode == 46) && !(event.keyCode == 8) && !(event.keyCode == 37) && !(event.keyCode == 39)) {
                    if (!((event.keyCode >= 48 && event.keyCode <= 57) || (event.keyCode >= 96 && event.keyCode <= 105))) {
                        return false;
                    }
                    if ($.trim($(this).val()).length > 6) {
                        return false;
                    }
                }

            }).on('keyup',selector,  function() {
                this.value = Math.max(this.value, 1);
                callback && callback.constructor == Function ? callback() : null;
            });
        }


    return {
        init: function(options) {
            p = $.extend(p, null, options || {});
            initConfig();
            initGamesMenu();
            bindGamesBoxEvent();
            numberFilter('.Fmultiple input');

            $(window).scroll(function() {
                if (!leftMenuStatus) {
                    scrollnav(true);
                }
            }).trigger('scroll');

        },
        initCountDown: initCountDown
    };
})();