<?php

declare(strict_types=1);

namespace App\Model;

use App\Helper\ArrayHelper;

/**
 * Class QuestionModel
 *
 * @package App\Model
 */
class QuestionModel extends Model
{
    protected $table = 'question';
    protected $primaryKey = 'question_id';
    protected $timestamps = true;
    protected $softDelete = true;

    protected $columns = [
        'question_id',
        'user_id',
        'title',
        'content_markdown',
        'content_rendered',
        'answer_count',
        'view_count',
        'follower_count',
        'create_time',
        'update_time',
        'delete_time',
    ];

    protected function beforeInsert(array $data): array
    {
        return ArrayHelper::fill($data, [
            'answer_count'     => 0,
            'view_count'       => 0,
            'follower_count'   => 0,
            'last_answer_time' => 0,
        ]);
    }
}
