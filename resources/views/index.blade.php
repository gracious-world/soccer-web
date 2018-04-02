@extends('l.base')

@section ('title')
欢迎来到
@parent
@stop

@section ('scripts')
@parent
{!! script('bootstrap') !!}
{!! script('jquery.cycle') !!}
{!! script('jquery.cycle.center') !!}

@stop

@section ('container')
    <!--top-->
    @include('w.top')

    <!--header-->

    <!--nav-->
    @include('w.nav')
    <div class="conter-main">
        <!--banner-->
        <div class=" banner-box">
            <div class="cycle-slideshow"
                data-cycle-slides="> a"
                data-cycle-fx=scrollHorz
                data-cycle-timeout=2000
                data-cycle-center-horz=true
                data-cycle-center-vert=true>
                <a href="##" style=" background:#230000;"><img src="../imager/index/ad.png" ></a>
                <a href="##" style=" background:#230000;"><img src="../imager/index/ad.png" ></a>
                <a href="##" style=" background:#230000;"><img src="../imager/index/ad.png" ></a>
            </div>
        </div>

        <div class="conter-box">
            <div class="container">
                <div class="c-l">
                    <!--彩种导航-->
                    <div class=" lottery-list">
                        <img src="../imager/index/1.png">
                    </div>

                    <!--热门购买-->
                    <div class="c-c">
                        <div class="speed-buy">
                            <!-- Nav tabs -->
                            <ul class="nav nav-tabs nav-justified" role="tablist">
                              <li role="presentation" class="active"><a href="#1" aria-controls="1" role="tab" data-toggle="tab">卡拉巴赫    VS   墨尔本城</a></li>
                              <li role="presentation"><a href="#2" aria-controls="2" role="tab" data-toggle="tab">卡拉巴赫    VS   墨尔本城</a></li>
                              <li role="presentation"><a href="#3" aria-controls="3" role="tab" data-toggle="tab">卡拉巴赫    VS   墨尔本城</a></li>
                            </ul>

                            <!-- Tab panes -->
                            <div class="tab-content">
                              <div role="tabpanel" class="tab-pane active" id="1"><img src="../imager/index/2.png"></div>
                              <div role="tabpanel" class="tab-pane" id="2">.2..</div>
                              <div role="tabpanel" class="tab-pane" id="3">.3..</div>
                            </div>
                        </div>
                    </div>

                    <!--购彩-->
                    <div class="clear">
                        <div class="panel panel-default ">
                          <div class="panel-heading">竞彩对阵<a class="pull-right" href="##">更多</a></div>
                          <div class="against">
                            <img src="../imager/index/4.png" height="510px" width="100%">
                          </div>
                        </div>
                    </div>
                </div>

                <div class="c-r pt10">
                    <!--消息-->
                    <div class="panel panel-info">
                        <div class="panel-body row">
                            <i class=" col-md-6"><button type="button" class="btn btn-default btn-block">注 册</button></i>
                            <i class=" col-md-6"><button type="button" class="btn btn-danger btn-block">登 录</button></i>
                        </div>
                        <div class="panel-heading">
                            平台公告
                        </div>
                        <div class="p10">
                            <ol>
                              <li><a href="#">关于足球竞彩撤单公告 11/23</a></li>
                              <li><a href="#">关于足球竞彩撤单公告 11/23</a></li>
                              <li><a href="#">关于足球竞彩撤单公告 11/23</a></li>
                              <li><a href="#">关于足球竞彩撤单公告 11/23</a></li>
                              <li><a href="#">关于足球竞彩撤单公告 11/23</a></li>
                              <li><a href="#">关于足球竞彩撤单公告 11/23</a></li>
                            </ol>
                        </div>
                    </div>
                    <!--累计-->
                    <div class="panel ">
                        <img src="../imager/index/3.png">
                    </div>

                    <div class="panel panel-default">
                      <div class="panel-heading">
                        最新中奖
                        <a class="pull-right" href="##">更多</a>
                      </div>
                      <div class="p10">
                        <ul>
                            <li><a href="##">[竞彩] 中奖了..... <font class="text-danger">18.00</font> 元</a></li>
                            <li><a href="##">[竞彩] 中奖了..... <font class="text-danger">18.00</font> 元</a></li>
                            <li><a href="##">[竞彩] 中奖了..... <font class="text-danger">18.00</font> 元</a></li>
                            <li><a href="##">[竞彩] 中奖了..... <font class="text-danger">18.00</font> 元</a></li>
                            <li><a href="##">[竞彩] 中奖了..... <font class="text-danger">18.00</font> 元</a></li>
                            <li><a href="##">[竞彩] 中奖了..... <font class="text-danger">18.00</font> 元</a></li>
                            <li><a href="##">[竞彩] 中奖了..... <font class="text-danger">18.00</font> 元</a></li>
                            <li><a href="##">[竞彩] 中奖了..... <font class="text-danger">18.00</font> 元</a></li>
                            <li><a href="##">[竞彩] 中奖了..... <font class="text-danger">18.00</font> 元</a></li>
                            <li><a href="##">[竞彩] 中奖了..... <font class="text-danger">18.00</font> 元</a></li>
                        </ul>
                      </div>
                    </div>

                    <div class="panel panel-default">
                      <div class="panel-heading">
                        新闻资讯
                        <a class="pull-right" href="##">更多</a>
                      </div>
                      <div class="p10">
                        <ul>
                            <li><a href="##">[专家推荐] 中奖了.....  元</a></li>
                            <li><a href="##">[专家推荐] 中奖了.....  元</a></li>
                            <li><a href="##">[专家推荐] 中奖了.....  元</a></li>
                            <li><a href="##">[专家推荐] 中奖了.....  元</a></li>
                            <li><a href="##">[专家推荐] 中奖了.....  元</a></li>
                            <li><a href="##">[专家推荐] 中奖了.....  元</a></li>
                            <li><a href="##">[专家推荐] 中奖了.....  元</a></li>
                            <li><a href="##">[专家推荐] 中奖了.....  元</a></li>
                            <li><a href="##">[专家推荐] 中奖了.....  元</a></li>
                            <li><a href="##">[专家推荐] 中奖了.....  元</a></li>
                            <li><a href="##">[专家推荐] 中奖了.....  元</a></li>
                        </ul>
                      </div>
                    </div>

                </div>
            </div>
        </div>
    </div>



@include('w.footer')
@stop




@section('end')
@parent

@stop

