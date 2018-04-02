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
        gDanArr = [],// 胆码信息
        gameSiatisticsMultiple = null, //倍数默认值
        mixSingle = false , // 混合单关boolean
        MIXDISABLED = 'mixdisabled',
        MAXLIMIT = 20000,//最大注单数限制
        availableDan = true;//dan ma是否可用


    var bindBettingOddEvent = function() {
        $(p.gamesListBox).on('click', function(e) {
            var targetDom = e.target || e.srcElement;
            var acType = $(targetDom).attr('action_type');
            if (!acType) { // 允许点击事件往上冒泡一级
                targetDom = $(targetDom).parent();
                acType = $(targetDom).attr('action_type');
            }
            if (!acType) e.stopPropagation();
            switch (acType) {
                case 'gamesShowSwitcher':
                    fireGamesShowSwitcher(targetDom);
                    break;
                case 'moreOddsSwitcher':
                    fireMoreOddsSwitcher(targetDom);
                    break;
                case 'moreOddsSwitcherBig':
                    fireMoreOddsSwitcherBig(targetDom);
                    break;
                case 'chooseOdd':
                    fireOddChooseEvent(targetDom);
                    break;
            }
        });
    };

    var fireGamesShowSwitcher = function(targetDom) {
        var targetDom = $(targetDom),
            betDate = targetDom.attr('bet_date'),
            dlDom = p.gamesListBox.find('dl[gamedate=' + betDate + ']');
        if (dlDom.hasClass('closeData')) {
            dlDom.removeClass('closeData');
            targetDom.find('font').text('隐藏');
        } else {
            dlDom.addClass('closeData');
            targetDom.find('font').text('显示');
        }
    };

    var bindSelectedOddEvent = function() {
        $(p.selectedGamesBox).on('click', function(e) {
            var targetDom = e.target || e.srcElement;
            var acType = $(targetDom).attr('action_type');
            if (!acType) { // 允许点击事件往上冒泡一级
                targetDom = $(targetDom).parent();
                acType = $(targetDom).attr('action_type');
            }
            if (!acType) e.stopPropagation();
            switch (acType) {
                case 'cancelOdd':
                    fireOddCancelEvent(targetDom);
                    break;
                case 'cancelGame':
                    fireGameCancelEvent(targetDom);
                    break;
                case 'danSettingEvent':
                    fireDanSettingEvent(targetDom);
                    break;
            }
        });
    };

    var fireDanSettingEvent = function(targetDom) {
        var gameBn = $(targetDom).parent().parent().attr('game_bn');
        if ($(targetDom).hasClass('icoDanDis')) return false;
        if ($(targetDom).hasClass('icoDanSele')) {
            $(targetDom).removeClass('icoDanSele');
            var index = $.inArray(gameBn, gDanArr);
            if (index > -1) {
                gDanArr.splice(index, 1);
            }
        } else {
            $(targetDom).addClass('icoDanSele');
            gDanArr.push(gameBn);
        }
        reRenderDanDom();
        fireConfirmBettingDataEvent();
    };
    /**
     * [fireMoreOddsSwitcher 展开更多玩法]
     * @param  [Dom] targetDom [点击事件dom对象]
     */
    var fireMoreOddsSwitcher = function(targetDom) {

        var targetDom = $(targetDom),
            gameCode = targetDom.attr('game'),
            relatedDom = $(p.gamesListBox).find('dd.analyMore[game=' + gameCode + ']');
        if (relatedDom.hasClass('hide')) {
            targetDom.addClass('closeBtn').html('隐藏<i class="arrow"></i>');
            relatedDom.removeClass('hide');
        } else {
            if (relatedDom.find('td[action_type=chooseOdd]').hasClass('active')) {
                targetDom.addClass('openBtnSele');
            } else {
                targetDom.removeClass('openBtnSele');
            }
            targetDom.removeClass('closeBtn').html('展开<i class="arrow"></i>');
            relatedDom.addClass('hide');
        }
    };
    /**
     * [fireMoreOddsSwitcherBig 展开更多玩法]
     * @param  [Dom] targetDom [点击事件dom对象]
     */
    var fireMoreOddsSwitcherBig = function(targetDom) {
        var targetDom = $(targetDom),
            gameCode = targetDom.attr('game'),
            relatedDom = $(p.gamesListBox).find('dd.analyMore[game=' + gameCode + ']');
        if (relatedDom.hasClass('hide')) {
            targetDom.parents('span.moreOptBtnBox').addClass('openBtn').end().find('.imitateSelect').html('隐藏比赛投注区');
            relatedDom.removeClass('hide');
        } else {
            var selectedOddDom = p.gamesListBox.find('dd[game=' + gameCode + ']').find('td[action_type=chooseOdd]').hasClass('active');
            selectedOddDom ? targetDom.addClass('active') : targetDom.removeClass('active');
            targetDom.parents('span.moreOptBtnBox').removeClass('openBtn').end().find('.imitateSelect').html('展开比赛投注区');
            relatedDom.addClass('hide');
        }
    };

    /**
     * [fireOddChooseEvent 触发赔率单元选择事件]
     * @param  [Dom] targetDom [点击事件dom对象]
     */
    var fireOddChooseEvent = function(targetDom) {
        var parentDdDom = $(targetDom).parents('dd'),
            gameBn = $(parentDdDom).attr('game'),
            oddIndex = $(targetDom).attr('index'),
            oddName = oddIndex.split('_')[1],
            selected = $(targetDom).hasClass('active');
        if (selected) {
            var selectedOddDom = $(p.selectedGamesBox).find('a[action_type=cancelOdd][index=' + oddIndex + ']');
            removeOddFromBasket(targetDom, selectedOddDom, gameBn, oddName);
        } else {
            addGameToBasket(parentDdDom, targetDom);
        }
    };
    /**
     * [addGameToBasket 添加赛事到选号区]
     * @param [Dom] gameDom [赛事dom节点]
     * @param [Dom] oddDom  [赔率单元dom节点]
     */
    var addGameToBasket = function(gameDom, oddDom) {

        var gameBn = $(gameDom).attr('game'),
            gameIndexNum = +$(gameDom).attr('game_index'),
            gameData = $.parseJSON($(gameDom).attr('data')),
            srollDom = $('.scrollMoni'),
            targetDom = null;

        gameData.gameIndexNum = gameIndexNum;

        //初始化选号区 滚动条
        var pan = srollDom.jScrollPane().data("jsp");


        if (!gBetsJson[gameBn]) {
            if (p.selectedGamesBox.html() != '') { // 确定插入的位置
                p.selectedGamesBox.find('tr[game_index]').each(function(i) {
                    if ($(this).attr('game_index') > gameIndexNum) {
                        targetDom = this;
                        return false;
                    }
                });
            }
            gBetsJson[gameBn] = {};
            Mango.Render.render(p.selectedGamesBox, {data:gameData,available:availableDan}, Mango.Config.scriptTmpl['selectedGames'], false, targetDom);
            pan.reinitialise();
            pan.scrollToY(parseInt(60));

        }
        if (!$.isEmptyObject(gBetsJson)) {
            $(p.selectedGamesContainer).removeClass('unselect').addClass('select');
        }

        var indexNum = p.selectedGamesBox.find('tr.gameOption[game_bn=' + gameBn + ']').attr('game_index');
        pan.scrollToY(parseInt(indexNum * 60));
        addOddToBasket(oddDom, gameBn);
    };
    /**
     * [removeOddFromBasket 删除已选赛事赔率单元]
     * @param  [Dom] oddDom         [选号区赔率单元dom节点]
     * @param  [Dom] selectedOddDom [购彩篮赔率单元dom节点]
     * @param  [String] gameBn      [赛事编码]
     * @param  [String] oddName     [赔率单元编码]
     */
    var removeOddFromBasket = function(oddDom, selectedOddDom, gameBn, oddName) {
        var oddIndex = gameBn + '_' + oddName,
            srollDom = $('.scrollMoni'),
            pan = srollDom.jScrollPane().data("jsp");
        var selectedOddDomParent = $(selectedOddDom).parent().parent();
        $(oddDom).removeClass('active');

        if ($(p.gamesListBox).find('dd.analyMore[game=' + gameBn + ']').find('td[action_type=chooseOdd]').hasClass('active')) {
            $(p.gamesListBox).find('[action_type="moreOddsSwitcher"][game="' + gameBn + '"]').addClass('openBtnSele');
        } else {
            $(p.gamesListBox).find('[action_type="moreOddsSwitcher"][game="' + gameBn + '"]').removeClass('openBtnSele');
        }

        $(selectedOddDom).unbind('click').remove();
        delete gBetsJson[gameBn][oddName];
        if ($.isEmptyObject(gBetsJson[gameBn])) {
            delete gBetsJson[gameBn];

            $(selectedOddDomParent).siblings('tr.gameTitle[game_bn=' + gameBn + ']').unbind('click').remove();
            $(selectedOddDomParent).unbind('click').remove();
            pan.reinitialise();

            fireDanCancelEvent(gameBn);

        }
        fireGatesRenderEvent();
    };

    /**
     * [addOddToBasket 添加赔率单元到选号区]
     * @param {[Dom]} oddDom  [赔率单元dom节点]
     * @param {[String]} gameBn   [赛事唯一编码]
     */
    var addOddToBasket = function(oddDom, gameBn) {
        var oddData = $.parseJSON($(oddDom).attr('data')),
            oddIndex = $(oddDom).attr('index'),
            oddName = oddData.name,
            single = +$(oddDom).attr('single');
        oddData.index = oddIndex;
        oddData.single = single;
        var activeDom = $(p.gamesListBox).find('[action_type=chooseOdd][index=' + oddIndex + ']');
        if (!gBetsJson[gameBn][oddName]) {
            gBetsJson[gameBn][oddName] = oddData.odd;
            // var selectedGameTitleDom = p.selectedGamesBox.find('.gameTitle[game_bn=' + gameBn + ']');
            // var isGameSingle = +selectedGameTitleDom.attr('single');
            var gameOddsContainer = p.selectedGamesBox.find('tr.gameOption[game_bn=' + gameBn + '] > td.betList');
            Mango.Render.render(gameOddsContainer, oddData, Mango.Config.scriptTmpl['selectedOdds']);
            var gameOddsOrderDom = gameOddsContainer.find('a[index="' + oddData.index + '"]');

            var emDom = $(gameOddsOrderDom).clone().css({
                position: 'absolute',
                display: 'block',
                zIndex: 100,
                left: $(oddDom).offset().left,
                top: $(oddDom).offset().top,
                opacity: 1
            }).appendTo('body');
            gameOddsOrderDom.hide();
            var animateData = {
                'left': gameOddsContainer.offset().left,
                'top': gameOddsContainer.offset().top,
                'opacity': 0
            };
            $(emDom).animate(animateData, function() {
                gameOddsOrderDom.show();
                $(emDom).remove();
            });

            activeDom.addClass('active');
            // if (!isGameSingle && isGameSingle != single) {
            //     selectedGameTitleDom.attr('single', single);
            // }
            fireGatesRenderEvent();
        }
    };
    /**
     * [fireGameCancelEvent 批量删除已选赛事的赔率单元]
     * @param  [Dom] targetDom [点击事件dom节点]
     */
    var fireGameCancelEvent = function(targetDom) {
        var gameBn = $(targetDom).attr('index');
        fireDanCancelEvent(gameBn);
    };

    /**
     * [fireDanCancelEvent 删除已选赛事的赔率单元]
     * @param  [gameBn] gameBn   赛事Number
     */
    var fireDanCancelEvent = function(gameBn) {
        var danIndex = $.inArray(gameBn, gDanArr);
        if (danIndex > -1) gDanArr.splice(danIndex, 1);
        p.selectedGamesBox.find('.gameOption[game_bn=' + gameBn + '] > .betList').find('a').each(function(e) {
            fireOddCancelEvent(this);
        });
        fireGatesRenderEvent();
    }

    /**
     * [fireOddCancelEvent 删除已选赛事赔率单元]
     * @param  [Dom] targetDom [点击事件的dom节点]
     */
    var fireOddCancelEvent = function(targetDom) {
        var oddIndex = $(targetDom).attr('index'),
            oddIndexArr = oddIndex.split('_'),
            gameBn = oddIndexArr[0],
            oddName = oddIndexArr[1],
            oddDom = $(p.gamesListBox).find('[action_type=chooseOdd][index=' + oddIndex + ']');
        removeOddFromBasket(oddDom, targetDom, gameBn, oddName);
    };

    var bindGatesSelectEvent = function() {
        $(p.gatesContainer).on('click', function(e) {
            var targetDom = e.target || e.srcElement;
            var acType = $(targetDom).attr('action_type');
            if (!acType) { // 允许点击事件往上冒泡一级
                targetDom = $(targetDom).parent();
                acType = $(targetDom).attr('action_type');
            }
            if (!acType) e.stopPropagation();
            switch (acType) {
                case 'gateEvent':
                    fireGateSelectEvent(targetDom);
                    break;
                case 'gateSwitcher':
                    fireGateSwitchEvent(targetDom);
                    break;
            }
            gGatesJson = getGateSelected();
            gGatesArr = Object.keys(gGatesJson);
            reRenderDanDom();
            if (gGatesArr.length > 0) {
                p.confirmContainer.removeClass('unselect');
            } else {
                p.confirmContainer.addClass('unselect');
            }
            fireConfirmBettingDataEvent();
        });
    };
    var fireConfirmBettingDataEvent = function() {
        gGatesJson = getGateSelected();
        gGamesArr = Object.keys(gBetsJson);
        gOrderData = reCompileBetGamesData();
        gBetsNum = calculateBetsNum(gOrderData, gGamesArr, gGatesJson, gDanArr);
        remind();
        p.confirmContainer
            .find('.gameNum').text(Object.keys(gBetsJson).length).end()
            .find('.selectedBets').text(gBetsNum).end();

        compileBetsNumText();
        filterGameDatas();


    };

    var remind = function(block) {
        if (gBetsNum > MAXLIMIT) {
            var message = new UIscript.Message();
            message.show({
                isShowMask: true,
                confirmIsShow: true,
                confirmText: '关 闭',
                confirmFun: function() {
                    message.hide();
                },
                content: '投注单数请不要大于20000'
            });
            if (block) {
                return true;
            }
        }
    }

    var compileBetsNumText = function(v) {
        var multiple = v || p.confirmContainer.find('.select-game-statics-multiple').find('.choose-input').val(),
            amount = gBetsNum * multiple * 2;
        //     maxbonus = calculateBetsMax(gOrderData, gGamesArr, gGatesJson, gDanArr) || '0';
        p.confirmContainer.find('.amount').text(amount);
        // p.confirmContainer.find('#maxbonus').text((multiple * 2*maxbonus).toFixed(2));

    };


    var reCompileBetGamesData = function() {
        var gamesJson = {};
        for (var m in gBetsJson) {
            gamesJson[m] = [];
            for (var n in gBetsJson[m]) {
                gamesJson[m].push(n + '_' + gBetsJson[m][n]);
            }
        }
        return gamesJson;
    };
    /**
     * [getGateSelected 获取过关方式数组]
     * @return [Array]           [过关方式数组]
     */
    var getGateSelected = function() {
        var gatesJson = {};
        p.gatesContainer.find('.guoguanList.active').find('li.active').each(function() {
            var gate = $(this).attr('gate'),
                combination = $(this).attr('combination'),
                combinations = combination ? combination.split(',') : null;
            gatesJson[gate] = combinations;
            gMaxDanNum = Math.max(gMaxDanNum, combinations.min()) - 1;
            // gatesArr.push(gate);
        });
        return gatesJson;
    };
    var reRenderDanDom = function() {
        var maxDan = p.selectedGamesBox.find('tr.gameTitle[game_bn]').length - 1;
        p.gatesContainer.find('.jtip[action_type=gateEvent]').each(function(e) {
            var gate = $(this).attr('gate'),
                gateMaxDan = $(this).attr('max_dan');
            if (gateMaxDan < gDanArr.length) {
                $(this).addClass('disabled').removeClass('active').find('i').addClass('icoFx_disable').removeClass('active');
                var gateIndex = $.inArray(gate, gGatesArr);
                if (gateIndex > -1) gGatesArr.splice(gateIndex, 1);
            } else {
                $(this).removeClass('disabled').find('i').removeClass('icoFx_disable');
            }
            if ($(this).hasClass('active')) {
                maxDan = Math.min(maxDan, gateMaxDan);
            }
        });
        if (gDanArr.length == maxDan) {
            p.selectedGamesBox.find('.icoDan').each(function() {
                if (!$(this).hasClass('icoDanSele')) {
                    $(this).addClass('icoDanDis');
                }
            });
            availableDan = false;
        } else {
            p.selectedGamesBox.find('.icoDan').each(function() {
                if (!$(this).hasClass('icoDanSele')) {
                    $(this).removeClass('icoDanDis');
                }
            });
            availableDan = true;
        }
    };
    /**
     * [fireGateSwitchEvent 触发过关类型切换事件]
     * @param  [Dom] targetDom [点击事件dom节点]
     */
    var fireGateSwitchEvent = function(targetDom) {
        var scrollGList = $('.scrollMoniGuoguanList');
        var gateType = $(targetDom).attr('gate_type');
        var gateTypeListTargetDom = p.gatesContainer.find('.guoguanList[gate_type=' + gateType + ']');
        $(targetDom).addClass('active').siblings('li.active').removeClass('active');
        if (gateType === "m_n") {
            scrollGList.siblings('.guoguanList').find('li,i').removeClass('active').removeClass(MIXDISABLED);
            scrollGList.siblings('.guoguanList').removeClass('active').removeClass(MIXDISABLED).hide();
            gateTypeListTargetDom.addClass('active');
            scrollGList.show();
            scrollGList.jScrollPane().data("jsp");
        } else {
            scrollGList.find('.guoguanList').find('li,i').removeClass('active');
            scrollGList.find('.guoguanList').removeClass('active');
            scrollGList.hide();
            gateTypeListTargetDom.addClass('active').show();
        }
        fireGatesRenderEvent();
    };
    /**
     * [fireGateSelectEvent 触发过关方式选择事件]
     * @param  [Dom] targetDom [点击事件dom节点]
     */
    var fireGateSelectEvent = function(targetDom) {
        var gateStatusDom = $(targetDom).find('i');

        if ($(targetDom).hasClass(MIXDISABLED) || $(targetDom).find('i').hasClass('icoFx_disable')) {
            return;
        }
        if ($(targetDom).hasClass('active')) {
            $(targetDom).removeClass('active');
            $(gateStatusDom).removeClass('active');
        } else {
            if ($(gateStatusDom).hasClass('icoDx')) {
                $(targetDom).siblings('li').removeClass('active').find('i.active').removeClass('active');
            }
            $(targetDom).addClass('active');
            $(gateStatusDom).addClass('active');
        }
        var freeGate = p.gatesContainer.find('.guoguanTab li:eq(0)').hasClass('active') ? true : false;
        var first = p.gatesContainer.find('.guoguanList[gate_type=m_1] li:eq(0)');
        var firstSelected = first.hasClass('active') ? true : false;
        var unFirst = p.gatesContainer.find('.guoguanList[gate_type=m_1] li:gt(0)');
        var unFirstActive = p.gatesContainer.find('.guoguanList[gate_type=m_1] li.active').not(first).size();
        if (mixSingle && freeGate) {
            if (firstSelected) {
                unFirst.addClass(MIXDISABLED);
            } else {
                unFirst.removeClass(MIXDISABLED);
                unFirstActive >= 1 ? first.addClass(MIXDISABLED) : first.removeClass(MIXDISABLED);
            }
        }
    };
    /**
     * [fireGatesRenderEvent 触发可选过关方式渲染事件]
     */

    var fireGatesRenderEvent = function() {
        gMaxGate = 8;
        gGatesJson = {};
        gGamesArr = Object.keys(gBetsJson);
        var singleGateAvailable = getMaxGate();
        // gMaxGate = +betType || gMaxGate;
        var gamesSelectedLen = gGamesArr.length,
            gatesContainerOpen = false,
            availableMaxGate = Math.min(+betType || gMaxGate, gamesSelectedLen);
        if (gamesSelectedLen == 1) {
            gatesContainerOpen = singleGateAvailable; // judgeSingleGame(gGamesArr[0]);
        } else if (gamesSelectedLen > 1) {
            gatesContainerOpen = true;
        }
        var freeGatesBox = p.gatesContainer.find('.guoguanList[gate_type=m_1]'),
            combinGatesBox = p.gatesContainer.find('.guoguanList[gate_type=m_n]');

        if (gatesContainerOpen) {
            if (p.gatesContainer.hasClass('unselect')) p.gatesContainer.removeClass('unselect').addClass('select');
            var gateData = compileGateData(availableMaxGate, singleGateAvailable);
            var compare = gateData.freeGatesData.data.length - freeGatesBox.find('li').size();
            var compareCombin = gateData.combinGatesData.data.length - combinGatesBox.find('li').size();

            var tmpl = $(Mango.Config.scriptTmpl['gatesBox']).html();
            var doTtmpl = doT.template(tmpl);
            var single = gateData.freeGatesData.data[0].identity === '1x1' ? true : false;
            var pageSingle = /单关/g.test($('.guoguanList.active').html());
            //Mango.Render.render(freeGatesBox, gateData.freeGatesData, Mango.Config.scriptTmpl['gatesBox'], true);
            //如果有单关
            if (single) {
                if (compare <= 0 || !pageSingle) {
                    Mango.Render.render(freeGatesBox, gateData.freeGatesData, Mango.Config.scriptTmpl['gatesBox'], true);
                    //如果当前的数据比页面上的过关数据大
                } else if (compare > 0) {
                    gateData.freeGatesData.data = gateData.freeGatesData.data.slice(0).splice(freeGatesBox.find('li').size(), compare);
                    var content = doTtmpl(gateData.freeGatesData);
                    freeGatesBox.append(content);
                }
                mixSingle = true;
            } else {
                //如果页面上有单关方式 || (如果当前的数据比页面上的过关数据小)
                if (pageSingle || compare < 0) {
                    Mango.Render.render(freeGatesBox, gateData.freeGatesData, Mango.Config.scriptTmpl['gatesBox'], true);
                    //如果当前的数据比页面上的过关数据大
                } else if (compare > 0) {
                    gateData.freeGatesData.data = gateData.freeGatesData.data.slice(0).splice(freeGatesBox.find('li').size(), compare);
                    var content = doTtmpl(gateData.freeGatesData);
                    freeGatesBox.append(content);
                }
                mixSingle = false;
            }


            if(compareCombin !== 0 ){
                if(compareCombin < 0 ){
                    if(gateData.combinGatesData.data.length !== 0){
                        Mango.Render.render(combinGatesBox, gateData.combinGatesData, Mango.Config.scriptTmpl['gatesBox'], true);
                    }else{
                        combinGatesBox.html('');
                    }
                }else{
                    gateData.combinGatesData.data = gateData.combinGatesData.data.slice(0).splice(combinGatesBox.find('li').size(),compareCombin);
                    var content = doTtmpl(gateData.combinGatesData);
                    combinGatesBox.append(content);
                }
            }

        } else {
            freeGatesBox.html('');
            combinGatesBox.html('');
            p.gatesContainer.removeClass('select').addClass('unselect');
        }
        // if (!$(p.confirmContainer).hasClass('unselect')) {
        fireConfirmBettingDataEvent();
        //$(p.confirmContainer).addClass('select');
        // }
        if (p.gatesContainer.hasClass('unselect')) {
            $(p.confirmContainer).removeClass('select').addClass('unselect');
        } else {
            $(p.confirmContainer).removeClass('unselect').addClass('select');
        }

        if ($('.guoguanTab li:eq(1)').hasClass('active')) {
            $('.scrollMoniGuoguanList').jScrollPane().data("jsp");
        }

        reRenderDanDom();
    };
    /**
     * [compileGateData 拼装自由过关和混合过关方式数据]
     * @param  [Int] availableMaxGate [允许的最大过关赛事数]
     * @return [Json]                 [自由过关和混合过关数据]
     */
    var compileGateData = function(availableMaxGate, singleGateAvailable) {
        var freeGates = Mango.Config.gameConfig.gate_map.free.slice(0, availableMaxGate - 1);
        if (+singleGateAvailable) {
            freeGates.unshift(Mango.Config.gameConfig.gate_map.single[0]);
        }
        var freeGatesData = {
                gate_type: 0,
                data: availableMaxGate <= 1 ? [Mango.Config.gameConfig.gate_map.single[0]]: freeGates
            },
            combinGatesData = {
                gate_type: 1,
                data: compileCombinGatesData(availableMaxGate)
            };
        return {
            freeGatesData: freeGatesData,
            combinGatesData: combinGatesData
        };
    };
    /**
     * [compileCombinGatesData 获取可用的混合过关方式数据]
     * @param  [Int] availableMaxGate [允许的最大过关赛事数]
     * @return [Array]                [混合过关方式数组]
     */
    var compileCombinGatesData = function(availableMaxGate) {
        return $(Mango.Config.gameConfig.gate_map.combin).map(function() {
            if (this.identity.substring(0, 1) <= availableMaxGate) return this;
        });
    };
    /**
     * [judgeSingleGame 判断某场比赛所选赔率单元是否支持单关]
     * @param  [String] gameBn [赛事编码]
     * @return [Boolean]       [是否支持]
     */
    var judgeSingleGame = function(gameBn) {
        var isGameSingle = +(p.selectedGamesBox.find('.gameTitle[game_bn=' + gameBn + ']').attr('single'));
        if (isGameSingle) return true;
        var isSingle = true;
        p.selectedGamesBox.find('.gameOption[game_bn=' + gameBn + '] > .betList').find('a').each(function(e) {
            var way = $(this).attr('way');
            // maxGate = Math.min(maxGate, Mango.Config.gameConfig.max_gates[way]);

            if ($.inArray(way, Mango.Config.gameConfig.defaultWays) > -1) {
                isSingle = false;
                return false;
            }
            // if (!waysJson[way]) waysJson[way] = 1;
        });
        return isSingle;
    };
    var getMaxGate = function() {
        var singleGateAvailable = 1;
        p.selectedGamesBox.find('.betList > a').each(function(e) {
            var way = $(this).attr('way'),
                single = +$(this).attr('single');
            if (!single) singleGateAvailable = single;

            gMaxGate = Math.min(gMaxGate, Mango.Config.gameConfig.max_gates[way]);
        });
        return singleGateAvailable;
    };

    var bindConfirmBettingEvent = function() {
        var multipleDom = p.confirmContainer.find('#multipleSelector');
        // 倍数控件
        gameSiatisticsMultiple = function(){
            var init ={
                config :{
                    btnSub: '.J-select-sub',
                    btnAdd: '.J-select-add',
                    realDom:'#multipleSelector'

                },
                setValue : function(v){
                    $(this.config.realDom).val(v);
                },
                getValue: function(){
                    var me = this,
                        v = $(me.config.realDom).val();
                   return v;
                },
                add:function(){
                    var me = this,
                        v = Number(me.getValue());
                    if(v < 9999){
                        me.setValue(v + 1);
                    }else{
                        me.setValue(v);
                    }
                },
                sub:function(){
                    var me = this,
                        v = Number(me.getValue());
                    if(v <= 1){
                        me.setValue(1);
                    }else{
                        me.setValue( v - 1);
                    };
                }
            };

            return init;
        };

        $('.J-select-add').click(function(){
                gameSiatisticsMultiple().add();
                var v = gameSiatisticsMultiple().getValue();
                $('.gameMultiple').html(v);
                filterGameDatas();
                compileBetsNumText(v);
            });

        $('.J-select-sub').click(function(){
                gameSiatisticsMultiple().sub();
                var v = gameSiatisticsMultiple().getValue();
                $('.gameMultiple').html(v);
                filterGameDatas();
                compileBetsNumText(v);
            });

        $('#multipleSelector').on('keydown', function(event) {
            if (!(event.keyCode == 35) && !(event.keyCode == 36) && !(event.keyCode == 46) && !(event.keyCode == 8) && !(event.keyCode == 37) && !(event.keyCode == 39)) {
                if (!((event.keyCode >= 48 && event.keyCode <= 57) || (event.keyCode >= 96 && event.keyCode <= 105))) {
                    return false;
                }
                if ($.trim($(this).val()).length > 6) {
                    return false;
                }
            }

        }).on('keyup',function(){
            var v = parseInt($.trim(this.value));
            if (v > 999999) {
                this.value = v = 999999;
            }
            $('.gameMultiple').html(v);
            filterGameDatas();
            compileBetsNumText(v);
        });



        $(p.confirmContainer).click(function(e) {
            if ($(this).hasClass('unselect')) return false;
        });

        $(p.confirmContainer)
            .find('a.clearBetting').click(function(e) {
                fireClearBettingEvent();
            }).end()
            .find('.submitBtn').click(function(event) {
                if(remind(true)){
                    return false;
                }
                compileBettingData();
            });
        // p.submitUrl = p.billSubmitUrl + '?userid=' + userid + '&username=' + username + '&identity=' + identity;
    };
    var multipleBoxEvent = function(meSelect, v) {
        if (isNaN(v)) return false;
        meSelect.setValue(v);
        compileBetsNumText(v);
    }
    var submitBettingData = function(data) {
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
                callbackSubmitSuccess(d, msgWin, msgWin2);
                gameSiatisticsMultiple().setValue(10);
                $('.gameMultiple').html(10);
            },
            complete: function(d) {
                msgWin.hideTip();
            },
            error: function(d) {
                callbackSubmitFailed(d, msgWin2)
            }
        });
    };
    var callbackSubmitSuccess = function(d, loadingWin, tipWin) {
        loadingWin.hideTip();
        var msgHtml = '<div class="bd text-center"><div class="pop-title"><i class="ico-error"></i><h4 class="pop-text">' + ((d && d.msg) ? d.msg : '') + '<br></h4></div></div>';
        if (+d.coding > 0) {
            msgHtml = '<div class="bd text-center"><div class="pop-title"><i class="ico-success"></i><h4 class="pop-text">' + d.msg + '<br></h4></div><p class="text-note" style="padding:5px 0;">您可以通过”投注记录“查询您的投注记录！</p></div>';
            //清空注单信息数据
            fireClearBettingEvent();
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
    var compileBettingData = function() {
        var mask = new UIscript.Mask(),
            message = new UIscript.MiniWindow(),
            sNum = 1,
            tipText = '',
            returnFlag = true;

        gGatesJson = getGateSelected();
        gGatesArr = Object.keys(gGatesJson);

        if (gGamesArr.length < 1) {
            tipText = '缺少赛事数据';
            returnFlag = false;
        }
        if (gGatesArr.length < 1) {
            tipText = '缺少过关方式数据';
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
        var multiple = p.confirmContainer.find('.gameMultiple').html(),
            amount = gBetsNum * multiple * 2,
            bettingData = {
                "max_gate": gMaxGate,
                "gate": gGatesArr,
                "games": gOrderData,
                "dan": gDanArr,
                "bet_num": gBetsNum,
                "multiple": multiple,
                "amount": amount
            };

        var orderMessage = new UIscript.Message({
            cls: 'w-12 game-form'
        });
        var _htmlDom = $('tbody.selectedGamesBox').clone().html();
        var _htmlDom2 = $('div.methodContent');
        var _htmlDom3 = '';
        _htmlDom2.find('li').each(function(index, el) {
            $(this).hasClass('active') ? _htmlDom3 += $(this).html() : '';
        });

        var _html = [
            '<div class="submitBox">', '<div class="tableBox">', '<table>', '<colgroup><col width="65"><col><col width="20"><col><col width="35"></colgroup>', '<thead><tr><th>场次</th><th colspan="3">主队　VS　客队</th><th>胆</th></tr></thead>', '<tbody>', _htmlDom, '</tbody>', '</table>', '</div>', '<div class="init">', '<h3>过关方式</h3><p>' + _htmlDom3 + '</p>', '<h3>投注信息</h3>', '<p class="text">', '注数：共<font>' + bettingData['bet_num'] + '</font>注<br/>', '倍数：<font>' + bettingData['multiple'] + '</font>倍<br/>', '总金额：<font>' + bettingData['amount'] + '</font>元', '</p>', '</div>', '<div style="clear:both"></div>', '</div>'
        ];
        //弹出确认框
        orderMessage.show({
            isShowMask: true,
            confirmIsShow: true,
            cancelIsShow: true,
            confirmText: '确 认',
            cancelText: '取 消',
            title: '订单详细',
            cancelFun: function() {
                orderMessage.hide();
            },
            confirmFun: function() {
                orderMessage.hide();
                submitBettingData(bettingData);
            },
            content: _html.join('')
        });
    };
    /**
     * [fireClearBettingEvent 触发清空投注数据的事件]
     */
    var fireClearBettingEvent = function() {
        gBetsJson = {}; // 选择的赛事和赔率单元树形数据
        gMaxGate = 8;
        gGamesArr = []; // 选择的赛事编号数组
        gOrderData = []; // 订单数据
        gBetsNum = 0; // 注单数
        gGatesJson = {}; // 过关方式数据对象
        gGatesArr = []; // 过关方式
        gMaxDanNum = 0; // 最大可选胆码赛事数
        gDanArr = []; // 胆码信息
        availableDan = true; //胆码可选择
        p.selectedGamesBox.html('');
        p.selectedGamesContainer.addClass('unselect');
        p.gatesContainer.addClass('unselect').find('.guoguanList').html('');
        p.confirmContainer.addClass('unselect');
        fireConfirmBettingDataEvent();
        p.gamesListBox.find('[action_type=chooseOdd].active').removeClass('active');
        p.gamesListBox.find('.openBtn.openBtnSele').removeClass('openBtnSele');
    };
    //固定头部菜单
    var bindWindowScrollEvent = function() {
        $(window).scroll(function(event) {
            //var scrollTop = 140;
            setTimeout(function() {
                if ($(window).scrollTop() > 320) {
                    //position: fixed; z-index: 888; top: 0px;
                    $(p.gamesContainer).find('dl.dataHead').css({
                        "position": 'fixed',
                        "top": '0px'
                    });

                    if ($(window).height() > $(p.submitContainer).height() - 20 ) {
                        var marginLeft = $('.docBody').width()/2 - $('#submitContainer').width();
                        $(p.gamesContainer).find('#submitContainer').css({
                            "position": 'fixed',
                            "top": '0px',
                            "left": "50%",
                            "margin-left": marginLeft+"px"
                        });
                    }
                    $(p.gamesContainer).find('div.dataHeadBg').show();


                } else {
                    $(p.gamesContainer).find('dl.dataHead').css({
                        position: 'relative',
                        top: 'auto'
                    });
                    $(p.gamesContainer).find('#submitContainer').css({
                        "position": 'relative',
                        "top": 'auto',
                        "left": "auto",
                        "margin-left": "0px"
                    });
                    $(p.gamesContainer).find('div.dataHeadBg').hide();
                }
            }, 5);
        });
        //$('.scrollMoni').jScrollPane();
    };
    // TODO 根据赛果类型重新渲染选号区
    var bindGameOddFilterEvent = function() {
        // 时间控件
        // var gameDateFilterSelector = new UIscript.Select({
        //     realDom: '#J-select-recharge',
        //     cls: 'w-2'
        // });

        $('#J-select-recharge').change(function(event) {
            var v = $(this).val();
            var param = {
                'bet_date': v
            };
            Mango.Data.getGameData(param);
        });

        // gameDateFilterSelector.addEvent('change', function(e, value, text) {
        //     var param = {
        //         'bet_date': value
        //     };
        //     Mango.Data.getGameData(param);
        // });



        //单关筛选
        $(p.gamesContainer).find('#supportDg').click(function(event) {
            $(p.gamesContainer).hasClass('showDgTips') ? $(p.gamesContainer).removeClass('showDgTips') : $(p.gamesContainer).addClass('showDgTips');
            $(this).find('i.icoFx').hasClass('active') ? $(this).find('i.icoFx').removeClass('active') : $(this).find('i.icoFx').addClass('active');
        });

        //已选比赛
        $(p.gamesContainer).find('.choosedGamesFilter').click(function(event) {
            if ($(this).find('i.icoFx').hasClass('active')) {
                $(this).find('i.icoFx').removeClass('active')
                p.gamesListBox.find('dd.basicWays,dd.league_470').removeClass('hide');
                // p.gamesListBox.find('dd.league_470').removeClass('hide');
            } else {
                p.gamesListBox.find('dd.basicWays,dd.league_470').addClass('hide');
                var gGamesArrStr = gGamesArr.join('_');
                p.gamesListBox.find('dd.basicWays').each(function(item, index) {
                    var gameNumber = $(this).attr('game');
                    //if no be selected
                    if (gGamesArrStr.indexOf(gameNumber) === -1) {
                        //if the analyMore is opened
                        if ($(this).find('.openBtn').hasClass('closeBtn')) {
                            $(this).find('.openBtn').click();
                        }
                        if ($(this).find('.co6.moreOptBtnBox').hasClass('openBtn')) {
                            $(this).find('.mcSelectBox').click();
                        }
                    }
                });
                $(this).find('i.icoFx').addClass('active')
                $('dd[game=' + gGamesArr.join('].basicWays,dd[game=') + '].basicWays').removeClass('hide');
                $('dd[game=' + gGamesArr.join('].league_470,dd[game=') + '].league_470').removeClass('hide');
            }
        });

        $(p.gamesContainer).find('.timeSwitcher').hover(function(e) {
            $(this).find('.optionList').show();
        }, function(e) {
            $(this).find('.optionList').hide();
        }).find('.optionList > a').click(function(event) {
            var acType = $(this).attr('action_type'),
                desc = $(this).text();
            $(p.gamesContainer).find('.timeSwitcher').find('a.imitateSelect').text(desc);
            reRenderTime($(this).index());
            $(p.gamesContainer).find('.optionList').hide();
        });

        // 已结束
        $(p.gamesContainer).find('.stopGame').click(function(event) {
            if ($(this).find('i.icoFx').hasClass('active')) {
                $(this).find('i.icoFx').removeClass('active')
                p.gamesListBox.find('[istop="1"]').addClass('isOver');
            } else {
                $(this).find('i.icoFx').addClass('active');
                p.gamesListBox.find('[istop="1"]').removeClass('isOver');
            }
        });

        // tip
        var minTip = new UIscript.Tip({
            cls: 'j-ui-tip-alert j-ui-tip-t j-ui-tip-showrule'
        });
        $(p.gamesContainer).find('.jtip').hover(function() {
            minTip.setText($(this).attr('inf'));
            minTip.show(minTip.getDom().width() / 2 * -1 + $(this).width() / 2, 20, $(this));
        }, function() {
            minTip.hide();
        })
    };

    var reRenderTime = function(num) {
        $(p.gamesListBox).find('.gameTime').each(function(index, el) {
            var tipData = ($(this).attr('data')).split(';');
            $(this).find('i').html(tipData[num]);
        });
    };


     /**
     * [filterGameDatas 计算理论奖金]     *
     */
    var filterGameDatas = function() {
        var money = {
                max: 0,
                min: 0
            },
            betDataObj = {}, // 理論獎金對象
            _gGatesArr = gGatesArr;

        p.selectedGamesBox.find('.gameOption').each(function(index, el) {
            var game_bn = $(el).attr('game_bn');
            betDataObj[game_bn] = {
                ranNumber:0,
                data:{}
            };
            $(el).find('a').each(function(indexI, dom) {
                var way = $(dom).attr('way');
                var index = $(dom).attr('index');
                var fixed = $(dom).attr('fixed');

                if (!betDataObj[game_bn].data[way]) {
                    betDataObj[game_bn].data[way] = [];
                }
                if(fixed !=='0'){
                    betDataObj[game_bn].ranNumber = parseInt(fixed,10);
                }
                betDataObj[game_bn].data[way].push({
                    desc:way,
                    value:index.split('_')[1],
                    odd:$(dom).attr('odd').split(',')[0],
                    game:game_bn
                });
            });
        });


        //判断是否组合过关
        if ($('.guoguanTab li:eq(1)').hasClass('active')) {
            var jtip = $('.scrollMoniGuoguanList').find('.jtip.active');
            if (jtip.size() > 0) {
                var combination = jtip.attr('combination');
                if (combination.indexOf(',') > -1) {
                    _gGatesArr = combination.split(',');
                } else {
                    _gGatesArr = [combination];
                }
            } else {
                _gGatesArr = [];
            }
        }

        for (var i = 0, len = _gGatesArr.length; i < len; i++) {
            var tmp = _gGatesArr[i];
            if (tmp.indexOf('x') > -1) {
                var num = +tmp.split('x')[0];
            } else {
                var num = +tmp;
            }

            var betArray = getEffectiveBet(betDataObj);
            var max = doCombinations(convert2oneLevelArray(betArray), num);

            var maxMoney = getTheoryMoney(max);

            money.max += maxMoney;

        }

        $('.theorybonus').html(parseFloat(money.max).toFixed(2) + '</strong>元');
    }

    /**
     * [convert2oneLevelArray 二维数组转成一维数组]
     * @param  [Array] array [数组]
     */
    var convert2oneLevelArray = function(array) {
        var _array = [];
        for (var i = 0, len = array.length; i < len; i++) {
            for (var j = 0, lenJ = array[i].length; j < lenJ; j++) {
                _array.push(array[i][j]);
            }
        }
        return _array;
    }

    /**
     * [getTheoryMoney 计算每小步的理论奖金]
     * @param  [Array] array [数组]
     */
    var getTheoryMoney = function(array) {
        var sum = 0;
        var multiple = p.confirmContainer.find('.select-game-statics-multiple').find('.choose-input').val();


        for (var i = 0, len = array.length; i < len; i++) {
            if (gDanArr.length > 0) {
                var arr = getBetIndexArray(array[i]);
                if (arrayContains(arr, gDanArr)) {
                    array[i].unshift(true);
                } else {
                    array[i].unshift(false);
                }
            } else {
                array[i].unshift(true);
            }
        }

        array = getPlianBetData(array);
        for (var i = 0, len = array.length; i < len; i++) {
            var tmpSum = parseFloat(array[i][1].odd);
            for (var j = 2, lenJ = array[i].length; j < lenJ; j++) {
                tmpSum *= parseFloat(array[i][j].odd);
            }
            sum += tmpSum * 2 * multiple;
        }
        return sum;
    }
    /**
     * [getPlianBetData 验证数组是否合法]
     * @param  [Array] array [数组]
     */
    var getPlianBetData = function(array){
        var _array = [];
        for(var i = 0,len=array.length;i<len;i++){
            if(array[i][0] && vertifyBet(array[i])){
                _array.push(array[i]);
            }
        }
        return _array;
    }


    /**
     * [vertifyBet 验证数组是否合法]
     * @param  [Array] array [数组]
     */
    function vertifyBet(array) {
        var hash = {};
        var result = true;
        for (var i = 0 ,len=array.length;i<len;i++) {
            if (hash[array[i].game]) {
                result = false;
            }
            // 不存在该元素，则赋值为true，可以赋任意值，相应的修改if判断条件即可
            hash[array[i].game] = true;
        }
        return result;
    }


    /**
     * [getBetIndexArray 过滤投注编号数组]
     * @param  [Array] array [数组]
     */
    var getBetIndexArray = function(array) {
        var _array = [];
        for (var i = 0, len = array.length; i < len; i++) {
            _array.push(array[i].game);
        }
        return _array;
    }

    /**
     * [arrayContains 判断数组a是否包含b]
     * @param  [a,b] 数组
     */
    var arrayContains = function(a, b) {
        for (var i = 0; i < b.length; i++) {
            t = false;
            for (var j = 0; j < a.length; j++) {
                if (b[i] == a[j]) {
                    t = true;
                    break;
                }
            }
            if (!t) return false;
        }
        return true;
    }


    /**
     * [getEffectiveBet 获取所选投注赔率和值最大的注]
     * @param  [Object]  obj 对象
     */
    var getEffectiveBet = function(obj) {
        var bets = [];
        for (var key in obj) {
            var map = createBetMap(obj[key].ranNumber);
            var currentBet = obj[key].data;
            var currentBetSum = 0;
            var maxBet = [];
            //遍历map与当前投注信息，输出赔率和值最大的投注组合
            for (var mapKey in map) {
                var oneCase = map[mapKey];
                //计算配置的赠送赔率合值
                var sum = 0;
                var bet = [];
                for (var way in currentBet) {
                    var comparedValue = oneCase[way];
                    var array = currentBet[way];
                    var hafuArray = [];
                    if (way === 'hafu') {
                        hafuArray = getHafuBetArray(comparedValue.value, array);
                        if (hafuArray.length > 0) {
                            sum += +hafuArray[0].odd;
                            bet.push(hafuArray[0]);
                        }
                    } else {
                        for (var i = 0, len = array.length; i < len; i++) {
                            if (array[i].value === comparedValue.value) {
                                sum += +array[i].odd;
                                bet.push(array[i]);
                            }
                        }
                    }
                }
                if (sum > currentBetSum) {
                    currentBetSum = sum;
                    maxBet = bet;
                }
            }
            //2.取和值最高的组合注
            bets.push(maxBet);
        }

        return bets;
    }

    /**
    * [getHafuBetArray 过滤半全场投注赔率最高的注数]
    ×  @param  [Array]  arrA 数组
    ×  @param  [Array]  arrB 数组
    */
    var getHafuBetArray = function(arrA, arrB) {
        var array = [];
        for (var i = 0, len = arrB.length; i < len; i++) {
            if (arrA.indexOf(arrB[i].value) > -1) {
                array.push(arrB[i]);
            }
        }
        return bubbleSort(array);
    }

    /**
     ** 创建一个选中比赛的假设比赛结果map
     ** 说明：ranNumber 让球数
     ** createBetMap(ranNumber)
     ** 返回值：假设比赛结果map对象
     **/
    var createBetMap = function(ranNumber) {
        return {
            '1:0': {
                had: {
                    desc: '胜平负',
                    value: 'h'
                },
                ttg: {
                    desc: '总进球',
                    value: 's1'
                },
                hhad: {
                    desc: '让球胜平负',
                    value: function() {
                        return ranNumber + 1 > 0 ? 'ch' : (ranNumber + 1 == 0 ? 'cd' : 'ca');
                    }()
                },
                hafu: {
                    desc: '半全场',
                    value: ['hh', 'dh'],
                    desc4value:'胜胜或平胜'
                },
                crs:{
                    desc:'比分',
                    value:'0100'
                }
            },
            '2:0': {
                had: {
                    desc: '胜平负',
                    value: 'h'
                },
                ttg: {
                    desc: '总进球',
                    value: 's2'
                },
                hhad: {
                    desc: '让球胜平负',
                    value: function() {
                        return ranNumber + 2 > 0 ? 'ch' : (ranNumber + 2 == 0 ? 'cd' : 'ca');
                    }()
                },
                hafu: {
                    desc: '半全场',
                    value: ['hh', 'dh'],
                    desc4value:'胜胜或平胜'
                },
                crs:{
                    desc:'比分',
                    value:'0200'
                }
            },
            '2:1': {
                had: {
                    desc: '胜平负',
                    value: 'h'
                },
                ttg: {
                    desc: '总进球',
                    value: 's3'
                },
                hhad: {
                    desc: '让球胜平负',
                    value: function() {
                        return ranNumber + 1 > 0 ? 'ch' : (ranNumber + 1 == 0 ? 'cd' : 'ca');
                    }()
                },
                hafu: {
                    desc: '半全场',
                    value: ['hh', 'dh','ca'],
                    desc4value:'胜胜或平胜或负胜'
                },
                crs:{
                    desc:'比分',
                    value:'0201'
                }
            },
            '3:0': {
                had: {
                    desc: '胜平负',
                    value: 'h'
                },
                ttg: {
                    desc: '总进球',
                    value: 's3'
                },
                hhad: {
                    desc: '让球胜平负',
                    value: function() {
                        return ranNumber + 3 > 0 ? 'ch' : (ranNumber + 3 == 0 ? 'cd' : 'ca');
                    }()
                },
                hafu: {
                    desc: '半全场',
                    value: ['hh', 'dh'],
                    desc4value:'胜胜或平胜'
                },
                crs:{
                    desc:'比分',
                    value:'0300'
                }
            },
            '3:1': {
                had: {
                    desc: '胜平负',
                    value: 'h'
                },
                ttg: {
                    desc: '总进球',
                    value: 's4'
                },
                hhad: {
                    desc: '让球胜平负',
                    value: function() {
                        return ranNumber + 2 > 0 ? 'ch' : (ranNumber + 2 == 0 ? 'cd' : 'ca');
                    }()
                },
                hafu: {
                    desc: '半全场',
                    value: ['hh', 'dh','ah'],
                    desc4value:'胜胜或平胜或负胜'
                },
                crs:{
                    desc:'比分',
                    value:'0301'
                }
            },
            '3:2': {
                had: {
                    desc: '胜平负',
                    value: 'h'
                },
                ttg: {
                    desc: '总进球',
                    value: 's5'
                },
                hhad: {
                    desc: '让球胜平负',
                    value: function() {
                        return ranNumber + 1 > 0 ? 'ch' : (ranNumber + 1 == 0 ? 'cd' : 'ca');
                    }()
                },
                hafu: {
                    desc: '半全场',
                    value: ['hh', 'dh','ah'],
                    desc4value:'胜胜或平胜或负胜'
                },
                crs:{
                    desc:'比分',
                    value:'0302'
                }
            },
            '4:0': {
                had: {
                    desc: '胜平负',
                    value: 'h'
                },
                ttg: {
                    desc: '总进球',
                    value: 's4'
                },
                hhad: {
                    desc: '让球胜平负',
                    value: function() {
                        return ranNumber + 4 > 0 ? 'ch' : (ranNumber + 4 == 0 ? 'cd' : 'ca');
                    }()
                },
                hafu: {
                    desc: '半全场',
                    value: ['hh', 'dh'],
                    desc4value:'胜胜或平胜'
                },
                crs:{
                    desc:'比分',
                    value:'0400'
                }
            },
            '4:1': {
                had: {
                    desc: '胜平负',
                    value: 'h'
                },
                ttg: {
                    desc: '总进球',
                    value: 's5'
                },
                hhad: {
                    desc: '让球胜平负',
                    value: function() {
                        return ranNumber + 3 > 0 ? 'ch' : (ranNumber + 3 == 0 ? 'cd' : 'ca');
                    }()
                },
                hafu: {
                    desc: '半全场',
                    value: ['hh', 'dh','ah'],
                    desc4value:'胜胜或平胜或负胜'
                },
                crs:{
                    desc:'比分',
                    value:'0401'
                }
            },
            '4:2': {
                had: {
                    desc: '胜平负',
                    value: 'h'
                },
                ttg: {
                    desc: '总进球',
                    value: 's6'
                },
                hhad: {
                    desc: '让球胜平负',
                    value: function() {
                        return ranNumber + 2 > 0 ? 'ch' : (ranNumber + 2 == 0 ? 'cd' : 'ca');
                    }()
                },
                hafu: {
                    desc: '半全场',
                    value: ['hh', 'dh','ah'],
                    desc4value:'胜胜或平胜或负胜'
                },
                crs:{
                    desc:'比分',
                    value:'0402'
                }
            },
            '5:0': {
                had: {
                    desc: '胜平负',
                    value: 'h'
                },
                ttg: {
                    desc: '总进球',
                    value: 's5'
                },
                hhad: {
                    desc: '让球胜平负',
                    value: function() {
                        return ranNumber + 5 > 0 ? 'ch' : (ranNumber + 5 == 0 ? 'cd' : 'ca');
                    }()
                },
                hafu: {
                    desc: '半全场',
                    value: ['hh', 'dh'],
                    desc4value:'胜胜或平胜'
                },
                crs:{
                    desc:'比分',
                    value:'0500'
                }
            },
            '5:1': {
                had: {
                    desc: '胜平负',
                    value: 'h'
                },
                ttg: {
                    desc: '总进球',
                    value: 's6'
                },
                hhad: {
                    desc: '让球胜平负',
                    value: function() {
                        return ranNumber + 4 > 0 ? 'ch' : (ranNumber + 4 == 0 ? 'cd' : 'ca');
                    }()
                },
                hafu: {
                    desc: '半全场',
                    value: ['hh', 'dh','ah'],
                    desc4value:'胜胜或平胜或负胜'
                },
                crs:{
                    desc:'比分',
                    value:'0501'
                }
            },
            '5:2': {
                had: {
                    desc: '胜平负',
                    value: 'h'
                },
                ttg: {
                    desc: '总进球',
                    value: 's7'
                },
                hhad: {
                    desc: '让球胜平负',
                    value: function() {
                        return ranNumber + 3 > 0 ? 'ch' : (ranNumber + 3 == 0 ? 'cd' : 'ca');
                    }()
                },
                hafu: {
                    desc: '半全场',
                    value: ['hh', 'dh','ah'],
                    desc4value:'胜胜或平胜或负胜'
                },
                crs:{
                    desc:'比分',
                    value:'0502'
                }
            },
            '-1-h': {
                had: {
                    desc: '胜平负',
                    value: 'h'
                },
                ttg: {
                    desc: '总进球',
                    value: 's7'
                },
                hhad: {
                    desc: '让球胜平负',
                    value: function() {
                        return ranNumber + 6 > 0 ? 'ch' : (ranNumber + 6 == 0 ? 'cd' : 'ca');
                    }()
                },
                hafu: {
                    desc: '半全场',
                    value: ['hh', 'dh','ah'],
                    desc4value:'胜胜或平胜或负胜'
                },
                crs:{
                    desc:'比分',
                    value:'-1-h'
                }
            },
            '0:0': {
                had: {
                    desc: '胜平负',
                    value: 'd'
                },
                ttg: {
                    desc: '总进球',
                    value: 's0'
                },
                hhad: {
                    desc: '让球胜平负',
                    value: function() {
                        return ranNumber  > 0 ? 'ch' : (ranNumber == 0 ? 'cd' : 'ca');
                    }()
                },
                hafu: {
                    desc: '半全场',
                    value: ['dd'],
                    desc4value:'平平'
                },
                crs:{
                    desc:'比分',
                    value:'0000'
                }
            },
            '1:1': {
                had: {
                    desc: '胜平负',
                    value: 'd'
                },
                ttg: {
                    desc: '总进球',
                    value: 's2'
                },
                hhad: {
                    desc: '让球胜平负',
                    value: function() {
                        return ranNumber  > 0 ? 'ch' : (ranNumber == 0 ? 'cd' : 'ca');
                    }()
                },
                hafu: {
                    desc: '半全场',
                    value: ['hd','dd','ad'],
                    desc4value:'胜平或平平或负平'
                },
                crs:{
                    desc:'比分',
                    value:'0101'
                }
            },
            '2:2': {
                had: {
                    desc: '胜平负',
                    value: 'd'
                },
                ttg: {
                    desc: '总进球',
                    value: 's4'
                },
                hhad: {
                    desc: '让球胜平负',
                    value: function() {
                        return ranNumber  > 0 ? 'ch' : (ranNumber == 0 ? 'cd' : 'ca');
                    }()
                },
                hafu: {
                    desc: '半全场',
                    value: ['hd','dd','ad'],
                    desc4value:'胜平或平平或负平'
                },
                crs:{
                    desc:'比分',
                    value:'0202'
                }
            },
            '3:3': {
                had: {
                    desc: '胜平负',
                    value: 'd'
                },
                ttg: {
                    desc: '总进球',
                    value: 's6'
                },
                hhad: {
                    desc: '让球胜平负',
                    value: function() {
                        return ranNumber  > 0 ? 'ch' : (ranNumber == 0 ? 'cd' : 'ca');
                    }()
                },
                hafu: {
                    desc: '半全场',
                    value: ['hd','dd','ad'],
                    desc4value:'胜平或平平或负平'
                },
                crs:{
                    desc:'比分',
                    value:'0303'
                }
            },
            '-1-d(平其它)': {
                had: {
                    desc: '胜平负',
                    value: 'd'
                },
                ttg: {
                    desc: '总进球',
                    value: 's7'
                },
                hhad: {
                    desc: '让球胜平负',
                    value: function() {
                        return ranNumber  > 0 ? 'ch' : (ranNumber == 0 ? 'cd' : 'ca');
                    }()
                },
                hafu: {
                    desc: '半全场',
                    value: ['hd','dd','ad'],
                    desc4value:'胜平或平平或负平'
                },
                crs:{
                    desc:'比分',
                    value:'-1-d'
                }
            },
            '0:1': {
                had: {
                    desc: '胜平负',
                    value: 'a'
                },
                ttg: {
                    desc: '总进球',
                    value: 's1'
                },
                hhad: {
                    desc: '让球胜平负',
                    value: function() {
                        return ranNumber - 1 > 0 ? 'ch' : (ranNumber - 1 == 0 ? 'cd' : 'ca');
                    }()
                },
                hafu: {
                    desc: '半全场',
                    value: ['da','aa'],
                    desc4value:'平负或负负'
                },
                crs:{
                    desc:'比分',
                    value:'0001'
                }
            },
            '0:2': {
                had: {
                    desc: '胜平负',
                    value: 'a'
                },
                ttg: {
                    desc: '总进球',
                    value: 's2'
                },
                hhad: {
                    desc: '让球胜平负',
                    value: function() {
                        return ranNumber - 2 > 0 ? 'ch' : (ranNumber - 2 == 0 ? 'cd' : 'ca');
                    }()
                },
                hafu: {
                    desc: '半全场',
                    value: ['da','aa'],
                    desc4value:'平负或负负'
                },
                crs:{
                    desc:'比分',
                    value:'0002'
                }
            },
            '1:2': {
                had: {
                    desc: '胜平负',
                    value: 'a'
                },
                ttg: {
                    desc: '总进球',
                    value: 's3'
                },
                hhad: {
                    desc: '让球胜平负',
                    value: function() {
                        return ranNumber - 1 > 0 ? 'ch' : (ranNumber - 1 == 0 ? 'cd' : 'ca');
                    }()
                },
                hafu: {
                    desc: '半全场',
                    value: ['ha','da','aa'],
                    desc4value:'胜负或平负或负负'
                },
                crs:{
                    desc:'比分',
                    value:'0102'
                }
            },
            '0:3': {
                had: {
                    desc: '胜平负',
                    value: 'a'
                },
                ttg: {
                    desc: '总进球',
                    value: 's3'
                },
                hhad: {
                    desc: '让球胜平负',
                    value: function() {
                        return ranNumber - 3 > 0 ? 'ch' : (ranNumber - 3 == 0 ? 'cd' : 'ca');
                    }()
                },
                hafu: {
                    desc: '半全场',
                    value: ['da','aa'],
                    desc4value:'平负或负负'
                },
                crs:{
                    desc:'比分',
                    value:'0003'
                }
            },
            '1:3': {
                had: {
                    desc: '胜平负',
                    value: 'a'
                },
                ttg: {
                    desc: '总进球',
                    value: 's4'
                },
                hhad: {
                    desc: '让球胜平负',
                    value: function() {
                        return ranNumber - 2 > 0 ? 'ch' : (ranNumber - 2 == 0 ? 'cd' : 'ca');
                    }()
                },
                hafu: {
                    desc: '半全场',
                    value: ['ha','da','aa'],
                    desc4value:'胜负或平负或负负'
                },
                crs:{
                    desc:'比分',
                    value:'0103'
                }
            },
            '2:3': {
                had: {
                    desc: '胜平负',
                    value: 'a'
                },
                ttg: {
                    desc: '总进球',
                    value: 's5'
                },
                hhad: {
                    desc: '让球胜平负',
                    value: function() {
                        return ranNumber - 1 > 0 ? 'ch' : (ranNumber - 1 == 0 ? 'cd' : 'ca');
                    }()
                },
                hafu: {
                    desc: '半全场',
                    value: ['ha','da','aa'],
                    desc4value:'胜负或平负或负负'
                },
                crs:{
                    desc:'比分',
                    value:'0203'
                }
            },
            '0:4': {
                had: {
                    desc: '胜平负',
                    value: 'a'
                },
                ttg: {
                    desc: '总进球',
                    value: 's4'
                },
                hhad: {
                    desc: '让球胜平负',
                    value: function() {
                        return ranNumber - 4 > 0 ? 'ch' : (ranNumber - 4 == 0 ? 'cd' : 'ca');
                    }()
                },
                hafu: {
                    desc: '半全场',
                    value: ['da','aa'],
                    desc4value:'平负或负负'
                },
                crs:{
                    desc:'比分',
                    value:'0004'
                }
            },
            '1:4': {
                had: {
                    desc: '胜平负',
                    value: 'a'
                },
                ttg: {
                    desc: '总进球',
                    value: 's5'
                },
                hhad: {
                    desc: '让球胜平负',
                    value: function() {
                        return ranNumber - 3 > 0 ? 'ch' : (ranNumber - 3 == 0 ? 'cd' : 'ca');
                    }()
                },
                hafu: {
                    desc: '半全场',
                    value: ['ha','da','aa'],
                    desc4value:'胜负或平负或负负'
                },
                crs:{
                    desc:'比分',
                    value:'0104'
                }
            },
            '2:4': {
                had: {
                    desc: '胜平负',
                    value: 'a'
                },
                ttg: {
                    desc: '总进球',
                    value: 's6'
                },
                hhad: {
                    desc: '让球胜平负',
                    value: function() {
                        return ranNumber - 2 > 0 ? 'ch' : (ranNumber - 2 == 0 ? 'cd' : 'ca');
                    }()
                },
                hafu: {
                    desc: '半全场',
                    value: ['ha','da','aa'],
                    desc4value:'胜负或平负或负负'
                },
                crs:{
                    desc:'比分',
                    value:'0204'
                }
            },
            '0:5': {
                had: {
                    desc: '胜平负',
                    value: 'a'
                },
                ttg: {
                    desc: '总进球',
                    value: 's5'
                },
                hhad: {
                    desc: '让球胜平负',
                    value: function() {
                        return ranNumber - 5 > 0 ? 'ch' : (ranNumber - 5 == 0 ? 'cd' : 'ca');
                    }()
                },
                hafu: {
                    desc: '半全场',
                    value: ['da','aa'],
                    desc4value:'平负或负负'
                },
                crs:{
                    desc:'比分',
                    value:'0005'
                }
            },
            '1:5': {
                had: {
                    desc: '胜平负',
                    value: 'a'
                },
                ttg: {
                    desc: '总进球',
                    value: 's6'
                },
                hhad: {
                    desc: '让球胜平负',
                    value: function() {
                        return ranNumber - 4 > 0 ? 'ch' : (ranNumber - 4 == 0 ? 'cd' : 'ca');
                    }()
                },
                hafu: {
                    desc: '半全场',
                    value: ['ha','da','aa'],
                    desc4value:'胜负或平负或负负'
                },
                crs:{
                    desc:'比分',
                    value:'0105'
                }
            },
            '2:5': {
                had: {
                    desc: '胜平负',
                    value: 'a'
                },
                ttg: {
                    desc: '总进球',
                    value: 's7'
                },
                hhad: {
                    desc: '让球胜平负',
                    value: function() {
                        return ranNumber - 3 > 0 ? 'ch' : (ranNumber - 3 == 0 ? 'cd' : 'ca');
                    }()
                },
                hafu: {
                    desc: '半全场',
                    value: ['ha','da','aa'],
                    desc4value:'胜负或平负或负负'
                },
                crs:{
                    desc:'比分',
                    value:'0205'
                }
            },
            '-1-a(负其他)': {
                had: {
                    desc: '胜平负',
                    value: 'a'
                },
                ttg: {
                    desc: '总进球',
                    value: 's7'
                },
                hhad: {
                    desc: '让球胜平负',
                    value: function() {
                        return ranNumber - 6 > 0 ? 'ch' : (ranNumber - 6 == 0 ? 'cd' : 'ca');
                    }()
                },
                hafu: {
                    desc: '半全场',
                    value: ['ha','da','aa'],
                    desc4value:'胜负或平负或负负'
                },
                crs:{
                    desc:'比分',
                    value:'-1-a'
                }
            }

        };
    }


    /**
     * [swap 交换位置]
     * @param  [Array]  items 数组
       @param  [Number]  firstIndex 数字
       @param  [Number]  secondIndex 数字
     */
    function swap(items, firstIndex, secondIndex) {
        var temp = items[firstIndex];
        items[firstIndex] = items[secondIndex];
        items[secondIndex] = temp;
    }

    /**
     * [bubbleSort 降序排序]
     * @param  [Array]  items 数组
     */
    function bubbleSort(items) {
        var len = items.length,
            i, j, stop;
        for (i = 0; i < len; i++) {
            for (j = 0, stop = len - i; j < stop; j++) {
                try {
                    if (+(items[j].odd) < +(items[j + 1].odd)) {
                        swap(items, j, j + 1);
                    }
                } catch (e) {}
            }
        }
        return items;
    }


    var initConfig = function() {
        p.gamesListBox = $(p.gamesContainer).find('.gamesList');
        p.selectedGamesContainer = $(p.submitContainer).find('.selectedGamesContainer');
        p.selectedGamesBox = $(p.selectedGamesContainer).find('.selectedGamesBox');
        p.gatesContainer = $(p.submitContainer).find('.gatesContainer');
        p.confirmContainer = $(p.submitContainer).find('.confirmContainer');

    };
    return {
        init: function(options) {
            p = $.extend(p, null, options || {});
            initConfig();
            bindGameOddFilterEvent();
            bindWindowScrollEvent();
            bindBettingOddEvent();
            bindSelectedOddEvent();
            bindGatesSelectEvent();
            bindConfirmBettingEvent();
        }
    };
})();