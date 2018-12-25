<?php

declare(strict_types=1);

namespace App\Library;

use App\Library\Cache\MemcachedAdapter;
use App\Library\Cache\RedisAdapter;
use Psr\SimpleCache\CacheInterface;

/**
 * 分布式键值缓存（包括 Memcached、Redis，根据用户在后台的设置而定）
 *
 * Class KvCache
 * @package App\Library
 */
class KvCache extends Cache implements CacheInterface
{
    protected $adapters = [
        'memcached' => MemcachedAdapter::class,
        'redis'     => RedisAdapter::class,
    ];
}
