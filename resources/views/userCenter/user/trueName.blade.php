@extends('l.home')

@section('title')
   昵称修改
@parent
@stop

@section('scripts')
@parent
    {!! script('gagame.Timer') !!}
    {!! script('gagame.Message') !!}
    {!! script('gagame.Mask') !!}
@stop

@section('main')
<div class="nav-bg nav-bg-tab">
    <div class="title-normal">个人中心</div>

    <ul class="tab-title clearfix">

        <li class="current"><a href="{!! route('users.true-name') !!}" ><span>真实姓名</span></a></li>
        <li><a href="{!! route('users.personal') !!}" ><span>昵称管理</span></a></li>
        <li><a href="{!! route('users.change-password') !!}"><span>登录密码管理</span></a></li>
        @if ( $oUser->fund_password)
        <li><a href="{!! route('users.change-fund-password') !!}"><span>资金密码管理</span></a></li>
        @else
        <li><a href="{!! route('users.safe-reset-fund-password') !!}"><span>资金密码设置</span></a></li>
        @endif
    </ul>
</div>


<div class="content">
    <form action="{!! route('users.true-name') !!}" method="post" id="J-form-login">
        <input type="hidden" name="_token" value="{!! csrf_token() !!}" />
        <input type="hidden" name="_method" value="PUT" />
        <table width="100%" class="table-field">
            <tr>
                <td align="right" style="width:350px;">用户名：</td>
                <td>
                     {!! Session::get('username') !!}
                </td>
            </tr>
            @if(!$oUser->name)
            <tr>
                <td align="right">真实姓名：</td>
                <td>
                    <input id="J-input-name" type="text" class="input w-2" name="name" value="{!! $oUser->name !!}">
                    &nbsp;&nbsp;
                    <span class="tip">请填写真实姓名</span>
                </td>
            </tr>
            <tr>
                <td align="right"></td>
                <td>
                    <input id="J-button-submit" type="submit" value="保存" class="btn" />
                </td>
            </tr>
            @else
             <tr>
                <td align="right" style="width:350px;">真实姓名：</td>
                <td>
                     {!! $oUser->name !!}
                </td>
            </tr>
            @endif
        </table>
    </form>

</div>
@stop

@section('end')
@parent
<script>
(function($){

    $('#J-button-submit').click(function(){
        // var v = $.trim($('#J-input-name').val());
        // if(v.length < 2 || v.length > 16){
        //     alert('昵称必须由2至6个字符组成，请重新输入');
        //     $('#J-input-name').focus();
        //     return false;
        // }
        // return true;


            // $('#myModal').modal();
            // var popWindow = new gagame.Message();
            // var data = {
            //     title          : '提示',
            //     content        : $('#popWindow').find('.pop-bd > .pop-content').html(),
            //     closeIsShow    : true,
            //     closeButtonText: '关闭',
            //     closeFun       : function() {
            //         this.hide();
            //     }
            // };
            // popWindow.show(data);


    });

})(jQuery);
</script>
@stop