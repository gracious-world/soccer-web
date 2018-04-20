@if(Session::get('user_id'))
<div id="header" class="wrap">
    <div class="wrap-inner">
        <div class="left top-nav">
            <!-- <div class="logo"></div> -->
            <!-- 用户中心 -->
            <div class="top-nav-user">
                <a href="{!! route('users.user') !!}" class="top-nav-toggle" data-overdropdown="handler">
                    <span>{!! Session::get('nickname') !!}</span>
                    <!-- <i class="caret"></i> -->
                </a>
            </div>
            <!-- 站内信 -->
            <div class="top-nav-msg">
                <a href="{!! route('station-letters.index') !!}" class="top-nav-toggle" data-overdropdown="handler">
                    <i></i>
                    <span>系统通知<b id="J-top-msg-num" class="highlight-color"></b></span>
                    <em class="caret"></em>
                </a>

                <div class="top-nav-menu" data-overdropdown="dropdown" id="J-top-system-msg-box"></div>


            </div>
            <div class="top-nav-logout">
                <a href="javascript:void(0);" class="top-nav-toggle logout">
                    <i></i>
                    <span>退出</span>
                </a>
            </div>
            @if(Session::get('is_agent'))
            <div class="top-nav-logout">
            </div>
            @endif
        </div>
        <div class="right">
            <ul class="top-account">

                <li class="top-account-balance">
                    <span class="balance-a" style="display:none;">余额：<span data-user-account-balance class="highlight-color">{!! $fAvailable !!}</span> 元
                    </span>
                    <span class="balance-b" style="display:none;">余额已隐藏</span>
                    <span  id="balance-toggle">隐藏</span>
                </li>
                @if(Session::get('is_player'))
                <li class="top-account-deposit">
                    <a href="{!! $sPaymentRouteStr !!}">
                        <i></i><span>充值</span>
                    </a>
                </li>
                @endif
                <li class="top-account-withdraw">
                    <a href="javascript:void(0);">
                        <i></i><span>提款</span>
                </a></li>
            </ul>
        </div>
    </div>
