<script id="gamesList" type="text/x-dot-template">
 @{{ var gameIndexNum = 0; var minLimitTime = aCustomSaleStopTimes[moment().format('YYYY-MM-DD')] ? aCustomSaleStopTimes[moment().format('YYYY-MM-DD')] : +($.inArray(moment().weekday(), [1,7]) > -1), systemNotAllowBet = (moment().hours() >= minLimitTime && moment().hours() < 9); }}
    @{{ for (var n in it) { var groupItem = it[n], bet_date = n.replace(/-/g, ''); }}
        <dl gamedate="@{{=bet_date || ''}}">
        <dt>@{{=n}} @{{=groupItem[0].week}} 12:00:00 - 次日 12:00:00 &nbsp;&nbsp;<span class="matchSize">@{{=groupItem.length }}</span> 场比赛可投注
                <span class="cuspText" bet_date="@{{=bet_date || ''}}" action_type="gamesShowSwitcher"><font>隐藏</font><i class="cusp"></i></span>
            </dt>
        @{{ for(var i=0,l=groupItem.length; i<l;i++) { var item = groupItem[i], notAllowBet = systemNotAllowBet && (moment() > moment(item.sale_stop_at)); var gameData = JSON.stringify({bn: item.bn, single: item.single, week: item.week, num: item.num, h_cn: item.h_cn_abbr, a_cn: item.a_cn_abbr}); var tip = '截止时间:' + item.sale_stop_at + '; 开赛时间:' + item.date + ' ' + item.time; var tipData = [item.time, item.sale_stop_at.split(' ')[1]].join(';'); }}
        <dd istop="@{{=(notAllowBet ? '1' : '0')}}" game="@{{=item.bn || ''}}" data=@{{=gameData || ''}} game_index="@{{=gameIndexNum || ''}}" bet_date="@{{=bet_date || ''}}" class="basicWays isStop @{{=(notAllowBet ? 'isOver' : '')}}">
            <span class="co1"><i class="jtip" inf="@{{=item.num || ''}}">@{{=item.num || ''}}</i></span>

            <span class="co2" style="background:#@{{=item.l_background_color || ''}}">
                <a href="javascript:void(0);" target="_blank" title="@{{=item.l_cn || ''}}">@{{=item.l_cn_abbr || ''}}</a>
                @{{ if(item.l_cn_abbr == "欧洲杯"){ }}
                    <span class="hotIcon" style="top:-4px">hot<i class="arrowsIcon"></i></span>
                @{{ } }}
            </span>

            <span class="co3 gameTime" data="@{{=tipData || ''}}">
                <i class="jtip" inf="@{{=tip || ''}}" title="@{{=tip || ''}}" >@{{=item.sale_stop_at.split(' ')[1]}}</i>
            </span>

            <span class="co4">
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
            @{{ if(notAllowBet) { }}
                <span class="co6 moreOptBtnBox over">
                        彩果：<b class="c_939393">暂停销售</b>
                </span>
            @{{ }else{ }}

            <span class="co6 moreOptBtnBox ">
                <div class="btnBg">
                    <div class="mcSelectBox moreOptionBtn" action_type="moreOddsSwitcherBig" game="@{{=item.bn || ''}}">
                        <a href="javascript:;" class="imitateSelect">展开比赛投注区</a>
                        <b class="fgx"></b>
                        <b class="arrow"></b>
                    </div>
                </div>
            </span>
            @{{ } }}

            <span class="co8">
                <!-- <a href="javascript:;" class="ico_square moreData">简</a><a href="http://zx.caipiao.163.com/library/football/match.html?mId=1171388&amp;hId=12306&amp;vId=3725" target="_blank" class="ico_square">析</a> -->
            </span>
        </dd>

        <dd  class="analyMore even hide" game="@{{=item.bn || ''}}" data=@{{=gameData || ''}} game_index="@{{=gameIndexNum || ''}}" bet_date="@{{=bet_date || ''}}" >
            <div class="tableCon clearfix">
            <table class="analyMoreTable">
                <colgroup><col width="45px"><col><col><col><col><col><col><col><col><col><col><col><col><col><col width="22"></colgroup>

                <tbody>
                    @{{ var wn = 'crs', oddNamesArr = Mango.Config.gameConfig.way_odds[wn]; var g = 0; }}
                        @{{ for (var k = 0, kl = oddNamesArr.length; k < kl; k++) { var oddNames = oddNamesArr[k]; }}
                        <tr class="@{{=(wn + 'Area')}}">

                            @{{ if (k==0) { }}
                            <th rowspan=@{{=kl || ''}} class="dgTips">@{{=Mango.Config.gameConfig.way_map[wn]['name']}}<br><i>单关</i></th>
                            @{{ } }}
                            @{{ for (var ki = 0, ll = oddNames.length; ki < ll; ki++) { var oddName = oddNames[ki], odd = item[wn][oddName][0], trend = item[wn][oddName][1], oddIndex = (item.bn+'_'+name); var index = wn == 'crs' ? g : ki; var oddIndex = (item.bn+'_'+oddName), cn = Mango.Config.gameConfig.way_odd_map[oddName]['name'], oddData = JSON.stringify({way: wn, name: oddName, odd: item[wn][oddName], cn: cn}); }}
                                <td action_type="chooseOdd" data=@{{=oddData || ''}} index="@{{=oddIndex || ''}}" single="1">
                                    @{{=cn || ''}}
                                    <div>@{{=odd || ''}}</div>
                                </td>
                                @{{ if (wn == 'crs') g++; }}
                            @{{ } }}
                        </tr>
                        @{{ } }}
                    </tbody>

            </table>
            </div>
        </dd>

        @{{ gameIndexNum++;} }}
        </dl>
    @{{ } }}
</script>