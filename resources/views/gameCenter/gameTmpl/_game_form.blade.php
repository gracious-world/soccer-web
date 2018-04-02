<script id="gameForm" type="text/x-dot-template">
    @{{ for() { }}
    <div class="bd game-submit-confirm-cont">
        <p class="game-submit-confirm-title">
            <label class="ui-label">彩种：<#=lotteryName#></label>
        </p>
        <ul class="ui-form">
            <li>
                <div class="textarea"
                    <#=lotteryInfo#>
                </div
            </li>
            <li class="game-submit-confirm-tip">
                <label class="ui-label">付款总金额：<span class="color-red"><#=lotteryamount#></span>元</label>
            </li>
            <li class="game-submit-confirm-tip">
                <label class="ui-label">所选奖金组：<span class="color-red"><#=lotterOptionalPrizes#></span></labe
            </li>
        </ul>
    </div>

    @{{ } }}

</script>