@extends('l.ghome')

@section('title') @if($iUserId) {!!$sBpName!!} @else 竞彩平台 @endif @parent  @stop


@section ('styles')
@parent

    {!! style('gameBase') !!}
    {!! style('gameUi') !!}
    {!! style('uicss') !!}
    {!! style('result') !!}
@stop

@section('scripts')
@parent
{!! script('echarts') !!}
@stop

@section ('container')

<div class="main-content">

    <div class="nav-bg nav-bg-tab">
        <div class="title-normal">
            奖金走势
        </div>
        <a href="javascript:history.back(-1)" class="mg-back"  >返回</a>
    </div>

    <div class="content">
        <!-- <div class="area-search">
            <a href="{!! route('games.trend') !!}">胜平负奖金走势</a>
            <a href="">让球胜平负奖金走势</a>
        </div> -->
        <div class="mt_10 all_play clearfix">
              <!--赛事详情 开始-->
            <table class="zqdc_table ">
                <colgroup>
                  <col width="8%">
                  <col width="10%">
                  <col width="15%">

                  <col width="10%">
                  <col width="5%">
                  <col width="10%">

                  <col width="5%">
                  <col width="5%">
                  <col width="5%">
                  <col width="5%">
                  <col width="5%">
                  <col width="5%">
                  <col >
                </colgroup>
                <thead>
                  <tr>
                    <th rowspan="2">赛事编号</th>
                    <th rowspan="2">赛事类型</th>
                    <th rowspan="2">截止时间</th>
                    <th rowspan="2"><b class="c_green">主队</b></th>
                    <th rowspan="2">让球</th>
                    <th rowspan="2">客队</th>
                    <th colspan="3">初始奖金</th>
                    <th colspan="3">即时奖金</th>
                    <th rowspan="2">走势图</th>
                  </tr>
                  <tr>
                    <td class="jqc_s">胜</td> <td class="jqc_p">平</td> <td  class="jqc_f">负</td>
                    <td>胜</td> <td>平</td> <td>负</td>
                  </tr>
                </thead>
                <!--每一天从这循环-->

                @foreach($datas as $key => $games)
                <tbody class="table-title">
                    <tr>
                        <td colspan="16">{!! $key !!} (10：00--次日10：00)<span class="dorpdown" data="dorpdown-{!! $key !!}">隐藏</span></td>
                    </tr>
                </tbody>
                <tbody class="dorpdown-{!! $key !!}">
                    @foreach($games as $data)

                    <tr>
                        <td>{!! $data['week'] . $data['num'] !!}</td>
                        <td style="background-color:{!! $data['l_background_color'] !!}">{!! $data['l_cn_abbr'] !!}</td>
                        <td>{!! $data['sale_stop_at'] !!}</td>
                        <td>
                            {!! $data['h_cn_abbr'] !!}
                        </td>
                        <td>
                            {!! $data['fixed'] !!}
                        </td>
                        <td>
                            {!! $data['a_cn_abbr'] !!}
                        </td>
                        <td class="originBonus">{!! isset($data['odd_trend']['h']) && is_array($data['odd_trend']['h']) ? current(array_slice($data['odd_trend']['h'], 0, 1)) : '' !!}</td>
                        <td class="originBonus">{!! isset($data['odd_trend']['d']) && is_array($data['odd_trend']['d']) ? current(array_slice($data['odd_trend']['d'], 0, 1)) : '' !!}</td>
                        <td class="originBonus">{!! isset($data['odd_trend']['a']) && is_array($data['odd_trend']['a']) ? current(array_slice($data['odd_trend']['a'], 0, 1)) : '' !!}</td>
                        <td class="currentBonus {!! isset($data['odd_trend']['h']) && is_array($data['odd_trend']['h']) ? (count($data['odd_trend']['h']) > 1 ? 'jqc_s' : '') : '' !!}">{!! isset($data['odd_trend']['h']) && is_array($data['odd_trend']['h']) ? current(array_slice($data['odd_trend']['h'], -1, 1)) : '' !!}</td>
                        <td class="currentBonus {!! isset($data['odd_trend']['d']) && is_array($data['odd_trend']['d']) ? (count($data['odd_trend']['d']) > 1 ? 'jqc_p' : '') : '' !!}">{!! isset($data['odd_trend']['d']) && is_array($data['odd_trend']['d']) ? current(array_slice($data['odd_trend']['d'], -1, 1)) : '' !!}</td>
                        <td class="currentBonus {!! isset($data['odd_trend']['a']) && is_array($data['odd_trend']['a']) ? (count($data['odd_trend']['a']) > 1 ? 'jqc_f' : '') : '' !!}">{!! isset($data['odd_trend']['a']) && is_array($data['odd_trend']['a']) ? current(array_slice($data['odd_trend']['a'], -1, 1)) : '' !!}
                        </td>
                        <td><a href="javascript:void(0);" class="trend" data='{!! json_encode($data['odd_trend']); !!}'>走势图</a>

                        </td>
                    </tr>
                    @endforeach
                </tbody>
                @endforeach
                <!--每一天从这循环 end-->
            </table>
            <div id="chart"></div>
        </div>
    </div>
