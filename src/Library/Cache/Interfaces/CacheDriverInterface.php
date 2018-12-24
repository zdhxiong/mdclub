<?php

declare(strict_types=1);

namespace App\Library\Cache\Interfaces;

use Psr\Container\ContainerInterface;
use Psr\SimpleCache\CacheInterface;

/**
 * 缓存驱动接口
 *
 * Interface CacheDriverInterface
 * @package App\Library\Cache\Interfaces
 */
interface CacheDriverInterface
{
    /**
     * CacheDriverInterface constructor.
     *
     * @param ContainerInterface $container
     * @param array              $option
     */
    public function __construct(ContainerInterface $container, array $option);

    /**
     * @return CacheInterface
     */
    public function __invoke();
}
