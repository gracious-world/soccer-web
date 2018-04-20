@extends('l.base')

@section ('styles')
@parent
    {!! style('ucenter') !!}
@stop

@section('scripts')
@parent
    {!! script('jquery.easing.1.3') !!}
    {!! script('jquery.mousewheel') !!}
    {!! script('gagame.Select') !!}
    {!! script('gagame.Message') !!}
    {!! script('gagame.Tip') !!}
@stop

@section ('container')
        <div class="g_33 main clearfix">
            <div class="main-content">
                @section ('main')
                @show
            </div>
        </div>
@stop

@section('end')
@parent
<script type="text/javascript">
    (function(){
        if ($('#popWindow').length) {
            // $('#myModal').modal();
            var popWindow = new gagame.Message();
            var data = {
                title          : '提示',
                content        : $('#popWindow').find('.pop-bd > .pop-content').html(),
                closeIsShow    : true,
                closeButtonText: '关闭',
                closeFun       : function() {
                    this.hide();
                }
            };
            popWindow.show(data);
        }
    })();
</script>
@stop
