<div class="area-search">
@if($reportName=='transaction')
    <form action="{!! route('user-transactions.index') !!}" class="form-inline" method="get">
@elseif($reportName=='jc-transaction')
    <form action="{!! route('user-transactions.jc-index') !!}" class="form-inline" method="get">
@elseif($reportName=='bonus')
    <form action="{!! route('user-transactions.mybonus',Session::get('user_id')) !!}" class="form-inline" method="get">
@elseif($reportName=='deposit')
    <form action="{!! route('user-transactions.mydeposit',Session::get('user_id')) !!}" class="form-inline" method="get">
@elseif($reportName=='withdraw')
    <form action="{!! route('user-transactions.mywithdraw',Session::get('user_id')) !!}" class="form-inline" method="get">
@elseif($reportName=='transfer')
    <form action="{!! route('user-transactions.mytransfer', Session::get('user_id')) !!}" class="form-inline" method="get">
@endif
        <input type="hidden" name="_token" value="{!! csrf_token() !!}" />
        <p class="row">
            时间：<input id="J-date-start" class="input w-3" type="text" name="created_at_from" value="{!! Input::get('created_at_from') !!}" /> 至 <input id="J-date-end" class="input w-3" type="text" name="created_at_to" value="{!! Input::get('created_at_to') !!}" />
            &nbsp;&nbsp;
            @if($reportName=='transaction')
            <input class="input w-3" type="hidden" name="number_type" value="serial_number" />
            账变编号：<input class="input w-3" type="text" name="number_value" value="{!! Input::get('number_value') !!}" />
            @endif
            <input type="submit" value="搜 索" class="btn btn-important" id="J-submit">
        </p>
    </form>
</div>