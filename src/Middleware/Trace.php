<?php

declare(strict_types=1);

namespace MDClub\Middleware;

use MDClub\Facade\Library\Cache;
use MDClub\Facade\Library\Db;
use MDClub\Facade\Library\Request;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Psr7\Factory\StreamFactory;

/**
 * 在 Response 中添加 Trace 信息
 */
class Trace implements MiddlewareInterface
{
    /**
     * @inheritDoc
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->handle($request);

        return $this->appendTraceMessage($response);
    }

    /**
     * 追加 trace 消息到 response 中
     *
     * @param  ResponseInterface      $response
     * @return ResponseInterface
     */
    public function appendTraceMessage(ResponseInterface $response): ResponseInterface
    {
        $trace = $this->generateTrace();

        if (strpos($response->getHeaderLine('Content-Type'), 'application/json') > -1) {
            $response = $this->renderJsonMessage($response, $trace);
        } else {
            $response = $this->renderHtmlMessage($response, $trace);
        }

        return $response;
    }

    /**
     * 把内存数值格式化为可读的格式
     *
     * @param  int    $memory 数值形式的内存
     * @return string         格式化后的内存信息
     */
    protected function memoryFormat(int $memory): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $pos = 0;

        while ($memory >= 1024) {
            $memory /= 1024;
            $pos++;
        }

        return round($memory, 2) . ' ' . $units[$pos];
    }

    /**
     * 把时间数值转化为可读的格式
     *
     * @param  float   $time 微秒时间
     * @return string        格式化后的时间
     */
    protected function timeFormat(float $time): string
    {
        $units = ['μs', 'ms', 's'];
        $pos = 0;
        $time *= 1000000;

        while ($time > 1000) {
            $time /= 1000;
            $pos++;
        }

        return round($time, 2) . ' ' . $units[$pos];
    }

    /**
     * 获取 Trace 信息
     *
     * @return array
     */
    protected function generateTrace(): array
    {
        $sql = Db::log();
        $cache = Cache::log();
        $time = microtime(true) - Request::microtime();
        $files = get_included_files();

        return [
            'TimeUsage'                    => $this->timeFormat($time),
            'MemoryUsage'                  => $this->memoryFormat(memory_get_usage()),
            'ThroughputRate'               => number_format(1 / $time, 2) . ' req/s',
            'Cache(' . count($cache) . ')' => $cache,
            'SQL(' . count($sql) . ')'     => $sql,
            'File(' . count($files) . ')'  => $files,
        ];
    }

    /**
     * 把 Trace 添加到 json 中
     *
     * @param  ResponseInterface $response
     * @param  array             $trace
     * @return ResponseInterface
     */
    protected function renderJsonMessage(ResponseInterface $response, array $trace): ResponseInterface
    {
        $body = json_decode($response->getBody()->__toString(), true);
        $body['trace'] = $trace;

        $json = (string) json_encode($body);

        $response = $response
            ->withHeader('Content-Type', 'application/json;charset=utf-8')
            ->withBody((new StreamFactory())->createStream($json));

        return $response;
    }

    /**
     * 把 Trace 添加到 html 中
     *
     * @param  ResponseInterface $response
     * @param  array             $trace
     * @return ResponseInterface
     */
    protected function renderHtmlMessage(ResponseInterface $response, array $trace): ResponseInterface
    {
        $html = '<script type="text/javascript">console.log(' . json_encode($trace) . ');</script>';

        $response->getBody()->write($html);

        return $response;
    }
}
