<table class="ord-tableother">
    <thead>
        <tr>
            <th colspan="10">方案详情</th>
        </tr>
    </thead>
    <tbody>
    @if($data->gate == 'chp')
        <colgroup>
            <col width="30%" />
            <col width="20%" />
            <col width="20%" />
            <col width="30%" />
        </colgroup>

        <tbody>
        <tr>
            <th>编号</th>
            <th>赛事</th>
            <th>冠军</th>
            <th>投注内容（参考奖金指数）</th>
        </tr>
        @foreach($oRelatedGames as $oRelatedGame)
            <tr>
                <td><span title="{!! $oRelatedGame->bn !!}">{!! $oRelatedGame->ds_g_bn !!}</span></td>
                <td><span title="{!! $oRelatedGame->l_cn !!}"> {!! $oRelatedGame ->l_cn_abbr !!}</span></td>
                <td><font>{!! $oRelatedGame->h_cn !!}</font></td>
                <td class="textleft">
                <?php
                    $aBetWays = [];
                ?>
                @foreach($aBetContent['games'][$oRelatedGame->bn] as $sBetOdd)
                    <?php
                        list($sOddIdentity, $sOdd) = explode('_', $sBetOdd);
                        $aBetWays[] = $aWayOddRevertMap[$sOddIdentity];
                    ?>
                    <span class="{!! in_array($sOddIdentity, $oRelatedGame->game_result) ? 'victory' : 'defeat' !!}">{!! ($aBetOddsDisplay && array_key_exists($oRelatedGame->bn, $aBetOddsDisplay) && array_key_exists($sOddIdentity, $aBetOddsDisplay[$oRelatedGame->bn])) ? $aBetOddsDisplay[$oRelatedGame->bn][$sOddIdentity] : $sBetOdd !!}</span>
                @endforeach
                <?php
                    $aBetWays = array_unique($aBetWays);
                    $aBetWays = array_flip($aBetWays);
                ?>
                </td>
            </tr>
        @endforeach
        </tbody>
    @elseif($data->gate =='fnl')
        <colgroup>
            <col width="20%" />
            <col width="20%" />
            <col width="20%" />
            <col width="20%" />
            <col width="20%" />
        </colgroup>

        <tbody>
        <tr>
            <th>编号</th>
            <th>赛事</th>
            <th>冠军</th>
            <th>亚军</th>
            <th>投注内容（参考奖金指数）</th>

        </tr>
        @foreach($oRelatedGames as $oRelatedGame)
            <tr>
                <td><span title="{!! $oRelatedGame->bn !!}">{!! $oRelatedGame->ds_g_bn !!}</span></td>
                <td><span title="{!! $oRelatedGame->l_cn !!}"> {!! $oRelatedGame ->l_cn_abbr !!}</span></td>
                <td><font>{!! explode('—',$oRelatedGame->h_cn)[0] !!}</font></td>
                <td><font>{!! explode('—',$oRelatedGame->h_cn)[1] !!}</font></td>
                <td class="textleft">
                <?php
                    $aBetWays = [];
                ?>
                @foreach($aBetContent['games'][$oRelatedGame->bn] as $sBetOdd)
                    <?php
                        list($sOddIdentity, $sOdd) = explode('_', $sBetOdd);
                        $aBetWays[] = $aWayOddRevertMap[$sOddIdentity];
                    ?>
                    <span class="{!! in_array($sOddIdentity, $oRelatedGame->game_result) ? 'victory' : 'defeat' !!}">{!! ($aBetOddsDisplay && array_key_exists($oRelatedGame->bn, $aBetOddsDisplay) && array_key_exists($sOddIdentity, $aBetOddsDisplay[$oRelatedGame->bn])) ? $aBetOddsDisplay[$oRelatedGame->bn][$sOddIdentity] : $sBetOdd !!}</span>
                @endforeach
                <?php
                    $aBetWays = array_unique($aBetWays);
                    $aBetWays = array_flip($aBetWays);
                ?>
                </td>
            </tr>
        @endforeach
        </tbody>
    @else
        <colgroup>
            <col width="12%" />
            <col width="10%" />
            <col width="10%" />
            <col width="9%" />
            <col width="5%" />
            <col width="7%" />
            <col width="18%">
            <col width="5%" />
            <col width="5%" />
            <col width="7%" />
        </colgroup>

        <tbody>
        <tr>
            <th>编号</th>
            <th>赛事</th>
            <th>比赛时间</th>
            <th>主队</th>
            <th>让球</th>
            <th>客队</th>
            <th>投注内容（参考奖金指数）</th>
            <th>胆码</th>
            <th>比分</th>
            <th>彩果</th>
        </tr>
        @foreach($oRelatedGames as $oRelatedGame)
            <tr>
                <td><span title="{!! $oRelatedGame->bn !!}">{!! substr($oRelatedGame->ds_g_bn, 0, 9) !!}</span></td>
                <td><span title="{!! $oRelatedGame->l_cn !!}"> {!! $oRelatedGame ->l_cn_abbr !!}</span></td>
                <td>{!! $oRelatedGame->played_at !!}</td>
                <td><font>{!! $oRelatedGame->h_cn !!}</font></td>
                <td>{!! $oRelatedGame->fixed_display !!}</td>
                <td><font>{!! $oRelatedGame->a_cn !!}</font></td>
                <td class="textleft">
                <?php
                    $aBetWays = [];
                ?>
                @foreach($aBetContent['games'][$oRelatedGame->bn] as $sBetOdd)
                    <?php
                        list($sOddIdentity, $sOdd) = explode('_', $sBetOdd);
                        $aBetWays[] = $aWayOddRevertMap[$sOddIdentity];
                    ?>
                    <span class="{!! in_array($sOddIdentity, $oRelatedGame->game_result) ? 'victory' : 'defeat' !!}">{!! ($aBetOddsDisplay && array_key_exists($oRelatedGame->bn, $aBetOddsDisplay) && array_key_exists($sOddIdentity, $aBetOddsDisplay[$oRelatedGame->bn])) ? $aBetOddsDisplay[$oRelatedGame->bn][$sOddIdentity] : $sBetOdd !!}</span>
                @endforeach
                <?php
                    $aBetWays = array_unique($aBetWays);
                    $aBetWays = array_flip($aBetWays);
                ?>
                </td>
                <td>{!! in_array($oRelatedGame->bn, $aBetContent['dan']) ? '<div class="danSignture">√</div>' : '' !!}</td>
                <td><b class="redcolor">{!! $oRelatedGame->score_display !!}</b></td>
                <td>{!! $oRelatedGame->status == 6 ? '已取消' : implode(', ', array_intersect_key($oRelatedGame->game_result_display, $aBetWays)) !!}</td>
            </tr>
        @endforeach
        </tbody>
    @endif
</table>