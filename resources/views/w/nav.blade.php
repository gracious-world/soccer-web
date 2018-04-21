<?php $iUserLevel=Session::get("user_level");?>
<style >
    .qq_kf a img {display: none !important;}

</style>
<div class="wrap box-nav">
    <div class="wrap-inner">
        <ul>
            <li><a href="/" class="logo"></a></li>
            @if(Session::get('user_id'))
            <li><a href="{!! route("announcements.index") !!}">网站公告{!! isset($iLatestBulletin) && $iLatestBulletin > 0 ? '(' . $iLatestBulletin . ')' : '' !!}</a></li>
            <li><a href="{!! route('games.index') !!}">购买竞彩</a></li>
            <li><a href="{!! route('user-transactions.mybonus') !!}">资金明细</a></li>
            <li><a href="{!! route('users.user') !!} ">个人中心</a></li>
            {{--TODO fix--}}
            @if(Session::get('is_agent'))
            <li><a href="{!! route('users.index') !!}">团队管理</a></li>
            @endif

            @endif
            <li class="right qq_kf">
                <a target="_blank" href=""><img border="0" src="" alt="联系客服" title="联系客服"/>联系客服</a>
            </li>
            @if (!Session::get('username'))


            <li class="right"><a href="{!! route('signup') !!}"></a></li>
            <li class="right"><a href="{!! route('signin') !!}">请登录</a></li>
            @endif
        </ul>
    </div>
</div>