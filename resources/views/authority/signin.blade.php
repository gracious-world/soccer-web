@extends('l.login', array('active' => 'signin'))

@section('title') 安全登录 @parent  @stop

@section ('styles')
@parent
    {!! style('login') !!}
@stop

@section ('container')
<div class="content clearfix">
    <div class="ad-box">
        <div  id="banner" class="cycle-slideshow"
             data-cycle-slides="> a"
             data-cycle-pager="> .cycle-pager"
             data-cycle-prev="> .cycle-prev"
             data-cycle-next="> .cycle-next"
             data-cycle-fx="fade"
             data-cycle-timeout="4000"
             data-cycle-random="false"
             data-cycle-loader="wait"
             data-cycle-speed="800"
             data-cycle-log="false"
             >
            @include('adTemp.3')
            <div class="cycle-pager"></div>
        </div>
    </div>
    <div class="login-box">
        <h2>安全登录</h2>
        {!! Form::open(array('role' => 'form', 'target' => '_self', 'name' => 'signinForm')) !!}
            <input type="hidden" name="_random" value="{!! Tool::createRandomStr() !!}" />

            <div class="login-error {!! $errors->first('attempt')?'':'hidden' !!}">
                {!! $errors->first('attempt') !!}
            </div>
            <ul class="form-ul">
                <li class="username-li">
                    <i class="input-icon"></i>
                    <input name="username" class="input" id="login-name" value="{!! Input::old('username') !!}" placeholder="{!! __('用户名') !!}" required autofocus/>
                </li>
                <li class="password-li">
                    <i class="input-icon"></i>
                    <input class="input" name="" id="login-pass" type="password" placeholder="{!!  __('_user.password') !!}" required />
                    <input name="password" id="login-pass-real" type="hidden" required />
                </li>
                @if ($bCapcha = Session::get('LOGIN_TIMES') && Session::get('LOGIN_TIMES') > 2)
               <li class="captcha-li">
                    <i class="input-icon"></i>
                    <input class="input" name="captcha" type="text" placeholder="{!! __('_basic.Captcha') !!}" />
                    <a class="verify" href="javascript:changeCaptcha();" title="{!! Lang::get('transfer.Captcha') !!}"> {!! Captcha::img() !!} </a>
                    <a href="javascript:changeCaptcha();">换一张</a>
                </li>
                @endif
                <li class="button-li">
                    <button id="loginButton" type="button">{!! Lang::get('transfer.Login') !!}</button>
                </li>
                <li class="option-li">
                    {{-- <a href="{!!route('signup')!!}">注册</a> --}}
                    {{-- <a href="{!!route('find-password')!!}">忘记密码?</a> --}}
</li>
</ul>

{!! Form::close() !!}
</div>
</div>

@stop

@section('scripts')
@parent
{!! script('md5') !!}
{!! script('jquery.cycle2') !!}
{!! script('cycle2.scrollVert') !!}
@stop

@section('end')

<script type="text/javascript">
// login page responsive
$(function(){
var $ft = $('#footer');
if($ft.length > 0) minH = $ft.offset().top + $ft.outerHeight();
else minH = 0;

function loginResponsive(){
var bh = $(window).height();
if( bh > minH ){
$ft.css({
marginTop: bh - minH+70
});
}
}
loginResponsive();

var resizeTimer = null;
$(window).on('resize', function () {
if ( resizeTimer ) {
clearTimeout(resizeTimer);
}
resizeTimer = setTimeout(function(){
loginResponsive();
}, 100);
});

});

// 错误提示眼睛朝上
var $tips = $('.form-error-tips'),
$eyes = $('.monkey');
if( $tips.length ){
$eyes.addClass('eye-up');
$tips.addClass('fade');
}

// 蒙眼睛
// var $target = $('.ds-login');
// $('#login-pass').on({
//     focus: function(){
//         $target.addClass('password');
//         $eyes.removeClass('eye-up');
//     },
//     blur: function(){
//         $target.removeClass('password');
//     }
// });

// login form
function changeCaptcha () {
// debugger;
captchaImg.src = "{!! URL::to('captcha?') !!}" + ((Math.random()*9 +1)*100000).toFixed(0);
};

$(function(){
$('#loginButton').click(function (e) {
var pwd = $('#login-pass').val();
var username = ($('#login-name').val()).toLowerCase();
$(this).text('登录中...');
$('#login-pass-real').val(md5(md5(md5(username + pwd))));
$('form[name=signinForm]').submit();
});
$('form[name=signinForm]').keydown(function(event) {
if (event.keyCode == 13) $('#loginButton').click();
});
});
</script>
@parent
@stop





