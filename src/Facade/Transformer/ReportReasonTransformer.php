<?php

declare(strict_types=1);

namespace MDClub\Facade\Transformer;

use MDClub\Initializer\Facade;
use MDClub\Transformer\ReportReason;

/**
 * ReportReasonTransformer Facade
 *
 * @method static array transform(array $items, array $knownRelationship = [])
 */
class ReportReasonTransformer extends Facade
{
    /**
     * @inheritDoc
     */
    protected static function getFacadeAccessor(): string
    {
        return ReportReason::class;
    }
}
