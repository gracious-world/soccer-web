<script id="championTmpl" type="text/x-dot-template">
    @{{ for(var i=0,len=it.length;i<len;i++){ }}
    <dd>
        <a href="javascript:void(0);" data='@{{=JSON.stringify(it[i])}}'>
            <b>@{{= i+1 }}</b>
            <span class="country @{{=it[i].num}}">@{{=it[i].h_cn}}</span>
            <span>开售</span>
            <span>@{{=it[i].chp.chp[0]}}</span>
            <span>@{{=it[i].chp.chp[3] || ''}}</span>
        </a>
    </dd>
    @{{  } }}
</script>