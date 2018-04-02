@if (!isset($sWayType) || !$sWayType || $sWayType == 'all')
<!--全部玩法-->
    <dl class="dataHead">
        <dd class="co1">场次</dd>
        <dd class="co2 attentionMenu">赛事<!-- <em class="arrow"></em> -->
            <div class="gameSeleList">
                <ul class="clearfix" jcid="6D11">
                    <li leagueid="113" title="关注赛事将置顶显示" jcid="6911"><i></i>荷乙</li>
                    <li leagueid="18" title="关注赛事将置顶显示"><i></i>意大利杯</li>
                    <li leagueid="2" title="关注赛事将置顶显示"><i></i>西甲</li>
                    <li leagueid="30" title="关注赛事将置顶显示"><i></i>葡超</li>
                    <li leagueid="4" title="关注赛事将置顶显示"><i></i>英超</li>
                    <li leagueid="419" title="关注赛事将置顶显示"><i></i>友谊赛</li>
                    <li leagueid="4915" title="关注赛事将置顶显示"><i></i>亚U23</li>
                    <li leagueid="695" title="关注赛事将置顶显示"><i></i>智利甲</li>
                    <li leagueid="7" title="关注赛事将置顶显示"><i></i>英足总杯</li>
                    <li leagueid="70" title="关注赛事将置顶显示"><i></i>法乙</li>
                </ul>
                <i class="fuzhu"></i>
                <div class="btnBox" jcid="6D21">
                    <label><i class="isSave icoFx icoFx_active"></i>记住我关注的赛事</label>
                    <a href="javascript:;" class="makeSure">确定</a>
                </div>
            </div>
        </dd>
        <dd class="co3 timeSwitcher">
            <a href="javascript:void(0);" class="imitateSelect">代购截止</a>
            <em class="arrow"></em>
            <div class="optionList" style="display: none;">
                <a href="javascript:void(0);" action_type="playedTime">开赛时间</a>
                <a href="javascript:void(0);" action_type="saleStopTime">代购截止</a>
            </div>
        </dd>
        <dd class="co4">主队 　 　 客队</dd>
        <dd class="co5">让球</dd>
        <dd class="co6">
            <span>投注区<i class="jtip questionMark" inf="
             示例：<br>
        胜平负：　　　比赛结果 0:1　　　　　　　　<strong>彩果：负</strong><br>
        让球胜平负：　比赛结果 2:1　　让球：-1　　<strong>彩果：让球平</strong><br>
        总进球：　　　比赛结果 2:5　　　　　　　　<strong>彩果：7+</strong><br>
        半全场：　　　上半场　 1:1　　全场：1:3　 <strong>彩果：平负</strong><br>
        比分：　　　　比赛结果 2:3　　　　　　　　<strong>彩果：2:3</strong>
        "></i></span>

            <span class="btnBox"><em>主胜</em><em>平</em><em>主负</em><em class="emMore">更多</em></span>
        </dd>
        <dd class="co7" id="betAn">
            <span>
                <a class="imitateSelect" href="#" jcid="6D51">平均欧赔</a>
                <em class="arrow"></em>
                <div class="optionList" style="display: none;">
                    <a href="#1" jcid="6D61">平均欧赔</a>
                    <a href="#3" jcid="6D63">指数分析</a>
                </div>
            </span>
            <span class="line2">
                <em>主胜</em>
                <em>平</em>
                <em class="lastOne">主负</em>
            </span>
        </dd>
        <dd class="co8">备注</dd>
    </dl>

