@extends('l.home')

@section('title')
    资金密码设置
@parent
@stop
@section ('styles')
@parent 
    {!! style('reg') !!}

@stop


@section ('container')
@include("userCenter.user.userCenterHeader")


<div class="box-register box-register-border">
    <!-- <div class="prompt">
        为了你的账户安全，充值之前请先设置资金密码。
    </div> -->
    <h1 class="text-center">设置资金密码</h1>
    <form action="{!! route('users.safe-reset-fund-password') !!}" method="post" id="J-form" class="box-settingpwd">
        <input type="hidden" name="_method" value="PUT" />
        <input type="hidden" name="_token" value="{!! csrf_token() !!}" />
        <div class="clearfix oneLine">
            <div class="f-4 label">设置资金密码：</div>
            <div class="f-2"><input type="password" class="input" id="J-input-passowrd" name="fund_password" /></div>
            <div class="f-3">
                <span class="ui-text-prompt-multiline w-6">由字母和数字组成6-16个字符；且必须包含数字和字母，不允许连续三位相同，不能和登录密码相同</span>
                    <div class="col-sm-4">
                        {!! $errors->first('fund_password', '<label class="text-danger control-label">:message</label>') !!}
                    </div>
            </div>
        </div>
        <div class="clearfix oneLine">
            <div class="f-4 label">确认资金密码：</div>
            <div class="f-2"><input type="password" class="input" id="J-input-passowrd2" name="fund_password_confirmation" /></div>
            <div class="f-3">
                 <span class="ui-text-prompt-multiline w-6">再次输入资金密码</span>
                <div class="col-sm-4">
                    {!! $errors->first('fund_password_confirmation', '<label class="text-danger control-label">:message</label>') !!}
                </div>
            </div>
        </div>
         <div class="clearfix">
           <div class="f-4 label">&nbsp;</div>
           <div class="f-3">
               <input class="btn" type="submit" value=" 提 交 " id="J-submit" />
           </div>
        </div>
    </form>

</div>
@stop

@section('end')
@parent
<script>
(function($){
    var ipt1 = $('#J-input-passowrd'),
    ipt2 = $('#J-input-passowrd2');

    $('#J-submit').click(function(){
        var v1 = $.trim(ipt1.val()),
          v2 = $.trim(ipt2.val());
        if(v1 == ''){
          alert('资金密码不能为空');
          ipt1.focus();
          return false;
        }
        if(v2 == ''){
          alert('确认资金密码不能为空');
          ipt2.focus();
          return false;
        }
        if(v1 != v2){
          alert('两次输入的资金密码不一致');
          ipt2.focus();
          return false;
        }
        if(!(/^(?=.*\d+)(?=.*[a-zA-Z]+)(?!.*?([a-zA-Z0-9]{1})\1\1).{6,16}$/).test(passwordNewV)){
            alert('资金密码格式不符合要求');
            ipt2.focus();
            return false;
        }
        return true;
    });


})(jQuery);
</script>
@stop