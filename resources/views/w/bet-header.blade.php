<div class="game-header pageWrap">
    <div class="headerBox clearfix">
        <span class="cz_logo cz_jczq"></span>
        <h1 jcid="6111"><a href="##">竞彩足球</a></h1>
        <span class="text"><i>提示：投注为全场90分钟（含伤停补时）的比分结果，不含加时赛及点球结果</i></span>
    </div>
    <ul class="gameMenu clearfix">
        <li class="{!! $sPageName == 'games' ? 'active' : '' !!}" >
            <a href="{!! route('games.index') !!}" hidefocus="true">混合投注</a>
        </li>
        <li class="{!! ($sPageName == 'singleGames') ? 'active' : '' !!}">
            <a href="{!! route('games.single') !!}" hidefocus="true">单关</a>
            <span class="cz_icon cz_rt">new<i class="arrowsIcon"></i></span>
        </li>
        <li class="{!! ($sPageName == 'focusGames') ? 'active' : '' !!}">
            <a href="{!! route('games.focus') !!}" hidefocus="true" target="_self">焦点赛事</a>
            <span class="cz_icon cz_rt">hot<i class="arrowsIcon"></i></span>
        </li>
        <!-- <li class="{!! ($sPageName == 'rankGames') ? 'active' : '' !!}">
            <a href="{!! route('games.rank') !!}" hidefocus="true" target="_self">2016欧洲杯</a>
            <span class="cz_icon cz_rt">hot<i class="arrowsIcon"></i></span>
        </li> -->
        @if(Session::get('user_id'))
                <li class="right {!! $sPageName == 'programs' ? 'active' : '' !!}">
                    <a href="{!! route('programs.index', ['sort_down' => 'bought_at']) !!}" hidefocus="true"
                       target="_self">投注记录</a>
                </li>
                    <li class="right {!! $sPageName == 'transactions' ? 'active' : '' !!}">
                        <a href="{!! route('user-transactions.index') !!}" hidefocus="true" target="_self">账户明细</a>
                    </li>
        @endif
    </ul>
    @if(isset($bSimple) && !$bSimple)
    <div class="headerUser">
        <div class="userName">你好：<span>{!! isset($sUsername) ? $sUsername : '游客' !!}</span></div>
        @if($iUserId)
        <div class="user-account-balance">
            <span class="user-cash balance-a" style="display: inline;">余额：<span data-user-account-balance="" class="num" id="userBalance">{!! isset($fAvailable) ? $fAvailable : 0 !!}</span> 元</span>
        </div>
        @endif
    </div>
    @endif
    @if($sPageName != 'focusGames')
    <div class="headerBg"></div>
    @endif
</div>

@section('end')
@parent
@if(isset($bSimple) && !$bSimple)
<script type="text/javascript">
{{--var pollBalance = function() {--}}
    {{--$.ajax({--}}
        {{--url: '{!! route('users.account') !!}',--}}
        {{--type: 'GET',--}}
        {{--dataType: 'json',--}}
        {{--data: {},--}}
    {{--})--}}
    {{--.done(function(d) {--}}
        {{--var amount =  d.data.amount && d.data.amount.toString()--}}
            {{--.replace(/(\d)(?=(\d{3})+\.)/g, function($0, $1) {--}}
                {{--return $1 + ",";--}}
            {{--});--}}
        {{--$('#userBalance').text(amount);--}}
        {{--// console.log("success");--}}
    {{--})--}}
    {{--.fail(function(d) {--}}
        {{--// console.log("error");--}}
    {{--})--}}
    {{--.always(function(d) {--}}
        {{--// console.log("complete");--}}
    {{--});--}}
{{--};--}}
{{--var bLogin = "{!! isset($sUsername) ? $sUsername : '' !!}";--}}
{{--var balanceTimer = null;--}}
{{--if (bLogin) {--}}
    {{--clearInterval(balanceTimer);--}}
    {{--balanceTimer = setInterval('pollBalance()', 3000);--}}
{{--} else {--}}
    {{--clearInterval(balanceTimer);--}}
{{--}--}}
</script>
@endif

@stop