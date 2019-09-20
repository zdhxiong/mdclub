<?php

declare(strict_types=1);

namespace MDClub\Initializer;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Interfaces\InvocationStrategyInterface;

/**
 * 路由回调策略
 */
class RouteStrategy implements InvocationStrategyInterface
{
    /**
     * @var Container
     */
    protected $container;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * 根据控制器返回的数组构建响应
     *
     * @param  ResponseInterface $response
     * @param  array             $data
     * @return ResponseInterface
     */
    protected function withArray(ResponseInterface $response, array $data): ResponseInterface {
        $result = ['code' => 0];

        if (isset($data['data'], $data['pagination'])) {
            $result = array_merge($data, $result);
        } else {
            $result['data'] = $data;
        }

        $json = (string) json_encode($result);

        $response = $response->withHeader('Content-Type', 'application/json;charset=utf-8');
        $response->getBody()->write($json);

        return $response;
    }

    /**
     * 回调函数的参数仅包含路由参数，去掉了 Slim 自带回调策略的 ServerRequestInterface 和 ResponseInterface 参数，
     * 并将这两个参数放入容器，控制器中要使用这两个参数，可从容器中获取
     *
     * 回调函数允许返回 ResponseInterface 或数组
     * 若返回数组，则组织成 restful api 的格式，并返回 ResponseInterface
     *
     * @param  callable               $callable
     * @param  ServerRequestInterface $request
     * @param  ResponseInterface      $response
     * @param  array                  $routeArguments
     * @return ResponseInterface
     */
    public function __invoke(
        callable $callable,
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $routeArguments
    ): ResponseInterface {
        $this->container->offsetSet('request', $request);
        $this->container->offsetSet('response', $response);

        // 将整数参数转化为整型，其他参数保持字符串型
        foreach ($routeArguments as &$arg) {
            if (is_numeric($arg)) {
                $arg = (int) $arg;
            }
        }

        $data = $callable(...array_values($routeArguments));

        if (is_null($data)) {
            return $response;
        } elseif ($data instanceof ResponseInterface) {
            return $data;
        } else {
            return $this->withArray($response, $data);
        }
    }
}