@elseif($sWayType == 'had')
<!--胜平负-->

    <dl class="dataHead">
        <dd class="co1">场次</dd>
        <dd class="co2 attentionMenu">赛事<!-- <em class="arrow"></em> -->
        <div class="gameSeleList">
            <ul class="clearfix">
                <li leagueid="11" title="关注赛事将置顶显示"><i></i>欧联杯</li>
                <li leagueid="113" title="关注赛事将置顶显示"><i></i>荷乙</li>
                <li leagueid="12" title="关注赛事将置顶显示"><i></i>苏超</li>
                <li leagueid="161" title="关注赛事将置顶显示"><i></i>墨联春</li>
                <li leagueid="17" title="关注赛事将置顶显示"><i></i>德乙</li>
                <li leagueid="2" title="关注赛事将置顶显示"><i></i>西甲</li>
                <li leagueid="21" title="关注赛事将置顶显示"><i></i>英冠</li>
                <li leagueid="22" title="关注赛事将置顶显示"><i></i>英甲</li>
                <li leagueid="3" title="关注赛事将置顶显示"><i></i>德甲</li>
                <li leagueid="30" title="关注赛事将置顶显示"><i></i>葡超</li>
                <li leagueid="336" title="关注赛事将置顶显示"><i></i>解放者杯</li>
                <li leagueid="419" title="关注赛事将置顶显示"><i></i>友谊赛</li>
                <li leagueid="470" title="关注赛事将置顶显示"><i></i>澳A联</li>
                <li leagueid="48" title="关注赛事将置顶显示"><i></i>阿甲</li>
                <li leagueid="5" title="关注赛事将置顶显示"><i></i>法甲</li>
                <li leagueid="548" title="关注赛事将置顶显示"><i></i>圣保罗锦</li>
                <li leagueid="6" title="关注赛事将置顶显示"><i></i>荷甲</li>
                <li leagueid="695" title="关注赛事将置顶显示"><i></i>智利甲</li>
                <li leagueid="70" title="关注赛事将置顶显示"><i></i>法乙</li>
            </ul>
            <i class="fuzhu"></i>
            <div class="btnBox"><label><i class="isSave icoFx icoFx_active"></i>记住我关注的赛事</label><a href="javascript:;" class="makeSure">确定</a></div>
        </div>
        </dd>
        <dd class="co3 timeSwitcher">
            <a href="javascript:void(0);" class="imitateSelect">代购截止</a>
            <em class="arrow"></em>
            <div class="optionList" style="display: none;">
                <a href="javascript:void(0);" action_type="playedTime">开赛时间</a>
                <a href="javascript:void(0);" action_type="saleStopTime">代购截止</a>
            </div>
        </dd>
        <dd class="co4">主队 　 　 客队</dd>
        <dd class="co5">让球</dd>
        <dd class="co6">
            <span>投注区</span>
            <span class="btnBox"><em>主胜</em><em>平</em><em>主负</em></span>
        </dd>
        <dd class="co7" id="betAn">
            <span><a class="imitateSelect" href="#">本站投注比例</a><em class="arrow"></em><div class="optionList"> <a href="#1">本站投注比例</a> <a href="#2">本站投注人气</a> <a href="#3">指数分析</a> </div></span>
            <span class="line2"><em>主胜</em><em>平</em><em class="lastOne">主负</em></span>
        </dd>
        <dd class="co8">备注</dd>
    </dl>


