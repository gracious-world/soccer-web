<script id="gamesList" type="text/x-dot-template">
    @{{ var hadWays = Mango.Config.gameConfig.way_odds.had[0], hhadWays = Mango.Config.gameConfig.way_odds.hhad[0], gameIndexNum = 0; }}
    @{{ for (var n in it) { var groupItem = it[n], bet_date = n.replace(/-/g, ''); }}
        <dt>@{{=n}}　
            <span class="cuspText" bet_date="@{{=bet_date}}" is_show=1 action_type="gamesShowSwitcher">隐藏<i class="cusp"></i></span>
        </dt>
        @{{ for(var i=0,l=groupItem.length; i<l;i++) { var item = groupItem[i]; var gameData = JSON.stringify({bn: item.bn, single: item.single, week: item.week, num: item.num, h_cn: item.h_cn_abbr, a_cn: item.a_cn_abbr}); }}
        <dd game="@{{=item.bn}}" data=@{{=gameData}} game_index="@{{=gameIndexNum}}" bet_date="@{{=bet_date}}" class="basicWays">
            <span class="co1"><i class="jtip" inf="@{{=item.num}}">@{{=item.num}}</i></span>
            <span class="co2" style="background:#@{{=item.l_background_color}}">
                <a href="##" target="_blank" title="@{{=item.l_cn}}">@{{=item.l_cn_abbr}}</a>
            </span>
            <span class="co3 gameTime">
                <i class="jtip" inf="截止时间：@{{=(item.date + ' ' + item.time)}}">@{{=item.time}}</i>
            </span>
            <span class="co4">
                <a href="##" target="_blank" >
                    <em class="hostTeam" title="@{{=item.h_cn}}">
                        <!--<i class="c_939393">[A2]</i>-->
                        <b>@{{=item.h_cn_abbr}}</b>
                    </em>
                    <em class="guestTeam" title="@{{=item.a_cn}}">
                        <b>@{{=item.a_cn_abbr}}</b>
                        <!--<i class="c_939393">[A4]</i>-->
                    </em>
                </a>
                @{{ if(+item.single) { }}
                    <i class="triAngle"></i>
                    <i class="text">单</i>
                @{{ } }}
            </span>
            <span class="co6_1 btnBox towLine hadArea">
                <div class="line1">
                    @{{ if (item.had) { }}
                    <em class="rq">0</em>
                    @{{ for(var hn=0, hl = hadWays.length; hn<hl; hn++) { var name = hadWays[hn], odd = item.had[name], oddIndex = (item.bn+'_'+name), oddData = JSON.stringify({way: 'had', name: name, odd: odd, cn: Mango.Config.gameConfig.way_odd_map[name]['name']}); }}
                    <em action_type="chooseOdd" data=@{{=oddData}} index="@{{=oddIndex}}" single="@{{=item.single}}" class="@{{=(hn == hl-1 ? 'lastOne' : '')}}">@{{=odd}}</em>
                    @{{ } }}
                    @{{ } else { }}
                    未开售
                    @{{ } }}
                </div>
                <div class="line2">
                    @{{ if (item.hhad) { }}
                    <em class="@{{=((item.fixed >0 )? 'c_e24949':'c_090' )}} rq">@{{=((item.fixed > 0 ? '+' : '') + item.fixed)}}</em>
                    @{{ for(var hhn=0, hhl = hhadWays.length; hhn<hhl; hhn++) { var name = hhadWays[hhn], odd = item.hhad[name], oddIndex = (item.bn+'_'+name), oddData = JSON.stringify({way: 'hhad', name: name, odd: odd, cn: Mango.Config.gameConfig.way_odd_map[name]['name']}); }}
                    <em action_type="chooseOdd" data=@{{=oddData}} index="@{{=oddIndex}}" single="@{{=item.single}}" class="@{{=(hhn == hhl-1 ? 'lastOne' : '')}}">@{{=odd}}</em>
                    @{{ } }}
                    @{{ } else { }}
                    未开售
                    @{{ } }}
                </div>
            </span>
            <span class="co6_2 hadArea">
                <div class="openBtn" action_type="moreOddsSwitcher" game="@{{=item.bn}}">
                    展开<i class="arrow"></i>
                </div>
            </span>
            <span class="co7 towLine hadArea">
                <div class="line1 bidCounts">
                    @{{ for(var hn=0, hl = hadWays.length; hn<hl; hn++) { }}
                    <em way="had"  num="@{{=item.num}}"><a href="javascript:;" class="maxNum">0%</a></em>
                    @{{ } }}
                </div>
                <div class="line2 bidCounts">
                    @{{ for(var hhn=0, hhl = hhadWays.length; hhn<hhl; hhn++) { }}
                    <em way="had"  num="@{{=item.num}}"><a href="javascript:;" class="maxNum">0%</a></em>
                    @{{ } }}
                </div>
            </span>
            <span class="co8">
                <a href="javascript:;" class="ico_square moreData">简</a><a href="http://zx.caipiao.163.com/library/football/match.html?mId=1171388&amp;hId=12306&amp;vId=3725" target="_blank" class="ico_square">析</a>
            </span>
        </dd>
        <dd game="@{{=item.bn}}" data=@{{=gameData}} game_index="@{{=gameIndexNum}}" bet_date="@{{=bet_date}}" class="analyMore even hide">
            <div class="tableCon clearfix">
                <table class="analyMoreTable">
                    <colgroup><col width="67px"></colgroup>
                    <tbody>
                        @{{ for (var wn in Mango.Config.gameConfig.way_odds) { if ($.inArray(wn, Mango.Config.gameConfig.extra_ways) > -1) { var oddNamesArr = Mango.Config.gameConfig.way_odds[wn]; var g = 0;}}
                            @{{ for (var k = 0, kl = oddNamesArr.length; k < kl; k++) { var oddNames = oddNamesArr[k]; }}
                            <tr class="@{{=(wn + 'Area')}}">

                                @{{ if (k==0) { }}
                                <th rowspan=@{{=kl}} class="dgTips">@{{=Mango.Config.gameConfig.way_map[wn]['name']}}<br><i>单关</i></th>
                                @{{ } }}
                                @{{ for (var ki = 0, ll = oddNames.length; ki < ll; ki++) { var oddName = oddNames[ki]; var index = wn == 'crs' ? g : ki; var oddIndex = (item.bn+'_'+oddName), cn = Mango.Config.gameConfig.way_odd_map[oddName]['name'], oddData = JSON.stringify({way: wn, name: oddName, odd: item[wn][oddName], cn: cn}); }}
                                    <td action_type="chooseOdd" data=@{{=oddData}} index="@{{=oddIndex}}" single="1">
                                        @{{=cn}}
                                        <div>@{{=item[wn][oddName]}}</div>
                                    </td>
                                    @{{ if (wn == 'crs') g++; }}
                                @{{ } }}
                            </tr>
                            @{{ } }}
                            @{{ } }}
                        @{{ } }}
                    </tbody>
                </table>
            </div>
        </dd>

        @{{ gameIndexNum++;} }}
    @{{ } }}
