<?php

declare(strict_types=1);

namespace App\Library\Cache;

use App\Interfaces\ContainerInterface;
use Symfony\Component\Cache\Adapter\MemcachedAdapter as SymfonyMemcachedAdapter;
use Symfony\Component\Cache\Simple\MemcachedCache;

/**
 * Memcached 缓存适配器
 *
 * Class MemcachedAdapter
 * @package App\Library\Cache
 */
class MemcachedAdapter extends MemcachedCache
{
    /**
     * MemcachedAdapter constructor.
     *
     * @param ContainerInterface $container
     * @param array              $options
     */
    public function __construct(ContainerInterface $container, array $options)
    {
        $username = $options['cache_memcached_username'];
        $password = $options['cache_memcached_password'];
        $host = $options['cache_memcached_host'];
        $port = $options['cache_memcached_port'];

        $client = SymfonyMemcachedAdapter::createConnection("memcached://{$username}:{$password}@{$host}:{$port}");
        $namespace = $options['cache_prefix'];

        parent::__construct($client, $namespace);
    }
}
