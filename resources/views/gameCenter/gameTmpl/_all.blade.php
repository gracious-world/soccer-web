<script id="gamesList" type="text/x-dot-template">
    @{{ var hadWays = Mango.Config.gameConfig.way_odds.had[0], hhadWays = Mango.Config.gameConfig.way_odds.hhad[0], gameIndexNum = 0; var minLimitTime = aCustomSaleStopTimes[moment().format('YYYY-MM-DD')] ? aCustomSaleStopTimes[moment().format('YYYY-MM-DD')] : +($.inArray(moment().weekday(), [1,7]) > -1), systemNotAllowBet = (moment().hours() >= minLimitTime && moment().hours() < 9); }}
    @{{ for (var n in it) { var groupItem = it[n], bet_date = n.replace(/-/g, ''); }}
    <!-- 按天分组的赛事数据循环开始 -->
        <dl gamedate="@{{=bet_date || ''}}">
            <dt>@{{=n}} @{{=groupItem[0].week || ''}} 12:00:00 - 次日 12:00:00 &nbsp;&nbsp;<span class="matchSize">@{{=groupItem.length  || ''}}</span> 场比赛可投注
                <span class="cuspText" bet_date="@{{=bet_date}}" action_type="gamesShowSwitcher"><font>隐藏</font><i class="cusp"></i></span>
            </dt>
            @{{ for(var i=0,l=groupItem.length; i<l;i++) { var item = groupItem[i], notAllowBet = systemNotAllowBet && (moment() > moment(item.sale_stop_at)); var gameData = JSON.stringify({bn: item.bn, single: item.single, week: item.week, num: item.num, h_cn: item.h_cn_abbr, a_cn: item.a_cn_abbr}); var tip = '截止时间:' + item.sale_stop_at + '; 开赛时间:' + item.date + ' ' + item.time; var tipData = [item.time, item.sale_stop_at.split(' ')[1]].join(';'); }}
        <!-- 每天的赛事数据循环开始 -->
            <dd istop="@{{=(notAllowBet ? '1' : '0')}}" game="@{{=item.bn || ''}}" data=@{{=gameData || ''}} game_index="@{{=gameIndexNum || ''}}" bet_date="@{{=bet_date || ''}}" class="basicWays  isStop @{{=(notAllowBet ? 'isOver' : '')}}">
                <span class="co1"><i class="jtip" inf="@{{=item.num || 0}}">@{{=item.num}}</i></span>
                <span class="co2" style="background:#@{{=item.l_background_color || ''}}">
                    <a href="javascript:void(0);" target="_blank" title="@{{=item.l_cn}}">@{{=item.l_cn_abbr || ''}}</a>
                    @{{ if(item.l_cn_abbr == "欧洲杯"){ }}
                    <span class="hotIcon">hot<i class="arrowsIcon"></i></span>
                    @{{ } }}
                </span>
                <span class="co3 gameTime" data="@{{=tipData || ''}}">
                    <i class="jtip" inf="@{{=tip || ''}}" title="@{{=tip || ''}}" >@{{=item.sale_stop_at.split(' ')[1]}}</i>
                </span>
                <span class="co4 dgTips spfDgTips rfspfDgTips twoDgTips">
                    <a href="javascript:void(0);" target="_blank" >
                        <em class="hostTeam" title="@{{=item.h_cn || ''}}">
                            <!--<i class="c_939393">[A2]</i>-->
                            <b>@{{=item.h_cn_abbr || ''}}</b>
                        </em>
                        <div class="finalScore">VS</div>
                        <em class="guestTeam" title="@{{=item.a_cn || ''}}">
                            <b>@{{=item.a_cn_abbr || ''}}</b>
                            <!--<i class="c_939393">[A4]</i>-->
                        </em>
                    </a>
                </span>
                <span class="co6_1 btnBox towLine hadArea ">
                    <div class="line1 twoDgTips dgTips">
                        @{{ if(!+betType && item.single_ways.had && +item.single_ways.had) { }}
                            <i class="triAngle"></i>
                            <i class="text">单</i>
                        @{{ } }}
                        @{{ if(notAllowBet) { }}
                            停止销售
                        @{{ } else { }}

                            @{{ if (!item.had) { }}
                                未开售
                            @{{ } else { }}
                                @{{ if (!+betType || (+betType && +item.single_ways.had)) { }}
                                    <em class="rq">0</em>
                                    @{{ for(var hn=0, hl = hadWays.length; hn<hl; hn++) { var name = hadWays[hn], odd = item.had[name][0], trend = item.had[name][1], oddIndex = (item.bn+'_'+name), oddData = JSON.stringify({way: 'had', name: name, odd: odd, cn: Mango.Config.gameConfig.way_odd_map[name]['name']}); }}
                                        <em action_type="chooseOdd" data=@{{=oddData}} index="@{{=oddIndex || ''}}" single="@{{=item.single_ways.had || ''}}" class="@{{=(hn == hl-1 ? 'lastOne' : '')}}">
                                            @{{=odd}}
                                            <i class="@{{=(trend >= 0 ? ['c_090','c_e24949'][trend]:'')}} ">@{{=(trend >= 0 ? ['↓', '↑'][trend] : '')}}</i>
                                        </em>
                                    @{{ } }}
                                @{{ } else { }}
                                    不支持单关固定
                                @{{ } }}
                            @{{ } }}
                        @{{ } }}
                    </div>
                    <div class="line2 twoDgTips dgTips">
                    @{{ if(!+betType && +item.single_ways.hhad) { }}
                        <i class="triAngle"></i>
                        <i class="text">单</i>
                    @{{ } }}
                        @{{ if(notAllowBet) { }}
                            停止销售
                        @{{ } else { }}
                            @{{ if (!item.hhad) { }}
                                未开售
                            @{{ } else { }}
                                @{{ if (!+betType || (+betType && +item.single_ways.hhad)) { }}
                                    <em class="@{{=((item.fixed >0 )? 'c_e24949':'c_090' )}} rq">@{{=((item.fixed > 0 ? '+' : '') + item.fixed)}}</em>
                                    @{{ for(var hhn=0, hhl = hhadWays.length; hhn<hhl; hhn++) { var name = hhadWays[hhn], odd = item.hhad[name][0], trend = item.hhad[name][1], oddIndex = (item.bn+'_'+name), oddData = JSON.stringify({way: 'hhad', name: name, odd: odd, cn: Mango.Config.gameConfig.way_odd_map[name]['name'],fixed:item.fixed}); }}
                                        <em action_type="chooseOdd" data=@{{=oddData || ''}} index="@{{=oddIndex || ''}}" single="@{{=item.single_ways.hhad || ''}}" class="@{{=(hhn == hhl-1 ? 'lastOne' : '')}}">
                                            @{{=odd}}
                                            <i class="@{{=(trend >= 0 ? ['c_090','c_e24949'][trend]:'')}} ">@{{=(trend >= 0 ? ['↓', '↑'][trend] : '')}}</i>
                                        </em>
                                    @{{ } }}
                                @{{ } else { }}
                                    不支持单关固定
                                @{{ } }}
                            @{{ } }}
                        @{{ } }}
                    </div>
                </span>
                <span class="co6_2 hadArea">
                    <div class="openBtn" action_type="@{{=(notAllowBet ? '':'moreOddsSwitcher') }}" game="@{{=item.bn || ''}}">
                        展开<i class="arrow"></i>
                    </div>
                </span>
                <span class="co7 towLine hadArea">
                    <div class="line1 bidCounts">
                    @{{ if (!item.had) { }}
                        --
                    @{{ } else { }}
                        @{{ for(var hn=0, hl = hadWays.length; hn<hl; hn++) { var name = hadWays[hn], euroOdd = item.had[name][2]; }}
                            @{{ if(notAllowBet || !(!+betType || (+betType && +item.single_ways.had))) { }}
                                <em> -- </em>
                            @{{ }else{ }}
                                <em way="had"  num="@{{=item.num || ''}}"><a href="javascript:;" class="maxNum">@{{=euroOdd}}</a></em>
                            @{{ } }}
                        @{{ } }}
                    @{{ } }}
                    </div>
                    <div class="line2 bidCounts">
                        @{{ if (!item.had) { }}
                            --
                        @{{ } else { }}
                            @{{ for(var hn=0, hl = hadWays.length; hn<hl; hn++) { var name = hadWays[hn], euroOdd = item.had[name][2]; }}
                                @{{ if(notAllowBet || !(!+betType || (+betType && +item.single_ways.hhad))) { }}
                                    <em> -- </em>
                                @{{ }else{ }}
                                    <em way="hhad"  num="@{{=item.num || ''}}"><a href="javascript:;" class="maxNum">@{{=euroOdd}}</a></em>
                                @{{ } }}
                            @{{ } }}
                        @{{ } }}
                    </div>
                </span>
                <span class="co8">
                    <!-- <a href="javascript:;" class="ico_square moreData">简</a><a href="http://zx.caipiao.163.com/library/football/match.html?mId=1171388&amp;hId=12306&amp;vId=3725" target="_blank" class="ico_square">析</a> -->
                </span>
            </dd>
            <dd  game="@{{=item.bn || ''}}" data=@{{=gameData || ''}} game_index="@{{=gameIndexNum || ''}}" bet_date="@{{=bet_date || ''}}" class="analyMore even hide">
                <div class="tableCon clearfix">
                    <table class="analyMoreTable">
                        <colgroup><col width="67px"></colgroup>
                        <tbody>
                            @{{ for (var wn in Mango.Config.gameConfig.way_odds) { }} <!-- 其他玩法赔率数据循环开始 -->
                                @{{ if ($.inArray(wn, Mango.Config.gameConfig.extra_ways) > -1) { }}
                                    @{{ if (!item[wn]) { }}
                                        <tr class="@{{=(wn + 'Area')}}">
                                            <th class="dgTips">@{{=Mango.Config.gameConfig.way_map[wn]['name']}}<br><i>单关</i></th>
                                            <td colspan="13">未开售</td>
                                        </tr>
                                    @{{ } else { }}
                                        @{{ if(!+betType || (+betType && +item.single_ways[wn])) { }}
                                            @{{ var oddNamesArr = Mango.Config.gameConfig.way_odds[wn]; var g = 0;}}
                                            @{{ for (var k = 0, kl = oddNamesArr.length; k < kl; k++) { var oddNames = oddNamesArr[k]; }}
                                                <tr class="@{{=(wn + 'Area')}}">
                                                    @{{ if (k==0) { }}
                                                    <th rowspan=@{{=kl}} class="dgTips">@{{=Mango.Config.gameConfig.way_map[wn]['name']}}
                                                        <br>
                                                        @{{ if (!+betType) { }}
                                                        <i>单关</i>
                                                        @{{ } }}
                                                    </th>
                                                    @{{ } }}
                                                    @{{ for (var ki = 0, ll = oddNames.length; ki < ll; ki++) { var oddName = oddNames[ki], odd = item[wn][oddName][0], trend = item[wn][oddName][1]; var index = wn == 'crs' ? g : ki; var oddIndex = (item.bn+'_'+oddName), cn = Mango.Config.gameConfig.way_odd_map[oddName]['name'], oddData = JSON.stringify({way: wn, name: oddName, odd: item[wn][oddName], cn: cn}); }}
                                                        <td action_type="chooseOdd" data=@{{=oddData || ''}} index="@{{=oddIndex || ''}}" single="@{{=item.single_ways[wn] || ''}}">
                                                            @{{=cn || ''}}
                                                            <div>@{{=odd || ''}}</div>
                                                        </td>
                                                        @{{ if (wn == 'crs') g++; }}
                                                    @{{ } }}
                                                </tr>
                                            @{{ } }}
                                        @{{ } else { }}
                                            不支持单关固定
                                        @{{ } }}
                                    @{{ } }}
                                @{{ } }}
                            @{{ } }} <!-- 其他玩法赔率数据循环结束 -->
                        </tbody>
                    </table>
                </div>
            </dd>

            @{{ gameIndexNum++; }}
            @{{ } }}
            <!-- 每天的赛事数据循环结束 -->
        </dl>
    @{{ } }}
    <!-- 按天分组的赛事数据循环结束 -->
</script>