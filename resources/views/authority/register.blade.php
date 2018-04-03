@extends('l.login', array('active' => 'signin'))

@section('title') 注册 - @parent - 用户 @stop

@section ('styles')
    @parent
    {{ style('reg') }}
@stop

@section('scripts')
    @parent
    {{ script('global')}}
@stop

@section('container')

    @include('w.public-header')
    <div id="reg-content" class="wrap mt18">
        <div class="wrap-inner clearfix">
            <div class="normal-title">欢迎注册金芒果账户</div>
            <div class="container">
                <form action="#" method="post" id="signupForm">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="_random" value="{{ Tool::createRandomStr() }}">
                    <input type="hidden" name="parent_id" value="{{ Input::get('uid')}}">
                    <!-- 密码强度 -->
                    <input type="hidden" name="password_strength" id="password_strength"/>
                    <ul class="form-ul ml190">
                        <li class="space-li" data-action="account">
                            <label>用户名&nbsp;:</label>
                            <div class="input-group">
                                <input type="text" tabindex="1" id="username" class="input" placeholder="请填写用户名"
                                       name="username" value=""/>
                            </div>
                            <span class="c-red fs18 mr4">*</span>
                            <span class="c-light fs10">用户名长度为5-16个字符，可使用数字、英文、中文</span>
                            <div id="username-error" class="c-error ml90"></div>
                        </li>
                        <li class="password-li" data-action="account">
                            <label>密码&nbsp;:</label>
                            <div class="input-group">
                                <input type="password" name="password" tabindex="3" id="password" class="input" value=""
                                       placeholder="请填写密码"/>
                            </div>
                            <span class="c-red fs18 mr4">*</span>
                            <span class="c-light width390px line-height16 fs10">由字母和数字组成6-16个字符； 且必须包含数字和字母，不允许连续三位相同,不能和资金密码相同</span>
                            <div id="password-error" class="c-error ml90"></div>
                        </li>

                        <li data-action="account" class="clearfix">
                            <label class="fl">密码强度:</label>
                            <div class="hard-block fl ml4">
                                <div class="fs10">
                                    <span class="c-light fl line-height22">强度&nbsp;<em
                                                class="c-table-link">低</em></span>
                                    <span class="c-light ml87 line-height22">强度&nbsp;<em class="c-blue">中</em></span>
                                    <span class="c-light fr mr2 line-height22">强度&nbsp;<em class="c-green">高</em></span>
                                </div>
                                <div class="progress-wrap">
                                    <div class="progress-block" id="passwordStrength"
                                         data-originCls="progress-block"></div>
                                </div>
                            </div>
                        </li>

                        <li class="repassword-li" data-action="account">
                            <label>确认密码&nbsp;:</label>
                            <div class="input-group">
                                <input type="password" tabindex="4" class="input" id="password_confirmation"
                                       name="password_confirmation"/>
                            </div>
                            <span class="c-red fs18 mr4">*</span>
                            <div id="password_confirmation-error" class="c-error ml90"></div>
                        </li>


                        <li>
                            <label>&nbsp;</label>
                            <div class="input-group clearfix overflow-hidden">
                                <input type="checkbox" class="vertical-middle mr4 -mt3" name="remember" id="remember"/>我已年满18周岁并同意<a
                                        href="javascript:;" class="c-link" id="service-rule-link">《网站服务条款》</a>
                            </div>
                            <div id="remember-error" class="c-error ml90"></div>
                        </li>
                        <li class="hide" id="service-rule-wrap">
                            <label>&nbsp;</label>
                            <textarea class="service-rule resize-none">@include('authority.service-rule')</textarea>

                        </li>

                        <li class="button-li">
                            <label>&nbsp;</label>
                            <input onclick="history.go(-1)" type="button" tabindex="6"
                                   class="btn-base btn-gray width80px" value="取消"/>
                            <input type="submit" tabindex="7" class="btn-base btn-brown ml6 width80px" value="立即注册"/>
                        </li>
                    </ul>

                </form>
            </div>


        </div>
    </div>

@stop

