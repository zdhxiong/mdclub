<?php

declare(strict_types=1);

namespace App\Model;

use App\Abstracts\ModelAbstracts;

/**
 * 私信模型
 */
class Inbox extends ModelAbstracts
{
    public $table = 'inbox';
    public $primaryKey = 'inbox_id';
    protected $timestamps = true;

    protected const UPDATE_TIME = false;

    public $columns = [
        'inbox_id',
        'receiver_id',
        'sender_id',
        'content_markdown',
        'content_rendered',
        'create_time',
        'read_time',
    ];
}
