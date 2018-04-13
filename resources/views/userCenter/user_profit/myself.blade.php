@extends('l.home')

@section('title')
个人盈亏报表
@parent
@stop

@section('scripts')
@parent
{{ script('jquery.jscrollpane') }}
{{ script('gagame.DatePicker') }}
@stop


@section ('main')

<!--导航栏-->
@include('w.transaction-nav')


<div class="content">
    @include('centerUser.user_profit._myself_search')
    @include('centerUser.user_profit._myself_table')

    {{ pagination($datas->appends(Input::except('page')), 'w.pages') }}
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

        //new gagame.Select({realDom: '#J-select-user-groups', cls: 'w-2'});

    })(jQuery);
</script>
@stop