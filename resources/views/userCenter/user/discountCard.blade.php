@extends('l.home')

@section('title')
优惠卡
@parent
@stop

@section('scripts')
@parent
{!! script('gagame.Mask') !!}
@stop

@section('main')
<div class="g_33 main clearfix">
    <div class="main-sider">


    </div>
    <div class="main-content">

        <div class="nav-bg">
            <div class="title-normal">我的优惠卡</div>
        </div>

        <div class="content myDiscountCard clearfix">

            <div class="myDiscountCard-box">
                <div class="dc-top">

                    @if(!empty($datas))
                    <div class="right">
                        <div class="list-show-mode">
                            <a href="javascript:void(0);" data-mode="view" class="view-mode">图片模式</a>
                            <a href="javascript:void(0);" data-mode="list" class="list-mode">列表模式</a>
                        </div>
                    </div>
                    @endif
                    <div class="">
                        <div class="filter-tabs">
                            <select id="J-select-discount" style="display:none;">
                              @foreach($oActivies as $oActivity)
                                <option @if($id == $oActivity->id)selected@endif value="{!!route('user-activity-user-bonuses.get-discount-card',$oActivity->id)!!}">{!!$oActivity->name!!}</option>
                              @endforeach
                            </select>
                        </div>
                        @if(!empty($oActivity))
                          <span style="margin-left:20px;">
                              <a target="_blank" class=" btn-small btn btn-important" href="{!!route('user-activities.show',$oActivity->id)!!}">活动详情</a>
                          </span>
                        @endif
                    </div>
                </div>
                @if(empty($datas) || $datas->isEmpty())
                  <div class="dc-list">
                      <div class="no-data">暂时没有找到符合当前条件的优惠卡哦～</div>
                  </div>
                @else
                @if($type=='pic')
                <ul class="dc-list clearfix" id="J-dc-list" data-show-mode="view" style="display:none;">
                    @foreach($datas as $data)
                    <?php
                    $aData = json_decode($data->data, true);
                    // $statusClass = 'dc-' . $data->type;
                    $statusClass = 'dc-rebate';
                    $btnHtml1 = $btnHtml2 = '';

                    // 已领取
                    if( $data->status == ActivityUserBonus::STATUS_SENT ){
                        $statusClass .= ' dc-locked';
                        $btnHtml1 = '<a class="btn" href="javascript:void(0);">已领取</a>';
                        $btnHtml2 = '已领取';
                    }
                    //  可领取
                    else if( $data->status == ActivityUserBonus::STATUS_VERIRIED ){
                        $btnHtml1 = '<a data-button=" '.$data->id.' " class="btn btn-important" href="javascript:void(0);">领取优惠卡</a>';
                        $btnHtml2 = '<a data-button=" '.$data->id.' " class="c-important" href="javascript:void(0);">领取优惠卡</a>';
                    }
                    // 过期的
                    else if( $data->status == ActivityUserBonus::STATUS_FINISHED ){
                        $statusClass .= ' dc-expired';
                        $btnHtml1 = '<a class="btn" href="javascript:void(0);">发放中</a>';
                        $btnHtml2 = '发放中';
                    }
                    // 锁定的
                    else if( $data->status == ActivityUserBonus::STATUS_CREATED ){
                        $statusClass .= ' dc-locked';
                        $btnHtml1 = '<a class="btn" href="javascript:void(0);">进行中</a>';
                        $btnHtml2 = '进行中';
                    }
                    ?>
                    <li class="{!!$statusClass!!}">
                        <div class="css-flip css-flip-x">
                            <div class="flip-front dc-cover">
                                <i>NO.{!!$data->id!!}</i>
                                <h2> <em>{!!$data->face_value!!}</em> 元优惠卡</h2>
                                <p>可用余额：{!!$data->amount!!}</p>
                            </div>
                            <div class="flip-back">
                                <dl>

                                    <dt>可用于：</dt>
                                    <dd><span class="c-important">{!!$data->lottery_names!!}</span></dd>
                                    <dt>获得于：</dt>
                                    <dd>{!!$data->created_at!!}</dd>
                                    <dt>优惠卡过期时间：</dt>
                                    <dd><span class="c-important">{!!$data->end_time!!}</span></dd>
                                </dl>
                            </div>
                        </div>
                        <p><span data-money-format>{!!$data->amount_formatted!!}</span> 元</p>
                        {!!$btnHtml1!!}
                    </li>
                    @endforeach
                </ul>
                <div class="dc-list" data-show-mode="list" style="display:none;">
                    <table class="table">
                        <tr>
                            <th>优惠卡类型</th>
                            <th>获得途径</th>
                            <th>获得时间</th>
                            <th>优惠卡金额（元）</th>
                            <th>过期时间</th>
                            <th>优惠卡状态</th>
                        </tr>
                        @foreach($datas as $data)
                        <?php
                        $aData = json_decode($data->data, true);
                        $statusClass = 'dc-rebate';
                    $btnHtml1 = $btnHtml2 = '';

                    // 已领取
                    if( $data->status == ActivityUserBonus::STATUS_SENT){
                        $statusClass .= ' dc-claimed';
                        $btnHtml2 = '已领取';
                    }
                    //  可领取
                    else if( $data->status == ActivityUserBonus::STATUS_VERIRIED ){
                        $btnHtml2 = '<a data-button=" '.$data->id.' " class="c-important" href="javascript:void(0);">领取优惠卡</a>';
                    }
                    // 过期的
                    else if( $data->status == ActivityUserBonus::STATUS_FINISHED ){
                        $statusClass .= ' dc-expired';
                        $btnHtml2 = '发放中';
                    }
                    // 锁定的
                    else if( $data->status == ActivityUserBonus::STATUS_CREATED ){
                        $statusClass .= ' dc-locked';
                        $btnHtml2 = '进行中';
                    }
                        ?>
                        <tr>
                            <td>活动优惠卡</td>
                            <td>{!!$data->prize_name!!}</td>
                            <td>{!!$data->created_at!!}</td>
                            <td><span>{!!$data->amount_formatted!!}</span> 元</td>
                            <td>{!!$data->expired_at!!}</td>
                            <td>{!!$btnHtml2!!}</td>
                        </tr>
                        @endforeach
                        </tr>
                    </table>
                </div>
                @elseif($type=='table')
                <div class="dc-list">
                    <table class="table">
                        <tr>
                            <th>优惠卡名称</th>
                            <th>优惠卡金额</th>
                            <th>领取时间</th>
                            <th>过期时间</th>
                        </tr>
                        @foreach($datas as $data)
                        <?php
                        $aData = json_decode($data->data, true);
                        ?>
                        <tr>
                            <td>{!!$data->prize_name!!}</td>
                            <td>{!!$data->amount!!}元</td>
                            <td>{!!$data->received_at!!}</td>
                            <td>{!!$data->expired_at!!}</td>
                        </tr>
                        @endforeach
                    </table>
                </div>
                @endif
                @endif
            </div>

        </div>
    </div>
