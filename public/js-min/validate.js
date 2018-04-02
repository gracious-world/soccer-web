/*表单验证*/
function coreValidatorFunction(o, rule) {
    var sourceDom = o.sourceDom;
    var errorMsgDom = o.errorMsgDom;
    var errorCls = o.errorCls || 'border-red';
    var errorMsg = o.errorMsg;
    if (rule) {
        errorMsgDom.innerHTML = '<span class="c-red">'+errorMsg+'</span>';
        $(sourceDom).addClass(errorCls);
        return errorMsg;
    } else {
        var original = $(errorMsgDom).attr('original');
        $(errorMsgDom).html(original);
        $(sourceDom).removeClass(errorCls);
        return void 0;
    }
}


/*
  密码检测密码强度
*/

function detectionPasswordStrength(sValue) {
    var modes = 0;
    //正则表达式验证符合要求的
    if (sValue.length < 1) return modes;
    if (/\d/.test(sValue)) modes++; //数字
    if (/[a-z]/.test(sValue)) modes++; //小写
    if (/[A-Z]/.test(sValue)) modes++; //大写
    if (/\W/.test(sValue)) modes++; //特殊字符
    //逻辑处理
    switch (modes) {
        case 1:
            return 1;
            break;
        case 2:
            return 2;
        case 3:
        case 4:
            return sValue.length < 12 ? 3 : 4
            break;
    }
}
/***********************策略对象**************************/
var strategies = {
    /*
        o = {sourceDom:sourceDom,errorMsg:errorMsg,errorMsgDom:errorMsgDom}
        验证checkbox是否选择
    */
    isChecked: function(o) {
        return coreValidatorFunction(o, !o.sourceDom.checked);
    },
    /*
        o = {sourceDom:sourceDom,errorMsg:errorMsg,errorMsgDom:errorMsgDom}
        验证电话号码是否正确
    */
    isMobile: function(o) {
        return coreValidatorFunction(o, !/(^1[3|5|8][0-9]{9}$)/.test($.trim(o.sourceDom.value)));
    },
    /*
        o = {sourceDom:sourceDom,errorMsg:errorMsg,errorMsgDom:errorMsgDom}
        验证邮箱号码是否正确
    */
    isEmail: function(o) {
        return coreValidatorFunction(o, !/\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*/.test($.trim(o.sourceDom.value)));
    },

    /*
        o = {sourceDom:sourceDom,errorMsg:errorMsg,errorMsgDom:errorMsgDom}
        验证银行卡账号是否正确
    */
    isBankCardNumber: function(o) {
        return coreValidatorFunction(o, !/^\d{16}|\d{19}$/.test($.trim(o.sourceDom.value).replace(/\s/g, '')));
    },
    /*
        o = {sourceDom:sourceDom,errorMsg:errorMsg,errorMsgDom:errorMsgDom}
    */
    isNonEmpty: function(o) {
        return coreValidatorFunction(o, $.trim(o.sourceDom.value).length === 0);
    },
    /*
       o = {sourceDom:sourceDom,errorMsg:errorMsg,errorMsgDom:errorMsgDom,minlength:minlength}
    */
    minLength: function(o) {
        return coreValidatorFunction(o, $.trim(o.sourceDom.value).length < o.minlength);
    },
    /*
       o = {sourceDom:sourceDom,errorMsg:errorMsg,errorMsgDom:errorMsgDom,maxLength:maxLength}
    */
    maxLength: function(o) {
        return coreValidatorFunction(o, $.trim(o.sourceDom.value).length > o.maxLength);
    },
    /*
       o = {sourceDom:sourceDom,errorMsg:errorMsg,errorMsgDom:errorMsgDom,compareDom:compareDom}
    */
    isEqual: function(o) {
        return coreValidatorFunction(o, $.trim(o.sourceDom.value) !== $.trim(o.compareDom.value));
    },

     /*
       o = {sourceDom:sourceDom,errorMsg:errorMsg,errorMsgDom:errorMsgDom,compareDom:compareDom}
    */
    canNotEqual: function(o) {
        return coreValidatorFunction(o, $.trim(o.sourceDom.value) === $.trim(o.compareDom.value));
    },
    

    /*
       o = {sourceDom:sourceDom,errorMsg:errorMsg,errorMsgDom:errorMsgDom}
       不允许连续三位相同字符
    */
    isContinuousEqual: function(o) {
        var sourceDom = o.sourceDom;
        var errorMsgDom = o.errorMsgDom;
        var errorCls = o.errorCls || 'border-red';
        var errorMsg = o.errorMsg;

        var value = $.trim(sourceDom.value);
        var chartArray = value.split('');
        var result = false;
        for (var i = 0, len = chartArray.length; i < len - 2; i++) {
            if (chartArray[i] == chartArray[i + 1] && chartArray[i + 1] == chartArray[i + 2]) {
                result = true;
                break;
            }
        }

        return coreValidatorFunction(o, result);
    },
    /*

        o = {sourceDom:sourceDom,errorMsg:errorMsg,errorMsgDom:errorMsgDom}  listenPasswordStrength
    */
    passwordShouldContainLetterAndNumber: function(o) {
        var sourceDom = o.sourceDom;
        var errorMsgDom = o.errorMsgDom;
        var errorCls = o.errorCls || 'border-red';
        var errorMsg = o.errorMsg;

        var value = $.trim(sourceDom.value);
        var index = detectionPasswordStrength(value);
        return coreValidatorFunction(o, index < 2);
    },

    /*
        密码强度 事件监听
        o = {sourceDom:sourceDom,operationDom:operationDom}  listenPasswordStrength
    */
    passwordStrength: function(o) {
        var sourceDom = o.sourceDom;
        var operationDom = o.operationDom;
        var relationInput = o.relationInput;
        var lowCls = "width10p";
        var middelCls = "width50p";
        var hightCls = "width90p";
        var veryHightCls = "width100p";

        $(sourceDom).bind('keyup onfocus onblur', function() {
            var value = $.trim(this.value)
            var index = detectionPasswordStrength(value);
            var originCls = $(operationDom).attr('data-originCls');
            relationInput.value = index;
            switch (index) {
                case 1:
                    operationDom.className = originCls;
                    $(operationDom).addClass(lowCls);
                    break;
                case 2:
                    operationDom.className = originCls;
                    $(operationDom).addClass(middelCls);
                    break;
                case 3:
                    operationDom.className = originCls;
                    $(operationDom).addClass(hightCls);
                    break;
                case 4:
                    operationDom.className = originCls;
                    $(operationDom).addClass(veryHightCls);
                    break;
                default:
                    operationDom.className = originCls;
            }

        });
    },
    /*o = {sourceDom:sourceDom,errorMsg:errorMsg,errorMsgDom:errorMsgDom}*/
    sixNumberCode:function(o){
        var sourceDom = o.sourceDom;
        $(sourceDom).on('keydown', function(event) {
            if (!(event.keyCode == 35) && !(event.keyCode == 36) && !(event.keyCode == 46) && !(event.keyCode == 8) && !(event.keyCode == 37) && !(event.keyCode == 39)) {
                if (!((event.keyCode >= 48 && event.keyCode <= 57) || (event.keyCode >= 96 && event.keyCode <= 105))) {
                    return false;
                }
                if ($.trim($(this).val()).length > 5) {
                    return false;
                }
            }
        });


       // return coreValidatorFunction(o,reg);
    }

};

