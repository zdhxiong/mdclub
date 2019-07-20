<?php

declare(strict_types=1);

namespace MDClub\Initializer;

use Closure;
use Psr\Container\ContainerInterface;
use Slim\Interfaces\CallableResolverInterface;

/**
 * 路由回调解析器
 *
 * 路由仅支持 Controller/method 这一种方式
 */
class CallableResolver implements CallableResolverInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @inheritDoc
     */
    public function resolve($toResolve): callable
    {
        if ($toResolve instanceof Closure) {
            return $toResolve->bindTo($this->container);
        }

        [$class, $method] = explode('/', $toResolve);
        $class = '\MDClub\Controller\\' . $class;

        $instance = new $class($this->container);

        return [$instance, $method];
    }
}
