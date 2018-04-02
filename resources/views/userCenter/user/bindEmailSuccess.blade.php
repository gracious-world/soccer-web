@extends('l.home', array('active' => 'signin'))

@section('title') 邮箱绑定成功 - @parent - 用户 @stop

@section ('styles')
@parent
    {!! style('reg') !!}

@stop

@section('scripts')
@parent 
{!! script('validate')!!}
@stop

@section('main')
<div class="wrap-inner main">
    @include("userCenter.user.userCenterHeader")
    <div class="box-register box-register-border">
        <h1 class="text-center">绑定邮箱</h1>
        <div class="box-settingpwd text-center">
            <div>已绑定邮箱：{!!$sEmailHidden!!}   <a href="{!!route("users.bind-email")!!}" >修改绑定</a></div>
            <div>密码找回：当您忘记密码时，可通过邮箱快速找回您的密码</div>
        </div>
    </div>
</div>
@include('w.notification')
@stop


@section('end')
 @parent
<script type="text/javascript">
    Validator.sendPhoneCodeLimit({
        selectorSendPhoneCode:'#sendPhoneCode',
        selectorPhone:null,
        _token:$('meta[name="_token"]').attr('content'),
        type:2,
        smsUrl:"http://jmg.user.com/auth/send-sms-password",
        selectorReciprocal:'#sendPhoneCode',
        isShowSuccessInfo:false
    });

</script>
@stop

