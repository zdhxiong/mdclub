<?php

declare(strict_types=1);

namespace App\Library;

use App\Library\Cache\Memcached;
use App\Library\Cache\Pdo;
use App\Library\Cache\Redis;
use Psr\Container\ContainerInterface;
use Psr\SimpleCache\CacheInterface;

/**
 * 缓存（不包括文件缓存，根据用户在后台的设置而定）
 *
 * Class Cache
 * @package App\Library
 */
class Cache implements CacheInterface
{
    /**
     * 缓存驱动名称和类名的数组
     *
     * @var array
     */
    protected $driverClass = [
        'pdo'       => Pdo::class,
        'memcached' => Memcached::class,
        'redis'     => Redis::class,
    ];

    /**
     * 缓存驱动实例
     *
     * @var CacheInterface
     */
    protected $driver;

    /**
     * Cache constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        /** @var \App\Service\OptionService $optionService */
        $optionService = $container->get(\App\Service\OptionService::class);
        $option = $optionService->getAll();

        $cacheType = $option['cache_type'];

        if (!isset($this->driverClass[$cacheType])) {
            throw new \Exception('不存在指定的缓存类型: ' . $option['cache_type']);
        }

        $this->driver = (new $this->driverClass[$cacheType]($container, $option))();
    }

    public function get($key, $default = null)
    {
        return $this->driver->get($key, $default);
    }

    public function set($key, $value, $ttl = null)
    {
        return $this->driver->set($key, $value, $ttl);
    }

    public function delete($key)
    {
        return $this->driver->delete($key);
    }

    public function clear()
    {
        return $this->driver->clear();
    }

    public function getMultiple($keys, $default = null)
    {
        return $this->driver->getMultiple($keys, $default);
    }

    public function setMultiple($values, $ttl = null)
    {
        return $this->driver->setMultiple($values, $ttl);
    }

    public function deleteMultiple($keys)
    {
        return $this->driver->deleteMultiple($keys);
    }

    public function has($key)
    {
        return $this->driver->has($key);
    }
}
