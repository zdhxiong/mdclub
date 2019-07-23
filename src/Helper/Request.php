<?php

declare(strict_types=1);

namespace MDClub\Helper;

use Psr\Http\Message\ServerRequestInterface;

/**
 * 请求相关函数
 */
class Request
{
    /**
     * 获取请求时间
     *
     * @param  ServerRequestInterface $request
     * @return int
     */
    public static function time(ServerRequestInterface $request): int
    {
        return (int) ($request->getServerParams()['REQUEST_TIME'] ?? time());
    }

    /**
     * 获取微秒精度的请求时间
     *
     * @param  ServerRequestInterface $request
     * @return float
     */
    public static function microtime(ServerRequestInterface $request): float
    {
        return (float) ($request->getServerParams()['REQUEST_TIME_FLOAT'] ?? microtime(true));
    }

    /**
     * 判断当前请求是否支持 webp 图片格式
     *
     * @param  ServerRequestInterface $request
     * @return bool
     */
    public static function isSupportWebp(ServerRequestInterface $request): bool
    {
        $serverParams = $request->getServerParams();

        if (!isset($serverParams['HTTP_ACCEPT'])) {
            return false;
        }

        return strpos($serverParams['HTTP_ACCEPT'], 'image/webp') > -1;
    }

    /**
     * 把 query 参数中用 , 分隔的参数转换为数组
     *
     * @param  ServerRequestInterface $request
     * @param  string                 $param   query参数名
     * @param  int                    $count   数组中最大条目数
     * @return array|null                      若存在参数，但值为空，则返回空数组；若不存在参数，则返回 null
     */
    public static function getQueryParamToArray(
        ServerRequestInterface $request,
        string $param,
        int $count = 100
    ): ?array {
        $queryParams = $request->getQueryParams();

        if (!isset($queryParams[$param])) {
            return null;
        }

        return collect(explode(',', $queryParams[$param]))->slice(0, $count)->filter()->unique()->all();
    }

    /**
     * 把 body 中用 , 分隔的参数转换为数组
     *
     * @param  ServerRequestInterface $request
     * @param  string                 $param
     * @param  int                    $count
     * @return array|null                     若存在参数，但值为空，则返回空数组；若不存在参数，则返回 null
     */
    public static function getBodyParamToArray(
        ServerRequestInterface $request,
        string $param,
        int $count = 100
    ): ?array {
        $bodyParams = $request->getParsedBody();

        if (!isset($bodyParams[$param])) {
            return null;
        }

        return collect(explode(',', $bodyParams[$param]))->slice(0, $count)->filter()->unique()->all();
    }
}
