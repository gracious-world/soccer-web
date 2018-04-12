@extends('l.home')

@section('title')
   精准开户
@parent
@stop

@section('scripts')
@parent
    {!! script('gagame.DatePicker') !!}
    {!! script('gagame.Tab') !!}
{{--    {!! script('gagame.SliderBar') !!}--}}
@stop
@section('main')
<div class="nav-bg nav-bg-tab">
    <div class="title-normal">用户管理</div>
    <ul class="tab-title clearfix">
        <li class="current"><a href="{!! route('users.accurate-create') !!}" ><span>精准开户</span></a></li>
        <li><a href="{!! route('user-links.create') !!}"><span>链接开户</span></a></li>
        <li><a href="{!! route('users.index') !!}"><span>用户列表</span></a></li>
        <li><a href="{!! route('user-links.index') !!}"><span>链接管理</span></a></li>
    </ul>
</div>
<form action="{!! route('users.accurate-create') !!}" method="post" id="J-form">
    <input type="hidden" name="_token" value="{!! csrf_token() !!}" />
    <input type="hidden" name="_random" value="{!! Tool::createRandomStr() !!}" />
    <input type="hidden" name="is_agent" id="J-input-userType" value="{!! Input::old('is_agent', 0) !!}" />
    <input type="hidden" name="prize_group_id" id="J-input-groupid" value="1" />
    <input type="hidden" id="J-input-prize" value="">
    <!-- 1: 固定奖金组, 2:自定义奖金组 -->
    <input type="hidden" name="prize_group_type" id="J-input-group-type" value="2" />
    <input type="hidden" name="agent_prize_set_quota" id="J-agent-quota-limit-json" value="" />

    <div class="content link-create-wrap" id="J-panel-cont">
        <div class="item-detail user-type-choose">
            <div class="item-title">
                <i class="item-icon-13"></i>选择账户类型
            </div>
            <div class="item-info filter-tabs-cont" id="J-user-type-switch-panel">
                <a data-userTypeId="0" href="javascript:void(0);">
                    <i class="user-type-icon-player"></i>
                    <span>玩家账号</span>
                </a>

            </div>
        </div>

        <div class="item-detail item-detail-hover user-info-config">
            <div class="item-title">
                <i class="item-icon-9"></i>设置用户账号信息
            </div>
            <div class="item-info">
                <p>
                    <label>设置登录账号：</label>
                    <input type="text" id="J-input-userName" name="username" class="input input-big w-3">
                    <span style="display:none;" class="ui-text-prompt-multiline w-7">第一个字符必须为字母，由0-9，a-z，A-Z组成的6-16个字符</span>
                </p>
                <p>
                    <label>设置登录密码：</label>
                    <input type="text" id="J-input-password" name="password" class="input input-big w-3">
                    <span style="display:none;" class="ui-text-prompt-multiline w-7">由字母和数字组成6-16个字符；且必须包含数字和字母，不允许连续三位相同</span>
                </p>
                <p>
                    <label>设置昵称：</label>
                    <input type="text" id="J-input-nickName" name="nickname" class="input input-big w-3">
                    <span style="display:none;" class="ui-text-prompt">由2-16个字符组成</span>
                </p>
                <!-- <p>
                    <label>真实姓名：</label>
                    <input type="text" id="J-input-realName" name="name" class="input input-big w-3">
                    <span style="display:none;" class="ui-text-prompt">真实名称必要信息</span>
                </p> -->
            </div>
        </div>

        <div class="item-detail item-detail-hover agent-user-limit J-agent-user-limit">
            <div class="item-title">
                <i class="item-icon-3"></i>设置开户配额
            </div>
            <div class="item-info">
                <p>通过此链接注册的用户最多配额如下</p>
                <input type="text" class="input" data-quota="{{$oUserAccountQuota->left_quota}}" value="0">
                <p>最大允许<span class="quota-max">{{$oUserAccountQuota->left_quota}}</span></p>
            </div>
        </div>

          <div class="item-detail item-detail-hover user-bonus-choose">
            <div class="item-title">
                <i class="item-icon-4"></i>竞彩游戏返点
            </div>

            <div class="bonusgroup-game-type clearfix ">
                <div class="bonusgroup-fb-list">
                    <h3>竞彩足球</h3>
                    <ul>
                        <li class="">
                            <label>单关返点：</label>
                            <input type="text" id="J-input-fb-s" name="fb_single" class="input J-football-input input-big w-1" value="0.0" max-data="{{$fUserSinglePercentValue}}" >%
                            <span>一共有 <i>{{$fUserSinglePercentValue}}</i> % ，可以分配</span>
                        </li>
                        <li class="">
                            <label>混合过关：</label>
                            <input type="text" id="J-input-fb-a" name="fb_all" class="input J-football-input input-big w-1" value="0.0" max-data="{{$fUserMultiPercentValue}}" >%
                            <span>一共有 <i>{{$fUserMultiPercentValue}}</i> % ，可以分配</span>
                        </li>
                    </ul>
                </div>
            </div>

        </div>
        <div class="bonus-config-result">
            <strong>账号设置结果：</strong>
            <label>账户类型<span class="J-user-type">----</span></label>
            {{--<label>数字彩奖金组<span class="J-init-bonusgroup">----</span></label>--}}
            <label>竞彩单关<span class="J-input-fb-s">0.0</span>%</label>
            <label>竞彩串关<span class="J-input-fb-a">0.0</span>%</label>

        </div>

        <div class="row-lastsubmit">
            <input type="button" class="btn btn-important" value="生成账户" id="J-button-submit" />
        </div>
    </div>
