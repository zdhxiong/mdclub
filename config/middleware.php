<?php

declare(strict_types=1);

/**
 * 将以斜杠结尾的路径跳转到不以斜杠结尾的路径（仅限 GET 请求）
 *
 * 例如：访问 https://example.com/book/ 将 301 跳转到 https://example.com/book
 */
$app->add(new \App\Middleware\TrailingSlashMiddleware());

/**
 * 在 Response 中添加 Trace 信息
 */
if (APP_DEBUG) {
    $app->add(new \App\Middleware\TraceMiddleware($app->getContainer()));
}
