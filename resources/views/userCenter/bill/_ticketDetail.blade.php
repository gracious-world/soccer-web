<script id="ticketDetailTmpl" type="text/x-dot-template">
    <table width="100%" class="ord-tableother" id="detail-table">
        <thead>
            <tr>
                <th>方案明细（出票奖金指数）</th>
                <th>游戏类型</th>
                <th>倍数</th>
                <th>金额</th>
                <th>注单状态</th>
                <th>奖金</th>

            </tr>
        </thead>
        <colgroup>
            <col width="40%" />
            <col width="15%" />
            <col width="10%" />
            <col width="10%" />
            <col width="10%" />
            <col width="15%" />

          </colgroup>
        <tbody>
        @{!!  var data = it.data; !!}
        @{!! for(var i=0,len=data.length;i<len;i++){ !!}
            <tr>
                <td>@{!!=data[i].bet_detail!!}</td>
                @{!! if(data[i].is_rank == '1'){ !!}
                <td>@{!!=it.gateTypeDisplay!!}</td>
                @{!! }else{ !!}
                <td>@{!!=data[i].gate_type_display!!}</td>
                @{!! } !!}
                <td>@{!!=data[i].multiple!!}</td>
                <td>@{!!=data[i].amount!!}</td>
                <td @{!! if(data[i].status === "6"){ !!} class="marklight" @{!! } !!} !!} >@{!!=data[i].status_display!!}</td>
                <td @{!! if(data[i].status === "6"){ !!} class="marklight" @{!! } !!} !!} >@{!!=data[i].prize_display!!}</td>
            </tr>
        @{!!  } !!}
        </tbody>
    </table>
</script>











