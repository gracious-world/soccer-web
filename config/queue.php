<?php
$prefix = env('QUEUE_PREFIX', 'jcdc-');
$driver = 'beanstalkd';
$host = env('QUEUE_HOST', '127.0.0.1');
$ttr = 60;
$connections = [
    'main',
    'ticket',
    'calculate',
    'prize',
    'fund',
    'stat'
];
$config = [
    'default' => $prefix . 'main',
    'connections' => [],
    'failed' => [
        'database' => env('DB_CONNECTION', 'mysql'),
        'table'    => 'failed_jobs',
    ],
    'prefix' => $prefix,
];
foreach ($connections as $name) {
    $config['connections'][$prefix . $name] = [
        'driver' => $driver,
        'host'   => $host,
        'queue'  => $prefix . $name,
        'ttr'    => $ttr,
    ];
}
return $config;

