@extends('l.home', array('active' => 'signin'))

@section('title') 注册下级 - @parent - 用户 @stop

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
        <!--TODO check-->
         <div class="box-teamMG">用户<a href="#">{!!$sUserName!!}</a>开户成功！<a href="{!!route("users.open-account")!!}"><input type="button" name="" value="继续开户" class="btn w-2"></a></div>
        　
    </div>
</div>
@include('w.notification')
@stop


@section('end')
 @parent
<script type="text/javascript">


</script>
@stop

