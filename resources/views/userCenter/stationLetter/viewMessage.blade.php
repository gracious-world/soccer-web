@extends('l.home')

@section('title')
   站内信详情
@parent
@stop

@section('main')
<div class="nav-bg">
    <div class="title-normal">系统通知详情</div>
    <a id="J-button-goback" class="button-goback" href="{!! route('station-letters.index') !!}">返回</a>
</div>

<div class="content">
    <div class="article-page">
        <div class="article-page-title">
            <h1>{!! $data->msg_title !!}</h1>
            <p class="article-page-time">{!! $data->created_at !!}</p>
            <div class="filter-tabs" data-font-size>
                <div class="filter-tabs-cont">
                    <a class="fs-10" href="javascript:;">A</a>
                    <a class="fs-12" href="javascript:;">A</a>
                    <a class="fs-15" href="javascript:;">A</a>
                </div>
            </div>
        </div>
        <div class="article-page-content">
            {!! $data->msg_content !!}
        </div>
    </div>
</div>
@stop

@section('end')
@parent
<script>
$(function(){
    var fontsizeClass = ['fs-12', 'fs-15', 'fs-17'],
        $content = $('.article-page-content');
    $('[data-font-size] a').on('click', function(){
        var $this = $(this),
            idx = $this.index();
        if( $this.hasClass('current') ) return false;
        $content.removeClass (function (index, css) {
            return (css.match (/(^|\s)fs-\d+/g) || []).join(' ');
        }).addClass(fontsizeClass[idx]);
        $this.addClass('current').siblings('.current').removeClass('current');
    }).eq(0).trigger('click');
});
</script>
@stop