</form>
@stop

@section('end')
@parent


<script>
/**全局变量**/

// slider事件是否已经绑定，
// 因为slider插件中要获取元素的宽度，
// 在tab切换中该元素display:none导致获取宽度为0，
// 所以需要在其父元素显示后绑定slider对象
var sliderEventBinded_player = sliderEventBinded_agent = false;

// 用户类型
var userModel;
//var isTopAgent = {!!intval(Session::get('is_top_agent'))!!};

var confirmWin = new gagame.Message();
var confirmWinMask = new gagame.Mask();
var dataInfo = ['',''];//数据缓存

var prizeGroupUrl = "{!! route('user-user-prize-sets.prize-set-detail') !!}"  ; //查看奖金组连接缓存
// 代理奖金组数据
{{--var agentPrizeGroup = {!!$oAllPossibleAgentPrizeGroups!!};--}}
//玩家奖金组数据
{{--var playerPrizeGroup = {!!$oAllPossiblePrizeGroups!!};--}}

//判断用户角色滑动控件初始化方法
var checkSlider =function (){
    if( !sliderEventBinded_player && $('.J-bonusgroup-player').is(':visible') ){
        bindAllSlider($('.J-bonusgroup-player'));
        sliderEventBinded_player = true;
    }else if( !sliderEventBinded_agent && $('.J-bonusgroup-agent').is(':visible') ){
        bindAllSlider($('.J-bonusgroup-agent'));
        sliderEventBinded_agent = true;
    }
};

//开户类型切换方法
var switchUser =function(){
    var switchHandles = $('#J-user-type-switch-panel').find('a');
    switchHandles.on('click', function(e){
        var index = switchHandles.index(this),userTypeId = $.trim($(this).attr('data-userTypeId'));
        e.preventDefault();
        switchHandles.removeClass('current');
        switchHandles.eq(index).addClass('current');
        $('#J-input-userType').val(userTypeId);
        // 代理
        if( userTypeId == '1' ){
            userModel = 'agent';
            $('#J-panel-group').hide();
            $('#J-panel-group-agent').show();
            $('.J-bonusgroup-player').hide();
            $('.J-bonusgroup-agent').show();
            // $('.J-agent-user-limit').show();
        }else{
        // 玩家
            userModel = 'player';
            $('#J-panel-group').show();
            $('#J-panel-group-agent').hide();
            $('.J-bonusgroup-player').show();
            $('.J-bonusgroup-agent').hide();
            // $('.J-agent-user-limit').hide();
        }
        // clearChooseGroup();
        checkSlider();
        $('.J-user-type').text($(this).text());
    }).eq(0).trigger('click');
};