</script>

<script id="selectedGames" type="text/x-dot-template">
    <tr game_bn="@{{=it.bn}}" single="@{{=it.single}}" game_index="@{{=it.gameIndexNum}}" class="gameTitle">
        <th>
            <a class="icoDel" action_type="cancelGame" index="@{{=it.bn}}"></a>@{{=(it.week + it.num)}}
        </th>
        <th class="tr">@{{=it.h_cn}}</th>
        <th></th>
        <th class="tl">@{{=it.a_cn}}</th>
        <th><a href="javascript:;" class="icoDan" action_type="danSettingEvent" disabled="disabled"></a></th>
    </tr>
    <tr class="gameOption" game_bn="@{{=it.bn}}" game_index="@{{=it.gameIndexNum}}">
        <td colspan="5" class="betList">
        </td>
    </tr>
</script>

<script id="selectedOdds" type="text/x-dot-template">
    <a href="javascript:void(0);" class="@{{=(Mango.Config.oddBlockClass[it.name] || 'blockYel')}}" single="@{{=it.single}}" index="@{{=it.index}}" action_type="cancelOdd" way="@{{=it.way}}">@{{=it.cn}}</a>
</script>

<script id="gatesBox" type="text/x-dot-template">
    @{{ for(var i=0,l=it.data.length; i<l;i++) { var item = it.data[i], minPrizeGates = item.combination.split(',').min(); }}
    <li title="至少猜中@{{=minPrizeGates}}场可中奖" class="jtip" gate="@{{=item.identity}}" combination="@{{=item.combination}}" max_dan="@{{=(minPrizeGates-1)}}" action_type="gateEvent">
        <i class="@{{=['icoFx', 'icoDx'][it.gate_type]}}" disabled="disabled"></i>
        @{{=item.name}}
    </li>
    @{{ } }}
