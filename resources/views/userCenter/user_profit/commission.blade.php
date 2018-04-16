@extends('l.home')

@section('title')
佣金报表
@parent
@stop

@section('scripts')
@parent
{!! script('jquery.jscrollpane') !!}
{!! script('gagame.DatePicker') !!}
@stop


@section ('main')


<div class="nav-bg nav-bg-tab">
    <div class="title-normal">佣金报表</div>
    <ul class="tab-title clearfix">
        <li><a href="{!! route('team-lottery-profits.index') !!}"><span>团队彩种盈亏报表</span></a></li>
        <li><a href="{!! route('user-lottery-profits.index') !!}"><span>用户彩种盈亏报表</span></a></li>
        <li><a href="{!! route('team-profits.index') !!}"><span>团队盈亏报表</span></a></li>
        <li><a href="{!! route('user-profits.index') !!}"><span>用户盈亏报表</span></a></li>
        @if(Session::get('is_top_agent'))
        <li><a href="{!! route('user-dividends.index') !!}"><span>分红报表</span></a></li>
      @endif
        <li class="current"><a href="{!! route('user-profits.commission') !!}"><span>佣金报表</span></a></li>
    </ul>
</div>


<div class="content">
    @include('centerUser.user_profit._agent_commission_search')
    @include('centerUser.user_profit._agent_commission_table')

    {!! pagination($datas->appends(Input::except('page')), 'w.pages') !!}
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

        new gagame.Select({realDom: '#J-select-user-groups', cls: 'w-2'});

    })(jQuery);
</script>
@stop
