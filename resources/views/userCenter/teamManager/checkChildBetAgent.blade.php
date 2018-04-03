@extends('l.home', array('active' => 'signin'))

@section('title') 查看下级投注 一代所见 - @parent - 用户 @stop

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
            <form action="{!! route('team-profits.index') !!}" class="form-inline" method="get">
                <input type="hidden" name="_token" value="{!! csrf_token() !!}" />
                    <div class="clearfix oneLine">
                        <div class="f-1 label">代理：</div>
                        <div class="f-2">
                            <select id="J-select-agent">
                                <option>全部</option>
                                <option>重庆时时彩</option>
                            </select>
                        </div>
                         <div class="left">投注时间：</div>
                        <div class="f-2"><input type="text" name="" class="input" id="J-date-start"></div>
                         <div class="left">至</div>
                        <div class="f-2"><input type="text" name="" class="input" id="J-date-end"></div>

                    </div>
                    <div class="clearfix">
                        <div class="f-1 label">彩种：</div>
                        <div class="f-2">
                            <select id="J-select-lottery">
                                <option>全部</option>
                                <option>重庆时时彩</option>
                            </select>
                        </div>
                        <div class="left">过关方式：</div>
                        <div class="f-2">
                            <select id="J-select-gate">
                                <option>全部</option>
                                <option>过关方式1</option>
                            </select>
                        </div>
                        <div class="left">方案状态：</div>
                        <div class="f-2">
                            <select id="J-select-status">
                                <option>全部</option>
                                <option>方案状态1</option>
                            </select>
                        </div>
                        <input type="submit" value="搜 索" class="btn btn-important" >
                    </div>
            </form>
        </div>
        <table class="table">
            <thead>
                <tr>
                    <th>代理名</th>
                    <th>用户名</th>
                    <th>方案编号</th>
                    <th>彩种</th>
                    <th>过关方式</th>
                    <th>倍数</th>
                    <th>方案金额</th>
                    <th>奖金</th>
                    <th>方案状态</th>
                    <th>投注时间</th>
                    <th>方案详情</th>
                </tr>
            </thead>
            <tbody>
             <?php for($i=0;$i<10;$i++){?>
                <tr>
                    <td>amos</td>
                    <td>amos 01</td>
                    <td><a href="#">AA123355687496</a></td>
                    <td>竞彩足球</td>
                    <td>自由过关</td>
                    <td>10</td>
                    <td>20</td>
                    <td>60.00</td>
                    <td>已中奖</td>
                    <td>2016/10/7 18:00</td>
                    <td><a href="#">查看</a></td>
                </tr>
                <?php  } ?>
            </tbody>
        </table>
        <!-- SONW 注意 ，这里需要分页条 -->
        <div class="text-center">SONW 注意 ，这里需要分页条</div>
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
//代理
new gagame.Select({realDom:'#J-select-agent',cls:'w-3'});
   //彩种：
   new gagame.Select({realDom:'#J-select-lottery',cls:'w-3'});
   //过关方式：
   new gagame.Select({realDom:'#J-select-gate',cls:'w-3'});
    //方案状态：
   new gagame.Select({realDom:'#J-select-status',cls:'w-3'});

</script>
@stop

