@extends('l.base')

@section ('title')
欢迎来到
@parent
@stop

@section ('styles')
@parent
@stop

@section ('container')

@include('w.top')
@include('w.nav')

  <div class="user-center">
        <div class="u-l">
        @include('w.userMenu')

        </div>
        <div class="u-c">
          @section ('main')
                @show
        </div>
        <div class="clearfix"></div>
    </div>

@stop


