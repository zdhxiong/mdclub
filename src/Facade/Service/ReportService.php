<?php

declare(strict_types=1);

namespace MDClub\Facade\Service;

use MDClub\Facade\Service\Traits\Getable;
use MDClub\Initializer\Facade;
use MDClub\Service\Report;

/**
 * ReportService Facade
 *
 * @method static void  hasTargetOrFail(string $reportableType, int $reportableId)
 * @method static array getReasons(string $reportableType, int $reportableId)
 * @method static void  delete(string $reportableType, int $reportableId)
 * @method static void  deleteMultiple(array $targets)
 * @method static int   create(string $reportableType, int $reportableId, array $data)
 */
class ReportService extends Facade
{
    use Getable;

    /**
     * @inheritDoc
     */
    protected static function getFacadeAccessor(): string
    {
        return Report::class;
    }
}
