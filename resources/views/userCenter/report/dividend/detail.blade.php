@extends('l.home')

@section('title')
分红报表
@parent
@stop

@section('scripts')
@parent
{{ script('jquery.jscrollpane') }}
{{ script('gagame.DatePicker') }}
@stop


@section ('main')

<div class="nav-bg nav-bg-tab">
    <div class="title-normal">查看分红明细</div>
    <ul class="tab-title clearfix">

        <li><a href="{{ route('team-lottery-profits.index') }}"><span>团队彩种盈亏报表</span></a></li>
        <li><a href="{{ route('user-lottery-profits.index') }}"><span>用户彩种盈亏报表</span></a></li>
        <li><a href="{{ route('team-profits.index') }}"><span>团队盈亏报表</span></a></li>
        <li><a href="{{ route('user-profits.index') }}"><span>用户盈亏报表</span></a></li>
        <li class="current"><a href="{{ route('user-dividends.index') }}"><span>分红报表</span></a></li>
    </ul>
</div>
<div class="content">

    <table width="100%" class="table">
        <thead>
            <tr>
                <th>频道名称</th>
                <th>销售总额</th>
                <th>奖金总额</th>
                <th>佣金总额</th>
                <th>活动奖金总额</th>
                <th>数值佣金总额</th>
                <th>盈亏总计</th>
                <th>分红比例</th>
                <th>分红金额</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $fTotalturnover = 0;
            $fTotalprize = 0;
            $fTotalcommission = 0;
            $fTotalbonus = 0;
            $fTotallose_commission = 0;
            $fTotalprofit = 0;
            $fTotalamount = 0;

            ?>
            @foreach ($datas as $data)
            <tr>
                <td>{{$aGameTypes[$data->game_type]}}</td>
                <td>{{$data->turnover}}</td>
                <td>{{$data->prize}}</td>
                <td>{{$data->commission}}</td>
                <td>{{$data->bonus}}</td>
                <td>{{$data->lose_commission}}</td>
                <td>{{$data->profit}}</td>
                <td>{{$data->rate * 100 . '%'}}</td>
                <td>{{$data->amount}}</td>
            </tr>

            <?php
            $fTotalturnover += $data->turnover;
            $fTotalprize += $data->prize;
            $fTotalcommission += $data->commission;
            $fTotalbonus += $data->bonus;
            $fTotallose_commission += $data->lose_commission;
            $fTotalprofit += $data->profit;
            $fTotalamount += $data->amount;

            ?>


            @endforeach
        </tbody>
        <tfoot>
            <td>合计</td>
            <td>{{$fTotalturnover}}</td>
            <td>{{$fTotalprize}}</td>
            <td>{{$fTotalcommission}}</td>
            <td>{{$fTotalbonus}}</td>
            <td>{{$fTotallose_commission}}</td>
            <td>{{$fTotalprofit}}</td>
            <td>&nbsp;</td>
            <td>{{$fTotalamount}}</td>
    </tfoot>
    </table>
    <br>
    <div class="text-center">
        <input type="button" id="J-go-back" name="" class="btn w-2" value="返回" />
    </div>
</div>
@stop

@section('end')
@parent
<script>
    (function ($) {
        $('#J-date-start').focus(function () {
            (new gagame.DatePicker({input: '#J-date-start', isShowTime: false, startYear: 2013})).show();
        });
        $('#J-date-end').focus(function () {
            (new gagame.DatePicker({input: '#J-date-end', isShowTime: false, startYear: 2013})).show();
        });
        $('#J-go-back').click(function () {
             location.href = '{{route('user-dividends.index')}}';
        });

    })(jQuery);
</script>
@stop