//
// //自定义奖金组设置组件
// var bindAllSlider = function ($parent){
//     var sliderConfig = {
//         // 'isUpOnly' : true,
//         'minDom'   : '[data-slider-sub]',
//         'maxDom'   : '[data-slider-add]',
//         'contDom'  : '[data-slider-cont]',
//         'handleDom': '[data-slider-handle]',
//         'innerDom' : '[data-slider-inner]',
//         'minNumDom': '[data-slider-min]',
//         'maxNumDom': '[data-slider-max]'
//     };
//     $('.bonusgroup-list', $parent).each(function(idx){
//         var $this = $(this),
//             globalSlider, // 统一设置slider
//             sliders = []; // 分段设置slider
//         if( $parent.hasClass('J-bonusgroup-agent') ){
//             var bonusData = agentPrizeGroup;
//         }else{
//             var bonusData = playerPrizeGroup;
//         }
//         $this.find('.slider-range').each(function(_idx){
//             var $that = $(this),
//                 settings = $.extend({}, sliderConfig, {
//                     'parentDom': $that,
//                     'step'     : 1,
//                     'minBound' : 0,
//                     'maxBound' : bonusData.length - 1,
//                     'value'    : 0
//                 });
//             if( $that.hasClass('slider-range-global') ){
//                 globalSlider = new gagame.SliderBar( settings );
//             }else{
//                 sliders.push(new gagame.SliderBar( settings ));
//             }
//         });
//         // 全局设置
//         if( globalSlider ){
//             globalSlider.addEvent('change', function(){
//                 var value = this.getValue(),
//                     $parent = this.getDom();
//                 $.each(sliders, function(i,s){
//                     if( s && s.setValue ){
//                         s.setValue(value);
//                     }
//                 });
//                 // 设置返奖率
//                 var maxBound = bonusData[this.maxBound]['classic_prize'],
//                     nowBound = bonusData[value]['classic_prize'];
//                 var rate = ( maxBound - nowBound ) / 2000;
//                 $parent.find('[data-slider-percent]').text((rate*100).toFixed(2) +'%');
//                 // 设置值
//                 $parent.find('[data-slider-value]').text(nowBound);
//                 $('#J-input-prize').val(nowBound);
//                 // 设置平均返点率
//                 $('.J-init-bonusgroup').text(nowBound);
//                 if( userModel == 'agent' ){
//                     checkQuotaLimitStatus(nowBound);
//                 }
//                 // 设置奖金组详情连接
//                 setWinGroupUrl($parent.find('[data-bonus-scan]'), nowBound, $parent.attr('data-id'));
//                 // $parent.find('[data-bonus-scan]').attr('href', prizeGroupUrl + '/' +nowBound+ '/'+ ($parent.attr('data-id')) );
//             });
//             globalSlider.setValue(0);
//         }
//     });
//     sliderEventBinded = true;
// }
//
// //查看奖金组详情
// var setWinGroupUrl = function( t, bonus, gameId){
//     var el = $(t), param = '', arr = [];
//     if( bonus ) arr.push(bonus);
//     if( gameId ) arr.push(gameId);
//     if( arr.length ) param = arr.join('/');
//     var url = prizeGroupUrl + '/' + param;
//     el.attr('href', url);
// };

// 配额输入验证
// var bindQuotaInput = function(){
//     $('input[data-quota]').on('change', function(){
//         var $this = $(this),
//             val = parseInt( $this.val() ) || 0,
//             max = parseInt( $this.data('quota') );
//         if( val < 1 ){
//             val = 0;
//         }else if( val > max ){
//             val = max
//         }
//         $this.val(val);
//     });
// };

// 通过奖金组来判断某配额设置是否显示
// var checkQuotaLimitStatus = function( prize ){
//     var prizeGroup = parseInt( prize ) || 0,
//         showNum = 0;
//     $('input[data-quota]').each(function(){
//         var prize = $(this).data('prize'),
//             quota = $(this).data('quota');
//         // console.log(prize, prizeGroup)
//         // if( prize < prizeGroup || (isTopAgent && prize == prizeGroup) ){
//         if( prize < prizeGroup ){
//             $(this).parent().show();
//             showNum++;
//         }else{
//             $(this).parent().hide();
//         }
//         // if( prize == prizeGroup && !isTopAgent ){
//         if( prize == prizeGroup ){
//             $(this).siblings('p').find('.quota-max').text(Math.max(quota-1, 0));
//         }else{
//             $(this).siblings('p').find('.quota-max').text(quota);
//         }
//     });
//     if( showNum > 0 && userModel == 'agent' ){
//         $('.J-agent-user-limit').show();
//     }else{
//         $('.J-agent-user-limit').hide();
//     }
// }

