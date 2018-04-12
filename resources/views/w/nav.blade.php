<?php $iUserLevel=Session::get("user_level");?>

{!! style('main') !!}
{!! script('jquery') !!}
{!! script('headerjs') !!}
<style >
    .qq_kf a img {display: none !important;}
</style>

<div id="header">
	<div class="header_view">
			<a href="#" style="float: left;">
				<img src="../imager/header/logo.png"/>
			</a>
		<div class="h_view_left">
			<div class="tongzhi">	
				<img style="margin-left: 8px;margin-top: 6px;float: left;" src="../imager/header/xlaba.png"/>
				<marquee direction=left style="color: white;width: 95%;">123456789</marquee>
			</div>
				<ul class="loginmenu">
					@if(Session::get('user_id'))
					 	<li style="color:white;">账户余额:<span data-user-account-balance=""  id="userBalance" style="color:red;">{!! $fAvailable !!}</span>元</span></li>
						<li  style="color:white;position: relative;">欢迎您!<a style="margin-left: 4px;" href="#">{{ Session::get('nickname')}}</a><i class="loutsession"></i><img style="margin-left: 24px;margin-top: -5px" src="../imager/header/USER.png"/>
							<div class="hidemenu">
								<i></i>
								<div><img src="../imager/header/USER.png"/><span>{{Session::get('nickname')}}</span></div>
								<div><img src="../imager/header/hidemenu_ye.png"/>余额:<span data-user-account-balance=""  id="userBalance" style="color:red;">{!! $fAvailable !!}</span>元</span></div>
								<div><img src="../imager/header/hidemenu_ck.png"/>存款</div>
								<div><img src="../imager/header/hidemenu_tx.png"/>提现</div>
								<div class="logout" style="border:none;"><img src="../imager/header/hidemenu_out.png"/>退出</div>
							</div>
						</li>
					@else
						<li><a href="#"><img style="float: left;margin-top: 4px;margin-right: 4px;" src="../imager/header/USER.png"/>您尚未登录</a></li>
						<li><a href="{!! route('signin') !!}" class="login">登录</a></li>
						<li><a href="{!! route('signup') !!}" class="registered">免费注册</a></li>
					@endif
				</ul>
			<div class="toumenu find_nav_list">
				<ul>
					 <li style="left: 0px;" class="find_nav_cur"><a href="#">首页</a></li>
					  <li style="left: 140px;"><a href="{!! route('games.index') !!}">竞猜投注</a></li>
					  <li style="left: 280px;"><a href="{!! route('user-transactions.mybonus') !!}">资金明细</a></li>
					  <li style="left: 420px;"><a href="{!! route('users.user') !!}">个人中心</a></li>
					  <li style="left: 560px;"><a target="_blank" href="http://wpa.qq.com/msgrd?v=3&uin=3566073817&site=qq&menu=yes">联系客服</a></li>
					  <li class="sideline"></li>
				</ul>
			</div>
		</div>
	</div>
</div>
			<!--<div class="wrap box-nav">
    <div class="wrap-inner">
        <ul>
            <li><a href="/" class="logo"></a></li>
            @if(Session::get('user_id'))
            <li><a href="{!! route("announcements.index") !!}">网站公告{!! isset($iLatestBulletin) && $iLatestBulletin > 0 ? '(' . $iLatestBulletin . ')' : '' !!}</a></li>
            <li><a href="{!! route('games.index') !!}">购买竞彩</a></li>
            <li><a href="{!! route('user-transactions.mybonus') !!}">资金明细</a></li>
            <li><a href="{!! route('users.user') !!} ">个人中心</a></li>
{{--            @if($iUserLevel != 3 && $iUserLevel != 0)--}}
            {{--TODO fix--}}
            @if(Session::get('is_top_agent') )
            <li><a href="{!! route('users.index') !!}">团队管理</a></li>
            @endif
            {{--@if($iUserLevel == 0)--}}
            {{--<li><a href="{!! route('programs.index') !!}">团队管理</a></li>--}}
            {{--@endif--}}
            @endif
            <li class="right qq_kf">
                <a target="_blank" href="http://wpa.qq.com/msgrd?v=3&uin=3566073817&site=qq&menu=yes"><img border="0" src="http://wpa.qq.com/pa?p=2:3566073817:52" alt="联系客服" title="联系客服"/>联系客服</a>
            </li>
            @if (!Session::get('username'))
            {{--
            <li class="right"><a href="{!! route('signup') !!}">注册</a></li>
            --}}
            <li class="right"><a href="{!! route('signup') !!}"></a></li>
            <li class="right"><a href="{!! route('signin') !!}">请登录</a></li>
            @endif
        </ul>
    </div>
</div>
-->
<script type="text/javascript">
	//用户退出
$('.logout').click(function(e) {
        window.location.href = "{!! route('logout') !!}";
    });
    //退出下拉菜单出现 消失
    var hidemenu = false;
    $('.loutsession').click(function(){
    	if(hidemenu){
    		$('.hidemenu').hide();
    		hidemenu = false;
    	}else{
    		$('.hidemenu').show();
    		hidemenu = true;
    	}
    	
    })
</script>