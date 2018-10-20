<?php

declare(strict_types=1);

namespace App\Model;

use App\Helper\ArrayHelper;

/**
 * Class AnswerModel
 *
 * @package App\Model
 */
class AnswerModel extends Model
{
    protected $table = 'answer';
    protected $primaryKey = 'answer_id';
    protected $timestamps = true;
    protected $softDelete = true;

    protected $columns = [
        'answer_id',
        'question_id',
        'user_id',
        'content_markdown',
        'content_rendered',
        'comment_count',
        'create_time',
        'update_time',
        'delete_time',
    ];

    protected function beforeInsert(array $data): array
    {
        return ArrayHelper::fill($data, [
            'comment_count' => 0,
        ]);
    }

    protected function afterSelect(array $data, bool $nest = true): array
    {
        if (!$nest) {
            $data = [$data];
        }

        return $data;
    }
}
