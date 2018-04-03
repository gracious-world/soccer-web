@extends('l.home')

@section('title')
    银行卡--充值
@parent
@stop

@section('scripts')
@parent
    {!! script('ZeroClipboard')!!}
    {!! script('gagame.Mask')!!}
    {!! script('gagame.Message')!!}
@stop

@section ('main')
<div class="nav-bg">
    <div class="title-normal">
        充值
    </div>
</div>

<div class="content recharge-confirm">
    <div class="prompt">
        此次充值申请的有效时间为30分钟，为保障充值成功，请在30分钟之内完成充值。返回首页：倒计时<span class="c-red" id="J-time-dom">00:00</span>
        <input type="hidden" value="1800" id="J-time-second" />
    </div>


    <table width="100%" class="table-field" id="J-table">
        <tr>
            <td width="150" align="right" valign="top">收款银行：</td>
            <td>
                <label class="img-bank" for="J-bank-name-{!! $oBank->identifier !!}" style="cursor:default;">
                    <input name="bank[]" id="J-bank-name-{!! $oBank->identifier !!}" type="radio" style="visibility:hidden;" />
                    <span class="ico-bank {!! $oBank->identifier !!}">{!!$oBank->name!!}</span>
                </label>
                <br />
                <span class="tip f14">
                    您目前选择的是 <span class="c-red">{!! $oBank->name !!}</span>
                    充值服务
                </span>
            </td>
        </tr>
        <tr>
          <td align="right" valign="top">收款账户名：</td>
          <td>
            <span class="field-value-width data-copy">{!! $oUserDeposit->accept_acc_name !!}</span>
             <input type="button" class="btn btn-small" value="点击复制" id="J-button-name" />
          </td>
      </tr>
        <tr>
          <td align="right" valign="top">收款账号：</td>
          <td>
                <span class="field-value-width data-copy">
                    @if ($oUserDeposit->accept_email)
                    {!!$oUserDeposit->accept_email!!}
                    @else
                    {!!$oUserDeposit->accept_card_num!!}
                    @endif
                </span>
                <input type="button" class="btn btn-small" value="点击复制" id="J-button-card" />
          </td>
      </tr>
        <!-- tr>
          <td align="right" valign="top">订单金额：</td>
          <td>
            <span class="field-value-width data-copy">{!!$oUserDeposit->amount!!}</span>
            <input type="button" class="btn btn-small" value="点击复制" id="J-button-money" />
          </td>
      </tr> -->
        <tr>
          <td align="right" valign="top">附言(充值订单号)：</td>
          <td>
            <span class="field-value-width">
                <span class="c-red data-copy">{!!$oUserDeposit->postscript!!}</span>
            </span>
            <input type="button" class="btn btn-small" value="点击复制" id="J-button-msg" />
            <span class="ui-text-prompt">(附言区分大小写，请正确复制)</span>
          </td>
      </tr>
        <tr>
          <td align="right" valign="top">充值说明：</td>
          <td>
            <div class="prompt-text">
                @if($oPlatform->notice)
                    {!! $oPlatform->notice !!}
                @else
                    {!! $oBank->deposit_notice !!}
                @endif

            </div>
          </td>
      </tr>
        <?php
            $hoplink = !empty($oPlatform->load_url)?$oPlatform->load_url : $oBank->url;
            switch($oPlatform->identifier){
                case 'ZFB':
                    $hoplink .= '?bankFullName='.urlencode(iconv('UTF-8','GBK',$oBank->name)).'&optCardNo='.$oUserDeposit->accept_card_num.'&optCardName='.urlencode(iconv('UTF-8','GBK',$oUserDeposit->accept_acc_name)).'&title='.$oUserDeposit->postscript;
                    break;
            }
        ?>
        <tr>
          <td align="right" valign="top"></td>
          <td>
              农业银行：<a class="link-url" href="{!!$hoplink!!}" target="_blank">{!!$hoplink!!}</a><br />
              工商银行：<a class="link-url" href="https://mybank.icbc.com.cn/icbc/perbank/index.jsp" target="_blank">https://mybank.icbc.com.cn/icbc/perbank/index.jsp</a><br />
              建设银行：<a class="link-url" href="https://ibsbjstar.ccb.com.cn/CCBIS/V6/STY1/CN/login.jsp" target="_blank">https://ibsbjstar.ccb.com.cn/CCBIS/V6/STY1/CN/login.jsp</a><br />
              交通银行：<a class="link-url" href="https://pbank.95559.com.cn/personbank/logon.jsp" target="_blank">https://pbank.95559.com.cn/personbank/logon.jsp</a><br />
              招商银行：<a class="link-url" href="https://pbsz.ebank.cmbchina.com/CmbBank_GenShell/UI/GenShellPC/Login/Login.aspx" target="_blank">https://pbsz.ebank.cmbchina.com/CmbBank_GenShell/UI/GenShellPC/Login/Login.aspx</a><br />
              民生银行：<a class="link-url" href="https://per.cmbc.com.cn/pweb/static/login.html" target="_blank">https://per.cmbc.com.cn/pweb/static/login.html</a><br />
              中信银行：<a class="link-url" href="https://e.bank.ecitic.com/perbank5/" target="_blank">https://e.bank.ecitic.com/perbank5/</a><br />
              浦发银行：<a class="link-url" href="https://ebank.spdb.com.cn/per/gb/otplogin.jsp" target="_blank">https://ebank.spdb.com.cn/per/gb/otplogin.jsp</a><br />
              中国银行：<a class="link-url" href="http://www.boc.cn/" target="_blank">http://www.boc.cn/</a><br />
       </td>
      </tr>
        <tr>
          <td align="right" valign="top">&nbsp;</td>
          <td>
              <a href="{!!$hoplink!!}" class="btn" target="_blank">点击充值</a>

          </td>
      </tr>
    </table>
