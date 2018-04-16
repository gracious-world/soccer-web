@extends('l.home')

@section('title')
分红报表
@parent
@stop

@section('scripts')
@parent
{!! script('jquery.jscrollpane') !!}
{!! script('gagame.DatePicker') !!}
@stop


@section ('main')

@include('userCenter.teamManager.teamHeader')
<div class="content">

    <!--    <div class="area-search">
            <p class="row">
                查询日期：
                <input type="text" value="" class="input w-3" id="J-date-start">&nbsp;&nbsp;
                <input type="button" class="btn" value="搜 索" />
            </p>
        </div>-->

    <table width="100%" class="table">
        <thead>
            <tr>
                <th>用户名</th>
                <th>分红时间</th>
                <th>销售总额</th>
                <th>奖金总额</th>
                <th>佣金总额</th>
                <th>活动奖金总额</th>
                <th>输值佣金总额</th>
                <th>盈亏总计</th>
                <th>分红比例</th>
                <th>分红金额</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($datas as $data)
            <tr>
                <td>{!! $data->username !!}</td>
                <td>{!! "$data->begin_date 至 $data->end_date" !!}</td>
                <td>{!! $data->turnover_formatted !!}</td>
                <td>{!! $data->prize_formatted !!}</td>
                <td>{!! $data->commission_formatted !!}</td>
                <td>{!! $data->bonus_formatted !!}</td>
                <td>{!! $data->lose_commission_formatted !!}</td>
                <td>{!! $data->profit_formatted !!}</td>
                <td>{!! $data->rate !!}</td>
                <td>{!! $data->amount_formatted !!}</td>
                <td><a href="{!!route('user-gt-dividends.detail', [$data->year, $data->month, $data->batch])!!}">查看明细</a></td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @include('pagination.default', ['paginator' => $datas])
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

    })(jQuery);
</script>
@stop
