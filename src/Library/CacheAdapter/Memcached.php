<?php

declare(strict_types=1);

namespace MDClub\Library\CacheAdapter;

use Psr\Container\ContainerInterface;
use Symfony\Component\Cache\Adapter\MemcachedAdapter;

/**
 * Memcached 缓存适配器
 */
class Memcached extends MemcachedAdapter
{
    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        [
            'cache_memcached_username' => $username,
            'cache_memcached_password' => $password,
            'cache_memcached_host' => $host,
            'cache_memcached_port' => $port,
            'cache_prefix' => $namespace,
        ] = $container->get('option')->all();

        $client = MemcachedAdapter::createConnection("memcached://${username}:${password}@${host}:${port}");

        parent::__construct($client, $namespace);
    }
}
