<?php

declare(strict_types=1);

namespace App\Library\CacheAdapter;

use App\Interfaces\ContainerInterface;
use Symfony\Component\Cache\Adapter\RedisAdapter as SymfonyRedisAdapter;
use Symfony\Component\Cache\Simple\RedisCache;

/**
 * Redis 缓存适配器
 *
 * Class RedisAdapter
 * @package App\Library\Cache
 */
class Redis extends RedisCache
{
    /**
     * RedisAdapter constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct($container)
    {
        [
            'cache_redis_username' => $username,
            'cache_redis_password' => $password,
            'cache_redis_host' => $host,
            'cache_redis_port' => $port,
            'cache_prefix' => $namespace
        ] = $container->optionService->getMultiple();

        $client = SymfonyRedisAdapter::createConnection("redis://{$username}:{$password}@{$host}:{$port}");

        parent::__construct($client, $namespace);
    }
}
