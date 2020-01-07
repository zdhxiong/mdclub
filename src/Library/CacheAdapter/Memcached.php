<?php

declare(strict_types=1);

namespace MDClub\Library\CacheAdapter;

use MDClub\Constant\OptionConstant;
use MDClub\Facade\Library\Option;
use Symfony\Component\Cache\Adapter\MemcachedAdapter;

/**
 * Memcached 缓存适配器
 */
class Memcached extends MemcachedAdapter
{
    public function __construct()
    {
        $username = Option::get(OptionConstant::CACHE_MEMCACHED_USERNAME);
        $password = Option::get(OptionConstant::CACHE_MEMCACHED_PASSWORD);
        $host = Option::get(OptionConstant::CACHE_MEMCACHED_HOST);
        $port = Option::get(OptionConstant::CACHE_MEMCACHED_PORT);
        $namespace = Option::get(OptionConstant::CACHE_PREFIX);

        $client = MemcachedAdapter::createConnection("memcached://${username}:${password}@${host}:${port}");

        parent::__construct($client, $namespace);
    }
}
