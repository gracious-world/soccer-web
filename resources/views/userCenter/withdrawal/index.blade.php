@extends('l.home')

@section('title')
            我的提现
@parent
@stop

@section('scripts')
@parent
    {!! script('jquery.jscrollpane')!!}
    {!! script('gagame.DatePicker')!!}
@stop


@section ('main')
    @include('userCenter.transaction.nav')
        <div class="content">
            @include('userCenter.withdrawal._search')
            @include('userCenter.withdrawal._list')
            @include('pagination.default', ['paginator' => $datas])
        </div>
@stop

@section('end')
@parent
<script>
(function($){

    // new gagame.Select({realDom:'#J-select-recharge',cls:'w-2'});

    $('#J-date-start').focus(function(){
        (new gagame.DatePicker({input:'#J-date-start',isShowTime:true, startYear:2013})).show();
    });
    $('#J-date-end').focus(function(){
        (new gagame.DatePicker({input:'#J-date-end',isShowTime:true, startYear:2013})).show();
    });
    // var getAmountSumPerPage = function ()
    // {
    //     var amountSum = 0, transactionAmountSum = 0, transactionChargeSum = 0;
    //     $('.changeAmount').each(function() {
    //         var amountDesc = $.trim($(this).text()).split(' ');
    //         if (amountDesc[0] == '+') amountSum += +amountDesc[1];
    //         if (amountDesc[0] == '-') amountSum -= +amountDesc[1];
    //     });
    //     $('#fundChangeNum').html(fmoney(amountSum, 4));
    // }
    // getAmountSumPerPage();


})(jQuery);
</script>
@stop