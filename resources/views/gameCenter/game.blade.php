@extends('l.ghome')

@section('title') @if($iUserId) {!!$sBpName!!} @else 竞彩平台 @endif @parent  @stop


@section ('styles')
@parent

    {!! style('gameBase') !!}
    {!! style('gameUi') !!}
    {!! style('uicss') !!}
    {!! script('md5') !!}
@stop

@section('scripts')

@parent
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
        var gameDataUrl = "{!! route('games.game-data', ['game_type' => $sGameType, 'bet_type' => $iBetType, 'way_type' => $sWayType]) !!}";
        var bettingUrl = "{!! route('programs.betting', ['game_type' => $sGameType]) !!}"; // + '?userid=' + userid + '&username=' + username + '&identity=' + identity;
        var betType = "{!! $iBetType !!}";
        var isFocusGame = "{{ $bIsFocus }}";
        var aCustomSaleStopTimes = {!! json_encode($aCustomSaleStopTimes) !!};
        $(function() {
            if(+betType == 1) {
                $('#supportDg').remove();
                $('#poolErrorTips').html('请选择比赛下注')
            }
        })
    </script>
    <!-- <script type="text/javascript" src="../data/config.json"></script> -->
    <!-- <script type="text/javascript" src="../data/game.json"></script> -->
    <script type="text/javascript" src="../../js-min/doT.js"></script>
    <!--  <script data-main="../../js/main" src="../../js-min/requirejs.min.js"></script> -->
    <script data-main="../../js-min/main" src="../../js-min/requirejs.min.js"></script>


@stop

@section('end')
@parent


@stop