</div>

@stop


@section('end')
@parent
<script type="text/javascript">
        $(function() {

            $('#J-date-start').focus(function() {
                (new UIscript.DatePicker({
                    input: '#J-date-start',
                    startYear: 2016
                })).show();
            });


            $('.dorpdown').click(function() {
                var doprId = $(this).attr('data');
                ($(this).html() == '隐藏') ? $(this).html('显示'): $(this).html('隐藏');
                $('.' + doprId).toggle();
            });

            var chartDom = document.getElementById('chart');
            var chart = (function() {
                // 基于准备好的dom，初始化echarts实例
                var myChart = echarts.init(chartDom);

                // 指定图表的配置项和数据
                var option = {
                    title: {
                        text: '走势图',
                        left: 'center'
                    },
                    tooltip: {
                        trigger: 'yxis'
                    },
                    legend: {
                        left: 'left',
                        data: ['胜', '平','负']
                    },

                    xAxis: {
                        type: 'category',
                        name: '时间',
                        splitLine: {
                            show: false
                        },
                        data: ['时间1', '时间2'],
                        nameLocation:'end',
                        nameTextStyle:{width:'60px'}
                    },
                    grid: {
                        left: '3%',
                        right: '9%',
                        bottom: '3%',
                        containLabel: true
                    },
                    yAxis: {
                        type: 'value',
                        name: '赔率',
                        precision:2
                    },
                    series: [{
                        name: '胜',
                        type: 'line',
                        data: [1.80, 3.50, 3.55]
                    }, {
                        name: '平',
                        type: 'line',
                        data: [1.94, 3.40, 3.20]
                    },
                    {
                        name: '负',
                        type: 'line',
                        data: [1.94, 3.40, 3.20]
                    }]
                };

                return {
                    setOption : function(data1,data2,data3,xAxisData){
                        // 使用刚指定的配置项和数据显示图表。
                        option.series[0].data = data1;
                        option.series[1].data = data2;
                        option.series[2].data = data3;
                        option.xAxis.data = xAxisData;
                        myChart.setOption(option);
                    }
                };

            })();


            $(document).on('click','.trend',function(e){
                var pos = $(this).parent().get(0).getBoundingClientRect();
                var data = pullBonus(this);
                var scrollTop = $(window).scrollTop();
                var top = pos.top + scrollTop + 100;
                if(pos.top + scrollTop > $(window).height() ){
                    top  = pos.top + scrollTop - 100;
                }

                $(chartDom).css({
                    left: pos.left - $(chartDom).width() + 'px',
                    top:top
                });
                chart.setOption(data.h,data.d,data.a,data.xAxisData);
                $(chartDom).show();
            });

            $(document).on('click',function(e){
                var target = e.target;
                if($(target).hasClass('trend')) return;
                if(target.tagName.toLowerCase() =='canvas') return;
                $(chartDom).hide();
            });

            function pullBonus(dom){
                var data = {
                    h:[],
                    d:[],
                    a:[],
                    xAxisData:[]
                };
                var string = $(dom).attr('data');

                var jdata = jQuery.parseJSON(string);
                data.h = pullArrFromObj(jdata.h);
                data.d = pullArrFromObj(jdata.d);
                data.a = pullArrFromObj(jdata.a);
                data.xAxisData = pullXAxisData(jdata.h);
                return data;
            }

            function pullArrFromObj(obj) {
                var arr = [];
                for (var o in obj) {
                    arr.push(+obj[o]);
                }
                return arr;
            }

            function pullXAxisData(obj) {
                var arr = [];
                for (var o in obj) {
                    arr.push(o.split(' ')[1].substr(0, 5));
                }
                return arr;
            }
        });
</script>
@stop




