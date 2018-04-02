@extends('l.home')

@section('title') 充值成功 @parent @stop

@section ('styles')
    @parent
    {!! style('reg') !!}


@stop

@section ('main')

    <div class="reg-result">
        <div class="alert alert-success">
            <i></i>
            <div class="txt">
                <h4>恭喜您，充值成功!</h4>
                <p>您可以在账变记录中查看，如有问题请联系客服</p>
                <div><a class="btn btn-small" href="{!! route('home') !!}">安捷竞彩平台首页</a></div>
            </div>
        </div>
    </div>

@stop
