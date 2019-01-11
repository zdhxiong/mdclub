<?php

declare(strict_types=1);

namespace App\Library;

use App\Library\Cache\MemcachedAdapter;
use App\Library\Cache\PdoAdapter;
use App\Library\Cache\RedisAdapter;
use App\Service\OptionService;
use Psr\Container\ContainerInterface;
use Psr\SimpleCache\CacheInterface;

/**
 * 缓存
 *
 * 缓存支持 pdo, memcached, redis，其中 memcached/redis 的优先级高于 pdo
 * 如果没有设置 memcached/redis，才会使用 pdo 缓存
 *
 * Class Cache
 * @package App\Library
 */
class Cache implements CacheInterface
{
    /**
     * @var OptionService
     */
    protected $optionService;

    /**
     * 缓存名称和适配器类名的数组
     *
     * @var array
     */
    protected $adapterMap = [
        'pdo'       => PdoAdapter::class,
        'memcached' => MemcachedAdapter::class,
        'redis'     => RedisAdapter::class,
    ];

    /**
     * 缓存适配器实例
     *
     * @var CacheInterface
     */
    protected $adapter;

    /**
     * Cache constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->optionService = $container->get(OptionService::class);
        $options = $this->optionService->getAll();
        $cacheType = $options['cache_type'];

        if (!isset($this->adapterMap[$cacheType])) {
            throw new \Exception('不存在指定的缓存类型: ' . $cacheType);
        }

        $this->adapter = new $this->adapterMap[$cacheType]($container, $options);
    }

    public function get($key, $default = null)
    {
        return $this->adapter->get($key, $default);
    }

    public function set($key, $value, $ttl = null)
    {
        return $this->adapter->set($key, $value, $ttl);
    }

    public function delete($key)
    {
        return $this->adapter->delete($key);
    }

    public function clear()
    {
        return $this->adapter->clear();
    }

    public function getMultiple($keys, $default = null)
    {
        return $this->adapter->getMultiple($keys, $default);
    }

    public function setMultiple($values, $ttl = null)
    {
        return $this->adapter->setMultiple($values, $ttl);
    }

    public function deleteMultiple($keys)
    {
        return $this->adapter->deleteMultiple($keys);
    }

    public function has($key)
    {
        return $this->adapter->has($key);
    }
}
