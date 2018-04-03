@extends('l.home')

@section('title')
    团队管理 --安捷竞彩平台
@parent
@stop

@section('scripts')
@parent
    {!! script('jquery.jscrollpane') !!}
    {!! script('gagame.DatePicker') !!}
@stop


@section('main')
<div class="nav-bg nav-bg-tab">
    <div class="title-normal">团队管理</div>
    <ul class="tab-title clearfix">
        @if(Session::get('user_level') == 2)
        <li><a href="{!! route('users.accurate-create') !!}" ><span>精准开户</span></a></li>
        <li><a href="{!! route('user-links.create') !!}"><span>链接开户</span></a></li>
        <li><a href="{!! route('user-links.index') !!}"><span>链接管理</span></a></li>
        @endif
        <li class="current"><a href="{!! route('users.index') !!}"><span>用户列表</span></a></li>
    </ul>
</div>

<div class="content">
    @include('userCenter.user._search')
    @include('userCenter.user._list')

    @include('pagination.default', ['paginator' => $datas])
</div>
@stop

@section('end')
@parent
<script>

(function($){

    new gagame.Select({realDom:'#J-select-user-groups',cls:'w-2'});
    // new gagame.Select({realDom:'#J-select-user-add',cls:'w-2'});

    $('#J-date-start').focus(function(){
        (new gagame.DatePicker({input:'#J-date-start',isShowTime:true, startYear:2013})).show();
    });
    $('#J-date-end').focus(function(){
        (new gagame.DatePicker({input:'#J-date-end',isShowTime:true, startYear:2013})).show();
    });

})(jQuery);
</script>
@stop