// 获取当前配额设置数据对象
// var getQuotaData = function(){
//     // 只有代理才有配额设定，所以可以直接指定获取该DOM的value值，作为最大奖金组
//     var prizeGroup = parseInt( $('#J-input-prize').val() ),
//         // 代理用户配额限制数据变量
//         dataObj = {};
//     $('input[data-quota]:visible').each(function(){
//         var quota = $(this).val(),
//             prize = $(this).data('prize');
//         // if( prize < prizeGroup || (isTopAgent && prize == prizeGroup) ){
//         if( prize < prizeGroup ){
//             dataObj[prize] = quota;
//         }
//     });
//     return dataObj;
// };

//联系qq-tip
var inputTipFun = function(){
    // Tip
    var inputTip = new gagame.Tip({cls:'j-ui-tip-b w-4'});
    $('.user-info-config input').on({
        focus: function(e){
            var $this = $(this),
                text = $this.siblings('span[class|="ui-text-prompt"]').text();
            inputTip.setText(text);
            inputTip.show(-30, inputTip.getDom().height() * -1 - 22, $this);
            e.preventDefault();
        },
        blur: function(){
            inputTip.hide();
        }
    });
};
//弹窗
var openWindow = function () {
    var mask = new gagame.Mask(),
        miniwindow = new gagame.MiniWindow({ cls: 'w-13 iframe-miniwindow' });

    var hideMask = function(){
        miniwindow.hide();
        mask.hide();
    };
    var getContent = function(url){
        return '<iframe src="' + url + '" id="bonus-scan-frame" ' +
        'width="100%" height="450" frameborder="0" allowtransparency="true" scrolling="no"></iframe>'
    }
    miniwindow.setTitle('玩法奖金详情');
    // miniwindow.showCancelButton();
    // miniwindow.showConfirmButton();
    miniwindow.showCloseButton();
    miniwindow.doNormalClose = hideMask;
    miniwindow.doConfirm     = hideMask;
    miniwindow.doClose       = hideMask;
    miniwindow.doCancel      = hideMask;
    $('[data-bonus-scan]').on('click', function(e){
        e.preventDefault();
        var $this = $(this),
            href = $this.attr('href');
        if( !href ) return false;
        miniwindow.setContent( getContent(href) );
        mask.show();
        miniwindow.show();
    });
};

//确认window
var generateConfirmInfo = function (userType, userName, password, nickName, prizeGroup, fba, fbs, agentQuota) {
    var userTypes = ['玩家', '代理'];
    // var htmlQuota = ['<div class="bonusgroup-title" style="margin-top:10px;">',
    //                     '<table width="100%">',
    //                         '<tbody><tr>'];
    // $.each(agentQuota, function(i,n){
    //     htmlQuota.push('<td>' + n + '<br><span class="tip">' + i + '奖金组配额数</span></td>');
    // });
    // htmlQuota.push('</tr></tbody></table></div>');
    var html = [
        '<div class="pop-content">',
            '<p class="pop-text">该用户的具体信息如下，是否立即开户？</p>',
            '<div class="bonusgroup-title" style="margin-top:10px;">',
                '<table width="100%">',
                    '<tr>',
                        '<td>' + userTypes[userType] + '<br><span class="tip">用户类型</span></td>',
                        '<td>' + userName + '<br><span class="tip">登录账号</span></td>',
                        '<td>' + password + '<br><span class="tip">登录密码</span></td>',
                        '<td class="last">' + nickName + '<br><span class="tip">用户昵称</span></td>',
                    '</tr>',
                '</table>',
            '</div>',
            // '<div class="bonusgroup-title" style="margin-top:10px;">',
            //     '<table width="100%">',
            //         '<tr>',
            //             '<td><span class="tip">数字彩奖金组</span><br>' + prizeGroup + '</td>',
            //             '<td><span class="tip">竞彩单关</span><br>' + fbs + '％ </td>',
            //             '<td class="last"><span class="tip">竞彩串关</span><br>' + fba + '％ </td>',
            //         '</tr>',
            //     '</table>',
            // '</div>',
            // htmlQuota.join(''),
        '</div>'
    ];
    return html.join('');
}

