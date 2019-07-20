<?php

declare(strict_types=1);

namespace MDClub\Library\CacheAdapter;

use Psr\Container\ContainerInterface;
use Symfony\Component\Cache\Adapter\RedisAdapter;

/**
 * Redis 缓存适配器
 */
class Redis extends RedisAdapter
{
    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        [
            'cache_redis_username' => $username,
            'cache_redis_password' => $password,
            'cache_redis_host' => $host,
            'cache_redis_port' => $port,
            'cache_prefix' => $namespace,
        ] = $container->get('option')->all();

        $client = RedisAdapter::createConnection("redis://${username}:${password}@${host}:${port}");

        parent::__construct($client, $namespace);
    }
}
