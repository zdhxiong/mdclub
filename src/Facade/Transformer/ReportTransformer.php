<?php

declare(strict_types=1);

namespace MDClub\Facade\Transformer;

use MDClub\Initializer\Facade;
use MDClub\Transformer\Report;

/**
 * ReportTransformer Facade
 *
 * @method static array transform(array $items, array $knownRelationship = [])
 */
class ReportTransformer extends Facade
{
    /**
     * @inheritDoc
     */
    protected static function getFacadeAccessor(): string
    {
        return Report::class;
    }
}
