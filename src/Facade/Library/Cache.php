<?php

declare(strict_types=1);

namespace MDClub\Facade\Library;

use MDClub\Initializer\App;
use MDClub\Initializer\Facade;

/**
 * Cache Facade
 *
 * @method static mixed    get($key, $default = null)
 * @method static bool     set($key, $value, $ttl = null)
 * @method static bool     delete($key)
 * @method static bool     clear()
 * @method static iterable getMultiple($keys, $default = null)
 * @method static bool     setMultiple($values, $ttl = null)
 * @method static bool     deleteMultiple($keys)
 * @method static bool     has($key)
 */
class Cache extends Facade
{
    /**
     * @inheritDoc
     */
    protected static function getFacadeAccessor(): string
    {
        return \MDClub\Library\Cache::class;
    }

    /**
     * 缓存日志
     *
     * @var array
     */
    protected static $logs = [];

    /**
     * 在操作缓存前，记录缓存操作日志
     *
     * @param string $name
     * @param array  $arguments
     *
     * @return mixed
     */
    public static function __callStatic(string $name, array $arguments = [])
    {
        if (App::$config['APP_DEBUG']) {
            self::$logs[] = "{$name}(" . implode(',', $arguments) . ")";
        }

        return parent::__callStatic($name, $arguments);
    }

    /**
     * 获取缓存操作记录
     *
     * @return array
     */
    public static function log(): array
    {
        return self::$logs;
    }
}
