<?php

declare(strict_types=1);

namespace App\Library;

use App\Library\Cache\Memcached;
use App\Library\Cache\Redis;
use Psr\SimpleCache\CacheInterface;

/**
 * 分布式键值缓存（包括 Memcached、Redis，根据用户在后台的设置而定）
 *
 * Class KvCache
 * @package App\Library
 */
class KvCache extends Cache implements CacheInterface
{
    protected $driverClass = [
        'memcached' => Memcached::class,
        'redis'     => Redis::class,
    ];
}
