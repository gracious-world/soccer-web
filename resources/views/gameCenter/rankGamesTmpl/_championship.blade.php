<script id="championshipTmpl" type="text/x-dot-template">
    @{{ for(var i=0,len=it.length;i<len;i++){ }}
    <dd>
        <a href="javascript:void(0);" data='@{{=JSON.stringify(it[i])}}'>
            <b>@{{= i+1 }}</b>
            <span class="country"><em class="textright">@{{=it[i].h_cn.split('—')[0]}}</em>VS<em class="textleft">@{{=it[i].h_cn.split('—')[1]}}</em></span>
            <span>开售</span>
            <span>@{{=it[i].fnl.fnl[0]}}</span>
            <span>@{{=it[i].fnl.fnl[3] || ''}}</span>
        </a>
    </dd>
    @{{  } }}
</script>