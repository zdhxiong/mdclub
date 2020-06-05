<?php

declare(strict_types=1);

namespace MDClub\Controller\RestApi;

use MDClub\Facade\Service\StatsService;

/**
 * 统计数据 API
 */
class Stats extends Abstracts
{
    protected function getService(): string
    {
        return \MDClub\Service\Stats::class;
    }

    /**
     * 获取统计数据
     *
     * @return array
     */
    public function get(): array
    {
        return StatsService::get();
    }
}
