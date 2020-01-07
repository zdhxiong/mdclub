<?php

declare(strict_types=1);

namespace MDClub\Model;

/**
 * 通知模型
 */
class Notification extends Abstracts
{
    public $table = 'notification';
    public $primaryKey = 'notification_id';
    protected $timestamps = true;

    protected const UPDATE_TIME = false;

    public $columns = [
        'notification_id',
        'user_id',
        'content',
        'create_time',
        'read_time',
    ];
}
