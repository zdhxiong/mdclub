<?php

declare(strict_types=1);

namespace MDClub\Facade\Service;

use MDClub\Initializer\Facade;
use MDClub\Service\Stats;

/**
 * StatsService Facade
 *
 * @method static array get()
 */
class StatsService extends Facade
{
    /**
     * @inheritDoc
     */
    protected static function getFacadeAccessor(): string
    {
        return Stats::class;
    }
}