@section ('container')
    <div id="gamesContainer" class="main clearfix {!! array_key_exists($sWayType, $aWayTypeCss) ? $aWayTypeCss[$sWayType] : ' mixGg onlyBidCounts showDgTips ' !!}">
        <!--betNav-->
        <div class="betNav" id="wayFilter">
            <div class="huntou_SelectBox">
                <span class="text">选择玩法</span>
                    <div class="btn-group " role="group">
                        <a class="btn btn-default {!! (!$sWayType || $sWayType == 'all') ? 'active' : '' !!}" href="{!! route('games.' . ['index', 'single'][$iBetType]) !!}" type="all" >全部玩法</a>
                        <a class="btn btn-default {!! $sWayType == 'had' ? 'active' : '' !!}" href="{!! route('games.' . ['index', 'single'][$iBetType], ['way_type' => 'had']) !!}" type="had">胜平负/让球胜平负</a>
                        <a class="btn btn-default {!! $sWayType == 'crs' ? 'active' : '' !!}" href="{!! route('games.' . ['index', 'single'][$iBetType], ['way_type' => 'crs']) !!}" type="crs">比分</a>
                        <a class="btn btn-default {!! $sWayType == 'ttg' ? 'active' : '' !!}" href="{!! route('games.' . ['index', 'single'][$iBetType], ['way_type' => 'ttg']) !!}" type="ttg">总进球</a>
                        <a class="btn btn-default {!! $sWayType == 'hafu' ? 'active' : '' !!}" href="{!! route('games.' . ['index', 'single'][$iBetType], ['way_type' => 'hafu']) !!}" type="hafu">半全场</a>
                    </div>
            <!--
                <label inf="主队得分>客队得分=胜，主队得分=客队得分=平，主队得分<客队得分=负" class="jtip" id="onlySpf" gametype="1">
                    <i class="icoFx icoFx_active"></i>胜平负
                </label>
                <label inf="主队得分（±让球数）后，与客队得分比较后的胜平负" class="jtip" id="onlyRqSpf" gametype="2">
                    <i class="icoFx  icoFx_active"></i>让球胜平负
                </label> -->
            </div>
            <div class="moreLinks">
                <a href="{!! route('games.result') !!}" >最新开奖</a>
                <a href="{!! route('games.trend') !!}" >奖金走势</a>
                <a  href="javascript:void(0);" id="data-spdex" >指数分析</a>
                <a  href="javascript:void(0);" id="data-rule" >玩法规则</a>
            </div>
        </div>

        <!--betLeft-->
        <div class="bettingBox" >
            <div class="selectBox">
                    <div class="mcSelectBox">

                        <select name="bet_date" id="J-select-recharge">
                            @foreach ($aBetDates as $sBetDate)
                            <option value="{!! $sBetDate !!}">{!! $sBetDate !!}</option>
                            @endforeach
                        </select>
                    </div>

                <!-- <label id="hotGame"><i class="icoFx"></i>热门赛事</label> -->
                <label class="stopGame"><i class="icoFx "></i>已截止赛事</label>
                    <i class="fgx">|</i>
                <label class="choosedGamesFilter"><i class="icoFx"></i>已选择比赛</label>
                    <i class="fgx">|</i>
                <label id="supportDg"><i class="icoFx active"></i>支持单关提示 <i class="dgTips"><i class="triAngle"></i><i class="text">单</i></i></label>

                <i class="dgTips"></i>
            </div>
            <!--list harder-->
            <i class="dgTips">
                @include('gameCenter.gameTitle')
                <div class="dataHeadBg" style="height: 60px; width: 750px;display: none;"></div>
                <div class="gameSelect">
                    <!--关注赛事-->
                    <div class="dataBody  attentionOuter">
                        <div class="attentionInner" style="display:none">
                            <div class="attentionTit">我关注的赛事 (0场比赛)</div>
                        </div>
                    </div>
                    <!--赛事-->
                    <div class="dataBody  unAttention">
                        <div class="gamesList">
                            <!-- <dt>2016-01-16　周六12:00 -- 周日12:00　
                                <span class="matchSize">0</span>
                                场比赛可投注
                                <span class="cuspText">隐藏<i class="cusp"></i></span>
                            </dt> -->


                        </div>
                    </div>
                </div>

                <div class="tzTips">
                    <h2>投注提示</h2>
                    <!-- <span class="cuspText">隐藏<i class="cusp"></i></span> -->
                    <ul class="betTips">
                        <li class="c_f6c15a"><em>1、</em>竞彩足球全部玩法过关投注、混合过关投注、及比分单关投注奖金为固定奖金，赛事选择界面显示仅为当前参考奖金。实际奖金以出票时刻固定奖金为准。</li>
                        <li><em>2、</em>竞彩足球混合过关：胜平负玩法上限8关，总进球玩法支持上限6关，比分、半全场玩法支持上限4关。投注订单支持关数与所选的玩法中，串关数上限最低的那个玩法一致。
                            <div>竞彩足球混合串关数限制规则：</div>
                            混合过关方案包含胜平负（则混合过关方案最高8串）<br>
                            混合过关方案包含比分（则混合过关方案最高4串）<br>
                            混合过关方案包含总进球（则混合过关方案最高6串）<br>
                            混合过关方案包含半全场（则混合过关方案方案最高4串）
                        </li>
                        <li><em>3、</em>让球符号含义，"+"为客让主，"-"为主让客。让球数含义，即（主队得分±让球数）减客队得分，大于0为胜，等于0为平，小于0为负。</li>
                        <li><em>4、</em>竞彩足球的官方销售时间为：周一至周五09:00-00:00，周六至周日09:00-01:00。</li>
                        <li><em>5、</em>2或3场过关投注，单注最高奖金限额20万元；4或5场过关投注，单注最高奖金限额50万元；6场和6场以上过关投注，单注最高奖金限额100万元。单场投注，单注最高奖金限额10万元；</li>
                        <li><em>6、</em>竞彩足球彩果，以比赛90分钟内比分（含伤停补时）结果为准。其中投注赛事取消、中断或改期，官方比赛彩果公布或确认取消将延后36小时，对应场次奖金派发或退款将同步延后处理；取消比赛的任何结果都算对，固定奖金按照1计算。</li>
                        <li><em>7、</em>让球胜平负玩法最多过8关，总进球数玩法最多过6关，比分和半全场胜平负玩法最多过4关。</li>
                        <li><em>8、</em>竞彩足球混合过关：胜平负、让分胜负、大小分玩法支持上限8关，胜分差玩法支持上限4关。投注订单支持关数与所选的玩法中，串关数上限最低的那个玩法一致。</li>
                        <li><em>9、</em>竞彩足球比分玩法是唯一可进行单关固定奖金投注的玩法，在比分玩法页面中可同时进行单关+过关的固定奖金投注。</li>
                    </ul>
                </div>
            </i>
        </div>
        <!--betright-->

            <div class="asideBox"  id="submitContainer">
                <div class="floatBox">
                    <!--第一步-->
                    <div class="selectedGamesContainer unselect">
                        <h2><i>1</i>选号明细</h2>
                        <table>
                            <colgroup><col width="65"><col><col width="30"><col><col width="35"></colgroup>
                            <thead>
                                <tr>
                                    <th>场次</th>
                                    <th colspan="3">主队　VS　客队</th>
                                    <th>胆<i class="questionMark jtip" inf="将您认为一定猜中的比赛设为&quot;胆&quot;,只保留<br />  包含该场次的投注，从而节省投注金额" tip="#tip1453180518773409"></i></th>
                                </tr>
                            </thead>
                        </table>

                        <div class="scrollMoni" style="height: 130px;">
                            <table id="selectGamePool">
                                <colgroup><col width="65"><col><col width="20"><col><col width="35"></colgroup>
                                <tbody class="selectedGamesBox"></tbody>
                            </table>
                        </div>
                        <div class="unSeleTips">请在左侧列表选择投注比赛</div>
                    </div>
                    <!--第二步 unselect -->
                    <div class="gatesContainer unselect">
                        <h2><i>2</i>过关方式</h2>
                        <div class="methodContent">
                            <ul class="guoguanTab clearfix" style={!! ($sPageName == 'singleGames') ? 'display:none;':''!!}>
                                <li class="active" gate_type="m_1" action_type="gateSwitcher">自由过关</li>
                                <li gate_type="m_n" action_type="gateSwitcher">组合过关</li>
                            </ul>

                                <ul class="guoguanList clearfix active" gate_type="m_1" >
                                    <!-- <li inf="至少猜中2场可中奖" class="jtip">
                                    <i class="icoFx" value="2_1" data-method="2_1" disabled="disabled"></i>2串1
                                    </li>
                                    <li inf="至少猜中3场可中奖" class="jtip" tip="#tip1453180615990888">
                                    <i class="icoFx" value="3_1" data-method="3_1" disabled="disabled"></i>3串1
                                    </li>
                                    <li inf="至少猜中4场可中奖" class="jtip" tip="#tip1453180614018547">
                                    <i class="icoFx" value="4_1" data-method="4_1" disabled="disabled"></i>4串1
                                    </li> -->
                                </ul>
                                <div class="scrollMoniGuoguanList" style="height: 91px;display:none;">
                                    <ul class="guoguanList  clearfix" gate_type="m_n">
                                    </ul>
                                </div>

                        </div>
                        <div class="unSeleTips" id="poolErrorTips">请选择至少2场比赛下注</div>
                    </div>
                    <!--第三步-->
                    <div class="confirmContainer unselect">
                        <h2><i>3</i>确认投注</h2>
                            <div class="qrtzBlock">
                                <div class="dott">
                                    我要买
                                    <span class="select-counter-action counter-decrease J-select-sub" ></span>
                                    <div class="choose-model select-game-statics-multiple w-1">
                                        <input class="choose-input" type="text" value="10" name="multiple" id="multipleSelector">
                                    </div>
                                    <span class="select-counter-action counter-increase J-select-add"></span>
                                    倍
                                </div>

                                <p>
                                    <a href="javascript:void(0);" class="clearBetting fr">清空</a>
                                    您选择了： <strong class="gameNum c_f6c15a">0</strong>
                                    场比赛，共<strong class="selectedBets c_f6c15a">0</strong> 注
                                </p>
                                <p>倍数：<strong class="gameMultiple">10</strong>倍 <span class="totalprice">总金额： <strong class="c_f6c15a"><span class="amount">0</span>元</strong></span></p>
                                <p>理论最高奖金:<span class="theorybonus"><strong>0.00</strong>元</span></p>
                                <!-- <p>
                                    <a href="#" class="fr" id="openDetailBonus">明细</a>
                                    <span id="bonusLabel">单注最高奖金：</span>
                                    <strong class="c_f6c15a"><span id="maxbonus">0</span>元</strong>
                                </p> -->
                                <!-- <p>
                                    <a id="bonusFilter" target="_blank" href="javascript:;" style="display: inline;">奖金优化
                                        <i inf="复式倍投神器，助您合理分配投注倍数，提高中奖收益" class="questionMark jtip"></i>
                                    </a>&nbsp;&nbsp;
                                    <a style="display: inline;" id="numFilter" target="_blank" href="javascript:;">在线过滤
                                        <i inf="使用在线过滤，将最大限度节约投注资金，助您精准命中大奖" class="questionMark filterMark jtip"></i>
                                    </a>
                                </p> -->

                                <div class="tzBtnBox">
                                @if($aDate['current_time'] >= $aDate['start_time'] && $aDate['end_time'] >= $aDate['current_time'])
                                    <a data-bet-type="1"  class="ljtzBtn" onclick="javascript:alert('{{$aDate['description']}}');" href="javascript:;"></a>
                                @else
                                     <a data-bet-type="1" id="submitBtn" class="submitBtn ljtzBtn" href="javascript:;"></a>
                                @endif
                                    <a data-bet-type="2" class="fqhmBtn" href="javascript:;" style="display: none;"></a>
                                    <i class="questionMark jtip" inf="•为避免投注退票风险，建议您尽早<br />  提交投注(大额订单建议赛前20分钟完成投注)<br />•官方存在夜间停售时段变化赔率情况，<br />  夜间提交订单请，请以出票成功出票赔率为准。"></i>
                                </div>

                                <!-- <p>风险提示</p> -->

                            </div>
                    </div>
                </div>
            </div>



    </div>
@stop






