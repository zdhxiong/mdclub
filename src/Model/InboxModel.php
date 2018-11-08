<?php

declare(strict_types=1);

namespace App\Model;

/**
 * Class InboxModel
 *
 * @package App\Model
 */
class InboxModel extends Model
{
    protected $table = 'inbox';
    protected $primaryKey = 'inbox_id';
    protected $timestamps = true;

    const UPDATE_TIME = false;

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
