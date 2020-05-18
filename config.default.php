<?php

return [
    'APP_DEBUG'         => false,             // 是否开启调试模式，
    'APP_RUNTIME'       => __DIR__ . '/var',  // 临时文件存放目录
    'APP_SHOW_API_DOCS' => false,             // 若为 true，则访问 /api 页面时，将显示 swagger 文档

    'DB_CONNECTION'     => 'mysql',           // 数据库类型
    'DB_HOST'           => '127.0.0.1',       // 数据库主机地址
    'DB_PORT'           => '3306',            // 数据库端口号
    'DB_DATABASE'       => 'mdclub',          // 数据库名称
    'DB_USERNAME'       => 'root',            // 数据库用户名
    'DB_PASSWORD'       => '123456',          // 数据库密码
    'DB_CHARSET'        => 'utf8mb4',         // 数据库字符集
    'DB_PREFIX'         => 'mc_'              // 数据库表前缀
];
