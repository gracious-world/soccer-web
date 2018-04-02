<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta content="webkit" name="renderer">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    @section ('metas')
    @show
    <title>
        {{ Config::get('title.prev') }}
        @section('title')
        欧洲杯玩法--竞彩网
        @show
    </title>
    @section ('styles')
        {!! style('global_reset')!!}
        {!! style('europeanCup')!!}
        {!! style('uicss') !!}
    @show

</head>
<body>
    <div class="conter">
        <div class="e_bg">
            <div class="bg01"></div>
            <div class="bg02"></div>
            <div class="bg03"></div>
            <div class="bg04"></div>
            <div class="bg05"></div>
            <div class="bg06"></div>
            <div class="bg07"></div>
            <div class="bg08"></div>
            <div class="bg09"></div>
            <div class="bg10"></div>
            <div class="bg11"></div>
        </div>
        <div class="main">
            <div class="e_top">
                <div class="e_time">
                    <div class="e_timeBg">
                        <p>比赛时间</p>
                        <p>6月10日 - 7月10日</p>
                    </div>
                </div>
                <div class="headerUser">
                    @if($iUserId)
                    <div class="user-account-balance">
                        <div>你好：<span class="userName">{!! $sUsername !!}</span><span class="user-cash balance-a" style="display: inline;"></div>
                        <div>余额：<span data-user-account-balance="" class="num" id="userBalance">{!! isset($fAvailable) ? $fAvailable : 0 !!}</span> 元</span></div>
                    </div>
                    @else
                    <div>你好：<span>游客</span></div>
                    @endif
                </div>

            </div>
            <div class="mid">
                <div class="e_nav">
                    <ul class="fix">
                        <li><a href="{!! route('games.index') !!}">混合投注</a></li>
                        <li><a href="{!! route('games.single') !!}">单关</a></li>
                        <li><a href="{!! route('games.focus') !!}">焦点赛事</a></li>

                        @if($iUserId)
                        <li class="right">
                            <a href="{!! route('programs.index', ['sort_down' => 'bought_at']) !!}" hidefocus="true" target="_self">投注记录</a>
                        </li>
                        <li class="right">
                            <a href="{!! route('user-transactions.index') !!}" hidefocus="true" target="_self">账户明细</a>
                        </li>
                        @endif

                    </ul>
                </div>
                <div class="e_cont">
                    <div class="contList">
                        <ul class="fix">
                            <li class="on">
                                <h3><b></b>2016欧洲杯冠军玩法</h3>
                            </li>
                            <li>
                                <h3><b></b>2016欧洲杯冠亚军玩法</h3>
                            </li>
                        </ul>
                    </div>
                    <div class="e_main">
                        <div class="e_mainMod fix">
                            <div class="e_contLeft e_line">
                                <hr>
                                <div class="e_single">
                                    <div class="e_inter">
                                        <div class="e_options">
                                            <hr>
                                            <dl class="fix">
                                                <dt>
                                                    <ul class="fix">
                                                        <li class="first">编号</li>
                                                        <li>球队</li>
                                                        <li>状态</li>
                                                        <li>奖金</li>
                                                        <li>概率</li>
                                                        <li class="first">编号</li>
                                                        <li>球队</li>
                                                        <li>状态</li>
                                                        <li>奖金</li>
                                                        <li>概率</li>
                                                    </ul>
                                                </dt>
                                                <div id="championBorther" class="blockformatcontext"></div>

                                            </dl>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="e_contRight">
                                <div class="e_line">
                                    <h3><strong>1</strong>选号明细<hr></h3>
                                    <div class="e_datatable">
                                        <table>
                                            <colgroup>
                                                <col width="50%" />
                                                <col/>
                                            </colgroup>
                                            <thead>
                                                <tr>
                                                    <th>球队</th>
                                                    <th>奖金</th>
                                                </tr>
                                            </thead>
                                        </table>
                                        <div id="championDetailWrap">
                                            <table>
                                                <colgroup>
                                                    <col width="50%" />
                                                    <col/>
                                                </colgroup>
                                                <tbody id="championDetail">
                                                    <tr>
                                                        <td colspan='2'>
                                                            <div>请进行投注</div>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="e_line">
                                    <h3><strong>2</strong>确认投注<hr></h3>
                                    <div class="e_betting">
                                        <div class="e_tiple">
                                            <span>我要买</span>
                                            <a href="javascript:void(0);" class="e_minusTiple" id="championMinusTiple">-</a>
                                            <input type="text" name="tiple" value="10" id="multipleChampion">
                                            <a href="javascript:void(0);" class="e_plusTtiple" id="championPlusTtiple">+</a>
                                            <span>倍</span>
                                        </div>
                                        <hr>
                                        <p>您选择了：<span class="Epaybig" id="championBet">0</span>支球队<a class="e_intertext" href="javascript:void(0);" id="clearChampionBtn">清空</a></p>
                                        <p>总金额：<span id="championBonce">0.00</span>元</p>
                                        <p>本单最高奖金：<span id="championMaxBonce">0.00</span>元</p>
                                        <div class="e_bettingBtn">
                                            <a href="javascript:void(0);" id="submitChampion">立即投注</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="e_mainMod fix" style="display:none;">
                            <div class="e_contLeft e_line">
                                <hr>
                                <div class="e_compared">
                                    <div class="e_inter">
                                        <div class="e_options">
                                            <hr>
                                            <dl class="fix">
                                                <dt>
                                                    <ul class="fix">
                                                        <li class="first">编号</li>
                                                        <li class="strw">球队</li>
                                                        <li>状态</li>
                                                        <li>奖金</li>
                                                        <li>概率</li>
                                                        <li class="first">编号</li>
                                                        <li class="strw">球队</li>
                                                        <li>状态</li>
                                                        <li>奖金</li>
                                                        <li>概率</li>
                                                    </ul>
                                                </dt>
                                                <div id="champtionshipBorder" class="blockformatcontext"></div>

                                            </dl>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="e_contRight">
                                <div class="e_line">
                                    <h3><strong>1</strong>选号明细<hr></h3>
                                    <div class="e_datatable">
                                        <table>
                                            <colgroup>
                                                <col width="50%" />
                                                <col/>
                                            </colgroup>
                                            <thead>
                                                <tr>
                                                    <th>组合</th>
                                                    <th>奖金</th>
                                                </tr>
                                            </thead>
                                        </table>
                                        <div id="championshipDetailWrap">
                                            <table>
                                                <colgroup>
                                                    <col width="50%" />
                                                    <col/>
                                                </colgroup>
                                                <tbody id="championshipDetail">
                                                    <tr>
                                                        <td colspan='2'>
                                                            <div>请进行投注</div>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="e_line">
                                    <h3><strong>2</strong>确认投注<hr></h3>
                                    <div class="e_betting">
                                        <div class="e_tiple">
                                            <span>我要买</span>
                                            <a href="javascript:void(0);" class="e_minusTiple" id="championshipMinusTiple">-</a>
                                            <input type="text" name="tiple" value="10" id="multipleChampionship">
                                            <a href="javascript:void(0);" class="e_plusTtiple" id="championshipPlusTtiple">+</a>
                                            <span>倍</span>
                                        </div>
                                        <hr>
                                        <p>您选择了：<span class="Epaybig" id="championshipBet">0</span>种组合<a class="e_intertext" href="javascript:void(0);" id="clearChampionshipBtn">清空</a></p>
                                        <p>总金额：<span id="championshipBonce">0.00</span>元</p>
                                        <p>本单最高奖金：<span id="championshipMaxBonce">0.00</span>元</p>
                                        <div class="e_bettingBtn">
                                            <a href="javascript:void(0);" id="submitChampionship">立即投注</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="e_foot">
                <p class="e_foottip">购彩有节制，请理性投注！未满18周岁的未成年不得购买彩票及兑奖</p>
                <!-- <p class="e_footlink">
                    <a href="javascript:void(0);" target="_blank">法律声明</a>
                    <a href="javascript:void(0);" target="_blank">网站导航</a>
                    <a href="javascript:void(0);" target="_blank">联系我们</a>
                    <a href="javascript:void(0);" target="_blank">在线客服</a>
                    <a class="e_linklast" href="javascript:void(0);" target="_blank">免责声明</a>
                </p> -->
                <p>本网站赛程、赛果、奖金等数据由国家体育总局体育彩票管理中心发布</p>
            </div>
        </div>
    </div>
    @include('gameCenter.rankGamesTmpl._champion')
    @include('gameCenter.rankGamesTmpl._championship')

    @section('scripts')
        @parent
        {!! script('moment') !!}
        <?php
            $sGameType = "football";
        ?>
    <script type="text/javascript">
        var userid = '{{$iUserId}}';
        var params = window.location.hash.substring(1).split(';');
        var username = params[0];
        var identity = params[1];
        var sign = params[2];
        var skin = (window.location.href).match(/skin=[0-9]/g) || 'skin=1';
        var skinType = (''+skin[0]).match(/[0-9]/g);

        // var sign = params[2] || md5('identity=' + identity + '&username=' + username + 'jU29QppbSLN');
        if(username !='') {
            var url = '{{route('signin')}}' + '#'+username+';'+identity+';'+sign;
            if (skin) url += ';' + skinType;
            window.location.href = url;
        }

        var gameConfigUrl = "{!! route('games.game-config', ['game_type' => $sGameType, 'way_type' => $sWayType]) !!}";
        var gameChampionDataUrl = "{!! route('games.game-data', ['game_type' => $sGameType, 'bet_type' => $iBetType, 'way_type' => 'chp']) !!}";
        var gameChampionshipDataUrl = "{!! route('games.game-data', ['game_type' => $sGameType, 'bet_type' => $iBetType, 'way_type' => 'fnl']) !!}";

        var bettingUrl = "{!! route('programs.betting', ['game_type' => $sGameType]) !!}"; // + '?userid=' + userid + '&username=' + username + '&identity=' + identity;
        var betType = "{!! $iBetType !!}";
        var isFocusGame = false;
    </script>
    <script type="text/javascript" src="../../js/lib/jquery-1.9.1.min.js"></script>
    <script type="text/javascript" src="../../js-min/doT.js"></script>
    <script data-main="../../js/mainRankGames" src="../../js-min/requirejs.min.js"></script>
    @show


</body>
</html>