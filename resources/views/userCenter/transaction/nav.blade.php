<div class="nav-bg nav-bg-tab">
            <div class="title-normal">
                资金明细
            </div>
            <ul class="tab-title">

                <li class="{!!$reportName=='transaction' ? 'current' : ''!!}">
                    <a href="{!! route('user-transactions.index') !!}"><span>账变记录</span></a>
                </li>
                <li class="{!!$reportName=='bonus' ? 'current' : ''!!}">
                    <a href="{!! route('user-transactions.mybonus') !!}"><span>{!! Session::get('is_player') ? '我的加奖' : '我的返点' !!}</span></a>
                </li>
                <!--TODO check child bet compare of this-->
                <li class="{!!$reportName=='jc-transaction' ? 'current' : ''!!}">
                    <a href="{!! route('user-transactions.jc-index') !!}"><span>我的投注</span></a>
                </li>
                <li class="{!!$reportName=='deposit' ? 'current' : ''!!}">
                    <a href="{!! route('user-transactions.mydeposit', Session::get('user_id')) !!}"><span>我的充值</span></a>
                </li>
                <li class="{!!$reportName=='depositApply' ? 'current' : ''!!}">
                    <a href="{!! route('user-recharges.index') !!}"><span>充值申请</span></a>
                </li>
                <li class="{!!$reportName=='withdraw' ? 'current' : ''!!}">
                    <a href="{!! route('user-transactions.mywithdraw', Session::get('user_id')) !!}"><span>我的提现</span></a>
                </li>
                <li class="{!!$reportName=='withdrawApply' ? 'current' : ''!!}">
                    <a href="{!! route('user-withdrawal.index') !!}"><span>提现申请</span></a>
                </li>
            </ul>
        </div>

