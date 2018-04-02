//公用页面用切换
function swh(a,b,c){
	$(b).each(function(){$(this).children().first().show().siblings().hide();});
	$(a).click(function () {
		var index = $(this).index();
		$(this).addClass(c).siblings().removeClass(c);
		$(this).parent().next().children().eq(index).show().siblings().hide();
	});
}

//平滑滚动到锚点位置
function Toscroll(obj) {
	$('html,body').animate({scrollTop:$(obj).offset().top}, 500);
};

//多选标签
function chgele(lelet, onstyle){
	lelet.on({
		click:function(){
				if(!$(this).attr("onstyle")){
					$(this).attr("onstyle","true").addClass(onstyle);
				}else{
					$(this).removeAttr("onstyle").removeClass(onstyle);
				};
			},
		mouseenter:function(){
				if(!$(this).attr("onstyle")){
					$(this).addClass(onstyle);
				};
			},
		mouseleave:function(){
				if(!$(this).attr("onstyle")){
					$(this).removeClass(onstyle);
				};
			}
	});
};

//分页
function fenye(a,b,c,d,e,h,i,j){
	$(a).mouseenter(function(){
		$(a).removeClass(i);
		$(this).addClass(i);
		});
	$(a).mouseleave(function(){
		$(a).removeClass(i);
		});
	$(b).click(function(){
		$(b).removeClass(j);
		$(this).addClass(j);
		});
	$(c).click(function(){
		$(d).next().addClass(j);
		$(d).prev().removeClass(j);
		});
	$(e).click(function(){
		$(d).prev().addClass(j);
		$(d).next().removeClass(j);
		});
}

//验证码倒计时
var wait=60;
function time(o) {
    if (wait == 0) {
        o.removeAttribute("disabled");
        o.value="点击发送验证码";
        wait = 60;
    } else {
        o.setAttribute("disabled", true);
        o.value= wait + "秒后重新发送";
        wait--;
        setTimeout(function() {
            time(o)
        },
        1000)
    }
}

$(function(){
	//图片切换
	var sWidth = $(".bnrfocus").width(); //获取焦点图的宽度（显示面积）
	var sHeight = $(".bnrfocus").height();
	var len = $(".bnrfocus ul li").length; //获取焦点图个数
	var index = 0;
	var picTimer;

	//添加按钮
	var btn = "<div class='bnrlist'>";
	for(var i=0; i < len; i++){
		btn += "<span>" + "</span>";
	}
	btn += "</div>"
	$(".bnrfocus").append(btn);

	$(".bnrfocusmid").width(sWidth).height(sHeight);


	//为按钮添加鼠标点击事件，以显示相应的内容
	$(".bnrlist span").click(function(){
		index = $(".bnrlist span").index(this);
		showPics(index);
	}).eq(0).trigger("click");

	//鼠标滑入某li中的某div里，调整其同辈div元素的透明度，由于li的背景为黑色，所以会有变暗的效果
	$(".bnrfocus ul li div").hover(function(){
		$(this).css("opacity",0.9);
	},function() {
		$(".bnrfocus ul li div").css("opacity",1);
	});

	//左右滚动，即所有li元素都是在同一排向左浮动，所以这里需要计算出外围ul元素的宽度
	$(".bnrfocus ul").css("width",sWidth * (len + 1));

	//鼠标滑上焦点图时停止自动播放，滑出时开始自动播放
	$(".bnrfocus").hover(function(){
		clearInterval(picTimer);
	},function(){
		picTimer = setInterval(function(){
			if(index == len){ //如果索引值等于li元素个数，说明最后一张图播放完毕，接下来要显示第一张图，即调用showFirPic()，然后将索引值清零
				showFirPic();
				index = 0;
			}else{ //如果索引值不等于li元素个数，按普通状态切换，调用showPics()
				showPics(index);
			}
			index++;
		},2500); //此3000代表自动播放的间隔，单位：毫秒
	}).trigger("mouseleave");

	//显示图片函数，根据接收的index值显示相应的内容
	function showPics(index){
		//普通切换
		var nowLeft = -index*sWidth;
		//根据index值计算ul元素的left值
		$(".bnrfocus ul").stop(true,false).animate({"left":nowLeft},500);
		//通过animate()调整ul元素滚动到计算出的position
		$(".bnrlist span").removeClass("on").eq(index).addClass("on");
		//为当前的按钮切换到选中的效果
	}

	function showFirPic(){
		//最后一张图自动切换到第一张图
		$(".bnrfocus ul").append($(".bnrfocus ul li:first").clone());
		var nowLeft = -len*sWidth;
		//通过li元素个数计算ul元素的left值，也就是最后一个li元素的右边
		$(".bnrfocus ul").stop(true,false).animate({"left":nowLeft},500,function(){
			//通过callback，在动画结束后把ul元素重新定位到起点，然后删除最后一个复制过去的元素
			$(".bnrfocus ul").css("left","0");
			$(".bnrfocus ul li:last").remove();
		}); 
		$(".bnrlist span").removeClass("on").eq(0).addClass("on");
		//为第一个按钮添加选中的效果
	}
});