</div>
@stop


@section('end')

@parent
<script>
(function($){

    var $lists = $('#J-dc-list');

    // Flip添加class
    $('li .css-flip', $lists).hover(function () {
        $(this).addClass('flip-hover');
    }, function () {
        $(this).removeClass('flip-hover');
    });

    // 列表显示方式
    var $modes = $('[data-mode]'),
            mode = dsCookie.readCookie('hongbaoListMode'),
            $lists = $('[data-show-mode]').fadeOut(0);
    if (mode != 'view' && mode != 'list') {
        mode = 'view';
    }
    $modes.on('click', function () {
        var mode = $(this).data('mode');
        if (!mode || (mode != 'view' && mode != 'list') || $(this).hasClass('active'))
            return false;
        $lists.fadeOut(0).filter('[data-show-mode="' + mode + '"]').fadeIn();
        dsCookie.eraseCookie('hongbaoListMode');
        dsCookie.createCookie('hongbaoListMode', mode, 100);
        $(this).addClass('active').siblings().removeClass('active');
    }).filter('[data-mode="' + mode + '"]').trigger('click');

    // 下拉组件

    var selectIssue = new gagame.Select({realDom:'#J-select-discount',cls:'w-5'});

    selectIssue.addEvent('change', function(e,value){
            document.location = $.trim(value);
        });


    // 领取优惠卡
    var hbWindow = new gagame.Message();
    var hbMask = new gagame.Mask();
    $('[data-button]', $lists).on('click', function (e) {
        e.preventDefault();
        var $this = $(this),
                id = $this.data('button');
        $.ajax({
            type: 'POST',
            url: '{!!route("user-activity-user-bonuses.get-bonus")!!}',
            data: 'id=' + id +'&_token={!! csrf_token() !!}',
            success: function (resp) {
                 resp = $.parseJSON(resp);
                var data = {
                    closeIsShow: true,
                    closeButtonText: '关闭',
                    closeFun: function () {
                        this.hide();
                        hbMask.hide();
                    }
                };
                if (resp.msgType == 'error') {
                    data['title'] = '优惠卡领取失败';
                    data['content'] = '优惠卡领取失败，请稍候再试';
                } else {
                    data['title'] = '优惠卡领取成功';
                    // console.log(resp, resp.money, resp.msgType);
                    data['content'] = '恭喜您获得' + resp.money + '元竞彩优惠卡，祝您愉快与尽兴。';
                    if ($this.hasClass('btn')) {
                        $this.parents('li:eq(0)').addClass('dc-claimed').end()
                                .replaceWith('<a class="btn" href="javascript:void(0);">已领取</a>');
                    } else {
                        $this.replaceWith('已领取');
                    }
                }
                hbWindow.show(data);
                hbMask.show();
            }
        });
    });

})(jQuery);
</script>
@stop
