@extends('l.login', array('active' => 'signin'))

@section('title') 注册 - @parent - 用户 @stop

@section ('styles')
@parent
    {!! style('reg') !!}
@stop

@section('scripts')
@parent
{!! script('global') !!}
{!! script('validate') !!}
@stop

@section('container') 
<div class="wrap-inner">
    <div class="box-register">
        <h2>欢迎注册安捷竞彩平台</h2>
        <form action="{!! route('signup') !!}" method="post" id="signupForm">
            <input type="hidden" name="_token" value="{!! csrf_token() !!}">
            <input type="hidden" name="_random" value="{!! Tool::createRandomStr() !!}">
             <div class="clearfix oneLine">
                <div class="f-2 label">用户名：</div>
                <div class="f-3"><input type="text" name="username" class="input" id="userName" ></div>
                <div class="f-5 light" id="userNameError">用户名长度为5-16个字符，可使用数字、英文、中文</div>
            </div>
            <div class="clearfix oneLine">
                <div class="f-2 label">登录密码：</div>
                <div class="f-3"><input type="password" name="password" class="input"  id="loginPwd"></div>
                <div class="f-5 light lh15" id="loginPwdError">由字母和数字组成6-16个字符；且必须包含数字和字母，不允许连续三位相同，不能和资金密码相同</div>
            </div>
            <div class="clearfix oneLine">
                <div class="f-2 label">确认密码：</div>
                <div class="f-3"><input type="password" name="password_confirmation" class="input" id="confirmPwd"></div>
                <div class="f-5 light" id="confirmPwdError"></div>
            </div>
            <div class="clearfix oneLine">
                <div class="f-2 label">邮箱：</div>
                <div class="f-3"><input type="text" name="email" class="input" id="email"></div>
                <div class="f-5 light" id="emailError">请输入您的邮箱，以便您在忘记密码时使用邮箱找回密码</div>
            </div>
            <div class="clearfix">
                <div class="f-2 label">验证码：</div>
                <div class="f-1"><input type="text" name="captcha" class="input" id="checkCode"></div>
                <div class="f-0">
                    <a id='captcha_a' class="verify" href="javascript:changeCaptcha();" title="{!! Lang::get('transfer.Captcha') !!}">
                        {!! Captcha::img() !!}
                    </a>
                 </div>
                 <div class="f-0"><a href="javascript:changeCaptcha();" id="refreshCaptcha">刷新</a></div>
                 <div class="f-3 light" id="checkCodeError"></div>
            </div>
           
            <div class="clearfix  optional">
                <div class="f-2 label"></div>
                <div class="f-3 text-center">选填项</div>
            </div>
            <div class="clearfix oneLine">
                <div class="f-2 label">真实姓名：</div>
                <div class="f-3"><input type="text" name="name" class="input"></div>
                <div class="f-5 light">请输入您的真实姓名，提现时银行卡需要与真实姓名一致</div>
            </div>
            <div class="clearfix oneLine">
                <div class="f-2 label">身份证号码：</div>
                <div class="f-3"><input type="text" name="shenfenzheng" class="input"></div>
                <div class="f-5 light">请输入您的真实身份证号，以便中大奖时核实身份</div>
            </div>
            <div class="clearfix oneLine">
                <div class="f-2 label">手机号码：</div>
                <div class="f-3"><input type="text" name="mobile" class="input"></div>
                <div class="f-5 light">请输入您的手机号码，以便您在忘记密码时使用手机找回密码</div>
            </div>
            <div class="clearfix oneLine">
                <div class="f-2 label">QQ号码：</div>
                <div class="f-3"><input type="text" name="qq" class="input"></div>
            </div>
            <div class="clearfix oneLine">
                <div class="f-2 label">微信号：</div>
                <div class="f-3"><input type="text" name="weixin" class="input"></div>
            </div>
            <div class="clearfix ">
                <div class="f-2 label"></div>
                <div class="f-3"><input type="submit" class="btn w-2" value="立即注册"/></div>
            </div>
        </form>
        　
    </div>
</div>
@include('w.notification')
@stop


@section('end')
 @parent
<script type="text/javascript">
function changeCaptcha() {
    $('#captcha_a img').attr('src',  "{!! URL::to('captcha?') !!}" + ((Math.random() * 9 + 1) * 100000).toFixed(0));
};

$('#signupForm .light').each(function() {
    $(this).attr('original', $(this).html());
});

var form = $("#signupForm").get(0);
var validator = new Validator();
var code = $("#code").get(0); //推广码
var codeError = $("#codeError").get(0); //推广码
var userName = $("#userName").get(0); //用户名
var userNameError = $("#userNameError").get(0); //用户名
var loginPwd = $("#loginPwd").get(0); //登录密码
var loginPwdError = $("#loginPwdError").get(0); //登录密码
var confirmPwd = $("#confirmPwd").get(0); //确认密码
var confirmPwdError = $("#confirmPwdError").get(0); //确认密码
var fundsPwd = $("#fundsPwd").get(0); //资金密码
var fundsPwdError = $("#fundsPwdError").get(0); //资金密码
var refundsPwd = $("#refundsPwd").get(0); //确认资金密码
var refundsPwdError = $("#refundsPwdError").get(0); //确认资金密码
var email = $("#email").get(0); //邮箱
var emailError = $("#emailError").get(0); //邮箱
var checkCode = $("#checkCode").get(0); //验证码
var checkCodeError = $("#checkCodeError").get(0); //验证码