@elseif($sWayType == 'crs')
<!--比分-->

    <dl class="dataHead">
        <dd class="co1">场次</dd>
        <dd class="co2 attentionMenu">赛事<!-- <em class="arrow"></em> -->
        <!-- <div class="gameSeleList">
            <ul class="clearfix">
                <li leagueid="11" title="关注赛事将置顶显示"><i></i>欧联杯</li>
                <li leagueid="113" title="关注赛事将置顶显示"><i></i>荷乙</li>
                <li leagueid="12" title="关注赛事将置顶显示"><i></i>苏超</li>
                <li leagueid="161" title="关注赛事将置顶显示"><i></i>墨联春</li>
                <li leagueid="17" title="关注赛事将置顶显示"><i></i>德乙</li>
                <li leagueid="2" title="关注赛事将置顶显示"><i></i>西甲</li>
                <li leagueid="21" title="关注赛事将置顶显示"><i></i>英冠</li>
                <li leagueid="22" title="关注赛事将置顶显示"><i></i>英甲</li>
                <li leagueid="3" title="关注赛事将置顶显示"><i></i>德甲</li>
                <li leagueid="30" title="关注赛事将置顶显示"><i></i>葡超</li>
                <li leagueid="336" title="关注赛事将置顶显示"><i></i>解放者杯</li>
                <li leagueid="419" title="关注赛事将置顶显示"><i></i>友谊赛</li>
                <li leagueid="470" title="关注赛事将置顶显示"><i></i>澳A联</li>
                <li leagueid="48" title="关注赛事将置顶显示"><i></i>阿甲</li>
                <li leagueid="5" title="关注赛事将置顶显示"><i></i>法甲</li>
                <li leagueid="548" title="关注赛事将置顶显示"><i></i>圣保罗锦</li>
                <li leagueid="6" title="关注赛事将置顶显示"><i></i>荷甲</li>
                <li leagueid="695" title="关注赛事将置顶显示"><i></i>智利甲</li>
                <li leagueid="70" title="关注赛事将置顶显示"><i></i>法乙</li>
            </ul>
          <i class="fuzhu"></i>
          <div class="btnBox"><label><i class="isSave icoFx icoFx_active"></i>记住我关注的赛事</label><a href="javascript:;" class="makeSure">确定</a></div>
        </div> -->
        </dd>
        <dd class="co3 timeSwitcher">
            <a href="javascript:void(0);" class="imitateSelect">代购截止</a>
            <em class="arrow"></em>
            <div class="optionList" style="display: none;">
                <a href="javascript:void(0);" action_type="playedTime">开赛时间</a>
                <a href="javascript:void(0);" action_type="saleStopTime">代购截止</a>
            </div>
        </dd>
        <dd class="co4">主队 　 　 客队</dd>
        <dd class="co6">
            <span>选择比分进行投注 / 参考指数</span>
        </dd>
        <dd class="co8">备注</dd>
    </dl>


@elseif($sWayType == 'ttg')
<!--总进球-->

    <dl class="dataHead">
        <dd class="co1">场次</dd>
        <dd class="co2 attentionMenu">赛事<!-- <em class="arrow"></em> -->
        <!-- <div class="gameSeleList">
            <ul class="clearfix">
                <li leagueid="11" title="关注赛事将置顶显示"><i></i>欧联杯</li>
                <li leagueid="113" title="关注赛事将置顶显示"><i></i>荷乙</li>
                <li leagueid="12" title="关注赛事将置顶显示"><i></i>苏超</li>
                <li leagueid="161" title="关注赛事将置顶显示"><i></i>墨联春</li>
                <li leagueid="17" title="关注赛事将置顶显示"><i></i>德乙</li>
                <li leagueid="2" title="关注赛事将置顶显示"><i></i>西甲</li>
                <li leagueid="21" title="关注赛事将置顶显示"><i></i>英冠</li>
                <li leagueid="22" title="关注赛事将置顶显示"><i></i>英甲</li>
                <li leagueid="3" title="关注赛事将置顶显示"><i></i>德甲</li>
                <li leagueid="30" title="关注赛事将置顶显示"><i></i>葡超</li>
                <li leagueid="336" title="关注赛事将置顶显示"><i></i>解放者杯</li>
                <li leagueid="419" title="关注赛事将置顶显示"><i></i>友谊赛</li>
                <li leagueid="470" title="关注赛事将置顶显示"><i></i>澳A联</li>
                <li leagueid="48" title="关注赛事将置顶显示"><i></i>阿甲</li>
                <li leagueid="5" title="关注赛事将置顶显示"><i></i>法甲</li>
                <li leagueid="548" title="关注赛事将置顶显示"><i></i>圣保罗锦</li>
                <li leagueid="6" title="关注赛事将置顶显示"><i></i>荷甲</li>
                <li leagueid="695" title="关注赛事将置顶显示"><i></i>智利甲</li>
                <li leagueid="70" title="关注赛事将置顶显示"><i></i>法乙</li>
            </ul>
          <i class="fuzhu"></i>
          <div class="btnBox"><label><i class="isSave icoFx icoFx_active"></i>记住我关注的赛事</label><a href="javascript:;" class="makeSure">确定</a></div>
        </div> -->
        </dd>
        <dd class="co3 timeSwitcher">
            <a href="javascript:void(0);" class="imitateSelect">代购截止</a>
            <em class="arrow"></em>
            <div class="optionList" style="display: none;">
                <a href="javascript:void(0);" action_type="playedTime">开赛时间</a>
                <a href="javascript:void(0);" action_type="saleStopTime">代购截止</a>
            </div>
        </dd>
        <dd class="co4">主队 　 　 客队</dd>
        <dd class="co6">
            <span>总进球=主队进球数+客队进球数<i class="questionMark jtip" inf="示例：<br/>完场比分  3:2&nbsp;&nbsp;<strong>总进球彩果：5</strong><br/>完场比分  5:3&nbsp;&nbsp;<strong>总进球彩果：7+</strong>"></i></span>
            <span class="btnBox"><em>0</em><em>1</em><em>2</em><em>3</em><em>4</em><em>5</em><em>6</em><em class="lastOne">7+</em></span>
        </dd>
        <dd class="co8">备注</dd>
    </dl>


