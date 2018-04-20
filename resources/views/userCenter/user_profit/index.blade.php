@extends('l.home')

@section('title')
个人盈亏报表
@parent
@stop

@section('scripts')
@parent
{{--{!! script('jquery.jscrollpane') !!}--}}
{!! script('gagame.DatePicker') !!}
@stop


@section ('main')


@include('userCenter.teamManager.teamHeader')




<div class="content">
    @if (Session::get('is_agent'))
    @include('userCenter.user_profit._agent_search')
    @include('userCenter.user_profit._agent_table')
    @else
    @include('userCenter.user_profit._user_search')
    @include('userCenter.user_profit._user_table')
    @endif

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

        // new gagame.Select({realDom: '#J-select-user-groups', cls: 'w-2'});

    })(jQuery);
</script>
@stop
