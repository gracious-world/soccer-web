<script id="gatesBox" type="text/x-dot-template">
    @{{ for(var i=0,l=it.data.length; i<l;i++) { var item = it.data[i], minPrizeGates = item.combination.split(',').min(); }}
    <li title="至少猜中@{{=minPrizeGates}}场可中奖" class="jtip" gate="@{{=item.identity}}" combination="@{{=item.combination}}" max_dan="@{{=(minPrizeGates-1)}}" action_type="gateEvent">
        <i class="@{{=['icoFx', 'icoDx'][it.gate_type]}}" disabled="disabled"></i>
        @{{=item.name}}
    </li>
    @{{ } }}
</script>