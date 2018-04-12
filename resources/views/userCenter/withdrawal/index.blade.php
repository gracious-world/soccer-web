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
<!--<div class="nav-bg nav-bg-tab">
    <div class="title-normal">
        资金明细
    </div>
    <ul class="tab-title">
        <li class="{!!$reportName=='bonus' ? 'current' : ''!!}">
            <a href="{!! route('user-transactions.mybonus') !!}"><span>{!! Session::get('is_player') ? '我的加奖' : '我的返点' !!}</span></a>
        </li>
        @if(Session::get('is_player'))
        <li class="{!!$reportName=='jc-transaction' ? 'current' : ''!!}">
            <a href="{!! route('user-transactions.jc-index') !!}"><span>我的投注</span></a>
        </li>
        <li class="{!!$reportName=='deposit' ? 'current' : ''!!}">
            <a href="{!! route('user-transactions.mydeposit',Session::get('user_id')) !!}"><span>我的充值</span></a>
        </li>
        <li class="{!!$reportName=='depositApply' ? 'current' : ''!!}">
            <a href="{!! route('user-recharges.index') !!}"><span>充值申请</span></a>
        </li>
        @endif
        <li class="{!!$reportName=='withdraw' ? 'current' : ''!!}">
            <a href="{!! route('user-transactions.mywithdraw',Session::get('user_id')) !!}"><span>我的提现</span></a>
        </li>
        <li class="{!!$reportName=='withdrawApply' ? 'current' : ''!!}">
            <a href="{!! route('user-withdrawal.index') !!}"><span>提现申请</span></a>
        </li>
    </ul>
</div>
        <div class="content">
            @include('userCenter.withdrawal._search')
            @include('userCenter.withdrawal._list')
            @include('pagination.default', ['paginator' => $datas])
        </div>-->
				<div class="user_header">
					<div>
						<ul>
							<li class="{!!$reportName=='bonus' ? 'usermenu samecolor' : 'usermenu'!!}">
								<a href="{!! route('user-transactions.mybonus') !!}"><span>{!! Session::get('is_player') ? '我的加奖' : '我的返点' !!}</span></a>
							</li>
							@if(Session::get('is_player'))
							<li class="{!!$reportName=='jc-transaction' ? 'usermenu samecolor' : 'usermenu'!!}">
								<a href="{!! route('user-transactions.jc-index') !!}"><span>我的投注</span></a>
							</li>
							<li class="{!!$reportName=='deposit' ? 'usermenu samecolor' : 'usermenu'!!}">
								<a href="{!! route('user-transactions.mydeposit',Session::get('user_id')) !!}"><span>我的充值</span></a>
							</li>
							<li class="{!!$reportName=='depositApply' ? 'usermenu samecolor' : 'usermenu'!!}">
								<a href="{!! route('user-recharges.index') !!}"><span>充值申请</span></a>
							</li>
							@endif
							<li class="{!!$reportName=='withdraw' ? 'usermenu samecolor' : 'usermenu'!!}">
								<a href="{!! route('user-transactions.mywithdraw',Session::get('user_id')) !!}"><span>我的提现</span></a>
							</li>
							<li class="{!!$reportName=='withdrawApply' ? 'usermenu samecolor' : 'usermenu'!!}">
								<a href="{!! route('user-withdrawal.index') !!}"><span>提现申请</span></a>
							</li>
						</ul>
					</div>
				</div>
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