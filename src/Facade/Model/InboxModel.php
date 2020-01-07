<?php

declare(strict_types=1);

namespace MDClub\Facade\Model;

use MDClub\Model\Inbox;

/**
 * InboxModel Facade
 */
class InboxModel extends Abstracts
{
    /**
     * @inheritDoc
     */
    protected static function getFacadeAccessor(): string
    {
        return Inbox::class;
    }
}
