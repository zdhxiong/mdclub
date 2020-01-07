<?php

declare(strict_types=1);

namespace MDClub\Facade\Model;

use MDClub\Model\Notification;

/**
 * NotificationModel Facade
 */
class NotificationModel extends Abstracts
{
    /**
     * @inheritDoc
     */
    protected static function getFacadeAccessor(): string
    {
        return Notification::class;
    }
}
