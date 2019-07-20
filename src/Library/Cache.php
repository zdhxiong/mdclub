<?php

declare(strict_types=1);

namespace MDClub\Library;

use MDClub\Exception\SystemException;
use MDClub\Library\CacheAdapter\Memcached;
use MDClub\Library\CacheAdapter\Pdo;
use MDClub\Library\CacheAdapter\Redis;
use Psr\Container\ContainerInterface;
use Psr\SimpleCache\CacheInterface;
use Symfony\Component\Cache\Psr16Cache;

/**
 * 实现了 PSR16 接口的缓存
 */
class Cache implements CacheInterface
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
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->cacheType = $container->get('option')->cache_type;

        if (!isset($this->adapterMap[$this->cacheType])) {
            throw new SystemException('不存在指定的缓存类型: ' . $this->cacheType);
        }

        $psr6Cache = new $this->adapterMap[$this->cacheType]($container);

        $this->adapter = new Psr16Cache($psr6Cache);
    }

    /**
     * @inheritDoc
     */
    public function get($key, $default = null)
    {
        $this->logs[] = "{$this->cacheType}:get {$key}";

        return $this->adapter->get($key, $default);
    }

    /**
     * @inheritDoc
     */
    public function set($key, $value, $ttl = null): bool
    {
        $this->logs[] = "{$this->cacheType}:set {$key}";

        return $this->adapter->set($key, $value, $ttl);
    }

    /**
     * @inheritDoc
     */
    public function delete($key): bool
    {
        $this->logs[] = "{$this->cacheType}:delete {$key}";

        return $this->adapter->delete($key);
    }

    /**
     * @inheritDoc
     */
    public function clear(): bool
    {
        $this->logs[] = "{$this->cacheType}:clear";

        return $this->adapter->clear();
    }

    /**
     * @inheritDoc
     */
    public function getMultiple($keys, $default = null)
    {
        $this->logs[] = "{$this->cacheType}:getMultiple " . collect($keys)->implode(',');

        return $this->adapter->getMultiple($keys, $default);
    }

    /**
     * @inheritDoc
     */
    public function setMultiple($values, $ttl = null): bool
    {
        $this->logs[] = "{$this->cacheType}:setMultiple " . collect($values)->keys()->implode(',');

        return $this->adapter->setMultiple($values, $ttl);
    }

    /**
     * @inheritDoc
     */
    public function deleteMultiple($keys): bool
    {
        $this->logs[] = "{$this->cacheType}:deleteMultiple " . collect($keys)->implode(',');

        return $this->adapter->deleteMultiple($keys);
    }

    /**
     * @inheritDoc
     */
    public function has($key): bool
    {
        $this->logs[] = "{$this->cacheType}:has {$key}";

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