</div>
@stop

@section('end')
@parent
<script>
(function($){
  ZeroClipboard.setMoviePath('/assets/js-min/ZeroClipboard.swf');

  var clip_name = new ZeroClipboard.Client(),
    clip_card = new ZeroClipboard.Client(),
    //clip_money = new ZeroClipboard.Client(),
    clip_msg = new ZeroClipboard.Client(),
    table = $('#J-table'),
    fn = function(client){
      var el = $(client.domElement),value = $.trim(el.parent().find('.data-copy').text());
      client.setText(value);
      alert('复制成功:\n\n' + value);
    };

  clip_name.setCSSEffects( true );
  clip_card.setCSSEffects( true );
  //clip_money.setCSSEffects( true );
  clip_msg.setCSSEffects( true );

  clip_name.addEventListener( "mouseUp", fn);
  clip_card.addEventListener( "mouseUp", fn);
  //clip_money.addEventListener( "mouseUp", fn);
  clip_msg.addEventListener( "mouseUp", fn);

  clip_name.glue('J-button-name');
  clip_card.glue('J-button-card');
  //clip_money.glue('J-button-money');
  clip_msg.glue('J-button-msg');


  var timeDom = $('#J-time-dom'),
    timeNum = Number($('#J-time-second').val()),
    timer = setInterval(function(){
      var m = Math.floor(timeNum/60),
        s = timeNum%60;
      m = m < 10 ? '0' + m : m;
      s = s < 10 ? '0' + s : s;
      timeDom.text(m + ':' + s);
      timeNum--;
      if(timeNum < 0){
        clearInterval(timer);
        showTimeout();
      }
    }, 1000);



  var showTimeout = function(){
    location.href = '/';
    /**
    var win = gagame.Message.getInstance();
    win.show({
      content:'<div class="pop-title"><i class="ico-waring"></i><h4 class="pop-text">该订单已失效，请重新发起</h4></div>',
      confirmIsShow:true,
      confirmFun:function(){
        this.hide();
      },
      closeIsShow:true,
      closeFun:function(){
        this.hide();
      },
      mask:true
    });
    **/
  };


})(jQuery);
</script>
@stop

