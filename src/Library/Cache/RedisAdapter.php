<?php

declare(strict_types=1);

namespace App\Library\Cache;

use App\Interfaces\ContainerInterface;
use Symfony\Component\Cache\Adapter\RedisAdapter as SymfonyRedisAdapter;
use Symfony\Component\Cache\Simple\RedisCache;

/**
 * Redis 缓存适配器
 *
 * Class RedisAdapter
 * @package App\Library\Cache
 */
class RedisAdapter extends RedisCache
{
    /**
     * RedisAdapter constructor.
     *
     * @param ContainerInterface $container
     * @param array              $options
     */
    public function __construct($container, array $options)
    {
        $username = $options['cache_redis_username'];
        $password = $options['cache_redis_password'];
        $host = $options['cache_redis_host'];
        $port = $options['cache_redis_port'];

        $client = SymfonyRedisAdapter::createConnection("redis://{$username}:{$password}@{$host}:{$port}");
        $namespace = $options['cache_prefix'];

        parent::__construct($client, $namespace);
    }
}
