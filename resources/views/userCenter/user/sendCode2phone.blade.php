@extends('l.home', array('active' => 'signin'))

@section('title') 发送验证码 - @parent - 用户 @stop

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
        <h1 class="text-center">绑定手机</h1>
        <form action="#" method="post" id="J-form" class="box-settingpwd">
            <input type="hidden" name="_method" value="PUT" />
            <input type="hidden" name="_token" value="{!! csrf_token() !!}" />
            <h3 class="text-center">您还没有绑定手机！请尽快验证</h3>
            <div class="clearfix oneLine">
                <div class="f-4 label">手机号码：</div>
                <div class="f-2"><input type="text" class="input" id="J-input-passowrd" name="" value="13888888888" /></div>
                <div class="f-5 light">请输入您的手机号码，系统会给您发送验证码短信</div>
            </div>
            <div class="clearfix oneLine">
                <div class="f-4 label">验证码：</div>
                <div class="f-2"><input type="text" class="input" id="J-input-passowrd2" name="" value="88888" /></div>
                <div class="f-4"><input type="button" name="" class="btn w-3" value="发送验证码" id="sendPhoneCode"/></div>
            </div>
             <div class="clearfix">
               <div class="f-4 label">&nbsp;</div>
               <div class="f-3">
                   <input class="btn" type="submit" value=" 提 交 " id="J-submit" />
               </div>
            </div>
        </form>
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

