<?php

declare(strict_types=1);

namespace MDClub\Facade\Library;

use MDClub\Initializer\Facade;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

/**
 * Request Facade
 *
 * ServerRequestInterface
 * @method static array                  getServerParams()
 * @method static array                  getCookieParams()
 * @method static ServerRequestInterface withCookieParams(array $cookies)
 * @method static array                  getQueryParams()
 * @method static ServerRequestInterface withQueryParams(array $query)
 * @method static array                  getUploadedFiles()
 * @method static ServerRequestInterface withUploadedFiles(array $uploadedFiles)
 * @method static null|array|object      getParsedBody()
 * @method static ServerRequestInterface withParsedBody($data)
 * @method static array                  getAttributes()
 * @method static mixed                  getAttribute($name, $default = null)
 * @method static ServerRequestInterface withAttribute($name, $value)
 * @method static ServerRequestInterface withoutAttribute($name)
 *
 * RequestInterface
 * @method static string                 getRequestTarget()
 * @method static ServerRequestInterface withRequestTarget($requestTarget)
 * @method static string                 getMethod()
 * @method static ServerRequestInterface withMethod($method)
 * @method static UriInterface           getUri()
 * @method static ServerRequestInterface withUri(UriInterface $uri, $preserveHost = false)
 *
 * MessageInterface
 * @method static string                 getProtocolVersion()
 * @method static ServerRequestInterface withProtocolVersion($version)
 * @method static string[][]             getHeaders()
 * @method static bool                   hasHeader($name)
 * @method static string[]               getHeader($name)
 * @method static string                 getHeaderLine($name)
 * @method static ServerRequestInterface withHeader($name, $value)
 * @method static ServerRequestInterface withAddedHeader($name, $value)
 * @method static ServerRequestInterface withoutHeader($name)
 * @method static StreamInterface        getBody()
 * @method static ServerRequestInterface withBody(StreamInterface $body)
 */
class Request extends Facade
{
    /**
     * @inheritDoc
     */
    protected static function getFacadeAccessor(): string
    {
        return ServerRequestInterface::class;
    }

    /**
     * 获取请求时间戳
     *
     * @return int
     */
    public static function time(): int
    {
        return (int) (self::getServerParams()['REQUEST_TIME'] ?? time());
    }

    /**
     * 获取微秒精度的请求时间
     *
     * @return float
     */
    public static function microtime(): float
    {
        return (float) (self::getServerParams()['REQUEST_TIME_FLOAT'] ?? microtime(true));
    }

    /**
     * 判断当前请求是否支持 webp 图片格式
     *
     * @return bool
     */
    public static function isSupportWebp(): bool
    {
        $serverParams = self::getServerParams();

        if (!isset($serverParams['HTTP_ACCEPT'])) {
            return false;
        }

        return strpos($serverParams['HTTP_ACCEPT'], 'image/webp') > -1;
    }

    /**
     * 是否请求 json 格式
     *
     * @return bool
     */
    public static function isJson(): bool
    {
        return strpos(self::getHeaderLine('accept'), 'application/json') > -1;
    }

    /**
     * 获取 query 参数，并把值按 , 分隔成数组
     *
     * @param string $name
     * @param int    $max
     *
     * @return array
     */
    public static function getQueryParamsSplit(string $name, int $max = 100): array
    {
        $queryParams = self::getQueryParams();

        if (!isset($queryParams[$name])) {
            return [];
        }

        return collect(explode(',', $queryParams[$name]))->slice(0, $max)->filter()->unique()->all();
    }
}
