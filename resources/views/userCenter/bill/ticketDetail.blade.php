@extends('l.ghome')

@section('title') @parent  @stop

@section ('styles')
@parent

    {!! style('gameBase') !!}
    {!! style('gameUi') !!}
    {!! style('uicss') !!}
@stop


@section ('container')

<div class="main-content">

    <div class="nav-bg nav-bg-tab">
        <div class="title-normal">
            出票详情-方案编号:{!! $data->sn !!}
        </div>
        <a href="javascript:history.back(-1)" class="mg-back"  >返回</a>
    </div>

    <div class="content">

        <table width="100%" class="table" id="J-table">
            <thead>
                <tr>
                    <th>序号</th>
                    <th>出票时间</th>
                    <th>投注明细[竞猜奖金明细]</th>
                    <th>金额[<span>{!! $data->amount_display !!}</span>]</th>
                    <th>过关方式</th>
                    <th>注数</th>
                    <th>中奖奖金[<span>{!! $data->prize_display !!}</span>]</th>
                    <th>出票状态</th>
                </tr>
            </thead>
            <tbody>
            <?php
                $iLimit = 99;
                $iTotal = $data->bet_num * $data->multiple;
                $iPages = intval($iTotal / $iLimit) + (($iTotal % $iLimit) ? 1 : 0);
            ?>
                @for($i = 1; $i < ($iPages+1); $i++)
                <?php
                    $iNum = $i * $iLimit > $iTotal ? ($iTotal - ($i-1)*$iLimit) : $iLimit;
                    $iAmount = number_format($iNum*2, 2, '.', ',');
                    $iPrize = number_format(($data->prize / $iTotal) * $iNum, 2, '.', ',');
                ?>
                <tr>
                    <td>{!! $i !!}</td>
                    <td>{!! $data->bought_at !!}</td>
                    <td>
                        <ul>
                            @foreach($oRelatedGames as $key => $oRelatedGame)
                            <?php $aBetOdds = ($aBetOddsDisplay && array_key_exists($oRelatedGame->bn, $aBetOddsDisplay)) ? $aBetOddsDisplay[$oRelatedGame->bn] : $aBetContent[$oRelatedGame->bn] ?>
                                @foreach($aBetOdds as $sBetOdd)
                                    <li><span>{!! $oRelatedGame->ds_g_bn . '>' . $sBetOdd !!}</span></li>
                                @endforeach
                            @endforeach
                        </ul>
                    </td>
                    <td>¥{!! $iAmount !!}</td>
                    <td><!--></td>
                    <td>{!! $iNum !!}</td>
                    <td>¥{!! $iPrize !!}</td>
                    <td>{!! $data->status_display !!}</td>
                </tr>
                @endfor
            </tbody>
        </table>
    </div>


</div>

@stop




@section('end')
@parent

@stop










