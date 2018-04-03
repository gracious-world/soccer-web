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

@stop

@section ('container')

<div class="main-content">

    <div class="nav-bg nav-bg-tab">
        <div class="title-normal">
            赛果开奖
        </div>
        <a href="javascript:history.back(-1)" class="mg-back"  >返回</a>
    </div>

    <div class="content">
        <div class="area-search">
            <form action="{!! route('games.result') !!}" class="form-inline" name="resultSearchForm" method="get">
                <input type="hidden" name="_token" value="{!! csrf_token() !!}" />
                <p class="row">
                    开奖日期：<input id="J-date-start" class="input w-3" type="text" name="sale_start_at" value="{!! isset($sale_start_at) ? $sale_start_at : date('Y-m-d', strtotime('today')) !!}" />
                    &nbsp;&nbsp;
                    <!-- <select id="J-select-issue" style="display:none;" name="number_type">
                        <option value="serial_number">注单编号</option>
                        <option value="issue" >奖期编号</option>
                    </select> -->

                    <input type="submit" value="搜 索" class="btn btn-important" id="J-submit">
                </p>
            </form>
        </div>
        <!--全部玩法比其他玩法的class多一个 all_play-->
        <div class="mt_10 all_play clearfix">
              <!--赛事详情 开始-->
              <table class="zqdc_table ss_list">
                <colgroup>
                  <col width="">
                  <col width="21%">
                  <col width="13%">
                  <col width="18%">
                  <col width="6%">
                  <col width="18%">
                </colgroup>
                <thead>
                  <tr>
                    <th>场次</th>
                    <th>赛事</th>
                    <th>比赛<br>时间</th>
                    <th><b class="c_green">主队</b></th>
                    <th>让球</th>
                    <th><b class="c_red">客队</b></th>
                  </tr>
                </thead>
                <tbody>
                    @foreach($datas as $key => $data)
                    <tr>
                        <td>{!! $data['week'] . $data['num'] !!}</td>
                        <td style="background-color:{!! $data['l_background_color'] !!}"><a class="szmc" href="javascript:void(0);" target="_blank">{!! $data['l_cn_abbr'] !!}</a></td>
                        <td>{!! $data['played_at'] !!}</td>
                        <td>
                            <a target="_blank" href="javascript:void(0);" title="{!! $data['h_cn'] !!}">
                            {!! $data['h_cn_abbr'] !!}</a>
                        </td>
                        <td>{!! $data['fixed'] !!}</td>
                        <td>
                            <a target="_blank" href="javascript:void(0);" title="{!! $data['a_cn'] !!}">
                            {!! $data['a_cn_abbr'] !!}</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <!--赛事详情 结束-->
            <!--比分详情 开始-->
            <dl class="bf_detail">
                <!--胜平负 开始-->
                <dd>
                  <table class="zqdc_table">
                    <colgroup>
                      <col width="50%">
                      <col width="50%">
                    </colgroup>
                    <thead>
                      <tr>
                        <th bgcolor="#FFDFE7" colspan="2" class="c_ba2636">胜平负</th>
                      </tr>
                      <tr>
                        <th bgcolor="#FFDFE7"><span class="c_ba2636">彩果</span></th>
                        <th bgcolor="#FFDFE7"><span class="c_ba2636">指数</span></th>
                      </tr>
                    </thead>
                    <tbody>
                        @foreach($datas as $data)
                        <tr>
                            <td class="
                            @if(isset($data['result']['had']))
                             @if($data['result']['had'] == '胜')
                             jqc_s
                             @elseif($data['result']['had'] == '负')
                             jqc_f
                             @elseif($data['result']['had'] == '平')
                             jqc_p
                             @else
                             @endif
                            @else
                            @endif
                            ">{!! isset($data['result']['had']) ? $data['result']['had'] : '--' !!}</td>
                            <td>{!! isset($data['result']['had']) && isset($data['odds']['had']) ? $data['odds']['had'] : '--' !!}</td>
                        </tr>
                        @endforeach
                    </tbody>
                  </table>
                </dd>
                <!--胜平负 结束-->
                <!--胜平负让球开始  -->
                <dd>
                  <table class="zqdc_table">
                    <colgroup>
                      <col width="50%">
                      <col width="50%">
                    </colgroup>
                    <thead>
                      <tr>
                        <th bgcolor="#FFDFE7" colspan="2" class="c_ba2636">让球胜平负</th>
                      </tr>
                      <tr>
                        <th bgcolor="#FFDFE7"><span class="c_ba2636">彩果</span></th>
                        <th bgcolor="#FFDFE7"><span class="c_ba2636">指数</span></th>
                      </tr>
                    </thead>
                    <tbody>
                        @foreach($datas as $data)
                        <tr>
                            <td class="
                            @if(isset($data['result']['hhad']))
                             @if($data['result']['hhad'] == '让球胜')
                             jqc_s
                             @elseif($data['result']['hhad'] == '让球负')
                             jqc_f
                             @elseif($data['result']['hhad'] == '让球平')
                             jqc_p
                             @else
                             @endif
                            @else
                            @endif
                            ">{!! isset($data['result']['hhad']) ? $data['result']['hhad'] : '--' !!}</td>
                            <td>{!! isset($data['result']['hhad']) && isset($data['odds']['hhad']) ? $data['odds']['hhad'] : '--' !!}</td>
                        </tr>
                        @endforeach
                    </tbody>
                  </table>
                </dd>
                <!--胜平负 让球结束-->
                <!--总进球 开始-->
                <dd>
                  <table class="zqdc_table">
                    <colgroup>
                      <col width="50%">
                      <col width="50%">
                    </colgroup>
                    <thead>
                      <tr>
                        <th bgcolor="#FFDFE7" colspan="2"><strong class="c_ba2636">总进球</strong></th>
                      </tr>
                      <tr>
                        <th bgcolor="#FFDFE7"><span class="c_ba2636">彩果</span></th>
                        <th bgcolor="#FFDFE7"><span class="c_ba2636">指数</span></th>
                      </tr>
                    </thead>
                    <tbody>
                        @foreach($datas as $data)
                        <tr>
                            <td class="jqc_qt">{!! isset($data['result']['ttg']) ? $data['result']['ttg'] : '--' !!}</td>
                            <td>{!! isset($data['result']['ttg']) && isset($data['odds']['ttg']) ? $data['odds']['ttg'] : '--' !!}</td>
                        </tr>
                        @endforeach
                    </tbody>
                  </table>
                </dd>
                <!--总进球 结束-->
                <!--半全场 开始-->
                <dd>
                  <table class="zqdc_table">
                    <colgroup>
                      <col width="50%">
                      <col width="50%">
                    </colgroup>
                    <thead>
                      <tr>
                        <th bgcolor="#FFDFE7" colspan="2"><strong class="c_ba2636">半全场</strong></th>
                      </tr>
                      <tr>
                        <th bgcolor="#FFDFE7"><span class="c_ba2636">彩果</span></th>
                        <th bgcolor="#FFDFE7"><span class="c_ba2636">指数</span></th>
                      </tr>
                    </thead>
                    <tbody>
                        @foreach($datas as $data)
                        <tr>
                            <td class="jqc_qt">{!! isset($data['result']['hafu']) ? $data['result']['hafu'] : '--' !!}</td>
                            <td>{!! isset($data['result']['hafu']) && isset($data['odds']['hafu']) ? $data['odds']['hafu'] : '--' !!}</td>
                        </tr>
                        @endforeach
                    </tbody>
                  </table>
                </dd>
                <!--半全场 结束-->
                <!--比分 开始-->
                <dd class="bf">
                    <table class="zqdc_table">
                        <thead>
                          <tr>
                            <th bgcolor="#FFDFE7" colspan="2"><strong class="c_ba2636">比分</strong></th>
                          </tr>
                          <tr>
                            <th bgcolor="#FFDFE7"><span class="c_ba2636">彩果</span></th>
                          </tr>
                        </thead>
                        <tbody>
                            @foreach($datas as $data)
                            <tr>
                                <td class="jqc_qt">{!! isset($data['result']['crs']) ? $data['result']['crs'] : '--' !!}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </dd>
            </dl>
        <!--比分详情 结束-->
        </div>
    </div>
</div>

@stop


@section('end')
@parent
<script type="text/javascript">
        $(function(){
            $('#J-date-start').focus(function(){
                (new UIscript.DatePicker({input:'#J-date-start', startYear:2016})).show();
            });
            // $('form[name=resultSearchForm]').submit();
        });

    </script>
@stop




