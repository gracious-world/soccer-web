@if (!isset($sWayType) || !$sWayType || $sWayType == 'all')
<!-- 混合投注 -->
@include('gameCenter.gameTmpl._all')
@include('gameCenter.gameTmpl._all_h')

@elseif($sWayType == 'had')
<!-- 胜平负 -->
@include('gameCenter.gameTmpl._spf')
@include('gameCenter.gameTmpl._spf_h')

@elseif($sWayType == 'crs')
<!-- 比分 -->
@include('gameCenter.gameTmpl._bifen')
@include('gameCenter.gameTmpl._bifen_h')

@elseif($sWayType == 'ttg')
<!-- 总进球 -->
@include('gameCenter.gameTmpl._jinqiu')
@include('gameCenter.gameTmpl._jinqiu_h')

@elseif($sWayType == 'hafu')
<!-- 半全场 -->
@include('gameCenter.gameTmpl._banquan')
@include('gameCenter.gameTmpl._banquan_h')
@endif



<!-- 注单格式 -->
@include('gameCenter.gameTmpl._selectedGames')
@include('gameCenter.gameTmpl._selectedOdds')
<!-- 过关方式 -->
@include('gameCenter.gameTmpl._gatesBox')
<!-- 提交form -->
@include('gameCenter.gameTmpl._game_form')
