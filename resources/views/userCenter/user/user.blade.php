@extends('l.home')
@section('title')
个人资料
@parent
@stop

@section ('styles')
@parent
    {!! style('reg') !!}
@stop

@section('scripts')
@parent
{!! script('gagame.Timer') !!}
{!! script('gagame.Message') !!}
{!! script('gagame.Mask') !!}
{!! script('gagame.Tab') !!}
@stop
@section('container')
<div class="wrap-inner main">
    @include("userCenter.user.userCenterHeader")
    <div class="box-register box-register-border">
         <div class="clearfix oneLine">
             <div class="f-4 label">用户名：</div>
             <div class="f-3">{!! $oUser->username !!}</div>
         </div>
         <div class="clearfix oneLine">
             <div class="f-4 label">账户余额：</div>
             <div class="f-3"><span class="c-red">{!! is_numeric($fWithdrawable) ? number_format($fWithdrawable, 2) : '读取中...' !!}</span>元</div>
         </div>
         @if(isset($aPercentSets) && $aPercentSets)
         <div class="clearfix oneLine">
             <div class="f-4 label">返点：</div>
             <div class="f-3">{!! '单关: ' . (isset($aPercentSets['single']) ? $aPercentSets['single'] : 0) * 100 . '%, 混关: ' . (isset($aPercentSets['multi']) ? $aPercentSets['multi'] : 0) * 100 . '%' !!}</div>
         </div>
         @endif
         <div class="clearfix oneLine">
             <div class="f-4 label">登录密码：</div>
             <div class="f-3">********</div>
             <div class="f-3"><a href="{!!route("users.change-password")!!}" class="btn" style="color:#444">修改登录密码</a></div>
         </div>
         <div class="clearfix oneLine">
             <div class="f-4 label">资金密码：</div>
             <div class="f-3">********</div>
             <div class="f-3"><a href="{!!route("users.change-fund-password")!!}" class="btn" style="color:#444">修改资金密码</a></div>
         </div>
         <div class="clearfix oneLine">
             <div class="f-4 label">真实姓名：</div>
             <div class="f-3">{!! $oUser->name ? $oUser->name : '未填写' !!}</div>
         </div>
         <div class="clearfix oneLine">
             <div class="f-4 label">身份证号码：</div>
             <div class="f-3">{!! $oUser->shenfenzheng ? $oUser->shenfenzheng : '未填写' !!}</div>
         </div>
         <div class="clearfix oneLine">
             <div class="f-4 label">手机号码：</div>
             @if($oUser->mobile)
                 <div class="f-3">{!! $oUser->mobile !!}</div>
             @else
                 <div class="f-3">{!! $oUser->mobile ? $oUser->mobile : '未填写' !!}</div>
                 <div class="f-3"><a href="{!!route("users.bind-cellphone")!!}" class="btn" style="color:#444">未验证</a></div>
             @endif
         </div>
         <div class="clearfix oneLine">
             <div class="f-4 label">邮箱：</div>
             @if($oUser->email)
                 <div class="f-3">{!! $oUser->email !!}</div>
             @else
                 <div class="f-3">{!! $oUser->email ? $oUser->email : '未填写' !!}</div>
                 <div class="f-3"><a  href="{!!route("users.bind-email")!!}" class="btn " style="color:#444">未验证</a></div>
             @endif
         </div>
          <div class="clearfix oneLine">
             <div class="f-4 label">银行名称：</div>
             <div class="f-3">{!! $oUserBankCard ? $oUserBankCard->bank : '未绑定' !!}</div>
         </div>
         <div class="clearfix oneLine">
             <div class="f-4 label">银行卡号码：</div>
             @if(!$oUserBankCard)
             <div class="f-3"><a href="#">未绑定</a></div>
              <div class="f-3"><a href="{!!route("bank-cards.bind-card",0)!!}" class="btn" style="color:#444">立即绑定</a></div>
             @else
             <div class="f-3">{!! $oUserBankCard->account_hidden !!}</div>
             @endif
         </div>
         <div class="clearfix oneLine">
             <form id="signupForm" action="{!!route("users.bind-qq")!!}" method="post">
                <input type="hidden" name="_token" value="{!! csrf_token() !!}" />
                 <div class="f-4 label">QQ：</div>
                 <div class="f-3"><input type="text" name="qq" class="input w-3" value="{!! $oUser->qq !!}"></div>
                 <div class="f-3"><input type="submit" class="btn w-2" value="保 存"></div>
             </form>
         </div>
         <div class="clearfix">
             <form id="signupForm" action="{!!route("users.bind-wx")!!}" method="post">
             <input type="hidden" name="_token" value="{!! csrf_token() !!}" />
                 <div class="f-4 label">微信号：</div>
                 <div class="f-3"><input type="text" name="" class="input w-3" value="{!! $oUser->weixin !!}"></div>
                  <div class="f-3"><input type="submit" class="btn w-2" value="保 存"></div>
             </form>
         </div>



    </div>
</div>
@include('w.notification')
@stop


@section('end')
 @parent
<script type="text/javascript">


</script>
@stop

