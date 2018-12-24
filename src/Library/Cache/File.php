<?php

declare(strict_types=1);

namespace App\Library\Cache;

use App\Library\Cache\Interfaces\CacheDriverInterface;
use Psr\Container\ContainerInterface;
use Psr\SimpleCache\CacheInterface;
use Symfony\Component\Cache\Simple\FilesystemCache;

/**
 * 文件缓存
 *
 * Class File
 * @package App\Library\Cache
 */
class File implements CacheDriverInterface
{
    /**
     * File constructor.
     *
     * @param ContainerInterface $container
     * @param array              $option
     */
    public function __construct(ContainerInterface $container, array $option)
    {
    }

    /**
     * @return CacheInterface
     */
    public function __invoke()
    {
        return new FilesystemCache('', 0, __DIR__ . '/../../../var/cache/');
    }
}
