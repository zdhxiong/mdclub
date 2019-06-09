<?php

declare(strict_types=1);

namespace App\Model;

use App\Abstracts\ModelAbstracts;

/**
 * 评论模型
 */
class Comment extends ModelAbstracts
{
    public $table = 'comment';
    public $primaryKey = 'comment_id';
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

    protected function beforeInsert(array $data): array
    {
        return collect($data)->union([
            'vote_count' => 0,
        ])->all();
    }
}