</script>

<script id="gamesHistoryList" type="text/x-dot-template">

@{{ var hadWays = Mango.Config.gameConfig.way_odds.had[0], hhadWays = Mango.Config.gameConfig.way_odds.hhad[0], gameIndexNum = 0; }}
    @{{ for (var n in it) { var groupItem = it[n], bet_date = n.replace(/-/g, ''); }}
        <dt>@{{=n}}　
            <span class="cuspText" bet_date="@{{=bet_date}}" is_show=1 action_type="gamesShowSwitcher">隐藏<i class="cusp"></i></span>
        </dt>
        @{{ for(var i=0,l=groupItem.length; i<l;i++) { var item = groupItem[i]; var gameData = JSON.stringify({bn: item.bn, single: item.single, week: item.week, num: item.num, h_cn: item.h_cn_abbr, a_cn: item.a_cn_abbr}); }}
        <dd game="@{{=item.bn}}" data=@{{=gameData}} game_index="@{{=gameIndexNum}}" bet_date="@{{=bet_date}}" class="basicWays isStop">
            <span class="co1"><i class="jtip" inf="@{{=item.num}}">@{{=item.num}}</i></span>
            <span class="co2" style="background:#@{{=item.l_background_color}}">
                <a href="##" target="_blank" title="@{{=item.l_cn}}">@{{=item.l_cn_abbr}}</a>
            </span>
            <span class="co3 gameTime">
                <i class="jtip" inf="截止时间：@{{=(item.date + ' ' + item.time)}}">@{{=item.time}}</i>
            </span>
            <span class="co4">
                <a href="##" target="_blank">
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
                    <em class="c_090 rq">@{{=((item.fixed > 0 ? '+' : '') + item.fixed)}}</em>
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
                    <em way="had"  num="@{{=item.num}}"><a href="javascript:;" class="maxNum">0%</a></em>
                    @{{ } }}
                </div>
                <div class="line2 bidCounts">
                    @{{ for(var hhn=0, hhl = hhadWays.length; hhn<hhl; hhn++) { }}
                    <em way="had"  num="@{{=item.num}}"><a href="javascript:;" class="maxNum">0%</a></em>
                    @{{ } }}
                </div>
            </span>
            <span class="co8">
                <a href="javascript:;" class="ico_square moreData">简</a><a href="http://zx.caipiao.163.com/library/football/match.html?mId=1171388&amp;hId=12306&amp;vId=3725" target="_blank" class="ico_square">析</a>
            </span>
        </dd>
        <dd game="@{{=item.bn}}" data=@{{=gameData}} game_index="@{{=gameIndexNum}}" bet_date="@{{=bet_date}}" class="analyMore even hide">
            <div class="tableCon clearfix">
                <table class="analyMoreTable">
                    <colgroup><col width="67px"></colgroup>
                    <tbody>
                        @{{ for (var wn in Mango.Config.gameConfig.way_odds) { if ($.inArray(wn, Mango.Config.gameConfig.extra_ways) > -1) { var oddNamesArr = Mango.Config.gameConfig.way_odds[wn]; var g = 0;}}
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
                            @{{ } }}
                        @{{ } }}
                    </tbody>
                </table>
            </div>
        </dd>

        @{{ gameIndexNum++;} }}
    @{{ } }}
</script>