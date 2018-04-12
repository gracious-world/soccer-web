@extends('l.login', array('active' => 'signin'))

@section('title') 注册成功 @parent @stop

@section ('styles')
    @parent
    {!! style('reg') !!}
    <style type="text/css">
        .center
        {
            text-align: center;
        }
    </style>

@stop

@section('container')

    <div class="reg-result">
        <div class="alert alert-success">
            <i></i>
            <div class="txt">
                <h4>恭喜您，注册成功!</h4>
                <p>请妥善保管您的密码，如有问题请联系客服</p>
                <div><a class="btn btn-small" href="{{ route('users.index') }}">返回用户管理页面</a></div>
            </div>
        </div>
    </div>

@stop
