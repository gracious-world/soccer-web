@extends('l.home')

@section('title')
            账变记录 - 资金明细
@parent
@stop

@section('scripts')
@parent
    {!! script('jquery.jscrollpane')!!}
    {!! script('gagame.DatePicker')!!}
    {!! script('gagame.Tip')!!}
@stop

@section ('main')
    @include('userCenter.transaction.nav')
        <div class="content">
            @include('userCenter.transaction._search')
            @include('userCenter.transaction._list')
            @include('pagination.default', ['paginator' => $datas])
        </div>
@stop


@section('end')
@parent
<script>
(function($){
      $('#J-date-start').focus(function(){
        (new gagame.DatePicker({input:'#J-date-start',isShowTime:true, startYear:2013})).show();
    });
    $('#J-date-end').focus(function(){
        (new gagame.DatePicker({input:'#J-date-end',isShowTime:true, startYear:2013})).show();
    });
    // new gagame.Select({realDom:'#J-select-bill-type',cls:'w-2'});
    @if($reportName=='transaction')
    var table = $('#J-table'),
        details = table.find('.view-detail'),
        tip         = new gagame.Tip({cls:'j-ui-tip-b j-ui-tip-page-records'});
        // selectIssue = new gagame.Select({realDom:'#J-select-issue',cls:'w-2'});

    // new gagame.Select({realDom:'#J-select-game-mode',cls:'w-2'});



    details.hover(function(e){
        var el = $(this),
            text = el.parent().find('.data-textarea').val();
        tip.setText(text);
        tip.show(-90, tip.getDom().height() * -1 - 22, el);

        e.preventDefault();
    },function(){
        tip.hide();
    });
    @endif

})(jQuery);
</script>

@stop
