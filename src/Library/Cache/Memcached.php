<?php

declare(strict_types=1);

namespace App\Library\Cache;

use App\Library\Cache\Interfaces\CacheDriverInterface;
use Psr\Container\ContainerInterface;
use Psr\SimpleCache\CacheInterface;
use Symfony\Component\Cache\Adapter\MemcachedAdapter;
use Symfony\Component\Cache\Simple\MemcachedCache;

/**
 * Memcached 缓存
 *
 * Class Memcached
 * @package App\Library\Cache
 */
class Memcached implements CacheDriverInterface
{
    /**
     * @array 配置参数
     */
    protected $option;

    /**
     * Memcached constructor.
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
        $username = $this->option['cache_memcached_username'];
        $password = $this->option['cache_memcached_password'];
        $host = $this->option['cache_memcached_host'];
        $port = $this->option['cache_memcached_port'];

        $adapter = MemcachedAdapter::createConnection("memcached://{$username}:{$password}@{$host}:{$port}");

        return new MemcachedCache($adapter);
    }
}
