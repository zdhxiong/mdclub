<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Abstracts\ContainerAbstracts;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * 在 Response 中添加 Trace 信息
 *
 * Class Trace
 * @package App\Middleware
 */
class Trace extends ContainerAbstracts
{
    /**
     * Trace 数据
     *
     * @var array
     */
    protected $trace = [];

    /**
     * @param  Request   $request
     * @param  Response  $response
     * @param  callable  $next
     * @return Response
     */
    public function __invoke(Request $request, Response $response, callable $next)
    {
        /** @var Response $response */
        $response = $next($request, $response);

        $this->getTrace($request);

        $responseContentType = $response->getHeaderLine('Content-Type');

        if (strpos($responseContentType, 'application/json') > -1) {
            $response = $this->renderJsonMessage($response);
        } else {
            $response = $this->renderHtmlMessage($response);
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
     * @param Request $request
     */
    protected function getTrace(Request $request)
    {
        $sql = $this->container->db->log();
        $time = microtime(true) - $request->getServerParams()['REQUEST_TIME_FLOAT'];
        $files = get_included_files();

        $this->trace = [
            'TimeUsage'                => $this->timeFormat($time),
            'MemoryUsage'              => $this->memoryFormat(memory_get_usage()),
            'ThroughputRate'           => number_format(1 / $time, 2) . ' req/s',
            'SQL('.count($sql).')'     => $sql,
            'File('.count($files).')'  => $files,
        ];
    }

    /**
     * 把 Trace 添加到 json 中
     *
     * @param Response $response
     * @return Response
     */
    protected function renderJsonMessage(Response $response)
    {
        $body = json_decode($response->getBody()->__toString());

        $body->trace = $this->trace;

        return $response->withJson($body);
    }

    /**
     * 把 Trace 添加到 html 中
     *
     * @param Response $response
     * @return Response
     */
    protected function renderHtmlMessage(Response $response)
    {
        $html = '<script type="text/javascript">console.log(' . json_encode($this->trace) . ');</script>';

        return $response->write($html);
    }
}
