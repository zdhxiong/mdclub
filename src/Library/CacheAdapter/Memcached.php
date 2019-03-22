<?php

declare(strict_types=1);

namespace App\Library\CacheAdapter;

use App\Interfaces\ContainerInterface;
use Symfony\Component\Cache\Adapter\MemcachedAdapter as SymfonyMemcachedAdapter;
use Symfony\Component\Cache\Simple\MemcachedCache;

/**
 * Memcached 缓存适配器
 *
 * Class MemcachedAdapter
 * @package App\Library\Cache
 */
class Memcached extends MemcachedCache
{
    /**
     * MemcachedAdapter constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct($container)
    {
        [
            'cache_memcached_username' => $username,
            'cache_memcached_password' => $password,
            'cache_memcached_host' => $host,
            'cache_memcached_port' => $port,
            'cache_prefix' => $namespace
        ] = $container->optionService->getMultiple();

        $client = SymfonyMemcachedAdapter::createConnection("memcached://{$username}:{$password}@{$host}:{$port}");

        parent::__construct($client, $namespace);
    }
}