/***********************Validator 类**************************/
var Validator = function() {
    this.cache = [];
};
//表单验证
Validator.prototype.add = function(dom, rules) {
    var self = this;
    for (var i = 0, rule; rule = rules[i++];) {
        (function(rule) {
            self.cache.push(function() {
                return strategies[rule.strategy].apply(dom, [rule.option]);
            });
        })(rule);
    }
};


//表单事件监听
Validator.prototype.listen = function(dom, rules) {
    var self = this;
    for (var i = 0, rule; rule = rules[i++];) {
        (function(rule) {
            strategies[rule.strategy].apply(dom, [rule.option]);
        })(rule);
    }
}

Validator.prototype.start = function() {
    var errorMsg;
    for (var i = 0, validatorFunc; validatorFunc = this.cache[i++];) {
        errorMsg = validatorFunc();
        if (errorMsg) break;
    }
    return errorMsg;
};

//高亮Tip提示用户输入的数字，应用场景 银行卡输入，金额输入等
Validator.tipmoney = function(selector) {
    var $money = $(selector),
        tip = new gagame.Tip({
            cls: 'j-ui-tip-b j-ui-tip-input-floattip'
        });
    $money.keyup(function(e) {
        var v = $.trim(this.value),
            arr = [],
            code = e.keyCode;
        if (code == 37 || code == 39) {
            return;
        }
        v = v.replace(/[^\d|^\.]/g, '');
        arr = v.split('.');
        if (arr.length > 2) {
            v = '' + arr[0] + '.' + arr[1];
        }
        arr = v.split('.');
        if (arr.length > 1) {
            arr[1] = arr[1].substring(0, 2);
            v = arr.join('.');
        }
        this.value = v;
        v = v == '' ? '&nbsp;' : v;
        tip.setText(v);
        tip.getDom().css({
            left: $money.offset().left + $money.width() / 2 - tip.getDom().width() / 2
        });
    });
    $money.focus(function() {
        var v = $.trim(this.value);
        if (v == '') {
            v = '&nbsp;';
        }
        tip.setText(v);
        tip.show($money.width() / 2 - tip.getDom().width() / 2, tip.getDom().height() * -1 - 20, this);
    });
    $money.blur(function() {
       /* var v = Number(this.value),
            maxNum = Number('70,000.00'.replace(/,/g, ''));
        v = v > maxNum ? maxNum : v;
        this.value = gagame.util.formatMoney(v).replace(/,/g, '');*/
        tip.hide();
    });
}
/*
    {
        selectorSendPhoneCode:'#sendPhoneCode', // 发送手机验证码选择器
        selectorPhone:'#newPhoneNumber', //手机号码input选择器
        smsUrl:"{{route("send-sms-message")}}",//发送验证码接口
        selectorReciprocal:'#reciprocal',// 倒数多少秒元素选择器
        _token:_token值,
        isShowSuccessInfo:true  
    }
*/
Validator.sendPhoneCodeLimit = function(option){
    //点击发送验证码
    var isSendPhonCode = false; 
    $(document).on('click', option.selectorSendPhoneCode, function() {
        var mobile = null;
        if (option.selectorPhone != null) {
            mobile = $(option.selectorPhone).val();
            if ($.trim(mobile).length == 0) {
                alert('请输入手机号码');
                return;
            }
            if (!/(^1[3|5|8][0-9]{9}$)/.test(mobile)) {
                alert('请输入正确的手机号码');
                return;
            }
        }
         if(isSendPhonCode) return;
        //$(this).text('重新发送');
        reciprocalSecond();
       isSendPhonCode = true;
        $.ajax({
            url: option.smsUrl,
            type: "POST",
            data: {
                'mobile': mobile,
                _token:option._token,
                type:option.type
            },
            dataType: "JSON",
            success: function(data) {
                if (data == 1) {
                    if(option.isShowSuccessInfo && option.isShowSuccessInfo == false) return;
                    alert("发送成功!") ;
                } else {
                    alert(data.message);
                }
               
            },
            fail: function(data) {
                alert("发送失败");
                isSendPhonCode = false;
            }
        });

    });

    //倒数，发送手机验证码
    var reciprocal = $(option.selectorReciprocal);
    var reciprocals = option.limit || 60;
    var reciprocalId = null;

    function reciprocalSecond() {
        reciprocalId = setInterval(function() {
            if (reciprocals == 0) {
                clearInterval(reciprocalId);
                //todo 再次发送手机验证码
                isSendPhonCode = false;
                reciprocal.val('再次发送');
            } else {
                --reciprocals;
                reciprocal.html(reciprocals + '秒后再次发送');
                reciprocal.val(reciprocals + '秒后再次发送');
            }

        }, 1000);
    }
}