var validataFunc = function() {
    //推广码
    validator.add(code, [{
        strategy: 'isNonEmpty',
        option: {
            errorMsg: '推广码不能为空',
            sourceDom: code,
            errorMsgDom: codeError
        }
    }]);

    //用户名
    validator.add(userName, [{
        strategy: 'isNonEmpty',
        option: {
            errorMsg: '用户名不能为空',
            sourceDom: userName,
            errorMsgDom: userNameError
        }

    }, {
        strategy: 'minLength',
        option: {
            minlength: 5,
            errorMsg: '用户名长度不能小于5位',
            sourceDom: userName,
            errorMsgDom: userNameError
        }

    }, {
        strategy: 'maxLength',
        option: {
            maxLength: 16,
            errorMsg: '用户名长度不能大于16位',
            sourceDom: userName,
            errorMsgDom: userNameError
        }
    }]);

    //登录密码
    validator.add(loginPwd, [{
        strategy: 'isNonEmpty',
        option: {
            errorMsg: '登录密码不能为空',
            sourceDom: loginPwd,
            errorMsgDom: loginPwdError
        }

    }, {
        strategy: 'minLength',
        option: {
            minlength: 6,
            errorMsg: '密码长度不能小于6位',
            sourceDom: loginPwd,
            errorMsgDom: loginPwdError
        }
    }, {
        strategy: 'maxLength',
        option: {
            maxLength: 16,
            errorMsg: '密码长度不能大于16位',
            sourceDom: loginPwd,
            errorMsgDom: loginPwdError
        }
    }, {
        strategy: 'passwordShouldContainLetterAndNumber',
        option: {
            errorMsg: '密码必须包含数字和字母',
            sourceDom: loginPwd,
            errorMsgDom: loginPwdError
        }
    }, {
        strategy: 'isContinuousEqual',
        option: {
            errorMsg: '密码不允许连续三位相同字符',
            sourceDom: loginPwd,
            errorMsgDom: loginPwdError
        }
    }
    ]);

    //确认密码
    validator.add(confirmPwd, [{
        strategy: 'isNonEmpty',
        option: {
            errorMsg: '确认密码不能为空',
            sourceDom: confirmPwd,
            errorMsgDom: confirmPwdError
        }
    }, {
        strategy: 'isEqual',
        option: {
            errorMsg: '两次输入密码不一致，请重新输入',
            sourceDom: confirmPwd,
            compareDom: loginPwd,
            errorMsgDom: confirmPwdError
        }
    }]);

    //资金密码
    /*validator.add(fundsPwd, [{
        strategy: 'isNonEmpty',
        option: {
            errorMsg: '资金密码不能为空',
            sourceDom: fundsPwd,
            errorMsgDom: fundsPwdError
        }

    }, {
        strategy: 'minLength',
        option: {
            minlength: 6,
            errorMsg: '密码长度不能小于6位',
            sourceDom: fundsPwd,
            errorMsgDom: fundsPwdError
        }
    }, {
        strategy: 'maxLength',
        option: {
            maxLength: 16,
            errorMsg: '密码长度不能大于16位',
            sourceDom: fundsPwd,
            errorMsgDom: fundsPwdError
        }
    }, {
        strategy: 'passwordShouldContainLetterAndNumber',
        option: {
            errorMsg: '密码必须包含数字和字母',
            sourceDom: fundsPwd,
            errorMsgDom: fundsPwdError
        }
    }, {
        strategy: 'isContinuousEqual',
        option: {
            errorMsg: '密码不允许连续三位相同字符',
            sourceDom: fundsPwd,
            errorMsgDom: fundsPwdError
        }
    }, {
        strategy: 'canNotEqual',
        option: {
            errorMsg: '不能和登录密码相同',
            sourceDom: fundsPwd,
            compareDom: loginPwd,
            errorMsgDom: fundsPwdError
        }
    }]);
*/

    //确认资金密码
    /*validator.add(refundsPwd, [{
        strategy: 'isNonEmpty',
        option: {
            errorMsg: '确认资金密码不能为空',
            sourceDom: refundsPwd,
            errorMsgDom: refundsPwdError
        }
    }, {
        strategy: 'isEqual',
        option: {
            errorMsg: '两次输入密码不一致，请重新输入',
            sourceDom: refundsPwd,
            compareDom: fundsPwd,
            errorMsgDom: refundsPwdError
        }
    }]);*/

    //邮箱
    validator.add(email, [{
        strategy: 'isNonEmpty',
        option: {
            errorMsg: '邮箱不能为空',
            sourceDom: email,
            errorMsgDom: emailError
        }
    }, {
        strategy: 'isEmail',
        option: {
            errorMsg: '邮件格式输入不正确',
            sourceDom: email,
            errorMsgDom: emailError
        }
    }]);

    //验证码
    validator.add(checkCode, [{
        strategy: 'isNonEmpty',
        option: {
            errorMsg: '验证码不能为空',
            sourceDom: checkCode,
            errorMsgDom: checkCodeError
        }
    }]);



    var errorMsg = validator.start();
    return errorMsg;
}

form.onsubmit = function() {
    return !validataFunc();
};
</script>
@stop