(function($) {
    var bankCache = {
        "1": {
            "id": 1,
            "identifier": "ICBC",
            "name": null
        },
        "2": {
            "id": 2,
            "identifier": "CCB",
            "name": null
        },
        "3": {
            "id": 3,
            "identifier": "ABC",
            "name": null
        },
        "4": {
            "id": 4,
            "identifier": "BOC",
            "name": null
        },
        "5": {
            "id": 5,
            "identifier": "CMB",
            "name": null
        },
        "6": {
            "id": 6,
            "identifier": "BCOM",
            "name": null
        },
        "7": {
            "id": 7,
            "identifier": "CMBC",
            "name": null
        },
        "8": {
            "id": 8,
            "identifier": "ECITIC",
            "name": null
        },
        "9": {
            "id": 9,
            "identifier": "SPDB",
            "name": null
        },
        "11": {
            "id": 11,
            "identifier": "SPABANK",
            "name": null
        },
        "13": {
            "id": 13,
            "identifier": "CIB",
            "name": null
        },
        "14": {
            "id": 14,
            "identifier": "HXB",
            "name": null
        },
        "15": {
            "id": 15,
            "identifier": "CEBB",
            "name": null
        },
        "16": {
            "id": 16,
            "identifier": "PSBC",
            "name": null
        }
    };
    var banks = $('#J-bank-list').children(),
    inputs = banks.find('input'),
    loadBankInfoById,
    buildingView,
    moneyInput = $('#J-input-money');

    loadBankInfoById = function(id, callback) {
        var data = bankCache[id];
        callback(data);
    };
    // buildingView = function(bankData) {
    //     $('#J-money-min').text(gagame.util.formatMoney(Number(bankData['min'])));
    //     $('#J-money-max').text(gagame.util.formatMoney(Number(bankData['max']))); //1,000,000.00        $('#J-input-money').val('');
    //     $('#J-input-password').val('');
    // };

    // 选择银行卡下拉
    var $dropdown = $('.bank_dropdown');
    var $banklists = $dropdown.find('.bank-list');
    var initBankId = $('#bank-name').val();
    $dropdown.on({
        mousedown: function(e) {
            if ($(this).hasClass('open')) return false;
            $(this).addClass('open');
            // return false;
        }
        // 点击
        ,
        click: function(e) {
            e.preventDefault();
        }
        // 失去焦点
        ,
        blur: function(e) {
            console.log('失去焦点');
            $(this).removeClass('open');
        }
    });
    $banklists.find('label').on('click',
    function() {
        var $bank = $(this).find('.ico-bank');
        var value = $bank.data('id');

        // addClass/removeClass active
        $(this).siblings('.active').removeClass('active').end().addClass('active');

        // replace html
        $('.dropdown_toggle .ico-bank').replaceWith($bank.clone());

        // change input value
        $('#bank-name').val(value);

        $dropdown.removeClass('open');

        //loadBankInfoById(value, buildingView);
    }).eq(0).trigger('click');

    moneyInput.keyup(function(e) {
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
	    v = v == '' ? '&nbsp;': v;
	    // tip.setText(v);
	    // tip.getDom().css({
	    //     left: moneyInput.offset().left + moneyInput.width() / 2 - tip.getDom().width() / 2
	    // });
	});
	moneyInput.focus(function() {
	    var v = $.trim(this.value);
	    if (v == '') {
	        v = '&nbsp;';
	    }
	    // tip.setText(v);
	    // tip.show(moneyInput.width() / 2 - tip.getDom().width() / 2, tip.getDom().height() * -1 - 20, this);
	});
    moneyInput.blur(function() {
        var v = Number(this.value),
        minNum = Number($('#J-money-min').text().replace(/,/g, '')),
        maxNum = Number('1,000,000.00'.replace(/,/g, '')) //Number($('#J-money-max').text().replace(/,/g, ''));
        //       v = v < minNum ? minNum : v;
        //        v = v > maxNum ? maxNum : v;
         if (v < minNum || v > maxNum) {
                $('#amount_notify').html('金额超出范围：金额须在' + minNum + '至' + maxNum + '元之间');
                this.focus();
        };
        if(v != ""){
        	$(this).val(parseFloat($(this).val()).toFixed(2));
        }
    });

    $('#J-submit').click(function() {
        var money = $('#J-input-money'),
        password = $('#J-input-password'),
        banks = $('input[name="bank"]').val();
        // bankCard = $('.choose-input-disabled');
        //if没有开启银行卡判断
        if (banks == undefined || banks == '') {
            alert('请选择充值银行');

            return false;
        }

        if ($.trim(money.val()) == '') {
            alert('金额不能为空');
            money.focus();
            return false;
        }

        var v = Number(money.val()),
        minNum = Number($('#J-money-min').text().replace(/,/g, '')),
        maxNum = Number('1,000,000.00'.replace(/,/g, '')) //Number($('#J-money-max').text().replace(/,/g, ''));
        if (v < minNum || v > maxNum) {
            alert('金额超出范围：金额须在' + minNum + '至' + maxNum + '元之间');
            money.focus();
            return false;
        }

        return true;
    });

})(jQuery);

