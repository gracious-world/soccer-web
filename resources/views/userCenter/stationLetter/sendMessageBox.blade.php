<div class="" id="sendMessageBox" style="display: none;">


    <form action="{!! route('station-letters.send-message') !!}" method="post" name="sendMessageForm">
        <input type="hidden" name="_token" value="{!! csrf_token() !!}" />
        <table width="100%" class="table-field ">
            <tr>
                <td align="right">收信人类型：</td>
                <td class="text-left">
                    <select style="display:none;" name="user_type">
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
                    <select style="display:none;" name="receiver">
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
                    <input type="text" class="input w-5" name="title" value="">
                </td>
            </tr>
            <tr>
                <td align="right">内容：</td>
                <td class="text-left">
                    <textarea class="w-5 input" style="height:auto" rows="5" name="content"></textarea>
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