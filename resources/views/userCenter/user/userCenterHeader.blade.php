<?php
use App\Models\User\UserUser;

$iUserLevel=Session::get("user_level");
$oUser=UserUser::find(Session::get("user_id"));
?>
<!--<div class="nav-bg nav-bg-tab">
    <div class="title-normal">个人中心</div>
    <ul class="tab-title clearfix">
        @if($iUserLevel == 3 )
            <?php
            $oDefaultPaymentPlatform = PaymentPlatform::find($iDefaultPaymentPlatformId);
            $sRoute = $oDefaultPaymentPlatform->type == 2 ? 'user-recharges.quick' : 'bank-deposits.netbank';
            ?>
        <li><a href="{!!route($sRoute, $iDefaultPaymentPlatformId) !!}"><span>我要充值</span></a></li>
        @endif
        <li><a href="{!! route("user-withdrawal.withdraw", ['step' => 0]) !!}"><span>我要提现</span></a></li>
        <li > <a href="{!! route("station-letters.index") !!}"><span>系统通知</span></a></li>
        <li><a href="{!! route('users.change-password') !!}"><span>登录密码</span></a></li>
        @if ( $oUser->fund_password)
            <li><a href="{!! route('users.change-fund-password') !!}"><span>资金密码</span></a></li>
        @else
            <li><a href="{!! route('users.safe-reset-fund-password') !!}"><span>资金密码</span></a></li>
        @endif
        <li><a href="{!! route('bank-cards.index') !!}"><span>银行卡管理</span></a></li>
    </ul>
</div>-->

<div class="user_header">
	<div>
		<ul>
			@if($iUserLevel == 3 )
	            <?php
	            $oDefaultPaymentPlatform = PaymentPlatform::find($iDefaultPaymentPlatformId);
	            $sRoute = $oDefaultPaymentPlatform->type == 2 ? 'user-recharges.quick' : 'bank-deposits.netbank';
	            ?>
			<li class="usermenu"><a href="{!!route($sRoute, $iDefaultPaymentPlatformId) !!}">我要充值</a></li>
			 
	          @endif
			<li class="usermenu"><a href="{!! route("user-withdrawal.withdraw", ['step' => 0]) !!}">我要提现</a></li>
			
			<li class="usermenu"><a href="{!! route("station-letters.index") !!}">系统通知</a></li>
			<li class="usermenu"><a href="{!! route('users.change-password') !!}">登录密码</a></li>
			@if ( $oUser->fund_password)
            <li class="usermenu"><a  href="{!! route('users.change-fund-password') !!}">资金密码</a></li>
	        @else
	        <li class="usermenu"><a href="{!! route('users.safe-reset-fund-password') !!}">资金密码</a></li>
	        @endif
			<li class="usermenu samecolor" ><a href="{!! route('bank-cards.index') !!}">银行卡管理</a></li>
		</ul>
	</div>
</div>