//用于注册页面输入框交互
var ckbox = $(".actcont table tr td .checkbox").find("input"),
	password = $("input[type='password']"),
	nochinese = $("input[name='usename']"),
	nips = $("input[name='name']"),
	bbkank = $("input[name='bbkank']"),
	myReg = /^\w*$/;

//弹出层zDialog插件(修改)
function open1()
{
	Dialog.open({URL:"../../view/global/test.html"});
}
function open2()
{
	var diag = new Dialog();
	diag.Width = 480;
	diag.Height = 260;
	diag.Title = "提示";
	diag.URL = "../../view/global/test.html";
	diag.show();
}
function open3()
{
	var diag = new Dialog();
	diag.Width = 900;
	diag.Height = 400;
	diag.Title = "内容页为外部连接的窗口";
	diag.URL = "../../view/global/test.html";
	diag.show();
}
function open4()
{
	var diag = new Dialog();
	diag.Width = 480;
	diag.Height = 124;
	diag.Title = "内容页为html代码的窗口";
	diag.InnerHtml='<div style="text-align:center;color:red;font-size:14px;">直接输出html，使用 <b>InnerHtml</b> 属性。</div>'
	diag.OKEvent = function(){diag.close();};//点击确定后调用的方法
	diag.show();
}
function open5()
{
	var diag = new Dialog();
	diag.Width = 300;
	diag.Height = 150;
	diag.Title = "内容页为隐藏的元素的html";
	diag.InvokeElementId="forlogin"
	diag.OKEvent = function(){topWin.$id("username").value||Dialog.alert("用户名不能为空");topWin.$id("userpwd").value||Dialog.alert("密码不能为空")};//点击确定后调用的方法
	diag.show();
}
function open6()
{
	var diag = new Dialog();
	diag.Modal = false;
	diag.Left = 400;
	diag.Title = "弹出没有遮罩层的窗口";
	diag.URL = "../../view/global/test.html";
	diag.show();
}
function closdlg()
{
    Dialog.close();
}
function open7()
{
	var diag = new Dialog();
	diag.Width = 200;
	diag.Height = 100;
	diag.Modal = false;
	diag.Title = "在指定位置弹出窗口";
	diag.Top="100%";
	diag.Left="100%";
	diag.URL = "../../view/global/test.html";
	diag.show();
}
function open8()
{
	var diag = new Dialog();
	diag.Title = "返回值到调用页面";
	diag.URL = "../../view/global/test.html";
	diag.OKEvent = function(){$id('getval').value = diag.innerFrame.contentWindow.document.getElementById('a').value;diag.close();};
	diag.show();
	var doc=diag.innerFrame.contentWindow.document;
	doc.open();
	doc.write('<html><body><input id="a" type="text"/>请在文本框里输入一些值</body></html>') ;
	doc.close();
}
function open9()
{
	//注册页面输入框交互
	var psda = password.eq(0).val(),
		psdb = password.eq(1).val(),
		bbka = bbkank.eq(0).val(),
		bbkb = bbkank.eq(1).val(),
		usever = $(".vertion"),
		ver = usever.next().attr("value");
	//第一次银行卡号码字符长度判断
	if(typeof(bbka) != 'undefined' && (bbka.length < 16 || bbka.match(myReg))){
		bbkank.eq(0).parent().addClass("off").children("b").removeClass().addClass("false");
		Dialog.alert("您输入的卡号有误！");
		return false;
	}else{
		bbkank.eq(0).parent().removeClass("off").children("b").removeClass("false").addClass("true");
	}
	//第二次银行卡号码是否一致
	if(typeof(bbkb) != 'undefined' && bbka != bbkb){
		bbkank.eq(1).parent().addClass("off").children("b").removeClass("true").addClass("false");
		Dialog.alert("两次输入的银行卡号码不一致，请重新输入！");
		return false;
	}else{
		bbkank.eq(1).parent().removeClass("off").children("b").removeClass("false").addClass("true");
	}
	//用户名字符长度和中文判断
	if(typeof(nochinese.val()) != 'undefined' && (nochinese.val().length < 6 || nochinese.val().length >12 || nochinese.val().match(myReg) == null)){
		nochinese.parent().addClass("off").children("b").removeClass("true").addClass("false");
		Dialog.alert("该用户名已被使用或者不符合规范，请您重新设置用户名！");
		return false;
	}else{
		nochinese.parent().removeClass("off").children("b").removeClass("false").addClass("true");
	}
	//昵称字符长度判断
	if(typeof(nips.val()) != 'undefined' && (nips.val().length < 6 || nips.val().length > 12)){
		nips.parent().addClass("off").children("b").removeClass().addClass("false");
		Dialog.alert("该昵称已被使用或者不符合规范，请您重新设置昵称！");
		return false;
	}else{
		nips.parent().removeClass("off").children("b").removeClass("false").addClass("true");
	}
	//判断密码是否格式正确
	if(typeof(psda) != 'undefined' && (psda.length < 6 || psda.length > 12)){
		password.eq(0).parent().addClass("off").children("b").removeClass("true").addClass("false");
		Dialog.alert("密码长度范围为6-16位。");
		return false;
	}else{
		password.eq(0).parent().removeClass("off").children("b").removeClass("false").addClass("true");
	}
	//判断二次密码是否一致
	if(typeof(psdb) != 'undefined' && psda != psdb){
		password.eq(1).parent().addClass("off").children("b").removeClass("true").addClass("false");
		Dialog.alert("两次密码不相同，请重新输入。");
		return false;
	}else{
		password.eq(1).parent().removeClass("off").children("b").removeClass("false").addClass("true");
	}
	//判断验证码是否正确且不为空
	// if(usever.val() != ver || usever.val() == ""){
	//	usever.parent().addClass("off").children("b").removeClass("true").addClass("false");
	// 	Dialog.alert("验证码输入错误！");
	// 	return false;
	// }else{
	// 	usever.parent().removeClass("off").children("b").removeClass("false").addClass("true");
	// }
	//判断是否勾选服务条款
	if(typeof(ckbox.val()) != 'undefined' && ckbox.is(':checked') == false){
		Dialog.alert("请勾选同意服务条款。");
		return false;
	}
	
}
function open10()
{
	Dialog.confirm('警告：您确认要XXOO吗？',function(){Dialog.alert("yeah，周末到了，正是好时候")});
}
function open11()
{
	var diag = new Dialog();
	diag.Title = "创建其它按钮";
	diag.URL = "../../view/global/test.html";
	diag.show();
	diag.addButton("next","下一步",function(){
		var doc=diag.innerFrame.contentWindow.document;
		doc.open();
		doc.write('<html><body>进入了下一步</body></html>') ;
		doc.close();
		diag.removeButton(this);
	})
}
function open12()
{
	var diag = new Dialog();
	diag.Title = "带有说明栏的新窗口";
	diag.Width = 900;
	diag.Height = 400;
	diag.URL = "../../view/global/test.html";
	diag.MessageTitle = "泽元网站内容管理系统";
	diag.Message = "泽元网站内容管理系统是一个基于J2EE及AJAX技术的企业级网站内容管理系统";
	diag.show();
}

