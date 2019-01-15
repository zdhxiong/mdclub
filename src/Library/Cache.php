<?php

declare(strict_types=1);

namespace App\Library;

use App\Interfaces\ContainerInterface;
use App\Library\Cache\MemcachedAdapter;
use App\Library\Cache\PdoAdapter;
use App\Library\Cache\RedisAdapter;
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
     * 缓存类型
     *
     * @var string
     */
    protected $cacheType;

    /**
     * 缓存操作记录
     *
     * @var array
     */
    protected $logs = [];

    /**
     * Cache constructor.
     * @param ContainerInterface $container
     */
    public function __construct($container)
    {
        $options = $container->optionService->getAll();
        $this->cacheType = $options['cache_type'];

        if (!isset($this->adapterMap[$this->cacheType])) {
            throw new \Exception('不存在指定的缓存类型: ' . $this->cacheType);
        }

        $this->adapter = new $this->adapterMap[$this->cacheType]($container, $options);
    }

    public function get($key, $default = null)
    {
        $this->logs[] = "{$this->cacheType} get {$key}";

        return $this->adapter->get($key, $default);
    }

    public function set($key, $value, $ttl = null)
    {
        $this->logs[] = "{$this->cacheType} set {$key}";

        return $this->adapter->set($key, $value, $ttl);
    }

    public function delete($key)
    {
        $this->logs[] = "{$this->cacheType} delete {$key}";

        return $this->adapter->delete($key);
    }

    public function clear()
    {
        $this->logs[] = "{$this->cacheType} clear";

        return $this->adapter->clear();
    }

    public function getMultiple($keys, $default = null)
    {
        $this->logs[] = "{$this->cacheType} getMultiple " . implode(',', $keys);

        return $this->adapter->getMultiple($keys, $default);
    }

    public function setMultiple($values, $ttl = null)
    {
        $this->logs[] = "{$this->cacheType} setMultiple " . implode(',', array_keys($values));

        return $this->adapter->setMultiple($values, $ttl);
    }

    public function deleteMultiple($keys)
    {
        $this->logs[] = "{$this->cacheType} deleteMultiple " . implode(',', $keys);
        return $this->adapter->deleteMultiple($keys);
    }

    public function has($key)
    {
        $this->logs[] = "{$this->cacheType} has {$key}";

        return $this->adapter->has($key);
    }

    /**
     * 获取缓存操作记录
     *
     * @return array
     */
    public function log(): array
    {
        return $this->logs;
    }
}