//足球返点
var football = function(){
    var $fb = $('.J-football-input'),
    dom = $(this).attr('id');
    $fb.keyup(function(e){
        var v = $.trim(this.value),arr = [],code = e.keyCode;
        if(code == 37 || code == 39){
            return;
        }
        v = v.replace(/[^\d|^\.]/g, '');
        arr = v.split('.');
        if(arr.length > 2){
            v = '' + arr[0] + '.' + arr[1];
        }
        arr = v.split('.');
        if(arr.length > 1){
            arr[1] = arr[1].substring(0, 2);
            v = arr.join('.');
        }
        this.value = v;
        v = v == '' ? '&nbsp;' : v;
        $('.'+dom).text(v);
    });
    $fb.focus(function(){
        var v = $.trim(this.value),
        dom = $(this).attr('id');
        if(v == ''){
            v = '&nbsp;';
        };
        $('.'+dom).text(v);
    });
    $fb.blur(function(){
        var v = Number(this.value),
            maxNum = Number($(this).attr('max-data')),
            dom = $(this).attr('id');
        v = v > maxNum ? maxNum : v;
        this.value =v;
        $('.'+dom).text(v);
    });



};




//加载完成执行方法
$(function(){

    //执行函数方法
    checkSlider();
    switchUser();
    inputTipFun();
    openWindow();
    // bindQuotaInput();
    football();

    //表单提交
    $('#J-button-submit').click(function(e){
        var userType = $.trim($('#J-input-userType').val()),
            userName = $.trim($('#J-input-userName').val()),
            password = $.trim($('#J-input-password').val()),
            passwordConfirm = $.trim($('#J-input-password-confirm').val()),
            nickName = $.trim($('#J-input-nickName').val()),
            // realName =  $.trim($('#J-input-realName').val()),
            panelType = +userType == 1 ? 'J-panel-group-agent' : 'J-panel-group',
            returnRebate = $('#' + panelType).find('li.current').find('.data-feedback').text(),
            prizeGroup = 0,
            agentQuota = getQuotaData(), // 代理用户配额限制数据变量
            //套餐还是自定义
            groupType = $.trim($('#J-input-group-type').val()),

            fbaVal =$.trim($('#J-input-fb-a').val()),
            fbsVal =$.trim($('#J-input-fb-s').val());

        // var lotteryPrizeGroupCache = {},seriesPrizeGroupCache = {};
        //
        //     seriesPrizeGroupCache[1] = $('#J-input-prize').val() || $('.J-bonusgroup-agent .slider-current-value').text();
        //
        // console.log(seriesPrizeGroupCache);
        //
        // var lotteriesJsonData = JSON.stringify(lotteryPrizeGroupCache),
        //     seriesJsonData    = JSON.stringify(seriesPrizeGroupCache),
        //     agentQuotaLimitJson = JSON.stringify( getQuotaData() );
        //     if (lotteriesJsonData != '{}') $('#J-input-lottery-json').val(seriesJsonData);
        //     if (seriesJsonData    != '{}') $('#J-input-series-json').val(seriesJsonData);
        //     $('#J-agent-quota-limit-json').val(agentQuotaLimitJson);
        // return false;
        if(userName == ''){
            alert('请输入登录账号');
            return false;
        }
        if(password == ''){
            alert('请输入登录密码');
            return false;
        } else if (!(/^(?=.*\d+)(?=.*[a-zA-Z]+)(?!.*?([a-zA-Z0-9]{1})\1\1).{6,16}$/).test(password)) {
            alert('密码格式不正确，请重新输入');
            return false;
        }
        if(nickName == ''){
            alert('请输入用户昵称');
            return false;
        }

        // if( userModel == 'agent' ){
        //     prizeGroup = $('.J-bonusgroup-agent').find('[data-slider-value]').text();
        // }else{

        //     prizeGroup = $('.J-bonusgroup-player').find('[data-slider-value]').text();
        // }
        // if(fbsVal == '' || fbaVal == ''){
        //     alert('请设置足球返点');
        //     return false;
        // }

        var data = {
            title            : '信息确认',
            content          : generateConfirmInfo(userType, userName, password, nickName, prizeGroup, fbaVal,fbsVal, agentQuota),
            confirmIsShow    : true,
            cancelIsShow     : true,
            confirmButtonText: '确认',
            cancelButtonText : '取消',
            cssName : 'w-13',
            confirmFun: function () {
                $('#J-form').submit();
            },
            cancelFun: function() {
                confirmWin.hide();
                confirmWinMask.hide();
            }
        };
        confirmWin.show(data);
        confirmWinMask.show();
        return true;
    });

});
</script>
@stop