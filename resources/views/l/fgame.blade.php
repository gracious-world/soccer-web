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

            {!! style('uicss') !!}
            {!! style('focus') !!}

        @show
        @section('scripts')
            {!! script('jquery') !!}
            {!! script('moment') !!}
            {!! script('uiScript') !!}
            {!! script('base') !!}
        @show
    </head>
    <body>
        <div class="Focuscont">
                @include('w.bet-header')
                @yield('container')
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

    </script>
        @include('w.footer')
    @show
</html>
