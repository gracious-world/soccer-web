<?php $iUserLevel=Session::get("user_level"); ?>
<div class="nav-bg nav-bg-tab">
    <div class="title-normal">团队管理</div>
    <ul class="tab-title clearfix">
        <li class="{!!$sCurrentRoute=="users.accurate-create"?"current":null!!}"><a href="{!!route("users.accurate-create")!!}"><span>精准开户</span></a></li>

 <li class="{!!$sCurrentRoute=="user-links.create"?"current":null!!}"><a href="{!!route("user-links.create")!!}"><span>链接开户</span></a></li>
        <li class="{!!$sCurrentRoute=="users.index" || $sCurrentRoute =="users.sub-users"?"current":null!!}">
            <a href="{!!route("users.index")!!}">
                <span>查看下级</span>
            </a>
        </li>
        <li class="{!!$sCurrentRoute=="programs.index"?"current":null!!}"> <a href="{!!route("programs.index")!!}"><span>查看下级投注</span></a></li>
        <li class="{!!$sCurrentRoute=="user-profits.index"?"current":null!!}"> <a href="{!!route("user-profits.index")!!}"><span>下级成员盈亏</span></a></li>
        <li class="{!!$sCurrentRoute=="team-profits.index"?"current":null!!}"> <a href="{!!route("team-profits.index")!!}"><span>下级团队盈亏</span></a></li>
        <li class="{!!$sCurrentRoute=="dividends.index"?"current":null!!}"> <a href="{!!route("dividends.index")!!}"><span>分红</span></a></li>
    </ul>
</div>