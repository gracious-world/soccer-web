@extends('l.ghome')

@section('title') {!! $sBpName !!} @parent  @stop

@section ('styles')
@parent

    {!! style('gameBase') !!}
    {!! style('gameUi') !!}
    {!! style('uicss') !!}
@stop


@section ('container')

<div class="main-content">

    <div class="nav-bg nav-bg-tab">
        <div class="title-normal">
            投注记录
        </div>
        <a href="javascript:history.back(-1)" class="mg-back"  >返回</a>
    </div>

    <div class="content">
        <div class="area-search">
            <form action=" " class="form-inline" method="get">
                <input type="hidden" name="_token" value=" " />
                <p class="row">
                    投注时间：<input id="J-date-start" class="input w-3" type="text" name="bought_at_from" value="{!! Input::get('bought_at_from', date('Y-m-d 00:00:00')) !!}" /> 至 <input id="J-date-end" class="input w-3" type="text" name="bought_at_to" value="{!! Input::get('bought_at_to', date('Y-m-d 23:59:59')) !!}" />
                    &nbsp;&nbsp;
                    <select id="J-select-issue" style="display:none;" name="number_type">
                        <option value="serial_number" {!! Input::get('number_type') == 'serial_number' ? 'selected' : '' !!}>注单编号</option>
                        <option value="issue" >奖期编号</option>
                    </select>
                </p>
                <p class="row">
                    彩种：
                    <select id="J-select-game" style="display:none;" name="game_type">
                        <option value="">所有...</option>
                        @foreach($aGameTypes as $key => $value)
                        <option value="{!! $key !!}">{!! $value !!}</option>
                        @endforeach
                    </select>
                    &nbsp;&nbsp;
                    游戏类型：
                    <select id="J-select-gate" style="display:none;" name="way_id">
                        <option value="">所有...</option>
                        @foreach($aGateTypes as $key => $value)
                        <option value="{!! $key !!}">{!! __('_method.' .$value) !!}</option>
                        @endforeach
                    </select>
                    方案状态：
                    <select id="J-select-status" style="display:none;" name="way_id">
                        <option value="">所有...</option>
                        @foreach($aStatus as $key => $value)
                        <option value="{!! $key !!}">{!! __('_bill.' .$value) !!}</option>
                        @endforeach
                    </select>
                    <input type="submit" value="搜 索" class="btn btn-important" id="J-submit">
                </p>
            </form>
        </div>

        <table width="100%" class="table" id="J-table">
            <thead>
                <tr>
                    <th>方案编号</th>
                    <th>彩种</th>
                    <th>游戏类型</th>
                    <th>倍数</th>
                    <th>方案金额(¥)</th>
                    <th>奖金(¥)</th>
                    <th>方案状态</th>
                    <th>投注时间</th>
                    <th>方案详情</th>
                </tr>
            </thead>
            <tbody>
                @foreach($datas as $data)
                <tr>
                    <td><a href="{!! route('bills.view', ['id' => $data->id]) !!}">{!! $data->sn !!}</a></td>
                    <td>{!! __('_gametype.' . $aGameTypes[$data->gt_id]) !!}</td>
                    <td>{!! __('_gatetype.' . $data->gate_type_display) !!}</td>
                    <td>{!! $data->multiple !!}</td>
                    <td>{!! $data->amount_display !!}</td>

                    @if($data['status'] > \App\Models\Bet\Bill::STATUS_SYSTEM_CANCELED)
                    <td>
                    {!! ($data->prize_display >0) ?'<div class="increase">'.$data->prize_display.'</div>':$data->prize_display !!}
                    </td>
                    <td>{!! ($data->prize_display >0) ?  '<div class="increase">'.__('_bill.' . $data->status_display).'</div>':__('_bill.' . $data->status_display) !!}</td>
                   @elseif($data['status'] == \App\Models\Bet\Bill::STATUS_TICKET_PRINTED || $data['status'] == \App\Models\Bet\Bill::STATUS_WAITING)
                    <td>&nbsp;</td>
                    <td>{!! __('_bill.' . $data->status_display) !!}</td>
                   @else

                   <td> — </td>
                    <td>{!! __('_bill.' . $data->status_display) !!}</td>
                    @endif

                    <td>{!! $data->bought_at !!}</td>
                    <td><a href="{!! route('bills.view', ['id' => $data->id]) !!}">查看</a></td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td>每页小结</td>
                    <td colspan="3">&nbsp;</td>
                    <td>¥ {!! $datas->sum('amount') !!}</td>
                    <td>¥ {!! $datas->sum('prize') !!}</td>
                    <td colspan="3">&nbsp;</td>
                </tr>
            </tfoot>
        </table>

        <div class=" page-wrapper clearfix">
        @include('pagination.default', ['paginator' => $datas])
        </div>
    </div>


</div>

@stop




@section('end')
    {!! script('easing') !!}
    {!! script('uiScript') !!}
@parent
    <script type="text/javascript">
        $(function(){
            $('#J-date-start').focus(function(){
                (new UIscript.DatePicker({input:'#J-date-start',isShowTime:true, startYear:2013})).show();
            });
            $('#J-date-end').focus(function(){
                (new UIscript.DatePicker({input:'#J-date-end',isShowTime:true, startYear:2013})).show();
            });


            var gameSelector = new UIscript.Select({realDom:'#J-select-game',cls:'w-2'});
            var gateSelector = new UIscript.Select({realDom:'#J-select-gate',cls:'w-2'});
            var gameStatus = new UIscript.Select({realDom:'#J-select-status',cls:'w-2'});

        });

    </script>
@stop










