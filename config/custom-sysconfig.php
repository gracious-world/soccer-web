<?php
# 自定义系统配置
return [
    'sale-stop-minutes' => 5, // 销售截止于开售前x分钟
    'ticket-checking-seconds' => 60, // 出票延迟时间
    'default-log-path'  => storage_path() . '/logs/',
    'boolean' => ['No', 'Yes'],
    'coefficients' => [
        '1.000' => '2元',
        '0.500' => '1元',
        '0.100' => '2角',
        '0.050' => '1角',
        '0.010' => '2分',
        '0.001' => '2厘',
    ],
    'boolean' => ['No', 'Yes'],
    'tax-limit' => 10000, // 扣税额
    'tax-percent' => 0.2, // 扣税百分比
];
