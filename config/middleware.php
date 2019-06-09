<?php

declare(strict_types=1);

/**
 * Http Cache
 * @link https://github.com/slimphp/Slim-HttpCache
 */
$app->add(new \Slim\HttpCache\Cache('public', 86400));

/**
 * 将以斜杠结尾的路径跳转到不以斜杠结尾的路径（仅限 GET 请求）
 *
 * 例如：访问 https://example.com/book/ 将 301 跳转到 https://example.com/book
 */
$app->add(new \App\Middleware\TrailingSlash());

/**
 * 在 Response 中添加 Trace 信息
 */
if (APP_DEBUG) {
    $app->add(new \App\Middleware\Trace($app->getContainer()));
}

/**
 * 扩展 Collection 类
 */
$app->add(new \App\Middleware\CollectionMacro());
