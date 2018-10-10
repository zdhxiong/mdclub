<?php

declare(strict_types=1);

namespace App\Model;

/**
 * Class CommentModel
 *
 * @package App\Model
 */
class CommentModel extends Model
{
    protected $table = 'comment';
    protected $primaryKey = 'comment_id';
    protected $timestamps = true;
    protected $softDelete = true;

    protected $columns = [
        'comment_id',
        'commentable_id',
        'commentable_type',
        'user_id',
        'content',
        'create_time',
        'update_time',
        'delete_time',
    ];
}
