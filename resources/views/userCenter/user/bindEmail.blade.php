@extends('l.home', array('active' => 'signin'))

@section('title') 绑定邮箱 - @parent - 用户 @stop

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
        <form action="#" method="post" id="J-form" class="box-settingpwd">
            <input type="hidden" name="_method" value="PUT" />
            <input type="hidden" name="_token" value="{!! csrf_token() !!}" />
            <h3 class="text-center">您还没有绑定邮箱！请尽快绑定</h3>
            <div class="clearfix oneLine">
                <div class="f-4 label">常用邮箱：</div>
                <div class="f-2"><input type="text" class="input" id="J-input-mail" name="email" /></div>
                <div class="f-5 light">请输入您的常用邮箱，系统会给您发送验证邮件</div>
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
     var mail = $('#J-input-mail');

     $('#J-submit').click(function() {
        var mailv = $.trim(mail.val());
        if (!(/\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*/).test(mailv)) {
            alert('邮箱格式填写不正确');
            mail.focus();
            return false;
        }
        return true;
     });
</script>
@stop

