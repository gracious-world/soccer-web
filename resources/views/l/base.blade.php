<!DOCTYPE html>
<html lang="zh-CN">
    <head>
        <meta charset="UTF-8">
        <meta name="description" content="@yield('description')">
        <meta name="keywords" content="@yield('keywords')" />
        <meta name="csrf-token" content="{!! csrf_token() !!}">
        @section ('metas')
        @show
        <title>
            {!! Config::get('title.prev') !!}
            @section('title')
            --竞彩网
            @show
        </title>
        @section ('styles')
            {!! style('gameUi') !!}
            {!! style('gameBase') !!}
            {!! style('global') !!}
        @show
        @section('scripts')
            {!! script('jquery') !!}
            {!! script('moment') !!}
            {!! script('gagame.base') !!}
            {!! script('base') !!}
        @show
    </head>
    <body>
        @include('w.top-header')
        @include('w.nav')
        @yield('container')
    </body>

    @section('end')
    <script type="text/javascript">
        $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                }
        });
    </script>
    @include('w.footer')
    @show
</html>
