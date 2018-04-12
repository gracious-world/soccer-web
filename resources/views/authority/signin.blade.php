@extends('l.login', array('active' => 'signin'))

@section('title') 安全登录 @parent  @stop

@section ('styles')
@parent
    {!! style('login') !!}
@stop

@section ('container')
<style>
form{
	width: 320px;
	float: right;
}
</style>
<div id="middle_wrap">
				<div id="banner">
					<img style="width: 872px;" src="../imager/header/banner.png"/>
					<!--找回密码-->
					<div class="login_kuan" id="find_password" style="display: none;">
						<ul>
							<li onclick="blockphone()"><a href="javascript:void(0)"><img src="../imager/header/phonenumber.png"/><p>手机登录</p><i></i></a></li>
							<li onclick="blockEmile()" style="margin:0 40px;"><a href="javascript:void(0)"><img src="../imager/header/phone.png"/><p>邮箱登录</p></a></li>
							<li><a target="_blank" href="http://wpa.qq.com/msgrd?v=3&uin=3566073817&site=qq&menu=yes"><img src="../imager/header/kefu.png"/><p>客服找回</p></a></li>
						</ul>
						<div class="subm">
							<img src="../imager/header/loginuser.png"/>
							<input id="usernamenumber" type="text" placeholder="请输入你的电话号码" name=""  value="" />
						</div>
						<div  class="subm" style="margin-top:20px;">
							<img style="margin-top: 11px;" src="../imager/header/message.png"/>
							<input id="passwordyzm" style="width: 50%;" placeholder="请输入验证码" type="text" name="" id="" value=""/>
							<a href="javascript:void(0)">获取验证码</a>
						</div>
						<a href="javascript:void(0)" onclick="submit()" class="submit">提交</a>
					</div>
					<!--登录-->
					{!! Form::open(array('role' => 'form', 'target' => '_self', 'name' => 'signinForm')) !!}
            		<input type="hidden" name="_random" value="{!! Tool::createRandomStr() !!}" />
					
					<div class="login_kuan" id="login_view" >
						<div style="position: absolute;top:-50px;" class="login-error {!! $errors->first('attempt')?'':'hidden' !!}">
		                {!! $errors->first('attempt') !!}
		            </div>
						<div class="subm" style="margin-top: 15px;">
							<img src="../imager/header/loginuser.png"/>
							<input id="username" type="text" placeholder="用户名(6-10位数字和字母)" name="username"  value="" />
						</div>
						<div  class="subm" style="margin-top:20px;">
							<img src="../imager/header/password.png"/>
							<input id="password" style="width: 50%;" placeholder="请输入您的密码" type="password" name="password" id="" value=""/>
						</div>
						<span class="size" style="float: left;margin-left:15px;">忘记密码?</span>
						<span class="size"  style="float: right;width: 95px;margin-right:5px;">记住密码
							<div class="onoffswitch">  
							    <input type="checkbox" name="onoffswitch" class="onoffswitch-checkbox" id="myonoffswitch">  
							    <label class="onoffswitch-label" for="myonoffswitch">  
							        <span class="onoffswitch-inner"></span>  
							        <span class="onoffswitch-switch"></span>  
							    </label>  
							</div> 
						</span>
						<input type="submit" class='submit' value="登录">
						<p style="text-align: center;margin-top:20px;font-size: 14px;">还没有账号?<a style="color:#1eb357;margin: 0;" href="javascript:void(0)">点击注册</a></p>
					</div>
					<!--注册-->
					<div class="login_kuan" id="register_view"  style="display: none;">
						<div class="registertop">
							<a class="ptzc1"  href="javascript:void(0)" onclick="ptzc()" style="float:left;margin-left: 10px;">普通注册</a>
							<a class="kszc1" href="javascript:void(0)" onclick="kszc()" style="float:right;margin-left:10px;border: none;">快速注册</a>
						</div>
						<!--普通注册-->
						<div class="ptzc" >
							<div class="subm" style="margin-top: 15px;">
								<img src="../imager/header/loginuser.png"/>
								<input id="register_username" type="text" placeholder="用户名(6-10位数字和字母)" name=""  value="" />
							</div>
							<div  class="subm" style="margin-top:20px;">
								<img src="../imager/header/password.png"/>
								<input id="register_password1" style="width: 50%;" placeholder="请输入您的密码" type="password" name="" id="" value=""/>
							</div>
							<div  class="subm" style="margin-top:20px;">
								<img src="../imager/header/password.png"/>
								<input id="register_password2" style="width: 50%;" placeholder="请再次输入您的密码" type="password" name="" id="" value=""/>
							</div>
							<div  class="subm" style="margin-top:20px;">
								<img style="margin-top: 11px;" src="../imager/header/message.png"/>
								<input id="register_passwordyzm" style="width: 50%;" placeholder="请输入验证码" type="text" name="" id="" value=""/>
							</div>
						</div>
						<!--快速注册-->
						<div class="kszc" style="display: none;">
							<div class="subm">
								<img src="../imager/header/loginuser.png"/>
								<input id="register_number" type="text" placeholder="请输入你的电话号码" name=""  value="" />
							</div>
							<div class="subm" style="margin-top:20px;">
								<img style="margin-top: 11px;" src="../imager/header/message.png"/>
								<input id="register_yzm" style="width: 50%;" placeholder="请输入验证码" type="text" name="" id="" value=""/>
								<a href="javascript:void(0)">获取验证码</a>
							</div>
						</div>
						<a href="javascript:void(0)" onclick="submit()" class="submit" style="margin-top:45px;">立即注册</a>
						<p style="text-align: center;margin-top:20px;font-size: 14px;">已有账号?<a style="color:#1eb357;margin: 0;" href="javascript:void(0)">点击登录</a></p>
					</div>
				</div>
			</div>
<!--<div class="content clearfix">
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
        <h2>安全登录1</h2>
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
</div>-->

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


function blockphone(){
	$('#usernamenumber').attr("placeholder","请输入你的电话号码");
};
function blockEmile(){
	$('#usernamenumber').attr("placeholder","请输入你的邮箱");
};
function kszc(){
	$('.ptzc').hide();
    $('.kszc').show();
	$('#register_view').css({
		'marginTop':'75px',
		'height':'170px',
	});

	$('.kszc1').css('border-bottom','2px solid #1eb357');
	$('.ptzc1').css('border','none');
};
function ptzc(){
	$('#register_view').css({
		'marginTop':'35px',
		'height':'273px',
	});
   
    $('.ptzc').show();
    $('.kszc').hide();
	$('.ptzc1').css('border-bottom','2px solid #1eb357');
	$('.kszc1').css('border','none');
};
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





