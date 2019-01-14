<?php

declare(strict_types=1);

namespace App\Model;

use App\Abstracts\ModelAbstracts;
use App\Helper\ArrayHelper;

/**
 * Class Answer
 * @package App\Model
 */
class Answer extends ModelAbstracts
{
    public $table = 'answer';
    public $primaryKey = 'answer_id';
    protected $timestamps = true;
    protected $softDelete = true;

    public $columns = [
        'answer_id',
        'question_id',
        'user_id',
        'content_markdown',
        'content_rendered',
        'comment_count',
        'vote_count',
        'create_time',
        'update_time',
        'delete_time',
    ];

    protected function beforeInsert(array $data): array
    {
        return ArrayHelper::fill($data, [
            'comment_count' => 0,
            'vote_count'    => 0,
        ]);
    }
}
