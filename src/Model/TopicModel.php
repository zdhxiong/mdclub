<?php

declare(strict_types=1);

namespace App\Model;

use App\Abstracts\ModelAbstracts;
use App\Helper\ArrayHelper;

/**
 * Class TopicModel
 *
 * @package App\Model
 */
class TopicModel extends ModelAbstracts
{
    protected $table = 'topic';
    protected $primaryKey = 'topic_id';
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
        return ArrayHelper::fill($data, [
            'article_count' => 0,
            'question_count' => 0,
            'follower_count' => 0,
        ]);
    }
}
