@extends('l.authority', array('active' => 'signin'))

@section('title') 激活成功 @parent @stop

@section('container')

    <div class="reg-result">
        <div class="alert alert-success">
            <i></i>
            <div class="txt">
                <h4>恭喜您，账号激活成功</h4>
                <div><a class="btn btn-small" href="{!! route('home') !!}">进入安捷竞彩平台首页</a></div>
            </div>
        </div>
    </div>

@stop
