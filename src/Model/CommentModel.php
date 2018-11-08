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

    public $columns = [
        'comment_id',
        'commentable_id',
        'commentable_type',
        'user_id',
        'content',
        'vote_count',
        'create_time',
        'update_time',
        'delete_time',
    ];
}
