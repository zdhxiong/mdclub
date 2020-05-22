<?php

declare(strict_types=1);

namespace MDClub\Facade\Transformer;

use MDClub\Initializer\Facade;
use MDClub\Transformer\Notification;

/**
 * Class NotificationTransformer
 *
 * @method static void  setInclude(array $includes)
 * @method static array getAvailableIncludes()
 * @method static array transform(array $items, array $knownRelationship = [], bool $canWithRelationships = true)
 */
class NotificationTransformer extends Facade
{
    /**
     * @inheritDoc
     */
    protected static function getFacadeAccessor(): string
    {
        return Notification::class;
    }
}
