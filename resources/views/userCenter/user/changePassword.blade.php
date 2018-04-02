@extends('l.home')

@section('title')
   登录密码管理
@parent
@stop

@section('scripts')
@parent
    {!! script('gagame.Timer') !!}
    {!! script('gagame.Message') !!}
    {!! script('gagame.Mask') !!}
@stop

@section('main')
@include("userCenter.user.userCenterHeader")

<div class="content">
    <h1 class="text-center">修改登录密码</h1>
    <form action="{!! route('users.change-password') !!}" method="POST" id="J-form-login">
        <input type="hidden" name="_method" value="PUT" />
        <input type="hidden" name="_token" value="{!! csrf_token() !!}" />
        <table width="100%" class="table-field">

            <tr>
                <td align="right" style="width:350px;">输入旧登录密码：</td>
                <td>
                    <input id="J-input-login-password-old" type="password" class="input w-4" name="old_password">
                </td>
                <td>
                    <div class="col-sm-4">
                        {!! $errors->first('old_password', '<label class="text-danger control-label">:message</label>') !!}
                    </div>
                </td>
            </tr>
            <tr>
                <td align="right">输入新登录密码：</td>
                <td>
                    <input id="J-input-login-password-new" type="password" class="input w-4" name="password">
                    <span class="ui-text-prompt-multiline w-6">由字母和数字组成6-16个字符；且必须包含数字和字母，不允许连续三位相同，不能和资金密码相同</span>
                    <div class="col-sm-4">
                        {!! $errors->first('password', '<label class="text-danger control-label">:message</label>') !!}
                    </div>
                </td>
            </tr>
            <tr>
                <td align="right">确认新登录密码：</td>
                <td>
                    <input id="J-input-login-password-new2" type="password" class="input w-4" name="password_confirmation">
                </td>
                <td>
                    <div class="col-sm-4">
                        {!! $errors->first('password_confirmation', '<label class="text-danger control-label">:message</label>') !!}
                    </div>
                </td>
            </tr>
            <tr>
                <td align="right"></td>
                <td>
                    <input id="J-button-submit-login" type="submit" value="修 改" class="btn">
                </td>
            </tr>
        </table>
    </form>

</div>
@stop


@section('end')
@parent
<script>
(function($){
    $('#J-button-submit-login').click(function(){
        var passwordOld = $('#J-input-login-password-old'),
            passwordNew = $('#J-input-login-password-new'),
            passwordNewV = $.trim(passwordNew.val()),
            passwordNew2 = $('#J-input-login-password-new2');
        if($.trim(passwordOld.val()) == ''){
            alert('旧登录密码不能为空');
            passwordOld.focus();
            return false;
        }
        if(passwordNewV == ''){
            alert('新登录密码不能为空');
            passwordNew.focus();
            return false;
        }
        if(!(/^(?=.*\d+)(?=.*[a-zA-Z]+)(?!.*?([a-zA-Z0-9]{1})\1\1).{6,16}$/).test(passwordNewV)){
            alert('新登录密码格式不符合要求');
            passwordNew.focus();
            return false;
        }
        if($.trim(passwordNew2.val()) != passwordNewV){
            alert('两次输入的密码不一致');
            passwordNew2.focus();
            return false;
        }

        return true;

    });


})(jQuery);
</script>
@stop