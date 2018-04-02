@extends('l.home', array('active' => 'signin'))

@section('title') 推广码 - @parent - 用户 @stop

@section ('styles')
@parent
    {!! style('ucenter') !!}
    {!! style('reg') !!}

@stop

@section('scripts')
@parent
{!! script('global')!!}

@stop

@section('main')
<div class="wrap-inner main">
    @include("userCenter.teamManager.teamHeader")
    <div class="box-register box-register-border">
         <div class="box-teamMG">
              请复制(Ctrl+C)推广码发送给您的下级会员，推广码是您的下级会员注册时的必填项<br/>
              推广码：{!!$iSpreadCode!!}<br/>
             <a href="#"><input type="button" name="" class="btn w-2" value="复制"></a>
         </div>
        　
    </div>
</div>
@include('w.notification')
@stop


@section('end')
 @parent
<script type="text/javascript">


</script>
@stop

