<?php

declare(strict_types=1);

namespace App\Library\Cache;

use Psr\Container\ContainerInterface;
use Symfony\Component\Cache\Simple\FilesystemCache;

/**
 * 文件缓存适配器
 *
 * Class FileAdapter
 * @package App\Library\Cache
 */
class FileAdapter extends FilesystemCache
{
    /**
     * FileAdapter constructor.
     *
     * @param ContainerInterface $container
     * @param array              $options
     */
    public function __construct(ContainerInterface $container, array $options)
    {
        $namespace = '';
        $defaultLifetime = 0;
        $directory = __DIR__ . '/../../../var/cache/';

        parent::__construct($namespace, $defaultLifetime, $directory);
    }
}
