<?php

declare(strict_types=1);

namespace App\Model;

use App\Abstracts\ModelAbstracts;

/**
 * Class Notification
 * @package App\Model
 */
class Notification extends ModelAbstracts
{
    public $table = 'notification';
    public $primaryKey = 'notification_id';
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
