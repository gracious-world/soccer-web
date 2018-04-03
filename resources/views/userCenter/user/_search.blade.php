<div class="area-search">
    <form action="{!! route('users.index') !!}" method="get" id="J-form" autocomplete="off">
        <input type="hidden" name="_token" value="{!! csrf_token() !!}" />
        <p class="row">
            @if(Session::get('is_agent') && Session::get('user_level') < 2)
            用户组：
            <select id="J-select-user-groups" style="display:none;" name="is_agent">
                <option value="">全部用户</option>
                <option value="0" {!! Input::get('is_agent') === '0' ? 'selected' : '' !!}>玩家</option>
                <option value="1" {!! Input::get('is_agent') == 1 ? 'selected' : '' !!}>代理</option>
            </select>
            &nbsp;
            @endif
            用户名：<input class="input w-2" type="text" value="{!! Input::get('username') !!}" name="username" />
            <input type="submit" value="搜 索" class="btn btn-important" id="J-submit">
        </p>
    </form>
</div>