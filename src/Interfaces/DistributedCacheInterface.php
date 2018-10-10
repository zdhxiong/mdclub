<?php

declare(strict_types=1);

namespace App\Interfaces;

use Psr\SimpleCache\CacheInterface;

/**
 * 分布式缓存接口
 *
 * Interface DistributedCacheInterface
 * @package App\Interfaces
 */
interface DistributedCacheInterface extends CacheInterface
{

}
