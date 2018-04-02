<script id="selectedOdds" type="text/x-dot-template">
    <a href="javascript:void(0);" class="@{{=(Mango.Config.oddBlockClass[it.name] || 'blockYel')}}" single="@{{=it.single}}" index="@{{=it.index}}" action_type="cancelOdd" way="@{{=it.way}}" odd="@{{=it.odd}}" fixed="@{{=it.fixed || '0'}}">@{{=it.cn}}</a>
</script>