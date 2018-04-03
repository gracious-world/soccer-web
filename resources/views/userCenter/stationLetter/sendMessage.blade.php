@extends('l.base')

@section('title')
发送消息
@parent
@stop

@section('scripts')
@parent
{!! script('gagame.Message') !!}
{!! script('gagame.Tip') !!}
{!! script('gagame.Select') !!}
{!! script('jquery.easing.1.3') !!}
{!! script('jquery.mousewheel') !!}
@stop

@section('container')

<div class="content">


    <form action="{!! route('station-letters.send-message') !!}" method="post" id="J-form">
        <input type="hidden" name="_token" value="{!! csrf_token() !!}" />
        <table width="100%" class="table-field ">
            <tr>
                <td align="right">收信人类型：</td>
                <td class="text-left">
                    <select id="J-select-type" style="display:none;" name="user_type">
                        @if (! Session::get('is_player'))
                        <option value="2">所有下级</option>
                        <option value="3">单一下级</option>
                        @endif
                        @if (! Session::get('is_top_agent'))
                        <option value="1">上级</option>
                        @endif
                    </select>
                </td>
            </tr>
            <tr>
                <td align="right">收信人名称：</td>
                <td class="text-left">
                    <select id="J-select-username" style="display:none;" name="receiver">
                        @if (! Session::get('is_player'))
                        <option value="">--</option>
                        @else
                        <option value="{!! $aParent['id'] !!}">{!! $aParent['username'] !!}</option>
                        @endif
                    </select>
                </td>
            </tr>
            <tr>
                <td align="right">标题：</td>
                <td class="text-left">
                    <input type="text" class="input w-5" id="J-input-title" name="title" value="">
                </td>
            </tr>
            <tr>
                <td align="right">内容：</td>
                <td class="text-left">
                    <textarea class="w-5 input" style="height:auto" rows="5" id="J-textarea" name="content"></textarea>
                </td>
            </tr>
            <tr>
                <td align="right"> </td>
                <td class="text-left">
                    <a href="javascript:;" id="J-submit"  class=" btn " >发 送</a>
                    <a href="javascript:;" id="J-cancel"  class=" btn " >取 消</a>
                </td>
            </tr>
        </table>
    </form>
</div>
@stop


@section('end')
@parent
<script type="text/javascript">

$(function(){
    var rerenderReceiverSelectBox = function (data) {
        if (!data.length) objUserSelector.reBuildSelect([{id: '', username:'--', checked:true}]);
        objUserSelector.reBuildSelect(data);
    };

    var parentInfo   = {!! $sJsonParent !!};
    var childrenInfo = {!! $sJsonChildren !!};
    // debugger;
    var objTypeSelector = new gagame.Select({realDom: '#J-select-type', cls: 'w-5'});
    var objUserSelector = new gagame.Select({realDom: '#J-select-username', cls: 'w-5', valueKey: 'id', textKey: 'username'});
    objTypeSelector.addEvent('change', function() {
        var index = objTypeSelector.getValue();
        var data = [parentInfo, [], childrenInfo][+index - 1];
        rerenderReceiverSelectBox(data);
    });

    $('#J-submit').click(function () {
        $('#J-form').submit();
    });
    // $('#J-cancel').click(function () {
    //     $('#J-form')
    // })；

});


</script>
@stop