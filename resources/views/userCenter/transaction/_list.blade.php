<table width="100%" class="table" id="J-table">
    <thead>
        <tr>
            <th>交易编号</th>
            <th>交易时间</th>
            @if($reportName == 'jc-transaction')
            <th>交易类型</th>
            @else
            <th></th>
            @endif
            <th>变动金额</th>
            @if($reportName == 'jc-transaction')
            <th>余额</th>
            @else
            @if(!in_array($reportName, ['withdraw', 'deposit']))
            <th>备注</th>
            @endif
            @endif
        </tr>
    </thead>
    <tbody>
        <?php $fTotalAmount = 0; ?>
        @if (count($datas))
            @foreach ($datas as $data)
            <tr>
                <td>
                    <a class="view-detail" title="{!! $data->serial_number !!}" href="javascript:void(0);">{!! $data->serial_number_short !!}</a>
                </td>
                <td>
                    <?php $aCreatedAt = explode(' ', $data->created_at); ?>
                    {!! $aCreatedAt[0] !!}
                    <br />
                    {!! $aCreatedAt[1] !!}
                </td>
                @if($reportName == 'jc-transaction')
                <td>{!! $data->friendly_description !!}</td>
                @else
                <td></td>
                @endif
                <td><span class="{!! $data->amount_formatted < 0 ? 'c-green' : 'c-red' !!}">{!! $data->amount_formatted !!}</span></td>
                @if($reportName == 'jc-transaction')
                <td>{!! $data->available_formatted !!}</td>
                @else
                @if(!in_array($reportName, ['withdraw', 'deposit']))
                <td>{!! $data->note !!}</td>
                @endif
                @endif
            </tr>
            <?php $fTotalAmount += $data->direct_amount; ?>
            @endforeach
        @else
            <tr><td colspan="{!! $reportName != 'withdraw' ? 10 : 9 !!}">没有符合条件的记录，请更改查询条件</td></tr>
        @endif
    </tbody>
    <tfoot>
        <tr>
            <td>本页资本变动</td>
            <td></td>
            <td></td>
            <td><span class="{!! $fTotalAmount < 0 ? 'c-green' : 'c-red' !!}" id="fundChangeNum">{!! number_format($fTotalAmount, 2) !!}</span></td>
            @if(!in_array($reportName, ['withdraw', 'deposit']))
            <td></td>
            @endif
        </tr>
    </tfoot>
</table>

