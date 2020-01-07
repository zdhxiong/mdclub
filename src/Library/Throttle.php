<?php

declare(strict_types=1);

namespace MDClub\Library;

use MDClub\Facade\Library\Request;
use MDClub\Facade\Library\Cache as CacheFacade;

/**
 * 限流
 */
class Throttle
{
    /**
     * 获取剩余可执行的次数
     *
     * NOTE: 调用该方法表示执行了一次
     *
     * @param  string $id       区别用户身份的字符串
     * @param  string $action   操作名称
     * @param  int    $maxCount 最多操作次数
     * @param  int    $period   在该时间范围内
     *
     * @return int                  剩余可执行的次数
     */
    public function getActLimit(string $id, string $action, int $maxCount, int $period): int
    {
        $time = Request::time();
        $ttl = (int) ($time / $period) * $period + $period - $time;
        $key = "throttle_{$action}_{$id}";
        $currentCount = (int) CacheFacade::get($key, 0) + 1;

        if ($currentCount > $maxCount) {
            return 0;
        }

        CacheFacade::set($key, $currentCount, $ttl);

        return $maxCount - $currentCount + 1;
    }
}
