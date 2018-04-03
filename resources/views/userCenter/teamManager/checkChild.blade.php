@extends('l.home', array('active' => 'signin'))

@section('title') 查看下级 @parent @stop

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
          <div class="area-search">
            <form action="{!! route('users.index') !!}" class="form-inline" method="get">
                <input type="hidden" name="_token" value="{!! csrf_token() !!}" />
                    <div class="clearfix">
                        <div class="left">用户名：</div>
                        <div class="f-2"><input type="text" name="username" class="input"></div>
                         <div class="left">用户余额：</div>
                        <div class="f-2"><input type="text" name="balance_from" class="input"></div>
                         <div class="left">至</div>
                        <div class="f-2"><input type="text" name="balance_to" class="input"></div>
                        <input type="submit" value="搜 索" class="btn btn-important" >
                    </div>
            </form>
        </div>
        <table class="table">
            <thead>
                <tr>
                    <th>上级代理</th>
                    <th>用户名</th>
                    <th>注册日期</th>
                    <th>最后登录时间</th>
                    <th>账户余额(元)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($datas as $data)
                <tr>
                    <td>{!! $data->parent !!}</td>
                    <td>{!! $data->username !!}</td>
                    <td>{!! $data->register_at !!}</td>
                    <td>{!! $data->signin_at !!}</td>
                    <td>{!! (isset($aBalances[$data->username]) && $aBalances[$data->username]) ? number_format($aBalances[$data->username], 2) : 0!!}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <!-- SONW 注意 ，这里需要分页条 -->
         <div class="text-center">
             @include('pagination.default', ['paginator' => $datas])
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

