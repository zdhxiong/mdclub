<?php

declare(strict_types=1);

namespace App\Model;

use App\Abstracts\ModelAbstracts;

/**
 * 话题模型
 */
class Topic extends ModelAbstracts
{
    public $table = 'topic';
    public $primaryKey = 'topic_id';
    protected $softDelete = true;

    public $columns = [
        'topic_id',
        'name',
        'cover',
        'description',
        'article_count',
        'question_count',
        'follower_count',
        'delete_time',
    ];

    protected function beforeInsert(array $data): array
    {
        return collect($data)->union([
            'article_count' => 0,
            'question_count' => 0,
            'follower_count' => 0,
        ])->all();
    }
}
