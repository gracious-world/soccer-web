<script id="gamesHistoryList" type="text/x-dot-template">

@{{ var hadWays = Mango.Config.gameConfig.way_odds.had[0], hhadWays = Mango.Config.gameConfig.way_odds.hhad[0], gameIndexNum = 0; }}
    @{{ for (var n in it) { var groupItem = it[n], bet_date = n.replace(/-/g, ''); }}
        <dl gamedate="@{{=bet_date}}">
        <dt>@{{=n}} @{{=groupItem[0].week}} 12:00:00 - 次日 12:00:00 &nbsp;&nbsp;<span class="matchSize">0</span> 场比赛可投注
                <span class="cuspText" bet_date="@{{=bet_date}}" action_type="gamesShowSwitcher"><font>隐藏</font><i class="cusp"></i></span>
            </dt>
        @{{ for(var i=0,l=groupItem.length; i<l;i++) { var item = groupItem[i]; var gameData = JSON.stringify({bn: item.bn, single: item.single, week: item.week, num: item.num, h_cn: item.h_cn_abbr, a_cn: item.a_cn_abbr}); var tip = '截止时间:' + item.sale_stop_at + '; 开赛时间:' + item.date + ' ' + item.time; var tipData = [item.time, item.sale_stop_at.split(' ')[1]].join(';'); }}
        <dd game="@{{=item.bn}}" data=@{{=gameData}} game_index="@{{=gameIndexNum}}" bet_date="@{{=bet_date}}" class="basicWays isStop">
            <span class="co1"><i class="jtip" inf="@{{=item.num}}">@{{=item.num}}</i></span>
            <span class="co2" style="background:#@{{=item.l_background_color}}">
                <a href="javascript:void(0);" target="_blank" title="@{{=item.l_cn}}">@{{=item.l_cn_abbr}}</a>
            </span>
            <span class="co3 gameTime" data="@{{=tipData}}">
                <i class="jtip" inf="@{{=tip}}" title="@{{=tip}}" >@{{=item.sale_stop_at.split(' ')[1]}}</i>
            </span>
            <span class="co4">
                <a href="javascript:void(0);" target="_blank">
                    <em class="hostTeam" title="@{{=item.h_cn}}">
                        <b>@{{=item.h_cn_abbr}}</b>
                    </em>
                    @{{ if(item.status == 5) { }}
                    <div class="finalScore">
                        @{{= (item.h_score + ':' + item.a_score) }}
                    </div>
                    @{{ } }}
                    <em class="guestTeam" title="@{{=item.a_cn}}">
                        <b>@{{=item.a_cn_abbr}}</b>
                    </em>
                </a>
            </span>
            <span class="co6_1 btnBox towLine over">
                <div class="line1">
                    @{{ if (item.had) { }}
                    <em class="rq">0</em>
                    @{{ for(var hn=0, hl = hadWays.length; hn<hl; hn++) { var name = hadWays[hn], hcn = Mango.Config.gameConfig.way_odd_map[name]['name']; }}
                    <em class="@{{=(name == item.had ? 'c_f6c15a' : (hn == hl-1 ? 'lastOne' : ''))}}">

                        @{{=hcn}}

                    </em>
                    @{{ } }}
                    @{{ } else { }}
                    未开售
                    @{{ } }}
                </div>
                <div class="line2">
                    @{{ if (item.hhad) { }}
                    <em class="@{{=((item.fixed >0 )? 'c_e24949':'c_090' )}} rq">@{{=((item.fixed > 0 ? '+' : '') + item.fixed)}}</em>
                    @{{ for(var hhn=0, hhl = hhadWays.length; hhn<hhl; hhn++) { var name = hhadWays[hhn], hhcn = Mango.Config.gameConfig.way_odd_map[name]['name']; }}
                    <em class="@{{=(name == item.hhad ? 'c_f6c15a' : (hhn == hhl-1 ? 'lastOne' : ''))}}">@{{=hhcn}}</em>
                    @{{ } }}
                    @{{ } else { }}
                    未开售
                    @{{ } }}
                </div>
            </span>
            <span class="co6_2 over">
                <div class="openBtn" action_type="moreOddsSwitcher" game="@{{=item.bn}}">
                    展开<i class="arrow"></i>
                </div>
            </span>
            <span class="co7 towLine over">
                <div class="line1 bidCounts">
                    @{{ for(var hn=0, hl = hadWays.length; hn<hl; hn++) { }}
                    <em way="had"  num="@{{=item.num}}">--</em>
                    @{{ } }}
                </div>
                <div class="line2 bidCounts">
                    @{{ for(var hhn=0, hhl = hhadWays.length; hhn<hhl; hhn++) { }}
                    <em way="had"  num="@{{=item.num}}">--</em>
                    @{{ } }}
                </div>
            </span>
            <span class="co8">
                <!-- <a href="javascript:;" class="ico_square moreData">简</a><a href="http://zx.caipiao.163.com/library/football/match.html?mId=1171388&amp;hId=12306&amp;vId=3725" target="_blank" class="ico_square">析</a>
     -->        </span>
        </dd>
        <dd game="@{{=item.bn}}" data=@{{=gameData}} game_index="@{{=gameIndexNum}}" bet_date="@{{=bet_date}}" class="analyMore even hide">
            <div class="tableCon clearfix">
                <table class="analyMoreTable">
                    <colgroup><col width="67px"></colgroup>
                    <tbody>
                        @{{ for (var wn in Mango.Config.gameConfig.way_odds) { }}
                            @{{ if ($.inArray(wn, Mango.Config.gameConfig.extra_ways) > -1) { }}
                                @{{ if (item[wn]) { }}
                                    @{{ var oddNamesArr = Mango.Config.gameConfig.way_odds[wn]; var g = 0;}}
                                    @{{ for (var k = 0, kl = oddNamesArr.length; k < kl; k++) { var oddNames = oddNamesArr[k]; }}
                                        <tr class="over">
                                            @{{ if (k==0) { }}
                                            <th rowspan=@{{=kl}} class="dgTips">@{{=Mango.Config.gameConfig.way_map[wn]['name']}}</th>
                                            @{{ } }}
                                            @{{ for (var ki = 0, ll = oddNames.length; ki < ll; ki++) { var oddName = oddNames[ki], cn = Mango.Config.gameConfig.way_odd_map[oddName]['name']; }}
                                                <td single="1" class="@{{=(oddName == item[wn] ? 'c_f6c15a' : '')}}">
                                                    @{{=cn}}
                                                    <div></div>
                                                </td>
                                                @{{ if (wn == 'crs') g++; }}
                                            @{{ } }}
                                        </tr>
                                    @{{ } }}
                                @{{ } else { }}
                                    <tr class="over">
                                        <th class="dgTips">@{{=Mango.Config.gameConfig.way_map[wn]['name']}}</th>
                                        <td colspan="13">未开售</td>
                                    </tr>
                                @{{ } }}
                            @{{ } }}
                        @{{ } }}
                    </tbody>
                </table>
            </div>
        </dd>

        @{{ gameIndexNum++;} }}
        </dl>
    @{{ } }}
</script>