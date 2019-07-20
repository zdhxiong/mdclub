<?php

declare(strict_types=1);

namespace MDClub\Traits;

use MDClub\Library\Cache;
use Psr\Http\Message\ServerRequestInterface;

/**
 * 操作频率限制
 *
 * @property-read ServerRequestInterface $request
 * @property-read Cache                  $cache
 */
trait Throttle
{
    /**
     * 获取剩余可执行的次数
     *
     * NOTE: 调用该方法表示执行了一次
     *
     * @param  string    $id        区别用户身份的字符串
     * @param  string    $action    操作名称
     * @param  int       $max_count 最多操作次数
     * @param  int       $period    在该时间范围内
     * @return int                  剩余可执行的次数
     */
    public function getActLimit(string $id, string $action, int $max_count, int $period): int
    {
        $time = (int) $this->request->getServerParams()['REQUEST_TIME'];
        $ttl = (int) (($time / $period) * $period + $period - $time);
        $key = "throttle_{$action}_{$id}";
        $currentCount = (int) $this->cache->get($key, 0) + 1;

        if ($currentCount > $max_count) {
            return 0;
        }

        $this->cache->set($key, $currentCount, $ttl);

        return $max_count - $currentCount + 1;
    }
}
