<?php

declare(strict_types=1);

namespace MDClub\Library;

use MDClub\Constant\OptionConstant;
use MDClub\Exception\SystemException;
use MDClub\Facade\Library\Option as OptionFacade;
use MDClub\Library\CacheAdapter\Memcached;
use MDClub\Library\CacheAdapter\Pdo;
use MDClub\Library\CacheAdapter\Redis;
use Symfony\Component\Cache\Psr16Cache;

/**
 * 实现了 PSR16 接口的缓存
 */
class Cache extends Psr16Cache
{
    /**
     * 缓存名称和适配器类名的数组
     *
     * @var array
     */
    protected $adapterMap = [
        'pdo'       => Pdo::class,
        'memcached' => Memcached::class,
        'redis'     => Redis::class,
    ];

    public function __construct()
    {
        $cacheType = OptionFacade::get(OptionConstant::CACHE_TYPE);

        if (!isset($this->adapterMap[$cacheType])) {
            throw new SystemException('不存在指定的缓存类型: ' . $cacheType);
        }

        $psr6Cache = new $this->adapterMap[$cacheType]();

        parent::__construct($psr6Cache);
    }
}
