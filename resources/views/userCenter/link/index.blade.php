@extends('l.home')

@section('title')
开户链接管理
@parent
@stop

@section('scripts')
@parent
    {!! script('jquery.jscrollpane')!!}
    {!! script('gagame.DatePicker')!!}
@stop
@section('main')
<div class="wrap mt18">
    <div class="wrap-inner">
        @include('userCenter.teamManager.teamHeader')
        <div class="content">
                        <div class="item-title">
                            <i class="item-icon-11"></i>链接概括
                        </div>
                        <div class="item-info">
                            <div class="data-overview">
                                <h3>{!! $datas->count() !!}</h3>
                                <p>总链接数</p>
                            </div>
                            <div class="data-overview">
                                <h3>{!! $totalUserCount !!}</h3>
                                <p>总人数</p>
                            </div>
                        </div>
            @include('userCenter.link._list')
            @include('pagination.default', ['paginator' => $datas])
        </div>
    </div>
</div>
@stop

@section('end')
@parent
<script>
(function($){
    var table = $('#J-table');
    table.find('.agent-link-name').click(function(e){
        var el = $(this),
            id = $.trim(el.attr('data-id')),
            ico = el.find('i');
        if(ico.hasClass('ico-fold')){
            table.find('.ico-unfold').removeClass('ico-unfold').addClass('ico-fold');
            table.find('.table-tr-item').addClass('table-tr-hidden');
            table.find('.table-tr-pid-' + id).removeClass('table-tr-hidden');
            //table.find('.ico-fold').replaceClass('ico-unfold', 'ico-fold');
            ico.removeClass('ico-fold').addClass('ico-unfold');
        }else{
            ico.removeClass('ico-unfold').addClass('ico-fold');
            table.find('.table-tr-pid-' + id).addClass('table-tr-hidden');
        }
        e.preventDefault();
    });
    $('.confirmDelete').click(function(event) {
        var url = $(this).attr('url');
        if (confirm('确定关闭该开户链接？')) {
            location.href = url;
        }
    });
})(jQuery);
</script>
@stop