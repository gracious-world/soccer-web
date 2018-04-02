@extends('l.home')

@section('title')
            银行卡管理
@parent
@stop



@section ('main')
    @include("userCenter.user.userCenterHeader")
<!-- <div class="nav-bg">
    <div class="title-normal">
        银行卡管理
    </div>
</div> -->

<div class="content">

    <div class="no-bank-card">
				<p class="alert-message"><i class="alert-icon"></i><span>添加一张银行卡吧</span></p>
				<a href="{!! route('bank-cards.bind-card', 1) !!}" data-add-bankcard>+  添加银行卡</a>
			</div>
</div>

@stop

@section('end')
    @parent
    <script>

        (function(){
        // 添加银行卡
        // 变量必须保证为全局变量，以便iframe内调用
        var addCardMask = new gagame.Mask(),
            addCardMiniwindow = new gagame.MiniWindow({ cls: 'w-13 add-card-miniwindow'}),
            addCardURL = "{!! route('bank-cards.bind-card', 1) !!}" ;

            var hideMask = function(){
                addCardMiniwindow.hide();
                addCardMask.hide();
            };

            addCardMiniwindow.setContent('<iframe src="'+addCardURL+'" width="100%" style="height:330px;" frameborder="0" allowtransparency="true" scrolling="no"></iframe>',1);
            addCardMiniwindow.setTitle('添加银行卡');

            addCardMiniwindow.doNormalClose = hideMask;
            addCardMiniwindow.doConfirm     = hideMask;
            addCardMiniwindow.doClose       = hideMask;
            addCardMiniwindow.doCancel      = hideMask;

            /*$('[data-add-bankcard]').on('click', function(){
                addCardMiniwindow.show();
                addCardMask.show();
            });*/

        })();

    </script>

@stop