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
    {!! script('utilData') !!}
    {!! script('doT') !!}
    {!! script('laypage') !!}


@parent
<script type="text/javascript" src="/js/game/mg.betRecord.js"></script>

<script type="text/javascript">
    $(function() {
        var map = {!! json_encode($oRelatedGames) !!};
        var gameBnMaps = {!! json_encode($aGameBnMaps) !!};
        var url = "{!! route('programs.bills', [$data->id]) !!}";
        // Mango.betRecord.setUrl();
        var  gateTypeDisplay = "{!! $data->gate_type_display !!}";
        Mango.betRecord.init({url: url, gameBnMaps: gameBnMaps,gateTypeDisplay:gateTypeDisplay});
    });
</script>
@stop


@section ('container')

<div class="Order_details">

    <div class="nav-bg nav-bg-tab">
        <div class="title-normal">
            <!-- 投注记录 - 订单详情 -->
            竞彩足球 {!! $data->gate_type_display . '[' . $data->gate . ']' !!} 第{!! $data->sn !!}期自购方案
        </div>
        <a href="javascript:history.back(-1)" class="mg-back"  >返回</a>
    </div>
    <div class="ordmain">
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
                    @if($data['status'] > \App\Models\Bet\Program::STATUS_SYSTEM_CANCELED)
                    <td>奖金：<b>￥<span>{!! $data->prize_display !!}</span></b>元</td>
                    @elseif($data['status'] == \App\Models\Bet\Program::STATUS_TICKET_PRINTED || $data['status'] == \App\Models\Bet\Program::STATUS_WAITING)
                    <td>奖金：&nbsp;</td>
                    @else
                    <td>奖金： — </td>
                    @endif
                </tr>
            </tbody>
        </table>

        @include('userCenter.program.programDetail')


        <!-- 方案明细 -->
        <div id="ticketDetailCon">
            <div id="ticketDetailWrap"></div>
            <div id="pagination"></div>
        </div>
</div>
@include('userCenter.bill._ticketDetail')
@stop