@elseif($sWayType == 'hafu')
<!--半全场-->

    <dl class="dataHead">
        <dd class="co1">场次</dd>
        <dd class="co2 attentionMenu">赛事<!-- <em class="arrow"></em> -->
        <!-- <div class="gameSeleList">
            <ul class="clearfix">
                <li leagueid="11" title="关注赛事将置顶显示"><i></i>欧联杯</li>
                <li leagueid="113" title="关注赛事将置顶显示"><i></i>荷乙</li>
                <li leagueid="12" title="关注赛事将置顶显示"><i></i>苏超</li>
                <li leagueid="161" title="关注赛事将置顶显示"><i></i>墨联春</li>
                <li leagueid="17" title="关注赛事将置顶显示"><i></i>德乙</li>
                <li leagueid="2" title="关注赛事将置顶显示"><i></i>西甲</li>
                <li leagueid="21" title="关注赛事将置顶显示"><i></i>英冠</li>
                <li leagueid="22" title="关注赛事将置顶显示"><i></i>英甲</li>
                <li leagueid="3" title="关注赛事将置顶显示"><i></i>德甲</li>
                <li leagueid="30" title="关注赛事将置顶显示"><i></i>葡超</li>
                <li leagueid="336" title="关注赛事将置顶显示"><i></i>解放者杯</li>
                <li leagueid="419" title="关注赛事将置顶显示"><i></i>友谊赛</li>
                <li leagueid="470" title="关注赛事将置顶显示"><i></i>澳A联</li>
                <li leagueid="48" title="关注赛事将置顶显示"><i></i>阿甲</li>
                <li leagueid="5" title="关注赛事将置顶显示"><i></i>法甲</li>
                <li leagueid="548" title="关注赛事将置顶显示"><i></i>圣保罗锦</li>
                <li leagueid="6" title="关注赛事将置顶显示"><i></i>荷甲</li>
                <li leagueid="695" title="关注赛事将置顶显示"><i></i>智利甲</li>
                <li leagueid="70" title="关注赛事将置顶显示"><i></i>法乙</li>
            </ul>
          <i class="fuzhu"></i>
          <div class="btnBox"><label><i class="isSave icoFx icoFx_active"></i>记住我关注的赛事</label><a href="javascript:;" class="makeSure">确定</a></div>
        </div> -->
        </dd>
        <dd class="co3 timeSwitcher">
            <a href="javascript:void(0);" class="imitateSelect">代购截止</a>
            <em class="arrow"></em>
            <div class="optionList" style="display: none;">
                <a href="javascript:void(0);" action_type="playedTime">开赛时间</a>
                <a href="javascript:void(0);" action_type="saleStopTime">代购截止</a>
            </div>
        </dd>
        <dd class="co4">主队 　 　 客队</dd>
        <dd class="co6">
            <span>半全场玩法：上半场比赛结果 +全场比赛结果 <i class="questionMark jtip" inf="示例：<br/>上半场比分 1:1&nbsp;&nbsp;半场彩果：平<br/>全场比分&nbsp;&nbsp;&nbsp;3:1&nbsp;&nbsp;全场彩果：胜<br/><strong>半全场彩果：平胜</strong>"></i></span>
            <span class="btnBox"><em>胜胜</em><em>胜平</em><em>胜负</em><em>平胜</em><em>平平</em><em>平负</em><em>负胜</em><em>负平</em><em class="lastOne">负负</em></span>
        </dd>
        <dd class="co8">备注</dd>
    </dl>

@endif
