@extends('l.home', array('active' => 'signin'))

@section('title') 绑定手机 - @parent - 用户 @stop

@section ('styles')
@parent
    {!! style('ucenter') !!}
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
            <h3 class="text-center">您还没有绑定手机！请尽快绑定。</h3>
            <div class="clearfix oneLine">
                <div class="f-4 label">手机号码：</div>
                <div class="f-2"><input type="text" class="input" id="mobile" name="mobile" /></div>
                <div class="f-5 light">请输入您的手机号码，系统会给您发送验证码短信</div>
            </div>
            <div class="clearfix oneLine">
                <div class="f-4 label">验证码：</div>
                <div class="f-2"><input type="text" class="input" id="captcha" name="captcha" /></div>
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
        selectorPhone:'#mobile',
        _token:$('meta[name="_token"]').attr('content'),
        type:2,
        smsUrl:"{!!route("send-sms-message")!!}",
        selectorReciprocal:'#sendPhoneCode',
        isShowSuccessInfo:false
    });

</script>
@stop

