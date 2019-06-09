<?php

declare(strict_types=1);

namespace App\Service;

use App\Abstracts\ContainerAbstracts;

/**
 * 操作频率限制
 */
class Throttle extends ContainerAbstracts
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
    public function getRemaining(string $id, string $action, int $max_count, int $period): int
    {
        $time = $this->requestService->time();
        $ttl = ($time / $period) * $period + $period - $time;
        $key = "throttle_{$action}_{$id}";
        $currentCount = (int) $this->cache->get($key, 0) + 1;

        if ($currentCount > $max_count) {
            return 0;
        }

        $this->cache->set($key, $currentCount, $ttl);

        return $max_count - $currentCount + 1;
    }
}
