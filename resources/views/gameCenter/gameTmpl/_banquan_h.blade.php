<script id="gamesHistoryList" type="text/x-dot-template">
@{{ var hafuWays = Mango.Config.gameConfig.way_odds.hafu[0], gameIndexNum = 0; }}
    @{{ for (var n in it) { var groupItem = it[n], bet_date = n.replace(/-/g, ''); }}
    <dl gamedate="@{{=bet_date}}">
    <dt>@{{=n}} @{{=groupItem[0].week}} 12:00:00 - 次日 12:00:00 &nbsp;&nbsp;<span class="matchSize">0</span> 场比赛可投注
                <span class="cuspText" bet_date="@{{=bet_date}}" action_type="gamesShowSwitcher"><font>隐藏</font><i class="cusp"></i></span>
            </dt>
    @{{ for(var i=0,l=groupItem.length; i<l;i++) { var item = groupItem[i]; var gameData = JSON.stringify({bn: item.bn, single: item.single, week: item.week, num: item.num, h_cn: item.h_cn_abbr, a_cn: item.a_cn_abbr}); var tip = '截止时间:' + item.sale_stop_at + '; 开赛时间:' + item.date + ' ' + item.time; var tipData = [item.time, item.sale_stop_at.split(' ')[1]].join(';'); }}
    <dd game="@{{=item.bn}}" data=@{{=gameData}} game_index="@{{=gameIndexNum}}" bet_date="@{{=bet_date}}" class="league_470">
        <span class="co1"><i class="jtip" inf="@{{=item.num}}">@{{=item.num}}</i></span>
        <span class="co2" style="background:#@{{=item.l_background_color}}">
            <a href="javascript:void(0);" target="_blank" title="@{{=item.l_cn}}">@{{=item.l_cn_abbr}}</a>
        </span>
        <span class="co3 gameTime" data="@{{=tipData}}">
                <i class="jtip" inf="@{{=tip}}" title="@{{=tip}}" >@{{=item.sale_stop_at.split(' ')[1]}}</i>
            </span>
        <span class="co4">
            <a href="javascript:void(0);" target="_blank" >
                <em class="hostTeam" title="@{{=item.h_cn}}">
                    <!--<i class="c_939393">[A2]</i>-->
                    <b>@{{=item.h_cn_abbr}}</b>
                </em>
                 @{{ if(item.status == 5) { }}
                    <div class="finalScore">
                        @{{= (item.h_score + ':' + item.a_score) }}
                    </div>
                    @{{ } }}
                <em class="guestTeam" title="@{{=item.a_cn}}">
                    <b>@{{=item.a_cn_abbr}}</b>
                    <!--<i class="c_939393">[A4]</i>-->
                </em>
            </a>
        </span>

        <span class="co6 btnBox  ">
            <div class="line1">

            @{{ for(var hn=0, hl = hafuWays.length; hn<hl; hn++) { var name = hafuWays[hn], hcn = Mango.Config.gameConfig.way_odd_map[name]['name']; }}
                    <em class="@{{=(name == item.hafu ? 'c_f6c15a' : (hn == hl-1 ? 'lastOne' : ''))}}">
                        @{{=hcn }}
                    </em>
                    @{{ } }}
            </div>
        </span>
        <span class="co8">
            <!-- <a href="javascript:;" class="ico_square moreData">简</a><a href="http://zx.caipiao.163.com/library/football/match.html?mId=1171388&amp;hId=12306&amp;vId=3725" target="_blank" class="ico_square">析</a> -->
        </span>

    </dd>
    @{{ gameIndexNum++;} }}
    </dl>
  @{{ } }}
</script>