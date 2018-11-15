<?php

declare(strict_types=1);

namespace App\Model;

use App\Abstracts\ModelAbstracts;

/**
 * Class NotificationModel
 *
 * @package App\Model
 */
class NotificationModel extends ModelAbstracts
{
    protected $table = 'notification';
    protected $primaryKey = 'notification_id';
    protected $timestamps = true;

    const UPDATE_TIME = false;

    public $columns = [
        'notification_id',
        'user_id',
        'content',
        'create_time',
        'read_time',
    ];
}
