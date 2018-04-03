@extends('l.base', array('active' => 'signin'))

@section('title') 安全登录 @parent  @stop


@section ('container')
    <!--login box-->
    <div class="">
        <div>
            {!! Form::open(array('role' => 'form', 'target' => '_self', 'name' => 'signinForm'))  !!}
                <input type="text" name="username" />
                <input type="password" name="identity" />
                <input type="hidden" name="sign" />
                <input type="hidden" name="skin" />
            <input type="submit" value="submit">
            {!! Form::close() !!}
        </div>
    </div>
@stop

@section('scripts')
@parent
    {!! script('md5') !!}
@stop

@section('end')

<script type="text/javascript">

$(function(){
    var params   = window.location.hash.substring(1).split(';');
    var username = params[0];
    var identity = params[1];
    var sign     = params[2];
    var skin     = params[3];

    {{--if (!username || !identity) {--}}
        {{--// alert('登录竞彩平台失败');--}}
        {{--window.location.href = "{!! route('games.index') !!}" + '?skin='+skin;--}}
    {{--}--}}

    $('input[name=username]').val(username);
    $('input[name=identity]').val(identity);
    $('input[name=sign]').val(sign);
    $('input[name=skin]').val(skin);
    // $('form[name=signinForm]').submit();
});
</script>
@parent
@stop