@section('end')
    @parent
    <script>
        (function ($) {
            $(document).on('click', '#service-rule-link', function () {
                $('#service-rule-wrap').toggle();
            });
            var form = document.getElementById('signupForm');
            var validator = new Validator();

            validator.listen(form.password, [{
                strategy: 'passwordStrength',
                option: {
                    sourceDom: form.password,
                    operationDom: $('#passwordStrength').get(0),
                    relationInput: $('#password_strength').get(0)
                }
            }, {
                strategy: 'sixNumberCode',
                option: {
                    errorMsg: '手机验证码必须为6位数字',
                    sourceDom: form.captcha,
                    errorMsgDom: $('#captcha-error').get(0)
                }
            }
            ]);

            var validataFunc = function () {

                validator.add(form.username, [{
                    strategy: 'isNonEmpty',
                    option: {
                        errorMsg: '用户名不能为空',
                        sourceDom: form.username,
                        errorMsgDom: $('#username-error').get(0)
                    }

                }, {
                    strategy: 'minLength',
                    option: {
                        minlength: 5,
                        errorMsg: '用户名长度不能小于5位',
                        sourceDom: form.username,
                        errorMsgDom: $('#username-error').get(0)
                    }

                }, {
                    strategy: 'maxLength',
                    option: {
                        maxLength: 16,
                        errorMsg: '用户名长度不能大于16位',
                        sourceDom: form.username,
                        errorMsgDom: $('#username-error').get(0)
                    }
                }]);

/*                //手机号码验证
                validator.add(form.phoneNumber, [{
                    strategy: 'isNonEmpty',
                    option: {
                        errorMsg: '手机号码不能为空',
                        sourceDom: form.phoneNumber,
                        errorMsgDom: $('#phoneNumber-error').get(0)
                    }

                }, {
                    strategy: 'isMobile',
                    option: {
                        errorMsg: '请输入正确的手机号码',
                        sourceDom: form.phoneNumber,
                        errorMsgDom: $('#phoneNumber-error').get(0)
                    }
                }]);

                validator.add(form.captcha, [{
                    strategy: 'isNonEmpty',
                    option: {
                        errorMsg: '手机验证码不能为空',
                        sourceDom: form.captcha,
                        errorMsgDom: $('#captcha-error').get(0)
                    }
                },
                    {
                        strategy: 'minLength',
                        option: {
                            minlength: 6,
                            errorMsg: '手机验证码不能小于6位',
                            sourceDom: form.captcha,
                            errorMsgDom: $('#captcha-error').get(0)
                        }

                    }]);*/


                validator.add(form.password, [{
                    strategy: 'isNonEmpty',
                    option: {
                        errorMsg: '密码不能为空',
                        sourceDom: form.password,
                        errorMsgDom: $('#password-error').get(0)
                    }

                }, {
                    strategy: 'minLength',
                    option: {
                        minlength: 6,
                        errorMsg: '密码长度不能小于6位',
                        sourceDom: form.password,
                        errorMsgDom: $('#password-error').get(0)
                    }
                }, {
                    strategy: 'maxLength',
                    option: {
                        maxLength: 16,
                        errorMsg: '密码长度不能大于16位',
                        sourceDom: form.password,
                        errorMsgDom: $('#password-error').get(0)
                    }
                }, {
                    strategy: 'passwordShouldContainLetterAndNumber',
                    option: {
                        errorMsg: '密码必须包含数字和字母',
                        sourceDom: form.password,
                        errorMsgDom: $('#password-error').get(0)
                    }
                }, {
                    strategy: 'isContinuousEqual',
                    option: {
                        errorMsg: '密码不允许连续三位相同字符',
                        sourceDom: form.password,
                        errorMsgDom: $('#password-error').get(0)
                    }
                }]);


                validator.add(form.password_confirmation, [{
                    strategy: 'isNonEmpty',
                    option: {
                        errorMsg: '确认密码不能为空',
                        sourceDom: form.password_confirmation,
                        errorMsgDom: $('#password_confirmation-error').get(0)
                    }
                }, {
                    strategy: 'isEqual',
                    option: {
                        errorMsg: '两次输入密码不一致，请重新输入',
                        sourceDom: form.password_confirmation,
                        compareDom: form.password,
                        errorMsgDom: $('#password_confirmation-error').get(0)
                    }
                }]);


                validator.add(form.remember, [{
                    strategy: 'isChecked',
                    option: {
                        errorMsg: '请同意网站服务条款',
                        sourceDom: form.remember,
                        errorMsgDom: $('#remember-error').get(0)
                    }
                }]);


                var errorMsg = validator.start();
                return errorMsg;
            }
            form.onsubmit = function () {
                var errorMsg = validataFunc();
                if (errorMsg) {
                    return false;
                }
            };




        })(jQuery);

    </script>
@stop