</div>
<script>
$(function() {
    function getIFrameDOM(id){
        return document.getElementById(id) || (document.frames && document.frames[id]) || window.myifr;
    }
    var withdrawalUrl = "{!! route('user-withdrawal.withdraw') !!}";
    $('.top-account-withdraw').click(function(event) {
        window.location.href = withdrawalUrl;
    });
    $('.logout').click(function(e) {
        window.location.href = "{!! route('logout') !!}";
    });
    // 顶部用户菜单下拉
    $('.top-nav-user, .top-nav-msg').overdropdown({
        activeClass: 'top-nav-toggle-active',
        handlerIsLink: true
    });

    // Tips
    // var tips = new gagame.Tip({cls: 'j-ui-tip-r w-3'});
    // $('[data-tips-top]').hover(function (e) {
    //     var el = $(this),
    //             text = el.data('tips-top');
    //     tips.setText(text);
    //     tips.show((tips.getDom().width()+30 )* -1, -8, el);
    //     e.preventDefault();
    // }, function () {
    //     tips.hide();
    // });

    // 余额显示隐藏
    var $balanceHandler = $('#balance-toggle, .balance-toggle');
    // 显示余额
    if( dsCookie.readCookie('userBalanceIsVisible') ){
        // $('[data-refresh-balance]').trigger('click');
        userBalanceOfTopNav('show');
    }else{
        userBalanceOfTopNav('hide');
    }
    $balanceHandler.on('click', function(){
        if( dsCookie.readCookie('userBalanceIsVisible') ){
            userBalanceOfTopNav('hide');
            dsCookie.eraseCookie('userBalanceIsVisible');
        }else{
            userBalanceOfTopNav('show');
            dsCookie.createCookie('userBalanceIsVisible', true, 7);
        }
        return false;
    });

    // 账户余额
    var balanceUrl ="{!!route('users.user-monetary-info')!!}";
    // $('body').on('click', '[data-refresh-balance]', function() {
    //     var me = this;
    //     if ($(me).hasClass('onhandled')) return false;
    //     $(me).addClass('onhandled');
    //
    //     var st = new Date().getTime(),
    //         delay = 2000,
    //         handler = function(resp){
    //             if (resp.isSuccess != 0) {
    //                 var msg = '';
    //                 if (resp.data.error) msg = '读取中...';
    //                 else msg = formatMoney(resp.data.available);
    //                 $('[data-user-account-balance]').html(msg);
    //             } else {
    //                 alert(resp.msg || '网络繁忙请稍后再试');
    //             }
    //             $(me).removeClass('onhandled');
    //         };
    //
    //     $.get(balanceUrl, function(resp) {
    //         var resp = $.parseJSON(resp);
    //         var st2 = new Date().getTime() - st;
    //         if( st2 > delay ){
    //             handler(resp);
    //         }else{
    //             setTimeout(function(){
    //                 handler(resp);
    //             }, delay - st2);
    //         }
    //     });
    //     return false;
    // }).find('[data-refresh-balance]').eq(0).trigger('click');

    function userBalanceOfTopNav(type){
        var type = type || 'show',
            $spans = $balanceHandler.siblings('span');
        if( type == 'show' ){
            $('.balance-a').show();
            $('.balance-b').hide();
            $balanceHandler.text('隐藏');
        }else{
            $('.balance-a').hide();
            $('.balance-b').show();
            $balanceHandler.text('显示');
        }
    }
    function getSiteMsg(){
        var unreaded = 0,
            html = '';
        $.ajax({
            type: 'GET',
            url: "{!! route('station-letters.get-user-messages') !!}",
            dataType: 'json',
            success: function(resp){
                if( Object.prototype.toString.call( resp ) === '[object Array]' && resp.length ){
                    html += '<ul class="clearfix"> \
                                <li class="tnm-title"> \
                                    <span class="tnm-time">时间</span> \
                                    <span>内容</span> \
                                </li>';
                    $.each(resp, function(i,msg){
                        var cl = '';
                        if( msg.is_readed == '0' ){
                            cl = ' class="un-read"';
                            unreaded++;
                        }
                        html += '<li' +cl+ '> \
                                <span class="tnm-time">' + msg.created_at + '</span> \
                                <a href="' + msg.url + '"><i></i>' + msg.msg_title + '</a> \
                            </li>';
                    });
                    html += '</ul><p class="text-right"><a href="' + '{!! route("station-letters.index") !!}' + '">查看更多>></a></p>';
                    // html = '<p>未读新消息<a href="' + '{!! route("station-letters.index") !!}' + '" class="highlight-color">(' + unreaded + ')</a></p>' + html;
                }else{
                    html += '<p class="text-center">未读新消息<a class="highlight-color">(0)</a></p><div class="top-nav-no-msg">没有新消息了！</div>';
                }
                $('#J-top-msg-box').html(html);
                if( unreaded > 0 ){
                    $('#J-top-msg-num').html('(' + unreaded + ')').show();
                }
            }
        });
    }
    getSiteMsg();
    setInterval(getSiteMsg, 5 * 60 * 1000);
    function getSystemMsg(){
        var unreaded = 0,
            html = '';
        $.ajax({
            type: 'GET',
            url: "{!! route('system-notices.user-notices') !!}",
            dataType: 'json',
            success: function(resp){
                if( Object.prototype.toString.call( resp ) === '[object Array]' && resp.length ){
                    html += '<ul class="clearfix"> \
                                <li class="tnm-title"> \
                                    <span class="tnm-time">时间</span> \
                                    <span>内容</span> \
                                </li>';
                    $.each(resp, function(i,msg){
                        var cl = '';
                        if( msg.is_readed == '0' ){
                            cl = ' class="un-read"';
                            unreaded++;
                        }
                        html += '<li' +cl+ '> \
                                <span class="tnm-time">' + msg.created_at + '</span> \
                                <a href="' + msg.url + '"><i></i>' + msg.title + '</a> \
                            </li>';
                    });
                    html += '</ul><p class="text-right"><a href="' + '{!! route("system-notices.index") !!}' + '">查看更多>></a></p>';
                    // html = '<p>未读新消息<a href="' + '{!! route("station-letters.index") !!}' + '" class="highlight-color">(' + unreaded + ')</a></p>' + html;
                }else{
                    html += '<p class="text-center">未读新消息<a class="highlight-color">(0)</a></p><div class="top-nav-no-msg">没有新消息了！</div>';
                }
                $('#J-top-system-msg-box').html(html);
                if( unreaded > 0 ){
                    $('#J-top-system-msg-num').html('(' + unreaded + ')').show();
                }
            }
        });
    }
    getSystemMsg();
    setInterval(getSystemMsg, 5 * 60 * 1000);
});
</script>
@endif