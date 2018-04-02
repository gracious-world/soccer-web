<script id="selectedGames" type="text/x-dot-template">
    @{{ var data = it.data; }}
    <tr game_bn="@{{=data.bn}}" single="@{{=data.single}}" game_index="@{{=data.gameIndexNum}}" class="gameTitle">
        <th>
            <a class="icoDel" action_type="cancelGame" index="@{{=data.bn}}"></a>@{{=(data.week + data.num)}}
        </th>
        <th class="tr">@{{=data.h_cn}}</th>
        <th></th>
        <th class="tl">@{{=data.a_cn}}</th>
        @{{ if(it.available){ }}
        <th><a href="javascript:;" class="icoDan" action_type="danSettingEvent" disabled="disabled"></a></th>
        @{{ }else{ }}
        <th><a href="javascript:;" class="icoDan icoDanDis" action_type="danSettingEvent" disabled="disabled"></a></th>
        @{{ } }}
    </tr>
    <tr class="gameOption" game_bn="@{{=data.bn}}" game_index="@{{=data.gameIndexNum}}">
        <td colspan="5" class="betList" height="28">
        </td>
    </tr>
</script>