<table width="100%" class="table">
    <thead>
        <tr>
            <th>用户名</th>
            <th>日期</th>
            <th>充值总额</th>
            <th>提现总额</th>
            <th>销售总额</th>
            <th>中奖总额</th>
            <th>佣金总额</th>
            <th>活动奖金总额</th>
            <th>输值佣金总额</th>
            <th>游戏总盈亏</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($datas as $data)
        <tr>
            <td>{{ $data->username }}</td>
            <td>{{ $data->date }}</td>
            <td>{{ $data->deposit }}</td>
            <td>{{ $data->withdrawal }}</td>
            <td>{{ $data->turnover_formatted }}</td>
            <td>{{ $data->prize_formatted }}</td>
            <td>{{ $data->commission_formatted }}</td>
            <td>{{ $data->bonus_formatted }}</td>
            <td>{{ $data->lose_commission_formatted }}</td>
            <td><span class="{{ $data->profit < 0 ? 'c-red' : 'c-green' }}">{{ ($data->profit < 0 ? '-' : '+') }}  {{ number_format(abs($data->profit), 2) }}</span></td>
        </tr>
        @endforeach
    </tbody>
</table>

