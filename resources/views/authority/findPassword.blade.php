@extends('l.login', array('active' => 'signin'))

@section('title') 注册 - @parent - 用户 @stop

@section ('styles')
    @parent
    {{ style('reg') }}
@stop

@section('scripts')
    @parent
    {{ script('validate')}}

@stop

@section('container')

    <div class="wrap">
        <div class="wrap-inner box-findPassword">
            <h1 class="text-center">找回密码</h1>
            <ol class="fs-step fs-step-4">
                <li class="fs-step-active start">
                    <div class="step-back">
                        <div class="left"></div>
                        <div class="right"></div>
                        <div class="all"></div>
                    </div>
                    <div class="step-no ie6Png" >1</div>
                    <div class="step-name">请输入需要找回密码的用户名</div>
                </li>
                <li class="fs-step-inactive ">
                    <div class="step-back">
                        <div class="left"></div>
                        <div class="right"></div>
                        <div class="all"></div>
                    </div>
                    <div class="step-no ie6Png" >2</div>
                    <div class="step-name">选择密码找回方式</div>
                </li>
                <li class="fs-step-inactive ">
                    <div class="step-back">
                        <div class="left"></div>
                        <div class="right"></div>
                        <div class="all"></div>
                    </div>
                    <div class="step-no ie6Png" >3</div>
                    <div class="step-name">安全验证</div>
                </li>
                <li class="fs-step-inactive end">
                    <div class="step-back">
                        <div class="left"></div>
                        <div class="right"></div>
                        <div class="all"></div>
                    </div>
                    <div class="step-no ie6Png" >4</div>
                    <div class="step-name">重设密码</div>
                </li>
            </ol>

            <div class="box-pwdContent">
                <form id="signupForm" action="{{route('find-password')}}" method="post">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                    <input type="hidden" name="_method" value="PUT" />
                    <div class="title">请输入您需要找回密码的用户名</div>
                    <div class="clearfix oneLine">
                        <div class="f-4 label">用户名：</div>
                        <div class="f-3"><input type="text" name="username" class="input"></div>
                    </div>
                    <div class="clearfix oneLine">
                        <div class="f-4 label">验证码：</div>
                        <div class="f-1"><input type="text" name="captcha" class="input" id="checkCode"></div>
                        <div class="f-0">
                            <a class="verify" href="javascript:changeCaptcha();" title="{{ Lang::get('transfer.Captcha') }}">
                                <img id="captchaImg"  src="{{ Captcha::img() }}"/>
                            </a>
                        </div>
                        <div class="f-0"><a href="javascript:changeCaptcha();" id="refreshCaptcha">刷新</a></div>
                        <div class="f-3 light" id="checkCodeError"></div>
                    </div>
                    <div class="clearfix ">
                        <div class="f-4 label"></div>
                        <div class="f-3"><input type="submit" name="" class="btn" value="下一步"/></div>
                    </div>
                </form>
            </div>

        </div>
    </div>
    @include('w.notification')
@stop


@section('end')
    @parent

    <script>
        function changeCaptcha() {
            document.getElementById('captchaImg').src = "{{ URL::to('captcha?') }}" + ((Math.random() * 9 + 1) * 100000).toFixed(0);
        };
    </script>
@stop