function open13()
{
	var diag = new Dialog();
	diag.URL = "../../view/global/test.html";
	diag.show();
}

function open14()
{
	var diag = new Dialog();
	diag.OnLoad=function(){alert("页面载入完成")};
	diag.URL = "../../global/test.html";
	diag.show();
}
function open15()
{
	var diag = new Dialog();
	diag.Title = "点击取消或关闭按钮时执行方法";
	diag.ShowButtonRow=true;
	diag.CancelEvent=function(){alert("点击取消或关闭按钮时执行方法");diag.close();};
	diag.URL = "../../global/test.html";
	diag.show();
}
function open16()
{
	var diag = new Dialog();
	diag.Title = "修改中窗体尺寸";
	diag.URL = "javascript:void(document.write(\'这是弹出窗口中的内容\'))";
	diag.OKEvent = function(){
		var doc=diag.innerFrame.contentWindow.document;
		doc.open();
		doc.write('<html><body>窗口尺寸改为600*300</body></html>') ;
		doc.close();
		diag.setSize(600,300);
		diag.okButton.disabled=true;
	};
	diag.show();
	diag.okButton.value="改变窗口大小"
}

function open17(val)
{
	var diag = new Dialog();
	diag.AutoClose=5;
	diag.ShowCloseButton=false;
	diag.URL = "javascript:void(document.write(\'这是弹出窗口中的内容\'))";
	diag.show();
}

