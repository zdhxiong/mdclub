<?php

declare(strict_types=1);

namespace MDClub\Facade\Service;

use MDClub\Facade\Service\Traits\Getable;
use MDClub\Initializer\Facade;
use MDClub\Service\Notification;

/**
 * Class NotificationService
 *
 * @method static int   getCount()
 * @method static array readMultiple(array $notificationIds)
 * @method static array read(int $notificationId)
 * @method static void  deleteMultiple(array $notificationIds)
 * @method static void  delete(int $notificationId)
 * @method static self  add(int $receiverId, string $type, array $relationshipIds = [])
 * @method static void  send()
 */
class NotificationService extends Facade
{
    use Getable;

    /**
     * @inheritDoc
     */
    protected static function getFacadeAccessor(): string
    {
        return Notification::class;
    }
}
