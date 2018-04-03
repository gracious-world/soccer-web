@extends('l.home')

@section('title')
充值确认
@parent
@stop

@section('scripts')
@parent
    {!! script('ZeroClipboard')!!}
    {!! script('gagame.Mask')!!}
    {!! script('gagame.Message')!!}
@stop

@section ('main')
<div class="nav-bg">
    <div class="title-normal">
        微信支付
    </div>
</div>

<div style="text-align:center;">
    <div>充值金额：{!! $sDisplayAmount !!} 元</div>
    <div>
        <img src="{!! $sWxQrcode !!}"/>
    </div>
</div>

@stop

@section('end')
@parent
@stop

