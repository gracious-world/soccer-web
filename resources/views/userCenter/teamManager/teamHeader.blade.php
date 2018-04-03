<?php $iUserLevel=Session::get("user_level"); ?>
<div class="nav-bg nav-bg-tab">
    <div class="title-normal">团队管理</div>
    <ul class="tab-title clearfix">
{{--        @if($iUserLevel != 0 && $iUserLevel!=1)--}}
        <li class="{!!$sCurrentRoute=="users.accurate-create"?"current":null!!}"><a href="{!!route("users.accurate-create")!!}"><span>注册下级</span></a></li>
            <!--
    <li class="{!!$sCurrentRoute=="user-links.create" ?"current":null!!}"><a href="{!!route("user-links.create")!!}"><span>下级推广码</span></a></li>
    -->

        {{--@endif--}}
{{--        @if($iUserLevel != 0)--}}
        <li class="{!!$sCurrentRoute=="users.index" || $sCurrentRoute =="users.sub-users"?"current":null!!}">
            <a href="{!!route("users.index")!!}">
                <span>查看下级</span>
            </a>
        </li>
        {{--@endif--}}
        <li class="{!!$sCurrentRoute=="programs.index"?"current":null!!}"> <a href="{!!route("programs.index")!!}"><span>查看下级投注</span></a></li>
    </ul>
</div>