@extends('l.home', array('active' => 'signin'))

@section('title') 查看下级投注 @parent @stop

@section ('styles')
@parent
    {!! style('ucenter') !!}
    {!! style('reg') !!}

@stop

@section('scripts')
@parent
{!! script('jquery.mousewheel') !!}
 {!! script('gagame.Select') !!}
{!! script('gagame.DatePicker') !!}
@stop

@section('main')
<div class="wrap-inner main">
    @include("userCenter.teamManager.teamHeader")
    <div class="box-register box-register-border">
          <div class="area-search">
            <form action="{!!route("programs.index")!!}" class="form-inline" method="get">
                <input type="hidden" name="_token" value="{!! csrf_token() !!}" />
                    <div class="clearfix oneLine">
                        <div class="f-1 label">用户名：</div>
                        <div class="f-2"><input type="text" name="username" class="input"></div>
                         <div class="left">投注时间：</div>
                         <div class="f-2"><input type="text" name="bought_at_from" class="input" id="J-date-start"></div>
                         <div class="left">至</div>
                        <div class="f-2"><input type="text" name="bought_at_to" class="input" id="J-date-end"></div>
                    </div>
                    <div class="clearfix">
                        <div class="f-1 label">彩种：</div>
                        <div class="f-2">
                            <select id="J-select-lottery" name="game_type">
                                <option value="">全部</option>
                                @foreach($aGameTypes as $key=>$value)
                                    <option value="{!!$key!!}">{!!$value!!}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="left">过关方式：</div>
                        <div class="f-2">
                            <select id="J-select-gate" name="gt_id">
                                <option value="">全部</option>
                                @foreach($aGateTypes as $key=>$value)
                                    <option value="{!!$key!!}">{!!__("_program.".$value)!!}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="left">方案状态：</div>
                        <div class="f-2">
                            <select id="J-select-status" name="status">
                                <option value="">全部</option>
                                @foreach($aStatus as $key=>$value)
                                    <option value="{!!$key!!}">{!!$value!!}</option>
                                @endforeach
                            </select>
                        </div>
                        <input type="submit" value="搜 索" class="btn btn-important" >
                    </div>
            </form>
        </div>
        <table class="table">
            <thead>
                <tr>
                    <th>上级代理</th>
                    <th>用户名</th>
                    <th>方案编号</th>
                    <th>过关方式</th>
                    <th>倍数</th>
                    <th>方案金额</th>
                    <th>奖金</th>
                    <th>方案状态</th>
                    <th>投注时间</th>
                </tr>
            </thead>
            <tbody>
            <?php
                if (isset($aTotalColumns)) {
                    $aTotals = array_fill_keys($aTotalColumns, 0);
                }
            ?>
            @if(isset($datas) && count($datas) > 0)
            @foreach($datas as $data)
                <tr>
                    <td>{!! $data->parent_user !!}</td>
                    <td>{!! $data->username !!}</td>
                    <td>{!! $data->sn !!}</td>
                    <td>{!! __('_gatetype.' . $data->gate_type_display) . '[' . $data->gate . ']' !!}</td>
                    <td>{!! $data->multiple !!}</td>
                    <td>{!! $data->amount_display !!}</td>
                    <td>{!! $data->prize_display !!}</td>
                    <td><div class="{!! $data->prize > 0 ? 'increase' : '' !!}">{!! $data->status_display !!}</div></td>
                    <td>{!! $data->created_at !!}</td>
                </tr>
                <?php
                    $aTotals['amount'] += ($data->amount ? $data->amount : 0);
                    $aTotals['prize'] += ($data->prize ? $data->prize : 0);
                ?>
            @endforeach
            @endif
            @if(isset($aTotalColumns))
                <tr>
                    <td>本页合计</td>
                    <td colspan="4"></td>
                    <td>{!! number_format($aTotals['amount'], 2) !!}</td>
                    <td>{!! number_format($aTotals['prize'], 2) !!}</td>
                    <td colspan="2"></td>
                </tr>
            @endif
            </tbody>
        </table>
        <!-- SONW 注意 ，这里需要分页条 -->
        <div class="text-center">
            @include('pagination.default', ['paginator' => $datas])
       </div>
    </div>
</div>
@include('w.notification')
@stop


@section('end')
 @parent
<script type="text/javascript">

//投注时间
$('#J-date-start').focus(function () {
    (new gagame.DatePicker({input: '#J-date-start', isShowTime: false, startYear: 2013})).show();
});
 $('#J-date-end').focus(function () {
    (new gagame.DatePicker({input: '#J-date-end', isShowTime: false, startYear: 2013})).show();
 });

   //彩种：
   new gagame.Select({realDom:'#J-select-lottery',cls:'w-3'});
   //过关方式：
   new gagame.Select({realDom:'#J-select-gate',cls:'w-3'});
    //方案状态：
   new gagame.Select({realDom:'#J-select-status',cls:'w-3'});

</script>
@stop

