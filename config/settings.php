<?php

declare(strict_types=1);

$config = require __DIR__ . '/../config.php';

define('APP_DEBUG', $config['APP_DEBUG']);

return [
    'settings' => [
        // 显示详细错误信息
        // NOTE: 因为错误处理已经全部重写，这个参数其实已经没有作用
        'displayErrorDetails' => APP_DEBUG,

        // 非调试模式时生成路由缓存
        'routerCacheFile'     => APP_DEBUG ? false : __DIR__ . '/../var/cache/CompiledRoute.php',

        // 数据库信息
        'database' => [
            'driver'    => $config['DB_CONNECTION'],
            'host'      => $config['DB_HOST'],
            'port'      => $config['DB_PORT'],
            'database'  => $config['DB_DATABASE'],
            'username'  => $config['DB_USERNAME'],
            'password'  => $config['DB_PASSWORD'],
            'charset'   => $config['DB_CHARSET'],
            'prefix'    => $config['DB_PREFIX'],
        ]
    ],
];
