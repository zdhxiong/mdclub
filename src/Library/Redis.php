<?php

declare(strict_types=1);

namespace App\Library;

use Psr\Container\ContainerInterface;

/**
 * Redis 客户端
 */
class Redis
{
    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
    }
}
