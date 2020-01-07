<?php

declare(strict_types=1);

namespace MDClub\Library\CacheAdapter;

use MDClub\Constant\OptionConstant;
use MDClub\Facade\Library\Option;
use Symfony\Component\Cache\Adapter\RedisAdapter;

/**
 * Redis 缓存适配器
 */
class Redis extends RedisAdapter
{
    public function __construct()
    {
        $username = Option::get(OptionConstant::CACHE_REDIS_USERNAME);
        $password = Option::get(OptionConstant::CACHE_REDIS_PASSWORD);
        $host = Option::get(OptionConstant::CACHE_REDIS_HOST);
        $port = Option::get(OptionConstant::CACHE_REDIS_PORT);
        $namespace = Option::get(OptionConstant::CACHE_PREFIX);

        $client = RedisAdapter::createConnection("redis://${username}:${password}@${host}:${port}");

        parent::__construct($client, $namespace);
    }
}
