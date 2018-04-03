@extends('l.home', array('active' => 'signin'))

@section('title') 发送验证邮箱 - @parent - 用户 @stop

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
            <div>系统已经向您的邮箱jinmangguo@sina.com发送了一封邮件，请立即查收。</div>
            <br/>
            <div><input type="button" name="" class="btn" value="立即进入邮箱"></div>
            <br/>
            <div class="clearfix text-left">
                   <div class="f-4">&nbsp;</div> 
                   <div class="f-8">
                       <div> 如果很久没有收到邮件，请选择 <a href="#">重新发送</a>，或者 <a href="#">换个邮箱</a></div>
                       <div>有问题请联系 <a href="#">在线客服</a></div>
                   </div>
            </div>
            

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

