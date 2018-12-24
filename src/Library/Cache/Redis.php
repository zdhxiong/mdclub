<?php

declare(strict_types=1);

namespace App\Library\Cache;

use App\Library\Cache\Interfaces\CacheDriverInterface;
use Psr\Container\ContainerInterface;
use Psr\SimpleCache\CacheInterface;
use Symfony\Component\Cache\Adapter\RedisAdapter;
use Symfony\Component\Cache\Simple\RedisCache;

/**
 * Redis 缓存
 *
 * Class Redis
 * @package App\Library\Cache
 */
class Redis implements CacheDriverInterface
{
    /**
     * @array 配置参数
     */
    protected $option;

    /**
     * Redis constructor.
     *
     * @param ContainerInterface $container
     * @param array              $option
     */
    public function __construct(ContainerInterface $container, array $option)
    {
        $this->option = $option;
    }

    /**
     * @return CacheInterface
     */
    public function __invoke()
    {
        $username = $this->option['cache_redis_username'];
        $password = $this->option['cache_redis_password'];
        $host = $this->option['cache_redis_host'];
        $port = $this->option['cache_redis_port'];

        $adapter = RedisAdapter::createConnection("redis://{$username}:{$password}@{$host}:{$port}");

        return new RedisCache($adapter);
    }
}
