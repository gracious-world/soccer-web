<script id="focusGamesMenu" type="text/x-dot-template">
@{{ var useOn = true; }}
@{{ for(var n in it) { var groupItem = it[n]; }}
    @{{ for(var i = 0, l = groupItem.length; i < l; i++) { var item = groupItem[i]; }}
    @{{ if(i == 0 && useOn){  useOn = false; }}
    <div class="Flefteventlist on" action_type="gameSwitcher">
    @{{ }else{ }}
    <div class="Flefteventlist" action_type="gameSwitcher">
    @{{ } }}
        <p>@{{=item.h_cn_abbr}}</p>
        <span>vs</span>
        <p>@{{=item.a_cn_abbr}}</p>
    </div>
    @{{ } }}
@{{ } }}
</script>