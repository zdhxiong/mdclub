<?php

declare(strict_types=1);

namespace MDClub\Facade\Validator;

use MDClub\Initializer\Facade;
use MDClub\Validator\Report;

/**
 * ReportValidator Facade
 *
 * @method static array create(string $reportableType, int $reportableId, array $data)
 */
class ReportValidator extends Facade
{
    /**
     * @inheritDoc
     */
    protected static function getFacadeAccessor(): string
    {
        return Report::class;
    }
}
