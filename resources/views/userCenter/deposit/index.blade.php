@extends('l.home')

@section('title')
            我的充值
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
    <div class="area-search">
        <form action="{!! route('user-recharges.index') !!}" class="form-inline" method="get">
            <input type="hidden" name="_token" value="{!! csrf_token() !!}" />
            <p class="row">
                时间：
                <input id="J-date-start" class="input w-3" type="text" name="request_time_from" value="{!! Input::get('request_time_from') !!}" />
                至
                <input id="J-date-end" class="input w-3" type="text" name="request_time_to" value="{!! Input::get('request_time_to') !!}" />
                &nbsp;&nbsp;
<!--                  类型：
              <select id="J-select-recharge" style="display:none;" name="transaction_type">
                    <option value="0" {!!Input::get('transaction_type') == 0 ? 'selected="selected"' : ''!!}>所有类型</option>
                    <option value="1" {!!Input::get('transaction_type') == 1 ? 'selected="selected"' : ''!!}>上级充值</option>
                    <option value="2" {!!Input::get('transaction_type') == 2 ? 'selected="selected"' : ''!!}>跨级充值</option>
                </select>-->
                &nbsp;&nbsp;
                @if (Session::get('is_agent'))
                游戏用户：<input class="input w-3" type="text" name="username" value="{!! Input::get('username') !!}" />
                &nbsp;&nbsp;
                @endif
                <input class="btn btn-important" type="submit" value=" 搜 索 " />
            </p>
        </form>
    </div>

    <table width="100%" class="table">
        <thead>
            <tr>
                <th>编号</th>
                <th>时间</th>
                <th>类型</th>
                <th>金额</th>
                <th>状态</th>
            </tr>
        </thead>
        <tbody>
            <!-- <tr>
                <td><a href="#">D140523034VFBCBIIJAB</a></td>
                <td>
                    2014-05-30
                    <br />
                    13:32:03
                </td>
                <td>人工充值</td>
                <td><span class="c-green">+ 10.00</span></td>
                <td><span class="c-red">- 10.00</span></td>
                <td>29,307.05</td>
                <td>成功</td>
            </tr>
            -->
            <?php $fTotalAmount = $fTotalTransAmount = $fTotalCharge = 0; ?>
            @foreach ($datas as $key => $data)
            <tr>
                <td><a href="#" title="{!! $data->order_no !!}">{!! $data->order_no_short !!}</a></td>
                <td>{!! $data->created_at !!}</td>
                <td>{!! $data->formatted_deposit_mode !!}</td>
                <td><span class="c-green real_amount">{!! $data->amount_formatted !!}</span></td>
                <td>{!! $data->formatted_status !!}</td>
            </tr>
            <?php
                $fTotalAmount      += $data->real_amount + $data->fee;
                $fTotalTransAmount += $data->real_amount;
                $fTotalCharge      += $data->fee;
            ?>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td>小结</td>
                <td></td>
                <td></td>
                <td>{!!  number_format($fTotalTransAmount, 2) !!}</td>
                <td></td>
            </tr>
        </tfoot>
    </table>
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


})(jQuery);
</script>
@stop