<?php

declare(strict_types=1);

namespace App\Model;

/**
 * Class NotificationModel
 *
 * @package App\Model
 */
class NotificationModel extends Model
{
    protected $table = 'notification';
    protected $primaryKey = 'notification_id';
    protected $timestamps = true;

    const UPDATE_TIME = false;

    protected $columns = [
        'notification_id',
        'user_id',
        'content',
        'create_time',
        'read_time',
    ];
}
