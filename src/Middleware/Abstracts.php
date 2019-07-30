<?php

declare(strict_types=1);

namespace MDClub\Middleware;

use Psr\Container\ContainerInterface;

/**
 * 中间件抽象类
 */
abstract class Abstracts
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __get(string $name)
    {
        return $this->container->get($name);
    }
}
