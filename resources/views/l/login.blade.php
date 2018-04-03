<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="_token" content="{!! csrf_token() !!}"/>
        <title>
            @section('title')
            安捷竞彩平台
            @show
        </title>

        @section ('styles')
          {!! style('gameUi') !!}
          {!! style('gameBase') !!}
          {!! style('global') !!}
        @show

        @section('scripts')
        {!! script('jquery-1.9.1') !!}
        {!! script('gagame.base') !!}
        {!! script('gagame.Mask') !!}
        {!! script('gagame.Message') !!}
        {!! script('gagame.Tip') !!}
        @show


    </head>

    <body>
    @include('w.nav')
    @yield('container')

    </body>
    @include('w.notification')
    @section('end')

        @include('w.footer')
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
    @show



</html>