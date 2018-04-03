<?php
# 自定义响应编码custom-response-code
return [
    // 数据中心响应码
     197 => '_datagate.retrieve-data-succeed',

    -100 => '_datagate.no-available-customer-error',
    -101 => '_datagate.sign-error',
    -102 => '_datagate.sign-missing',
    -103 => '_datagate.illegal-request',
    -197 => '_datagate.retrieve-data-failed',
    // -199 => '_datagate.retrieve-data-failed',
    // 注单功能响应码
     198 => '_bill.calculate-prize-succeed',
     199 => '_bill.already-calculated',

    // -102 => '_bill.insert-bill-all-failed',
    // -103 => '_bill.insert-bill-partly-succeed',
    -104 => '_bill.no-available-game-type-error',
    -105 => '_bill.betting-games-not-equal-on-sale-games',
    -106 => '_bill.betting-odd-not-exist-in-table',

    -200 => '_bill.not-finished-all-chosen-games',
    -201 => '_bill.can-not-find-related-games',
    -202 => '_bill.error-prize-number',
    -203 => '_bill.calculate-bill-failed',

    -204 => '_bill.error-bill-data',
    -205 => '_bill.invalid-customer-user',
    -206 => '_bill.invalid-game-type',
    -207 => '_bill.sale-time-overdue', // 所选赛事中有超过销售截止时间的赛事
    -208 => '_bill.error-pre-status', // 前置状态非法
    -209 => '_bill.error-on-saving',
    -210 => '_bill.no-bill',
    -211 => '_bill.lock-failed',
    -212 => '_bill.pause-betting',
    // 账户功能响应码
    -300 => '_account.no-available-account', // 没有符合条件的账户(可能被锁记录)
    -301 => '_account.account-low-balance', //余额不足
    -302 => '_account.lock-account-failed',
    -303 => '_account.invalid-amount',
    // 商户荷包功能响应码
    -400 => '_wallet.no-available-wallet', // 没有符合条件的荷包(可能被锁记录)
    -401 => '_wallet.wallet-low-balance', //余额不足
    -402 => '_wallet.cannot-lock-wallet',
    // 账变响应码
     599 => '_transaction.create-success',
    -500 => '_transaction.error-create-data',
    -501 => '_transaction.error-on-saving',
    -502 => '_transaction.error-create-balance',
    // 商户荷包账变响应码
     699 => '_pointjournal.create-success',
    -600 => '_pointjournal.error-on-saving',
    -601 => '_pointjournal.error-create-data',
    -602 => '_pointjournal.error-create-balance',
    // 用户登录响应码
    -700 => '_user.username-error',
    -701 => '_user.login-failed',
    -702 => '_user.not-yours-data',
    // 方案响应码
    -801 => '_program.no-program',
    -802 => '_program.no-related-bills',
];