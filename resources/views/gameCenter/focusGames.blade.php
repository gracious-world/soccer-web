
@extends('l.fgame')

@section('title') @if($iUserId) {!!$sBpName!!} @else 竞彩平台 @endif @parent  @stop


@section ('styles')
@parent

@stop

@section('scripts')
@parent
    <script type="text/javascript">
        var userid = '{{$iUserId}}';
        var params = window.location.hash.substring(1).split(';');
        var username = params[0];
        var identity = params[1];
        var token = params[2];
        if(username !='' && userid == ''){
            window.location.href = '{{route('signin')}}'+'#'+username+';'+identity+';'+token;
        }

        var gameConfigUrl = "{!! route('games.game-config', ['game_type' => $sGameType, 'way_type' => $sWayType]) !!}";
        var gameDataUrl = "{!! route('games.game-data', ['game_type' => $sGameType, 'bet_type' => $iBetType, 'way_type' => $sWayType]) !!}" + '?is_focus=1';
        var bettingUrl = "{!! route('programs.betting', ['game_type' => $sGameType]) !!}";
        var betType = "{!! $iBetType !!}";
        var isFocusGame = "{{ $bIsFocus }}";
        var aCustomSaleStopTimes = {!! json_encode($aCustomSaleStopTimes) !!};
    </script>
    <!-- <script type="text/javascript" src="../data/config.json"></script> -->
    <!-- <script type="text/javascript" src="../data/game.json"></script> -->
    <script type="text/javascript" src="../../js-min/doT.js"></script>
   <!-- <script data-main="../../js/mainFocus" src="../../js-min/requirejs.min.js"></script>  -->
 <script data-main="../../js-min/mainFocus" src="../../js-min/requirejs.min.js"></script> 
    <!-- <script language="javascript" type="text/javascript" src="../../js/jquery-1.11.2.min.js"></script> -->
    <script language="javascript" type="text/javascript" src="../../js-min/public.js"></script>

@stop

@section ('container')

    <div id="gamesContainer">
        <div class="focusmain fix">
            @include('gameCenter.focusGameTmpl.not_focus_games')
            <div class="focusmid">
                <div class="gamesList"></div>
            </div>
            <div class="focusleft">
                <h2>焦点赛事</h2>
                <div class="gamesMenu">
                </div>
            </div>
            <div class="focusright">
                <img src="../../imager/focus/football.png">
                <a href="{!! route('games.index') !!}">投注更多比赛</a>
            </div>

        </div>
    </div>
@stop


