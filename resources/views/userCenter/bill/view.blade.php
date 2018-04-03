@extends('l.ghome')

@section('title') @parent  @stop

@section ('styles')
@parent

    {!! style('gameBase') !!}
    {!! style('gameUi') !!}
    {!! style('uicss') !!}
@stop

@section('end')
    {!! script('easing') !!}
    {!! script('uiScript') !!}
@parent
    <script type="text/javascript">
        $(function(){
            $('#J-date-start').focus(function(){
                (new UIscript.DatePicker({input:'#J-date-start',isShowTime:true, startYear:2013})).show();
            });
            var detailTable = $('#detail-table');
            $(document).on('click','.game-detail',function(){
                detailTable.toggle();
            });
        });

    </script>
@stop


@section ('container')

<div class="Order_details">

    <div class="nav-bg nav-bg-tab">
        <div class="title-normal">
            <!-- 投注记录 - 订单详情 -->
            竞彩足球 {!! $data->gate_type_display . '[' . $data->gate . ']' !!} 第 XXXX期自购方案
        </div>
        <a href="javascript:history.back(-1)" class="mg-back"  >返回</a>
    </div>
    <div class="ordmain">
        <!-- <div class="planned-box planned-{!! $data->status == 0 ? 1 : (($data->status > 0 && $data->status < 3) ? 2 : 4) !!}">
            <ul>
                <li class="p-1">
                    <em>1<font>发起<span> [{!! $data->bought_at !!}]</span></font></em>
                </li>
                <li class="p-2">
                    <em>2<font>
                    {!! $data->status == 0 ? '等待出票' : ($data->status == 1 ? '出票失败' : '已出票') !!}
                        @if ($data->status > 1)
                        （<a href="{!! route('bills.ticket-detail', [$data->id]) !!}">查看出票详情</a>）
                        @endif
                        </font>
                    </em>
                </li>
                <li class="p-3">
                    <em>3<font>{!! $data->counted_at ? ' 已计奖' : '等待计奖' !!}</font></em>
                </li>
                <li class="last-child p-4">
                    <em>4<font>完成</font></em>
                </li>
            </ul>
            <div class="planned-line">
                <span></span>
            </div>
        </div> -->

        <table class="ord-tabletop">
            <colgroup>
                <col width="25%" />
                <col width="25%" />
                <col width="25%" />
                <col />
            </colgroup>
            <tbody>
                <tr>
                    <th colspan="4">方案编号：[<span>{!! $data->sn !!}</span>]<a href="javascript:void(0);" class="game-detail">方案明细</a></th>
                </tr>
                <tr>
                    <td>方案金额：<b>￥<span>{!! $data->amount_display !!}</span></b>元</td>
                    <td>倍数：<span>{!! $data->multiple !!}</span></td>
                    <td>游戏类型：<span>{!! $data->gate_type_display . '[' . $data->gate . ']' !!}</span></td>
                    @if($data['status'] > \App\Models\Bet\Bill::STATUS_SYSTEM_CANCELED)
                    <td>奖金：<b>￥<span>{!! $data->prize_display !!}</span></b>元</td>
                   @elseif($data['status'] == \App\Models\Bet\Bill::STATUS_TICKET_PRINTED || $data['status'] == \App\Models\Bet\Bill::STATUS_WAITING)
                    <td>奖金：&nbsp;</td>
                   @else
                    <td>奖金： — </td>
                    @endif
                </tr>
            </tbody>
        </table>

        <table class="ord-tableother">
            <colgroup>
                <col width="15%" />
                <col width="7%" />
                <col width="10%" />
                <col width="9%" />
                <col width="5%" />
                <col width="7%" />
                <col width="18%">
                <col width="5%" />
                <col width="5%" />
                <col width="7%" />
            </colgroup>
            <thead>
                <tr>
                    <th colspan="10">方案详情</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <th>编号</th>
                    <th>赛事</th>
                    <th>比赛时间</th>
                    <th>主队</th>
                    <th>让球</th>
                    <th>客队</th>
                    <th>投注内容（参考奖金指数）</th>
                    <th>胆码</th>
                    <th>比分</th>
                    <th>彩果</th>
                </tr>
                @foreach($oRelatedGames as $oRelatedGame)
                    <tr>
                        <td><span title="{!! $oRelatedGame->bn !!}">{!! substr($oRelatedGame->ds_g_bn, 0, 9) !!}</span></td>
                        <td><span title="{!! $oRelatedGame->l_cn !!}"> {!! $oRelatedGame ->l_cn_abbr !!}</span></td>
                        <td>{!! $oRelatedGame->played_at !!}</td>
                        <td><font>{!! $oRelatedGame->h_cn !!}</font></td>
                        <td>{!! $oRelatedGame->fixed_display !!}</td>
                        <td><font>{!! $oRelatedGame->a_cn !!}</font></td>
                        <td class="textleft">
                        <?php
                            $aBetWays = [];
                        ?>
                        @foreach($aBetContent[$oRelatedGame->bn] as $sBetOdd)
                            <?php
                                list($sOddIdentity, $sOdd) = explode('_', $sBetOdd);
                                $aBetWays[] = $aWayOddRevertMap[$sOddIdentity];
                            ?>
                            <span class="{!! in_array($sOddIdentity, $oRelatedGame->game_result) ? 'victory' : 'defeat' !!}">{!! ($aBetOddsDisplay && array_key_exists($oRelatedGame->bn, $aBetOddsDisplay) && array_key_exists($sOddIdentity, $aBetOddsDisplay[$oRelatedGame->bn])) ? $aBetOddsDisplay[$oRelatedGame->bn][$sOddIdentity] : $sBetOdd !!}</span>
                        @endforeach
                        <?php
                            $aBetWays = array_unique($aBetWays);
                            $aBetWays = array_flip($aBetWays);
                        ?>
                        </td>
                        <td><!--></td>
                        <td><b class="redcolor">{!! $oRelatedGame->score_display !!}</b></td>
                        <td>{!! implode(', ', array_intersect_key($oRelatedGame->game_result_display, $aBetWays)) !!}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- 方案明细 -->
        @include('userCenter.bill._ticketDetail')


    </div>
</div>

@stop