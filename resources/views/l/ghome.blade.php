<!DOCTYPE html>
<html lang="zh-CN">
    <head>
        <meta charset="UTF-8">
        <meta name="description" content="@yield('description')">
        <meta name="keywords" content="@yield('keywords')" />
        <meta name="csrf-token" content="{!! csrf_token() !!}" />
        @section ('metas')
        @show
        <title>
            @section('title')

            @show
        </title>
        @section ('styles')
          {!! style('gameUi') !!}
          {!! style('gameBase') !!}
          {!! style('global') !!}
        @show
        @section('scripts')
            {!! script('jquery') !!}
            {!! script('doT') !!}
            {!! script('moment') !!}
            {!! script('uiScript') !!}
            {!! script('gagame.base') !!}
            {!! script('base') !!}
        @show
    </head>
    <body>
        @include('w.top-header')
        @include('w.nav')
        <div class="wrapper">
            <div class="article docBody clearfix">
                @include('gameCenter.notice')
                @include('w.bet-header')
                @yield('container')
            </div>
        </div>
        @if(isset($bNeedShowDesc) && $bNeedShowDesc)
        <i class="dgTips">
            <div class="guideList">
                <ul>
                    <li class="howGet" data-ctype="howGet"><a href="javascript:;"><i></i>如何兑奖</a></li>
                    <li class="howCal" data-ctype="howCal"><a href="javascript:;"><i></i>如何算奖</a></li>
                    <li class="gameIntr " data-ctype="gameIntr"><a href="javascript:;"><i></i>玩法说明</a></li>
                   <!--  <li class="newGuide last" data-ctype="newGuide" data-game-type="mixp"><a href="javascript:;"><i></i>新手引导</a></li> -->
                </ul>
            </div>
        </i>
        @endif
        @if (isset($bIsFocus) && $bIsFocus)
            @include('gameCenter.tmpl_focus')
        @else
            @include('gameCenter.tmpl')
        @endif
    </body>

    @section('end')
    <script type="text/javascript">
        $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
        });
        var skin = (window.location.href).match(/skin=[0-9]/g) || 'skin=1';
        var skinType = (''+skin[0]).match(/[0-9]/g);
        if(skinType == 1) {
            $('.wrapper').addClass('wrapper-bg');
        }
        //右侧浮动功能弹窗

        $('.guideList li').on('click', function(e) {
            var targetDom = this;
            var acType = $(targetDom).attr('data-ctype');
            if (!acType) { // 允许点击事件往上冒泡一级
                targetDom = $(targetDom).parent();
                acType = $(targetDom).attr('data-ctype');
            }
            if (!acType) e.stopPropagation();
            switch (acType) {
                case 'howGet':
                    var openWindow = new UIscript.Message({cls:'guideList-img w-15'});
                    openWindow.show({
                        isShowMask:true,
                        content:'<img src="/imager/game/howGet.jpg">'
                    });
                    break;
                case 'howCal':
                    var openWindow = new UIscript.Message({cls:'guideList-img w-15'});
                    openWindow.show({
                        isShowMask:true,
                        content:'<img src="/imager/game/howCalGray.png">'
                    });
                    break;
                case 'gameIntr':
                    var openWindow = new UIscript.Message({cls:'guideList-img w-15'});
                    openWindow.show({
                        isShowMask:true,
                        content:'<img src="/imager/game/gameIntr.jpg">'
                    });
                    break;
            }
        });
        /*$('#orderRule').click(function(event) {
             var openWindow = new UIscript.Message({cls:' w-15'});
            openWindow.show({
                isShowMask:true,
                content:"@include('w.userAgreement')"
            });
        });*/

        $('#data-spdex').click(function(event) {
            var dataurl ="http://c.spdex.com/spdex500b";
            window.open(dataurl,"","height=520,width=850,top=0,left=0,toolbar=no,menubar=no,scrollbars=no,resizable=no,location=no,status=no");
            return false;
        });

        $('#data-rule').click(function(event) {
            var dataurl ="/rule";
            window.open(dataurl,"","height=520,width=650,top=0,left=0,toolbar=no,menubar=no,scrollbars=no,resizable=no,location=no,status=no");
            return false;
        });

    </script>
        @include('w.footer')
    @show
</html>
