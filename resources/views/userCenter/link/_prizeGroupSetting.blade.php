
<div class="item-detail item-detail-hover user-bonus-choose">
    <div class="item-title">
        <i class="item-icon-4"></i>数字彩奖金组
    </div>

    <ul class="tab-panels">

    <li class="tab-panel-li">
        <input type="hidden" name="series_id" id="J-input-custom-type" value="{!! Input::old('series_id') !!}" />
        <input type="hidden" name="lottery_id" id="J-input-custom-id" value="{!! Input::old('lottery_id') !!}" />

        <div class="bonusgroup-game-type clearfix J-bonusgroup-player">
            <div class="bonusgroup-game-type clearfix J-bonusgroup-player">
                <div class="bonusgroup-list bonusgroup-list-line">
                    <h3>玩家数字彩奖金组</h3>
                    <ul>
                        <li class="slider-range slider-range-global" onselectstart="return false;" data-slider-step="1">
                            <div class="slider-range-scale">
                                <span class="slider-title">统一设置</span>
                                <a href="" data-bonus-scan class="c-important">查 看</a>
                                <span class="small-number" data-slider-min>{!!$iPlayerMinPrizeGroup!!}</span>
                                <span class="percent-number" data-slider-percent>0%</span>
                                <span class="big-number" data-slider-max>{!!$iCurrentPrize!!}</span>
                            </div>
                            <div class="slider-current-value" data-slider-value>{!!$iPlayerMinPrizeGroup!!}</div>
                            <div class="slider-action">
                                <div class="slider-range-sub" data-slider-sub>-</div>
                                <div class="slider-range-add" data-slider-add>+</div>
                                <div class="slider-range-wrapper" data-slider-cont>
                                    <div class="slider-range-inner" data-slider-inner></div>
                                    <div class="slider-range-btn" data-slider-handle></div>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        @if (Session::get('is_agent'))
        <div class="bonusgroup-game-type J-bonusgroup-agent">
            <div class="bonusgroup-list  bonusgroup-list-line">
                <h3>代理数字彩奖金组</h3>
                <ul>
                    <li class="slider-range slider-range-global" onselectstart="return false;" data-slider-step="1">
                        <div class="slider-range-scale">
                            <span class="slider-title">统一设置</span>
                            <a href="" data-bonus-scan class="c-important">查 看</a>
                            <span class="small-number" data-slider-min>{!!$iAgentMinPrizeGroup!!}</span>
                            <span class="percent-number" data-slider-percent>0%</span>
                            <span class="big-number" data-slider-max>{!!$iCurrentPrize!!}</span>
                        </div>
                        <div class="slider-current-value" data-slider-value>{!!$iAgentMinPrizeGroup!!}</div>
                        <div class="slider-action">
                            <div class="slider-range-sub" data-slider-sub>-</div>
                            <div class="slider-range-add" data-slider-add>+</div>
                            <div class="slider-range-wrapper" data-slider-cont>
                                <div class="slider-range-inner" data-slider-inner></div>
                                <div class="slider-range-btn" data-slider-handle></div>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
        @endif
        <input type="hidden" id="J-input-bonusgroup-gameid" value="" />
        <input type="hidden" id="J-input-series-json" name="series_prize_group_json" />
    </li>
</ul>

</div>





