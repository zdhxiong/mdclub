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
 * 缓存（不包括文件缓存，根据用户在后台的设置而定）
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
    protected $adapters = [
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
        /** @var OptionService $optionService */
        $optionService = $container->get(OptionService::class);
        $options = $optionService->getAll();

        $cacheType = $options['cache_type'];

        if (!isset($this->adapters[$cacheType])) {
            throw new \Exception('不存在指定的缓存类型: ' . $options['cache_type']);
        }

        $this->adapter = new $this->adapters[$cacheType]($container, $options);
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
