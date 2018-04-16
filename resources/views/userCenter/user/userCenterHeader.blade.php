<?php
use App\Models\User\UserUser;
use App\Models\Payment\PaymentPlatform;

$iUserLevel=Session::get("user_level");
$oUser=UserUser::find(Session::get("user_id"));
?>
<div class="nav-bg nav-bg-tab">
    <div class="title-normal">个人中心</div>
    <ul class="tab-title clearfix">
            <?php
            $oDefaultPaymentPlatform = PaymentPlatform::find($iDefaultPaymentPlatformId);
            $sRoute = $oDefaultPaymentPlatform->type == 2 ? 'user-recharges.quick' : 'bank-deposits.netbank';
            ?>
        <li class="{!! $sCurrentRoute==$sRoute?'current':null !!}"><a href="{!!route($sRoute, $iDefaultPaymentPlatformId) !!}"><span>我要充值</span></a></li>
        <li class="{!! $sCurrentRoute == 'user-withdrawal.withdraw'?'current':null !!}"><a href="{!! route("user-withdrawal.withdraw", ['step' => 0]) !!}"><span>我要提现</span></a></li>
        <li class="{!! $sCurrentRoute == 'station-letters.inex'?'current':null !!}"> <a href="{!! route("station-letters.index") !!}"><span>系统通知</span></a></li>
        <li class="{!! $sCurrentRoute == 'users.change-password'?'current':null !!}"><a href="{!! route('users.change-password') !!}"><span>登录密码</span></a></li>
        @if ( $oUser->fund_password)
            <li class="{!! $sCurrentRoute == 'users.change-fund-password'?'current':null !!}"> <a href="{!! route('users.change-fund-password') !!}"><span>资金密码</span></a></li>
        @else
            <li class="{!! $sCurrentRoute == 'users.safe-reset-fund-password'?'current':null !!}"><a href="{!! route('users.safe-reset-fund-password') !!}"><span>资金密码</span></a></li>
        @endif
        <li class="{!! $sCurrentRoute=='bank-cards.index'?'current':null !!}"><a href="{!! route('bank-cards.index') !!}"><span>银行卡管理</span></a></li>
    </ul>
</div>