<?php

declare(strict_types=1);

namespace App\Library;

use App\Library\Cache\FileAdapter;
use Psr\Container\ContainerInterface;
use Psr\SimpleCache\CacheInterface;

/**
 * 文件缓存
 *
 * Class FileCache
 * @package App\Library
 */
class FileCache extends Cache implements CacheInterface
{
    /**
     * FileCache constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->adapter = new FileAdapter($container, []);
    }
}