function open18()
{
	var diag = new Dialog();
	diag.Title="设置确定按钮及取消按钮的属性";
	diag.ShowButtonRow=true;
	diag.URL = "javascript:void(document.write('确定改为OK，取消改为Cancel'))";
	diag.show();
	diag.okButton.value=" OK ";
	diag.cancelButton.value="Cancel";
}


function open19()
{
	var diag = new Dialog();
	diag.Title = "窗体内的按钮操作父Dialog";
	diag.URL = "../../global/test.html";
	diag.CancelEvent=function(){alert("我要关闭了");diag.close();};
	diag.show();
	var doc=diag.innerFrame.contentWindow.document;
	doc.open();
	doc.write('<html><body><input type="button" id="a" value="修改父Dialog尺寸" onclick="parentDialog.setSize(function(min,max){return Math.round(min+(Math.random()*(max-min)))}(300,800))" /> <input type="button" id="b" value="关闭父窗口" onclick="parentDialog.close()" /> <input type="button" id="b" value="点击窗口取消按钮" onclick="parentDialog.cancelButton.onclick()" /></body></html>') ;
	doc.close();
}
function test(){
	var diag = new Dialog();
	diag.OKEvent=function(){
		Dialog.alert("提交成功",function(){diag.close()})
	};
	diag.show();
}