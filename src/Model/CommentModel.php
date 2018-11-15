<?php

declare(strict_types=1);

namespace App\Model;

use App\Abstracts\ModelAbstracts;
use App\Helper\ArrayHelper;

/**
 * Class CommentModel
 *
 * @package App\Model
 */
class CommentModel extends ModelAbstracts
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

    protected function beforeInsert(array $data): array
    {
        return ArrayHelper::fill($data, [
            'vote_count' => 0,
        ]);
    }
}
