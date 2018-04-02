@extends('l.ghome')

@section('title') @if($iUserId) {!!$sBpName!!} @else 竞彩平台 @endif @parent  @stop


@section ('styles')
@parent

    {!! style('gameBase') !!}
    {!! style('gameUi') !!}
    {!! style('uicss') !!}
    {!! style('result') !!}
@stop

@section('scripts')
@parent

@stop

@section ('container')

<div class="main-content">

    <div class="nav-bg nav-bg-tab">
        <div class="title-normal">
            赛果开奖
        </div>
    </div>

    <div class="content">
        <iframe id="chart" name="chart" src="http://www.spdex.com/spdex500b" width="100%" height="700" frameborder="0" scrolling="yes"></iframe>
    </div>
</div>

@stop


@section('end')
@parent
<script type="text/javascript">
        $(function(){
            $('#J-date-start').focus(function(){
                (new UIscript.DatePicker({input:'#J-date-start', startYear:2016})).show();
            });
            // $('form[name=resultSearchForm]').submit();
        });

    </script>
@stop




