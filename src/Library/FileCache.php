<?php

declare(strict_types=1);

namespace App\Library;

use App\Library\Cache\File;
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
    public function __construct(ContainerInterface $container)
    {
        $this->driver = (new File($container, []))();
    }
}
